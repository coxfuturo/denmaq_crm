<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    private function checkPermission(string $permission): void
    {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->hasRole('Super Admin') ||
                $user->can($permission)
            ),
            403,
            'You do not have permission to access this page.'
        );
    }

    private function isSuperAdmin(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('Super Admin');
    }

    private function invoiceQuery()
    {
        $query = Invoice::with([
            'client',
            'creator',
            'quotation',
            'items',
            'payments',
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    public function index(Request $request)
    {
        $this->checkPermission('Invoices View');

        $query = $this->invoiceQuery();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhereHas('quotation', function ($quotationQuery) use ($search) {
                    $quotationQuery->where(
                        'quotation_number',
                        'like',
                        "%{$search}%"
                    );
                })
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($this->isSuperAdmin() && $request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $allowedSorts = [
            'id',
            'invoice_number',
            'invoice_date',
            'due_date',
            'status',
            'total',
            'created_at',
        ];

        $sort = $request->get('sort', 'id');
        $direction = strtolower($request->get('direction', 'desc'));

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $perPage = (int) $request->get('per_page', 15);

        if (!in_array($perPage, [10, 15, 25, 50, 100, 200, 500], true)) {
            $perPage = 15;
        }

        $invoices = $query
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        if ($this->isSuperAdmin()) {
            $users = User::query()
            ->where('status', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

            $clients = Client::query()
            ->orderBy('company_name')
            ->orderBy('id')
            ->get();
        } else {
            $users = collect();

            $clients = Client::query()
            ->where('created_by', auth()->id())
            ->orderBy('company_name')
            ->orderBy('id')
            ->get();
        }

        $statuses = [
            'Draft',
            'Sent',
            'Partially Paid',
            'Paid',
            'Overdue',
            'Cancelled',
        ];

        return view('admin.invoices.index', compact(
            'invoices',
            'users',
            'clients',
            'statuses',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create(Request $request)
    {
        $this->checkPermission('Invoices Create');

        $query = Quotation::query()
        ->with([
            'client',
            'items',
        ])
        ->where('status', 'Accepted')
        ->whereDoesntHave('invoices');

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        $quotations = $query
        ->orderByDesc('id')
        ->get();

        $selectedQuotation = null;

        if ($request->filled('quotation_id')) {
            $selectedQuotation = $quotations->firstWhere(
                'id',
                (int) $request->quotation_id
            );

            abort_unless(
                $selectedQuotation,
                404,
                'Quotation not found or invoice already exists for this quotation.'
            );
        }

        return view('admin.invoices.create', compact(
            'quotations',
            'selectedQuotation'
        ));
    }

    public function store(Request $request)
    {
        $this->checkPermission('Invoices Create');

        $validated = $this->validateInvoice($request);

        $quotation = Quotation::query()
        ->with([
            'client',
            'items',
        ])
        ->where('id', $validated['quotation_id'])
        ->where('status', 'Accepted')
        ->firstOrFail();

        if (!$this->isSuperAdmin()) {
            abort_unless(
                (int) $quotation->created_by === (int) auth()->id(),
                403,
                'You do not have permission to use this quotation.'
            );
        }

        abort_unless(
            !$quotation->invoices()->exists(),
            422,
            'An invoice already exists for this quotation.'
        );

        abort_unless(
            $quotation->items->isNotEmpty(),
            422,
            'The selected quotation does not contain any items.'
        );

        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($quotation->items as $item) {
                $quantity = max((float) $item->quantity, 0);
                $rate = max((float) $item->rate, 0);
                $amount = round($quantity * $rate, 2);

                $subtotal += $amount;
            }

            $subtotal = round($subtotal, 2);

            $discountType = $quotation->discount_type ?: 'fixed';
            $discountValue = round((float) $quotation->discount, 2);

            $discountValue = max($discountValue, 0);

            if ($discountType === 'percentage') {
                $discountValue = min($discountValue, 100);

                $discountAmount = round(
                    $subtotal * ($discountValue / 100),
                    2
                );
            } else {
                $discountAmount = round($discountValue, 2);
            }

            $discountAmount = min(
                max($discountAmount, 0),
                $subtotal
            );

            $taxPercentage = round((float) $quotation->tax, 2);

            $taxPercentage = min(
                max($taxPercentage, 0),
                100
            );

            $taxableAmount = round(
                max($subtotal - $discountAmount, 0),
                2
            );

            $taxAmount = round(
                $taxableAmount * ($taxPercentage / 100),
                2
            );

            $total = round(
                max($taxableAmount + $taxAmount, 0),
                2
            );

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'quotation_id' => $quotation->id,
                'client_id' => $quotation->client_id,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subject' => $quotation->subject,
                'status' => 'Draft',
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax' => $taxPercentage,
                'total' => $total,
                'notes' => $quotation->notes,
                'terms' => $quotation->terms,
                'created_by' => auth()->id(),
            ]);

            foreach ($quotation->items as $index => $item) {
                $quantity = max((float) $item->quantity, 0);
                $rate = max((float) $item->rate, 0);
                $amount = round($quantity * $rate, 2);

                $invoice->items()->create([
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $amount,
                    'position' => $index,
                ]);
            }

            DB::commit();

            return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully from quotation.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
            ->withInput()
            ->with('error', 'Unable to create invoice. Please try again.');
        }
    }

    public function show(Invoice $invoice)
    {
        $this->checkPermission('Invoices View');

        $this->authorizeOwnership($invoice);

        $invoice->load([
            'client',
            'creator',
            'quotation',
            'items',
            'payments',
        ]);

        $paidAmount = $invoice->payments()
        ->where('status', 'completed')
        ->sum('amount');

        $paidAmount = round((float) $paidAmount, 2);

        $remainingAmount = max(
            round((float) $invoice->total - $paidAmount, 2),
            0
        );

        return view('admin.invoices.show', compact(
            'invoice',
            'paidAmount',
            'remainingAmount'
        ));
    }

    public function edit(Invoice $invoice)
    {
        $this->checkPermission('Invoices Edit');

        $this->authorizeOwnership($invoice);

        $invoice->load([
            'items',
            'quotation',
            'client',
        ]);

        return view('admin.invoices.edit', compact(
            'invoice'
        ));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->checkPermission('Invoices Edit');

        $this->authorizeOwnership($invoice);

        $validated = $this->validateInvoice(
            $request,
            $invoice->id,
            false
        );

        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $quantity = max((float) $item['quantity'], 0);
                $rate = max((float) $item['rate'], 0);
                $amount = round($quantity * $rate, 2);

                $subtotal += $amount;
            }

            $subtotal = round($subtotal, 2);

            $discountType = $validated['discount_type'];

            $discountValue = round(
                (float) ($validated['discount_value'] ?? 0),
                2
            );

            $discountValue = max($discountValue, 0);

            if ($discountType === 'percentage') {
                $discountValue = min($discountValue, 100);

                $discountAmount = round(
                    $subtotal * ($discountValue / 100),
                    2
                );
            } else {
                $discountAmount = round($discountValue, 2);
            }

            $discountAmount = min(
                max($discountAmount, 0),
                $subtotal
            );

            $taxPercentage = round(
                (float) ($validated['tax'] ?? 0),
                2
            );

            $taxPercentage = min(
                max($taxPercentage, 0),
                100
            );

            $taxableAmount = round(
                max($subtotal - $discountAmount, 0),
                2
            );

            $taxAmount = round(
                $taxableAmount * ($taxPercentage / 100),
                2
            );

            $total = round(
                max($taxableAmount + $taxAmount, 0),
                2
            );

            $paidAmount = $invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');

            $paidAmount = round((float) $paidAmount, 2);

            if ($total < $paidAmount) {
                return back()
                ->withInput()
                ->with(
                    'error',
                    'Invoice total cannot be less than the amount already paid.'
                );
            }

            $invoice->update([
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'status' => $validated['status'],
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax' => $taxPercentage,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                $quantity = max((float) $item['quantity'], 0);
                $rate = max((float) $item['rate'], 0);
                $amount = round($quantity * $rate, 2);

                $invoice->items()->create([
                    'item_name' => $item['item_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $amount,
                    'position' => $index,
                ]);
            }

            DB::commit();

            return redirect()
            ->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
            ->withInput()
            ->with('error', 'Unable to update invoice. Please try again.');
        }
    }

    public function destroy(Invoice $invoice)
    {
        $this->checkPermission('Invoices Delete');

        $this->authorizeOwnership($invoice);

        $invoice->delete();

        return redirect()
        ->route('admin.invoices.index')
        ->with('success', 'Invoice moved to trash successfully.');
    }

    public function trash(Request $request)
    {
        $this->checkPermission('Invoices Delete');

        $query = Invoice::onlyTrashed()
        ->with([
            'client',
            'creator',
            'quotation',
            'items',
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhereHas('quotation', function ($quotationQuery) use ($search) {
                    $quotationQuery->where(
                        'quotation_number',
                        'like',
                        "%{$search}%"
                    );
                })
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
                });
            });
        }

        if ($this->isSuperAdmin() && $request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $invoices = $query
        ->latest('deleted_at')
        ->paginate(15)
        ->withQueryString();

        $users = $this->isSuperAdmin()
        ? User::query()
        ->where('status', true)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get()
        : collect();

        return view('admin.invoices.trash', compact(
            'invoices',
            'users'
        ));
    }

    public function restore(int $id)
    {
        $this->checkPermission('Invoices Restore');

        $invoice = Invoice::onlyTrashed()->findOrFail($id);

        $this->authorizeOwnership($invoice);

        $invoice->restore();

        return redirect()
        ->route('admin.invoices.trash')
        ->with('success', 'Invoice restored successfully.');
    }

    public function forceDelete(int $id)
    {
        $this->checkPermission('Invoices Force Delete');

        $invoice = Invoice::onlyTrashed()->findOrFail($id);

        $this->authorizeOwnership($invoice);

        DB::beginTransaction();

        try {
            $invoice->items()->delete();
            $invoice->forceDelete();

            DB::commit();

            return redirect()
            ->route('admin.invoices.trash')
            ->with('success', 'Invoice permanently deleted.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
            ->with('error', 'Unable to permanently delete invoice.');
        }
    }

    public function changeStatus(Request $request, Invoice $invoice)
    {
        $this->checkPermission('Invoices Status');

        $this->authorizeOwnership($invoice);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'Draft',
                    'Sent',
                    'Partially Paid',
                    'Paid',
                    'Overdue',
                    'Cancelled',
                ]),
            ],
        ]);

        $invoice->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Invoice status updated successfully.'
        );
    }

    private function validateInvoice(
        Request $request,
        ?int $invoiceId = null,
        bool $requireQuotation = true
    ): array {
        $rules = [
            'invoice_date' => [
                'required',
                'date',
            ],
            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:invoice_date',
            ],
            'subject' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::in([
                    'Draft',
                    'Sent',
                    'Partially Paid',
                    'Paid',
                    'Overdue',
                    'Cancelled',
                ]),
            ],
            'discount_type' => [
                'required',
                Rule::in([
                    'percentage',
                    'fixed',
                ]),
            ],
            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'tax' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'terms' => [
                'nullable',
                'string',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.item_name' => [
                'required',
                'string',
                'max:255',
            ],
            'items.*.description' => [
                'nullable',
                'string',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'items.*.rate' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];

        if ($requireQuotation) {
            $rules['quotation_id'] = [
                'required',
                'integer',
                'exists:quotations,id',
            ];
        }

        $validated = $request->validate($rules);

        $discountType = $validated['discount_type'];

        $discountValue = (float) ($validated['discount_value'] ?? 0);

        if (
            $discountType === 'percentage' &&
            $discountValue > 100
        ) {
            abort(
                422,
                'Percentage discount cannot be greater than 100.'
            );
        }

        return $validated;
    }

    private function authorizeOwnership(Invoice $invoice): void
    {
        if ($this->isSuperAdmin()) {
            return;
        }

        abort_unless(
            (int) $invoice->created_by === (int) auth()->id(),
            403,
            'You do not have permission to access this invoice.'
        );
    }

    private function generateInvoiceNumber(): string
    {
        $lastInvoice = Invoice::withTrashed()
        ->orderByDesc('id')
        ->first();

        if (!$lastInvoice) {
            return 'INV-0001';
        }

        preg_match(
            '/(\d+)$/',
            $lastInvoice->invoice_number,
            $matches
        );

        $lastNumber = isset($matches[1])
        ? (int) $matches[1]
        : $lastInvoice->id;

        $nextNumber = $lastNumber + 1;

        return 'INV-' . str_pad(
            $nextNumber,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}