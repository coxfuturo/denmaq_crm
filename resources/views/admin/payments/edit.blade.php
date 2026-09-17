@extends('admin.layout.app')

@section('title', 'Edit Payment')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('payments.edit'));
@endphp

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="page-title-box d-flex align-items-center justify-content-between">

                <div>
                    <h4 class="page-title mb-1">Edit Payment</h4>
                    <p class="text-muted mb-0">
                        Update payment information
                    </p>
                </div>

                <div class="d-flex gap-2">

                    <a
                    href="{{ route('admin.payments.show', $payment->id) }}"
                    class="btn btn-info"
                    >
                    <i class="ri-eye-line me-1"></i>
                    View
                </a>

                <a
                href="{{ route('admin.payments.index') }}"
                class="btn btn-light"
                >
                <i class="ri-arrow-left-line me-1"></i>
                Back
            </a>

        </div>

    </div>

</div>
</div>

@if(!$canEdit)

<div class="alert alert-danger">
    You do not have permission to edit payments.
</div>

@else

<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">

                <h5 class="card-title mb-0">
                    Edit Payment
                </h5>

                <span class="badge bg-primary">
                    {{ $payment->payment_number }}
                </span>

            </div>

            <div class="card-body">

                <form
                action="{{ route('admin.payments.update', $payment->id) }}"
                method="POST"
                enctype="multipart/form-data"
                >

                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">

                        <label for="payment_number" class="form-label">
                            Payment Number
                        </label>

                        <input
                        type="text"
                        id="payment_number"
                        class="form-control"
                        value="{{ $payment->payment_number }}"
                        readonly
                        >

                    </div>

                    <div class="col-md-6">

                        <label for="client_id" class="form-label">
                            Client <span class="text-danger">*</span>
                        </label>

                        <select
                        name="client_id"
                        id="client_id"
                        class="form-select @error('client_id') is-invalid @enderror"
                        required
                        >

                        <option value="">
                            Select Client
                        </option>

                        @foreach($clients as $client)

                        <option
                        value="{{ $client->id }}"
                        {{ old('client_id', $payment->client_id) == $client->id ? 'selected' : '' }}
                        >
                        {{ $client->name }}
                    </option>

                    @endforeach

                </select>

                @error('client_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

            </div>

            <div class="col-md-6">

                <label for="project_id" class="form-label">
                    Project
                </label>

                <select
                name="project_id"
                id="project_id"
                class="form-select @error('project_id') is-invalid @enderror"
                >

                <option value="">
                    Select Project
                </option>

                @foreach($projects as $project)

                <option
                value="{{ $project->id }}"
                {{ old('project_id', $payment->project_id) == $project->id ? 'selected' : '' }}
                >
                {{ $project->name }}
            </option>

            @endforeach

        </select>

        @error('project_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>

    <div class="col-md-6">

        <label for="invoice_id" class="form-label">
            Invoice
        </label>

        <select
        name="invoice_id"
        id="invoice_id"
        class="form-select @error('invoice_id') is-invalid @enderror"
        >

        <option value="">
            Select Invoice
        </option>

        @foreach($invoices as $invoice)

        <option
        value="{{ $invoice->id }}"
        {{ old('invoice_id', $payment->invoice_id) == $invoice->id ? 'selected' : '' }}
        >
        {{ $invoice->invoice_number ?? 'Invoice #'.$invoice->id }}
    </option>

    @endforeach

</select>

@error('invoice_id')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror

</div>

<div class="col-md-6">

    <label for="amount" class="form-label">
        Amount <span class="text-danger">*</span>
    </label>

    <div class="input-group">

        <span class="input-group-text">
            ₹
        </span>

        <input
        type="text"
        name="amount"
        id="amount"
        class="form-control @error('amount') is-invalid @enderror"
        value="{{ old('amount', $payment->amount) }}"
        placeholder="Enter payment amount"
        inputmode="decimal"
        autocomplete="off"
        required
        >

    </div>

    @error('amount')
    <div class="text-danger small mt-1">
        {{ $message }}
    </div>
    @enderror

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
    value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '') }}"
    required
    >

    @error('payment_date')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
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

    <option value="">
        Select Payment Method
    </option>

    <option
    value="cash"
    {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}
    >
    Cash
</option>

<option
value="upi"
{{ old('payment_method', $payment->payment_method) == 'upi' ? 'selected' : '' }}
>
UPI
</option>

<option
value="bank_transfer"
{{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}
>
Bank Transfer
</option>

<option
value="cheque"
{{ old('payment_method', $payment->payment_method) == 'cheque' ? 'selected' : '' }}
>
Cheque
</option>

<option
value="credit_card"
{{ old('payment_method', $payment->payment_method) == 'credit_card' ? 'selected' : '' }}
>
Credit Card
</option>

<option
value="debit_card"
{{ old('payment_method', $payment->payment_method) == 'debit_card' ? 'selected' : '' }}
>
Debit Card
</option>

<option
value="other"
{{ old('payment_method', $payment->payment_method) == 'other' ? 'selected' : '' }}
>
Other
</option>

</select>

@error('payment_method')
<div class="invalid-feedback">
    {{ $message }}
</div>
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
    value="{{ old('transaction_id', $payment->transaction_id) }}"
    maxlength="100"
    placeholder="Enter transaction ID"
    >

    @error('transaction_id')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
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
    value="{{ old('bank_account', $payment->bank_account) }}"
    maxlength="100"
    placeholder="Enter bank account details"
    >

    @error('bank_account')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
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

    <option
    value="completed"
    {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}
    >
    Completed
</option>

<option
value="pending"
{{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}
>
Pending
</option>

<option
value="failed"
{{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}
>
Failed
</option>

<option
value="cancelled"
{{ old('status', $payment->status) == 'cancelled' ? 'selected' : '' }}
>
Cancelled
</option>

<option
value="refunded"
{{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}
>
Refunded
</option>

<option
value="partially_refunded"
{{ old('status', $payment->status) == 'partially_refunded' ? 'selected' : '' }}
>
Partially Refunded
</option>

</select>

@error('status')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror

</div>

<div class="col-md-6">

    <label class="form-label">
        Current Payment Proof
    </label>

    @if($payment->attachment)

    <div class="d-flex align-items-center gap-2">

        <a
        href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($payment->attachment) }}"
        target="_blank"
        class="btn btn-sm btn-info"
        >
        <i class="ri-eye-line me-1"></i>
        View
    </a>

    <span class="text-muted small">
        Existing attachment
    </span>

</div>

@else

<div class="text-muted">
    No attachment uploaded.
</div>

@endif

</div>

<div class="col-md-6">

    <label for="attachment" class="form-label">
        Replace Payment Proof
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
    <div class="invalid-feedback">
        {{ $message }}
    </div>
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
    >{{ old('notes', $payment->notes) }}</textarea>

    @error('notes')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror

</div>

<div class="col-12">

    <div class="d-flex gap-2 pt-2">

        <button
        type="submit"
        class="btn btn-primary"
        >
        <i class="ri-save-line me-1"></i>
        Update Payment
    </button>

    <a
    href="{{ route('admin.payments.show', $payment->id) }}"
    class="btn btn-light"
    >
    Cancel
</a>

</div>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

@endif

</div>

@endsection