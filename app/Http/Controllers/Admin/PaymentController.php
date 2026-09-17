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

    public function index(Request $request)
    {
        $this->checkPermission('Payments View');

        $query = Payment::with([
            'client',
            'project',
            'invoice',
            'creator'
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', Auth::id());
        }

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
        ->latest('id')
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

    public function create()
    {
        $this->checkPermission('Payments Create');

        $clients = Client::query()
        ->orderBy('company_name')
        ->orderBy('contact_person')
        ->get();

        $projects = Project::query()
        ->orderBy('name')
        ->get();

        $invoices = Invoice::query()
        ->latest('id')
        ->get();

        return view('admin.payments.create', compact(
            'clients',
            'projects',
            'invoices'
        ));
    }

    public function store(Request $request)
    {
        $this->checkPermission('Payments Create');

        $validated = $request->validate([
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],
            'invoice_id' => [
                'nullable',
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

        if (!empty($validated['project_id'])) {
            $projectExists = Project::whereKey($validated['project_id'])
            ->where('client_id', $validated['client_id'])
            ->exists();

            if (!$projectExists) {
                return back()
                ->withInput()
                ->withErrors([
                    'project_id' => 'Selected project does not belong to the selected client.',
                ]);
            }
        }

        if (!empty($validated['invoice_id'])) {
            $invoice = Invoice::find($validated['invoice_id']);

            if (!$invoice) {
                return back()
                ->withInput()
                ->withErrors([
                    'invoice_id' => 'Selected invoice was not found.',
                ]);
            }
        }

        $validated['payment_number'] = $this->generatePaymentNumber();
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
            ->file('attachment')
            ->store('payments', 'public');
        }

        Payment::create($validated);

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
            'invoice',
            'creator',
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $this->checkPermission('Payments Edit');

        $this->authorizePaymentAccess($payment);

        $clients = Client::query()
        ->orderBy('company_name')
        ->orderBy('contact_person')
        ->get();

        $projects = Project::query()
        ->orderBy('name')
        ->get();

        $invoices = Invoice::query()
        ->latest('id')
        ->get();

        return view('admin.payments.edit', compact(
            'payment',
            'clients',
            'projects',
            'invoices'
        ));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->checkPermission('Payments Edit');

        $this->authorizePaymentAccess($payment);

        $validated = $request->validate([
            'client_id' => [
                'required',
                'integer',
                'exists:clients,id',
            ],
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],
            'invoice_id' => [
                'nullable',
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

        if (!empty($validated['project_id'])) {
            $projectExists = Project::whereKey($validated['project_id'])
            ->where('client_id', $validated['client_id'])
            ->exists();

            if (!$projectExists) {
                return back()
                ->withInput()
                ->withErrors([
                    'project_id' => 'Selected project does not belong to the selected client.',
                ]);
            }
        }

        if (!empty($validated['invoice_id'])) {
            $invoice = Invoice::find($validated['invoice_id']);

            if (!$invoice) {
                return back()
                ->withInput()
                ->withErrors([
                    'invoice_id' => 'Selected invoice was not found.',
                ]);
            }
        }

        if ($request->hasFile('attachment')) {
            if ($payment->attachment) {
                Storage::disk('public')->delete($payment->attachment);
            }

            $validated['attachment'] = $request
            ->file('attachment')
            ->store('payments', 'public');
        }

        $payment->update($validated);

        return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $this->checkPermission('Payments Delete');

        $this->authorizePaymentAccess($payment);

        $payment->delete();

        return redirect()
        ->route('admin.payments.index')
        ->with('success', 'Payment deleted successfully.');
    }

    private function generatePaymentNumber(): string
    {
        return DB::transaction(function () {
            $lastPayment = Payment::withTrashed()
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

            $nextNumber = $lastPayment
            ? $lastPayment->id + 1
            : 1;

            return 'PAY-' . str_pad(
                $nextNumber,
                5,
                '0',
                STR_PAD_LEFT
            );
        });
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
