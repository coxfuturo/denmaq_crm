<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
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
            403
        );
    }

    private function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    private function quotationQuery()
    {
        $query = Quotation::query();

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    private function statuses(): array
    {
        return [
            'Draft',
            'Sent',
            'Accepted',
            'Rejected',
            'Expired',
        ];
    }

    public function index(Request $request)
    {
        $this->checkPermission('Quotations View');

        $query = $this->quotationQuery()
        ->with(['client', 'creator'])
        ->withCount('items');

        $search = trim($request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%')
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->query('client_id'));
        }

        if ($this->isSuperAdmin() && $request->filled('created_by')) {
            $query->where('created_by', (int) $request->query('created_by'));
        }

        $allowedSorts = [
            'id',
            'quotation_number',
            'quotation_date',
            'valid_until',
            'subtotal',
            'total',
            'status',
            'created_at',
        ];

        $sort = $request->query('sort', 'id');
        $direction = $request->query('direction', 'desc');

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        $allowedPerPage = [10, 25, 50, 100, 200, 500];

        $perPage = (int) $request->query('per_page', 10);

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $quotations = $query
        ->paginate($perPage)
        ->withQueryString();

        $clientsQuery = Client::query()
        ->where('status', true);

        if (!$this->isSuperAdmin()) {
            $clientsQuery->where('created_by', auth()->id());
        }

        $clients = $clientsQuery
        ->orderBy('company_name')
        ->get();

        $users = $this->isSuperAdmin()
        ? User::query()
        ->where('status', true)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get()
        : collect();

        $statuses = $this->statuses();

        return view('admin.quotations.index', compact(
            'quotations',
            'clients',
            'users',
            'statuses',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create()
    {
        $this->checkPermission('Quotations Create');

        $clientsQuery = Client::query()
        ->where('status', true);

        if (!$this->isSuperAdmin()) {
            $clientsQuery->where('created_by', auth()->id());
        }

        $clients = $clientsQuery
        ->orderBy('company_name')
        ->get();

        $statuses = $this->statuses();

        return view('admin.quotations.create', compact(
            'clients',
            'statuses'
        ));
    }

    public function store(Request $request)
    {
        $this->checkPermission('Quotations Create');

        $validated = $this->validateQuotation($request);

        $this->validateClientOwnership((int) $validated['client_id']);

        $quotation = DB::transaction(function () use ($validated) {
            $quotation = Quotation::create([
                'quotation_number' => $this->generateQuotationNumber(),
                'client_id' => $validated['client_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'subject' => trim($validated['subject']),
                'subtotal' => 0,
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'] ?? 0,
                'discount_amount' => 0,
                'tax' => $validated['tax'] ?? 0,
                'total' => 0,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $subtotal = $this->createItems(
                $quotation,
                $validated['items']
            );

            $discountValue = (float) ($validated['discount_value'] ?? 0);

            if ($validated['discount_type'] === 'percentage') {
                $discountAmount = round(
                    $subtotal * ($discountValue / 100),
                    2
                );
            } else {
                $discountAmount = $discountValue;
            }

            $discountAmount = min(
                max($discountAmount, 0),
                $subtotal
            );

            $taxAmount = round(
                (float) ($validated['tax'] ?? 0),
                2
            );

            $total = max(
                0,
                round(
                    $subtotal - $discountAmount + $taxAmount,
                    2
                )
            );

            $quotation->update([
                'subtotal' => $subtotal,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax' => $taxAmount,
                'total' => $total,
            ]);

            return $quotation;
        });

        return redirect()
        ->route('admin.quotations.index')
        ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $this->checkPermission('Quotations View');

        $this->authorizeOwnership($quotation);

        $quotation->load([
            'client',
            'creator',
            'items',
        ]);

        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->checkPermission('Quotations Edit');

        $this->authorizeOwnership($quotation);

        $quotation->load('items');

        $clientsQuery = Client::query()
        ->where('status', true);

        if (!$this->isSuperAdmin()) {
            $clientsQuery->where('created_by', auth()->id());
        }

        $clients = $clientsQuery
        ->orderBy('company_name')
        ->get();

        $statuses = $this->statuses();

        return view('admin.quotations.edit', compact(
            'quotation',
            'clients',
            'statuses'
        ));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->checkPermission('Quotations Edit');

        $this->authorizeOwnership($quotation);

        $validated = $this->validateQuotation($request);

        $this->validateClientOwnership((int) $validated['client_id']);

        DB::transaction(function () use ($validated, $quotation) {
            $quotation->update([
                'client_id' => $validated['client_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'subject' => trim($validated['subject']),
                'discount_type' => $validated['discount_type'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $quotation->items()->delete();

            $subtotal = $this->createItems(
                $quotation,
                $validated['items']
            );

            $discountValue = (float) ($validated['discount_value'] ?? 0);

            if ($validated['discount_type'] === 'percentage') {
                $discountAmount = round(
                    $subtotal * ($discountValue / 100),
                    2
                );
            } else {
                $discountAmount = $discountValue;
            }

            $discountAmount = min(
                max($discountAmount, 0),
                $subtotal
            );

            $taxAmount = round(
                (float) ($validated['tax'] ?? 0),
                2
            );

            $total = max(
                0,
                round(
                    $subtotal - $discountAmount + $taxAmount,
                    2
                )
            );

            $quotation->update([
                'subtotal' => $subtotal,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax' => $taxAmount,
                'total' => $total,
            ]);
        });

        return redirect()
        ->route('admin.quotations.index')
        ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->checkPermission('Quotations Delete');

        $this->authorizeOwnership($quotation);

        $quotation->delete();

        return redirect()
        ->route('admin.quotations.index')
        ->with('success', 'Quotation moved to trash successfully.');
    }

    public function trash(Request $request)
    {
        $this->checkPermission('Quotations Delete');

        $query = Quotation::onlyTrashed()
        ->with(['client', 'creator', 'items']);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        $search = trim($request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%')
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%');
                });
            });
        }

        if ($this->isSuperAdmin() && $request->filled('created_by')) {
            $query->where(
                'created_by',
                (int) $request->query('created_by')
            );
        }

        $quotations = $query
        ->latest('deleted_at')
        ->paginate(10)
        ->withQueryString();

        $users = $this->isSuperAdmin()
        ? User::query()
        ->where('status', true)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get()
        : collect();

        return view('admin.quotations.trash', compact(
            'quotations',
            'users',
            'search'
        ));
    }

    public function restore(int $id)
    {
        $this->checkPermission('Quotations Delete');

        $quotation = Quotation::onlyTrashed()->findOrFail($id);

        $this->authorizeOwnership($quotation);

        $quotation->restore();

        return redirect()
        ->route('admin.quotations.trash')
        ->with('success', 'Quotation restored successfully.');
    }

    public function forceDelete(int $id)
    {
        $this->checkPermission('Quotations Delete');

        $quotation = Quotation::onlyTrashed()->findOrFail($id);

        $this->authorizeOwnership($quotation);

        DB::transaction(function () use ($quotation) {
            $quotation->items()->forceDelete();
            $quotation->forceDelete();
        });

        return redirect()
        ->route('admin.quotations.trash')
        ->with('success', 'Quotation permanently deleted.');
    }

    public function changeStatus(Request $request, Quotation $quotation)
    {
        $this->checkPermission('Quotations Status');

        $this->authorizeOwnership($quotation);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in($this->statuses()),
            ],
        ]);

        $quotation->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Quotation status updated successfully.'
        );
    }

    private function validateQuotation(Request $request): array
    {
        $validated = $request->validate([
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'quotation_date' => [
                'required',
                'date',
            ],
            'valid_until' => [
                'nullable',
                'date',
                'after_or_equal:quotation_date',
            ],
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'discount_type' => [
                'required',
                Rule::in([
                    'fixed',
                    'percentage',
                ]),
            ],
            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'tax' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'status' => [
                'required',
                Rule::in($this->statuses()),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'terms' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'items.*.item_name' => [
                'required',
                'string',
                'max:255',
            ],
            'items.*.description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100000000',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'items.*.rate' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
        ]);

        if (
            $validated['discount_type'] === 'percentage' &&
            (float) ($validated['discount_value'] ?? 0) > 100
        ) {
            abort(
                422,
                'Percentage discount cannot be greater than 100.'
            );
        }

        return $validated;
    }

    private function createItems(Quotation $quotation, array $items): float
    {
        $subtotal = 0;

        foreach ($items as $index => $item) {
            $quantity = round((float) $item['quantity'], 2);
            $rate = round((float) $item['rate'], 2);
            $amount = round($quantity * $rate, 2);

            $quotation->items()->create([
                'item_name' => trim($item['item_name']),
                'description' => isset($item['description'])
                ? trim($item['description'])
                : null,
                'quantity' => $quantity,
                'rate' => $rate,
                'amount' => $amount,
                'position' => $index,
            ]);

            $subtotal += $amount;
        }

        return round($subtotal, 2);
    }

    private function validateClientOwnership(int $clientId): void
    {
        if ($this->isSuperAdmin()) {
            return;
        }

        abort_unless(
            Client::query()
            ->where('id', $clientId)
            ->where('created_by', auth()->id())
            ->exists(),
            403
        );
    }

    private function authorizeOwnership(Quotation $quotation): void
    {
        if (
            !$this->isSuperAdmin() &&
            (int) $quotation->created_by !== (int) auth()->id()
        ) {
            abort(403);
        }
    }

    private function generateQuotationNumber(): string
    {
        do {
            $number = 'QT-' .
            now()->format('Ym') .
            '-' .
            random_int(10000, 99999);
        } while (
            Quotation::withTrashed()
            ->where('quotation_number', $number)
            ->exists()
        );

        return $number;
    }
}