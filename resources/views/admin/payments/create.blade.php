@extends('admin.layout.app')

@section('title', 'Add Payment')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canCreate = $isSuperAdmin || ($user && $user->can('Payments Create'));
@endphp

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1">Add Payment</h4>
                    <p class="text-muted mb-0">Record a payment against an invoice</p>
                </div>

                <div>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line me-1"></i>
                        Back
                    </a>
                </div>
            </div>

        </div>
    </div>

    @if(!$canCreate)

    <div class="alert alert-danger">
        You do not have permission to create payments.
    </div>

    @else

    @if($errors->any())

    <div class="alert alert-danger alert-dismissible fade show">
        <div class="fw-semibold mb-2">
            Please fix the following errors:
        </div>

        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    @endif

    <form
    action="{{ route('admin.payments.store') }}"
    method="POST"
    enctype="multipart/form-data"
    id="paymentForm"
    >

    @csrf

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card mb-4">

                <div class="card-header">
                    <h5 class="mb-0">Invoice Information</h5>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-12">

                            <label for="invoice_id" class="form-label">
                                Invoice <span class="text-danger">*</span>
                            </label>

                            <select
                            name="invoice_id"
                            id="invoice_id"
                            class="form-select @error('invoice_id') is-invalid @enderror"
                            required
                            >

                            <option value="">Select Invoice</option>

                            @foreach($invoices as $invoice)

                            <option
                            value="{{ $invoice->id }}"
                            {{ old('invoice_id', $selectedInvoice?->id) == $invoice->id ? 'selected' : '' }}
                            >
                            {{ $invoice->invoice_number }} -
                            {{ $invoice->client?->company_name ?: $invoice->client?->name }}
                        </option>

                        @endforeach

                    </select>

                    @error('invoice_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if($invoices->isEmpty())

                    <div class="text-danger small mt-2">
                        No invoices with outstanding balance are available.
                    </div>

                    @else

                    <div class="form-text">
                        Only invoices with an outstanding balance are shown.
                    </div>

                    @endif

                </div>

                <div class="col-md-6">

                    <label class="form-label">Client</label>

                    <input
                    type="text"
                    id="client_display"
                    class="form-control"
                    value="{{ $selectedInvoice?->client?->company_name ?: $selectedInvoice?->client?->name }}"
                    readonly
                    >

                </div>

                <div class="col-md-6">

                    <label class="form-label">Invoice Number</label>

                    <input
                    type="text"
                    id="invoice_number_display"
                    class="form-control"
                    value="{{ $selectedInvoice?->invoice_number }}"
                    readonly
                    >

                </div>

                <div class="col-md-4">

                    <label class="form-label">Invoice Date</label>

                    <input
                    type="text"
                    id="invoice_date_display"
                    class="form-control"
                    value="{{ $selectedInvoice?->invoice_date?->format('d M Y') }}"
                    readonly
                    >

                </div>

                <div class="col-md-4">

                    <label class="form-label">Due Date</label>

                    <input
                    type="text"
                    id="due_date_display"
                    class="form-control"
                    value="{{ $selectedInvoice?->due_date?->format('d M Y') }}"
                    readonly
                    >

                </div>

                <div class="col-md-4">

                    <label class="form-label">Invoice Status</label>

                    <input
                    type="text"
                    id="invoice_status_display"
                    class="form-control"
                    value="{{ $selectedInvoice?->status }}"
                    readonly
                    >

                </div>

            </div>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Payment Details</h5>
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">

                    <label for="amount" class="form-label">
                        Payment Amount <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">₹</span>

                        <input
                        type="text"
                        name="amount"
                        id="amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        value="{{ old('amount') }}"
                        placeholder="Enter payment amount"
                        inputmode="decimal"
                        autocomplete="off"
                        required
                        >

                    </div>

                    <div id="amountHelp" class="form-text">
                        Select an invoice to see the maximum payable amount.
                    </div>

                    @error('amount')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    <div id="amountError" class="text-danger small mt-1 d-none"></div>

                </div>

                <div class="col-md-6">

                    <label for="payment_date" class="form-label">
                        Payment Date <span class="text-danger">*</span>
                    </label>

                    <input
                    type="date"
                    name="payment_date"
                    id="payment_date"
                    class="form-control @error('payment_date') is-invalid @enderror"
                    value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                    required
                    >

                    @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                </div>

                <div class="col-md-6">

                    <label for="payment_method" class="form-label">
                        Payment Method <span class="text-danger">*</span>
                    </label>

                    <select
                    name="payment_method"
                    id="payment_method"
                    class="form-select @error('payment_method') is-invalid @enderror"
                    required
                    >

                    <option value="">Select Payment Method</option>

                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>
                        Cash
                    </option>

                    <option value="upi" {{ old('payment_method') === 'upi' ? 'selected' : '' }}>
                        UPI
                    </option>

                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                        Bank Transfer
                    </option>

                    <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>
                        Cheque
                    </option>

                    <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>
                        Credit Card
                    </option>

                    <option value="debit_card" {{ old('payment_method') === 'debit_card' ? 'selected' : '' }}>
                        Debit Card
                    </option>

                    <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>
                        Other
                    </option>

                </select>

                @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>

            <div class="col-md-6">

                <label for="status" class="form-label">
                    Status <span class="text-danger">*</span>
                </label>

                <select
                name="status"
                id="status"
                class="form-select @error('status') is-invalid @enderror"
                required
                >

                <option value="completed" {{ old('status', 'completed') === 'completed' ? 'selected' : '' }}>
                    Completed
                </option>

                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>
                    Pending
                </option>

                <option value="failed" {{ old('status') === 'failed' ? 'selected' : '' }}>
                    Failed
                </option>

                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>

                <option value="refunded" {{ old('status') === 'refunded' ? 'selected' : '' }}>
                    Refunded
                </option>

                <option value="partially_refunded" {{ old('status') === 'partially_refunded' ? 'selected' : '' }}>
                    Partially Refunded
                </option>

            </select>

            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="col-md-6">

            <label for="transaction_id" class="form-label">
                Transaction ID
            </label>

            <input
            type="text"
            name="transaction_id"
            id="transaction_id"
            class="form-control @error('transaction_id') is-invalid @enderror"
            value="{{ old('transaction_id') }}"
            maxlength="100"
            placeholder="Enter transaction ID"
            >

            @error('transaction_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="col-md-6">

            <label for="bank_account" class="form-label">
                Bank Account
            </label>

            <input
            type="text"
            name="bank_account"
            id="bank_account"
            class="form-control @error('bank_account') is-invalid @enderror"
            value="{{ old('bank_account') }}"
            maxlength="100"
            placeholder="Enter bank account details"
            >

            @error('bank_account')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="col-12">

            <label for="attachment" class="form-label">
                Payment Proof
            </label>

            <input
            type="file"
            name="attachment"
            id="attachment"
            class="form-control @error('attachment') is-invalid @enderror"
            accept=".jpg,.jpeg,.png,.pdf,.webp"
            >

            <small class="text-muted">
                JPG, JPEG, PNG, WEBP or PDF. Maximum 5 MB.
            </small>

            @error('attachment')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

        <div class="col-12">

            <label for="notes" class="form-label">
                Notes
            </label>

            <textarea
            name="notes"
            id="notes"
            rows="4"
            class="form-control @error('notes') is-invalid @enderror"
            placeholder="Enter payment notes"
            >{{ old('notes') }}</textarea>

            @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>

    </div>

</div>

</div>

</div>

<div class="col-lg-4">

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Invoice Summary</h5>
        </div>

        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <span>Invoice Total</span>
                <strong id="invoice_total_display">₹0.00</strong>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <span>Paid Amount</span>
                <strong id="paid_amount_display" class="text-success">₹0.00</strong>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Remaining Due</span>
                <strong id="remaining_due_display" class="text-danger fs-5">₹0.00</strong>
            </div>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-body">

            <div id="paymentInfo" class="alert alert-info mb-0">
                <i class="ri-information-line me-1"></i>
                Select an invoice to enter its payment.
            </div>

        </div>

    </div>

    <div class="d-flex flex-wrap gap-2">

        <button
        type="submit"
        class="btn btn-primary"
        id="savePaymentButton"
        >
        <i class="ri-save-line me-1"></i>
        Save Payment
    </button>

    <a
    href="{{ route('admin.payments.index') }}"
    class="btn btn-light"
    >
    Cancel
</a>

</div>

</div>

</div>

</form>

@endif

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const invoiceSelect = document.getElementById('invoice_id');
        const clientDisplay = document.getElementById('client_display');
        const invoiceNumberDisplay = document.getElementById('invoice_number_display');
        const invoiceDateDisplay = document.getElementById('invoice_date_display');
        const dueDateDisplay = document.getElementById('due_date_display');
        const invoiceStatusDisplay = document.getElementById('invoice_status_display');
        const invoiceTotalDisplay = document.getElementById('invoice_total_display');
        const paidAmountDisplay = document.getElementById('paid_amount_display');
        const remainingDueDisplay = document.getElementById('remaining_due_display');
        const amount = document.getElementById('amount');
        const amountHelp = document.getElementById('amountHelp');
        const amountError = document.getElementById('amountError');
        const paymentInfo = document.getElementById('paymentInfo');
        const paymentForm = document.getElementById('paymentForm');
        const savePaymentButton = document.getElementById('savePaymentButton');

        const invoices = @json($invoiceData);

        const invoiceMap = {};

        invoices.forEach(function (invoice) {
            invoiceMap[String(invoice.id)] = invoice;
        });

        function money(value) {
            const number = Number(value) || 0;

            return '₹' + number.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function resetSummary() {
            clientDisplay.value = '';
            invoiceNumberDisplay.value = '';
            invoiceDateDisplay.value = '';
            dueDateDisplay.value = '';
            invoiceStatusDisplay.value = '';
            invoiceTotalDisplay.textContent = money(0);
            paidAmountDisplay.textContent = money(0);
            remainingDueDisplay.textContent = money(0);
            amountHelp.textContent = 'Select an invoice to see the maximum payable amount.';
            paymentInfo.className = 'alert alert-info mb-0';
            paymentInfo.innerHTML = '<i class="ri-information-line me-1"></i> Select an invoice to enter its payment.';
            amount.removeAttribute('max');
            amountError.classList.add('d-none');
            amountError.textContent = '';
            amount.classList.remove('is-invalid');
        }

        function renderInvoice(invoice) {
            if (!invoice) {
                resetSummary();
                return;
            }

            clientDisplay.value = invoice.client_name || '';
            invoiceNumberDisplay.value = invoice.invoice_number || '';
            invoiceDateDisplay.value = invoice.invoice_date || '';
            dueDateDisplay.value = invoice.due_date || '';
            invoiceStatusDisplay.value = invoice.status || '';

            invoiceTotalDisplay.textContent = money(invoice.total);
            paidAmountDisplay.textContent = money(invoice.paid_amount);
            remainingDueDisplay.textContent = money(invoice.remaining_due);

            amount.max = Number(invoice.remaining_due).toFixed(2);

            if (Number(invoice.remaining_due) > 0) {
                amountHelp.textContent = 'Maximum payment: ' + money(invoice.remaining_due);

                paymentInfo.className = 'alert alert-success mb-0';
                paymentInfo.innerHTML =
                '<i class="ri-checkbox-circle-line me-1"></i>' +
                'This invoice has <strong>' +
                money(invoice.remaining_due) +
                '</strong> remaining.';
            } else {
                amountHelp.textContent = 'This invoice is fully paid.';

                paymentInfo.className = 'alert alert-warning mb-0';
                paymentInfo.innerHTML =
                '<i class="ri-alert-line me-1"></i>' +
                'This invoice is already fully paid.';
            }

            validateAmount();
        }

        function validateAmount() {
            amountError.classList.add('d-none');
            amountError.textContent = '';
            amount.classList.remove('is-invalid');

            const invoice = invoiceMap[String(invoiceSelect.value)];

            if (!invoice || !amount.value) {
                return true;
            }

            const value = parseFloat(amount.value);

            if (isNaN(value) || value <= 0) {
                amountError.textContent = 'Payment amount must be greater than 0.';
                amountError.classList.remove('d-none');
                amount.classList.add('is-invalid');
                return false;
            }

            if (value > Number(invoice.remaining_due)) {
                amountError.textContent =
                'Payment amount cannot be greater than remaining due of ' +
                money(invoice.remaining_due) +
                '.';

                amountError.classList.remove('d-none');
                amount.classList.add('is-invalid');
                return false;
            }

            return true;
        }

        invoiceSelect.addEventListener('change', function () {
            const invoice = invoiceMap[String(this.value)];

            amount.value = '';

            renderInvoice(invoice || null);
        });

        amount.addEventListener('input', function () {
            let value = this.value.replace(/[^0-9.]/g, '');
            const parts = value.split('.');

            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            const decimalParts = value.split('.');

            if (decimalParts[1] !== undefined) {
                value = decimalParts[0] + '.' + decimalParts[1].slice(0, 2);
            }

            this.value = value;

            validateAmount();
        });

        paymentForm.addEventListener('submit', function (event) {
            const invoice = invoiceMap[String(invoiceSelect.value)];

            if (!invoice) {
                event.preventDefault();
                invoiceSelect.classList.add('is-invalid');
                return;
            }

            invoiceSelect.classList.remove('is-invalid');

            if (!validateAmount()) {
                event.preventDefault();
                amount.focus();
                return;
            }

            const selectedStatus = document.getElementById('status').value;
            const paymentAmount = parseFloat(amount.value) || 0;

            if (
                selectedStatus === 'completed' &&
                paymentAmount > Number(invoice.remaining_due)
                ) {
                event.preventDefault();
            validateAmount();
            amount.focus();
            return;
        }

        savePaymentButton.disabled = true;
        savePaymentButton.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    });

        const initialInvoiceId = invoiceSelect.value;

        if (initialInvoiceId && invoiceMap[String(initialInvoiceId)]) {
            renderInvoice(invoiceMap[String(initialInvoiceId)]);
        } else {
            resetSummary();
        }
    });
</script>
@endpush
