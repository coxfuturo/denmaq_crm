@extends('admin.layout.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');

$statusClass = match($invoice->status) {
    'Paid' => 'success',
    'Sent' => 'primary',
    'Partially Paid' => 'info',
    'Overdue' => 'danger',
    'Cancelled' => 'dark',
    default => 'secondary',
};
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Invoice {{ $invoice->invoice_number }}
            </h4>

            <p class="text-muted mb-0">
                Invoice details
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
            href="{{ route('admin.invoices.index') }}"
            class="btn btn-light"
            >
            <i class="bi bi-arrow-left"></i>
            Back
        </a>

        @if($user && ($isSuperAdmin || $user->can('Invoices Edit')))
        <a
        href="{{ route('admin.invoices.edit', $invoice->id) }}"
        class="btn btn-warning"
        >
        <i class="bi bi-pencil"></i>
        Edit
    </a>
    @endif

    <button
    type="button"
    class="btn btn-primary"
    onclick="window.print()"
    >
    <i class="bi bi-printer"></i>
    Print
</button>

</div>

</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}

    <button
    type="button"
    class="btn-close"
    data-bs-dismiss="alert"
    ></button>
</div>
@endif

<div class="card invoice-card">

    <div class="card-body p-4 p-md-5">

        <div class="row align-items-start mb-5">

            <div class="col-md-6">

                <h2 class="fw-bold mb-1">
                    DANMAQ CRM
                </h2>

                <div class="text-muted">
                    Invoice
                </div>

            </div>

            <div class="col-md-6 text-md-end mt-4 mt-md-0">

                <h3 class="fw-bold mb-2">
                    {{ $invoice->invoice_number }}
                </h3>

                <span class="badge bg-{{ $statusClass }} fs-6">
                    {{ $invoice->status }}
                </span>

            </div>

        </div>

        <div class="row mb-5">

            <div class="col-md-6">

                <h6 class="text-uppercase text-muted mb-2">
                    Bill To
                </h6>

                @if($invoice->client)

                <h5 class="mb-1">
                    {{ $invoice->client->name }}
                </h5>

                @if($invoice->client->company_name)
                <div class="text-muted">
                    {{ $invoice->client->company_name }}
                </div>
                @endif

                @if($invoice->client->email)
                <div class="text-muted">
                    {{ $invoice->client->email }}
                </div>
                @endif

                @if($invoice->client->phone)
                <div class="text-muted">
                    {{ $invoice->client->phone }}
                </div>
                @endif

                @else

                <div class="text-muted">
                    Client deleted
                </div>

                @endif

            </div>

            <div class="col-md-6 mt-4 mt-md-0">

                <div class="row">

                    <div class="col-6 text-muted">
                        Invoice Date
                    </div>

                    <div class="col-6 text-end fw-semibold">
                        {{ $invoice->invoice_date?->format('d-m-Y') ?? '-' }}
                    </div>

                </div>

                <div class="row mt-2">

                    <div class="col-6 text-muted">
                        Due Date
                    </div>

                    <div class="col-6 text-end fw-semibold">
                        {{ $invoice->due_date?->format('d-m-Y') ?? '-' }}
                    </div>

                </div>

                @if($invoice->subject)

                <div class="row mt-2">

                    <div class="col-6 text-muted">
                        Subject
                    </div>

                    <div class="col-6 text-end fw-semibold">
                        {{ $invoice->subject }}
                    </div>

                </div>

                @endif

                @if($isSuperAdmin && $invoice->creator)

                <div class="row mt-2">

                    <div class="col-6 text-muted">
                        Added By
                    </div>

                    <div class="col-6 text-end fw-semibold">
                        {{ trim($invoice->creator->first_name . ' ' . $invoice->creator->last_name) }}
                    </div>

                </div>

                @endif

            </div>

        </div>

        <div class="table-responsive mb-4">

            <table class="table table-bordered align-middle">

                <thead class="table-light">

                    <tr>
                        <th style="width:5%">#</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Amount</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($invoice->items as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            <strong>
                                {{ $item->item_name }}
                            </strong>
                        </td>

                        <td>
                            {{ $item->description ?: '-' }}
                        </td>

                        <td class="text-end">
                            {{ number_format((float) $item->quantity, 2) }}
                        </td>

                        <td class="text-end">
                            ₹{{ number_format((float) $item->rate, 2) }}
                        </td>

                        <td class="text-end">
                            ₹{{ number_format((float) $item->amount, 2) }}
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                        colspan="6"
                        class="text-center text-muted py-4"
                        >
                        No invoice items found.
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="row justify-content-end">

        <div class="col-md-5">

            <div class="d-flex justify-content-between mb-2">

                <span class="text-muted">
                    Subtotal
                </span>

                <strong>
                    ₹{{ number_format((float) $invoice->subtotal, 2) }}
                </strong>

            </div>

            <div class="d-flex justify-content-between mb-2">

                <span class="text-muted">
                    Discount

                    @if($invoice->discount_type === 'percentage')
                    ({{ number_format((float) $invoice->discount_value, 2) }}%)
                    @endif
                </span>

                <strong>
                    -₹{{ number_format((float) $invoice->discount_amount, 2) }}
                </strong>

            </div>

            <div class="d-flex justify-content-between mb-2">

                <span class="text-muted">
                    Tax
                </span>

                <strong>
                    ₹{{ number_format((float) $invoice->tax, 2) }}
                </strong>

            </div>

            <hr>

            <div class="d-flex justify-content-between">

                <h5 class="mb-0">
                    Total
                </h5>

                <h5 class="mb-0">
                    ₹{{ number_format((float) $invoice->total, 2) }}
                </h5>

            </div>

        </div>

    </div>

    @if($invoice->notes)

    <div class="mt-5">

        <h6 class="fw-bold">
            Notes
        </h6>

        <div class="text-muted">
            {!! nl2br(e($invoice->notes)) !!}
        </div>

    </div>

    @endif

    @if($invoice->terms)

    <div class="mt-4">

        <h6 class="fw-bold">
            Terms & Conditions
        </h6>

        <div class="text-muted">
            {!! nl2br(e($invoice->terms)) !!}
        </div>

    </div>

    @endif

</div>

</div>

</div>

@endsection

@push('styles')

<style>
    @media print {
        body {
            background: #fff !important;
        }

        .admin-sidebar,
        .admin-header,
        .sidebar,
        .navbar,
        .breadcrumb,
        .btn,
        .alert {
            display: none !important;
        }

        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            border: 0 !important;
            box-shadow: none !important;
        }

        .invoice-card {
            width: 100% !important;
        }
    }
</style>

@endpush