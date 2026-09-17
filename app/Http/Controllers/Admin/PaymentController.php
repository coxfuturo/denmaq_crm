<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    private function checkPermission(string $permission): void
    {
        $user = Auth::user();

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
        return Auth::check() && Auth::user()->hasRole('Super Admin');
    }

    private function paymentQuery()
    {
        $query = Payment::with([
            'client',
            'project',
            'invoice',
            'creator',
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', Auth::id());
        }

        return $query;
    }

    public function index(Request $request)
    {
        $this->checkPermission('Payments View');

        $query = $this->paymentQuery();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                ->orWhere('transaction_id', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
                })
                ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                    $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($this->isSuperAdmin() && $request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 15, 25, 50, 100, 200, 500], true)) {
            $perPage = 10;
        }

        $payments = $query
        ->latest('id')
        ->paginate($perPage)
        ->withQueryString();

        $clients = Client::query()
        ->orderBy('company_name')
        ->orderBy('contact_person')
        ->get();

        $projects = Project::query()
        ->orderBy('name')
        ->get();

        $invoices = Invoice::query()
        ->select([
            'id',
            'invoice_number',
            'client_id',
            'total',
            'status',
        ])
        ->with('client')
        ->orderByDesc('id')
        ->get();

        $users = $this->isSuperAdmin()
        ? User::query()
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get()
        : collect();

        return view('admin.payments.index', compact(
            'payments',
            'clients',
            'projects',
            'invoices',
            'users'
        ));
    }

    public function create(Request $request)
    {
        $this->checkPermission('Payments Create');

        $invoiceQuery = Invoice::with('client')
        ->whereNotIn('status', ['Cancelled']);

        if (!$this->isSuperAdmin()) {
            $invoiceQuery->where('created_by', Auth::id());
        }

        $invoices = $invoiceQuery
        ->orderByDesc('id')
        ->get()
        ->filter(function ($invoice) {
            return $this->calculateRemainingDue($invoice) > 0;
        })
        ->values();

        $invoiceData = $invoices->map(function ($invoice) {
            $paidAmount = $this->calculatePaidAmount($invoice);
            $total = (float) $invoice->total;
            $remainingDue = max($total - $paidAmount, 0);

            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'client_name' => $invoice->client?->company_name ?: $invoice->client?->name,
                'invoice_date' => $invoice->invoice_date?->format('d M Y'),
                'due_date' => $invoice->due_date?->format('d M Y'),
                'status' => $invoice->status,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'remaining_due' => $remainingDue,
            ];
        })->values();

        $selectedInvoice = null;

        if ($request->filled('invoice_id')) {
            $selectedInvoice = $invoices->firstWhere(
                'id',
                (int) $request->invoice_id
            );
        }

        return view('admin.payments.create', compact(
            'invoices',
            'invoiceData',
            'selectedInvoice'
        ));
    }

    public function store(Request $request)
    {
        $this->checkPermission('Payments Create');

        $validated = $request->validate([
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id',
            ],
            'amount' => [
                'required',
                'regex:/^\d{1,13}(\.\d{1,2})?$/',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
            ],
            'payment_date' => [
                'required',
                'date',
            ],
            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'upi',
                    'bank_transfer',
                    'cheque',
                    'credit_card',
                    'debit_card',
                    'other',
                ]),
            ],
            'transaction_id' => [
                'nullable',
                'string',
                'max:100',
            ],
            'bank_account' => [
                'nullable',
                'string',
                'max:100',
            ],
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'completed',
                    'failed',
                    'cancelled',
                    'refunded',
                    'partially_refunded',
                ]),
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:5120',
            ],
        ], [
            'amount.regex' => 'Amount must be a valid number with maximum 2 decimal places.',
            'amount.min' => 'Amount must be greater than 0.',
            'amount.max' => 'Amount is too large.',
            'attachment.max' => 'Payment proof must not exceed 5 MB.',
            'attachment.mimes' => 'Payment proof must be JPG, JPEG, PNG, WEBP or PDF.',
        ]);

        $invoice = Invoice::with('client')->find($validated['invoice_id']);

        if (!$invoice) {
            return back()
            ->withInput()
            ->withErrors([
                'invoice_id' => 'Selected invoice was not found.',
            ]);
        }

        if (
            !$this->isSuperAdmin() &&
            (int) $invoice->created_by !== (int) Auth::id()
        ) {
            abort(403, 'You do not have permission to access this invoice.');
        }

        if ($invoice->status === 'Cancelled') {
            return back()
            ->withInput()
            ->withErrors([
                'invoice_id' => 'Payment cannot be added to a cancelled invoice.',
            ]);
        }

        $remainingDue = $this->calculateRemainingDue($invoice);

        if ($remainingDue <= 0) {
            return back()
            ->withInput()
            ->withErrors([
                'invoice_id' => 'This invoice is already fully paid.',
            ]);
        }

        if (
            $validated['status'] === 'completed' &&
            (float) $validated['amount'] > $remainingDue
        ) {
            return back()
            ->withInput()
            ->withErrors([
                'amount' => 'Payment amount cannot be greater than the remaining due amount of ₹' . number_format($remainingDue, 2),
            ]);
        }

        DB::transaction(function () use ($request, $validated, $invoice) {
            $attachment = null;

            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment')->store(
                    'payments',
                    'public'
                );
            }

            Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'client_id' => $invoice->client_id,
                'project_id' => null,
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'bank_account' => $validated['bank_account'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'attachment' => $attachment,
                'created_by' => Auth::id(),
            ]);

            $this->updateInvoicePaymentStatus($invoice->fresh());
        });

        return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        $this->checkPermission('Payments View');

        $this->authorizePaymentAccess($payment);

        $payment->load([
            'client',
            'project',
            'invoice.client',
            'invoice.payments',
            'creator',
        ]);

        $paidAmount = 0;
        $remainingDue = 0;

        if ($payment->invoice) {
            $paidAmount = $this->calculatePaidAmount($payment->invoice);
            $remainingDue = max(
                (float) $payment->invoice->total - $paidAmount,
                0
            );
        }

        return view('admin.payments.show', compact(
            'payment',
            'paidAmount',
            'remainingDue'
        ));
    }

    public function edit(Payment $payment)
    {
        $this->checkPermission('Payments Edit');

        $this->authorizePaymentAccess($payment);

        $payment->load([
            'invoice.client',
        ]);

        $invoice = $payment->invoice;

        if (!$invoice) {
            return redirect()
            ->route('admin.payments.index')
            ->with('error', 'Invoice not found for this payment.');
        }

        if ($invoice->status === 'Cancelled') {
            return redirect()
            ->route('admin.payments.index')
            ->with('error', 'Payment for a cancelled invoice cannot be edited.');
        }

        $paidOtherPayments = (float) Payment::where('invoice_id', $invoice->id)
        ->where('status', 'completed')
        ->where('id', '!=', $payment->id)
        ->sum('amount');

        $remainingForPayment = max(
            (float) $invoice->total - $paidOtherPayments,
            0
        );

        return view('admin.payments.edit', compact(
            'payment',
            'invoice',
            'remainingForPayment'
        ));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->checkPermission('Payments Edit');

        $this->authorizePaymentAccess($payment);

        $validated = $request->validate([
            'amount' => [
                'required',
                'regex:/^\d{1,13}(\.\d{1,2})?$/',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
            ],
            'payment_date' => [
                'required',
                'date',
            ],
            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'upi',
                    'bank_transfer',
                    'cheque',
                    'credit_card',
                    'debit_card',
                    'other',
                ]),
            ],
            'transaction_id' => [
                'nullable',
                'string',
                'max:100',
            ],
            'bank_account' => [
                'nullable',
                'string',
                'max:100',
            ],
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'completed',
                    'failed',
                    'cancelled',
                    'refunded',
                    'partially_refunded',
                ]),
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,webp',
                'max:5120',
            ],
        ], [
            'amount.regex' => 'Amount must be a valid number with maximum 2 decimal places.',
            'amount.min' => 'Amount must be greater than 0.',
            'amount.max' => 'Amount is too large.',
            'attachment.max' => 'Payment proof must not exceed 5 MB.',
            'attachment.mimes' => 'Payment proof must be JPG, JPEG, PNG, WEBP or PDF.',
        ]);

        $invoice = Invoice::find($payment->invoice_id);

        if (!$invoice) {
            return back()
            ->withInput()
            ->withErrors([
                'amount' => 'Invoice not found for this payment.',
            ]);
        }

        if ($invoice->status === 'Cancelled') {
            return back()
            ->withInput()
            ->withErrors([
                'amount' => 'Payment for a cancelled invoice cannot be updated.',
            ]);
        }

        if (
            !$this->isSuperAdmin() &&
            (int) $invoice->created_by !== (int) Auth::id()
        ) {
            abort(403, 'You do not have permission to access this invoice.');
        }

        $otherCompletedAmount = (float) Payment::where('invoice_id', $invoice->id)
        ->where('status', 'completed')
        ->where('id', '!=', $payment->id)
        ->sum('amount');

        if ($validated['status'] === 'completed') {
            $maximumAllowed = max(
                (float) $invoice->total - $otherCompletedAmount,
                0
            );

            if ((float) $validated['amount'] > $maximumAllowed) {
                return back()
                ->withInput()
                ->withErrors([
                    'amount' => 'Payment amount cannot be greater than ₹' . number_format($maximumAllowed, 2),
                ]);
            }
        }

        DB::transaction(function () use ($request, $validated, $payment, $invoice) {
            $data = [
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'bank_account' => $validated['bank_account'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ];

            if ($request->hasFile('attachment')) {
                if ($payment->attachment) {
                    Storage::disk('public')->delete($payment->attachment);
                }

                $data['attachment'] = $request
                ->file('attachment')
                ->store('payments', 'public');
            }

            $payment->update($data);

            $this->updateInvoicePaymentStatus($invoice->fresh());
        });

        return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $this->checkPermission('Payments Delete');

        $this->authorizePaymentAccess($payment);

        $invoice = $payment->invoice;

        DB::transaction(function () use ($payment, $invoice) {
            $payment->delete();

            if ($invoice) {
                $this->updateInvoicePaymentStatus($invoice->fresh());
            }
        });

        return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment moved to trash successfully.');
    }

    public function trash()
    {
        $this->checkPermission('Payments Delete');

        $query = Payment::onlyTrashed()->with([
            'client',
            'project',
            'invoice',
            'creator',
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', Auth::id());
        }

        $payments = $query
        ->latest('deleted_at')
        ->paginate(15);

        return view('admin.payments.trash', compact('payments'));
    }

    public function restore($id)
    {
        $this->checkPermission('Payments Restore');

        $payment = Payment::onlyTrashed()->findOrFail($id);

        if (
            !$this->isSuperAdmin() &&
            (int) $payment->created_by !== (int) Auth::id()
        ) {
            abort(403);
        }

        DB::transaction(function () use ($payment) {
            $payment->restore();

            $invoice = Invoice::find($payment->invoice_id);

            if ($invoice) {
                $this->updateInvoicePaymentStatus($invoice->fresh());
            }
        });

        return redirect()
        ->route('admin.payments.trash')
        ->with('success', 'Payment restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->checkPermission('Payments Force Delete');

        $payment = Payment::onlyTrashed()->findOrFail($id);

        if (
            !$this->isSuperAdmin() &&
            (int) $payment->created_by !== (int) Auth::id()
        ) {
            abort(403);
        }

        $invoice = Invoice::find($payment->invoice_id);

        DB::transaction(function () use ($payment, $invoice) {
            if ($payment->attachment) {
                Storage::disk('public')->delete($payment->attachment);
            }

            $payment->forceDelete();

            if ($invoice) {
                $this->updateInvoicePaymentStatus($invoice->fresh());
            }
        });

        return redirect()
        ->route('admin.payments.trash')
        ->with('success', 'Payment permanently deleted.');
    }

    private function calculatePaidAmount(Invoice $invoice): float
    {
        return (float) Payment::where('invoice_id', $invoice->id)
        ->where('status', 'completed')
        ->sum('amount');
    }

    private function calculateRemainingDue(Invoice $invoice): float
    {
        $paidAmount = $this->calculatePaidAmount($invoice);

        return max(
            (float) $invoice->total - $paidAmount,
            0
        );
    }

    private function updateInvoicePaymentStatus(Invoice $invoice): void
    {
        if ($invoice->status === 'Cancelled') {
            return;
        }

        $paidAmount = $this->calculatePaidAmount($invoice);
        $total = (float) $invoice->total;

        if ($total > 0 && $paidAmount >= $total) {
            $invoice->update([
                'status' => 'Paid',
            ]);

            return;
        }

        if ($paidAmount > 0) {
            $invoice->update([
                'status' => 'Partially Paid',
            ]);

            return;
        }

        if (in_array($invoice->status, ['Paid', 'Partially Paid'], true)) {
            $invoice->update([
                'status' => 'Draft',
            ]);
        }
    }

    private function generatePaymentNumber(): string
    {
        $lastPayment = Payment::withTrashed()
        ->orderByDesc('id')
        ->first();

        $nextNumber = $lastPayment
        ? $lastPayment->id + 1
        : 1;

        do {
            $paymentNumber = 'PAY-' . str_pad(
                $nextNumber,
                5,
                '0',
                STR_PAD_LEFT
            );

            $exists = Payment::withTrashed()
            ->where('payment_number', $paymentNumber)
            ->exists();

            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $paymentNumber;
    }

    private function authorizePaymentAccess(Payment $payment): void
    {
        if (
            !$this->isSuperAdmin() &&
            (int) $payment->created_by !== (int) Auth::id()
        ) {
            abort(403, 'You do not have permission to access this payment.');
        }
    }
}
