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

$paidAmount = (float) ($paidAmount ?? 0);
$remainingAmount = (float) ($remainingAmount ?? max((float) $invoice->total - $paidAmount, 0));
$discountValue = (float) ($invoice->discount_value ?? 0);
$discountAmount = (float) ($invoice->discount_amount ?? 0);
$taxPercentage = (float) ($invoice->tax ?? 0);
$taxAmount = max((float) $invoice->total - ((float) $invoice->subtotal - $discountAmount), 0);

$clientName = $invoice->client?->contact_person ?: '-';
$companyName = $invoice->client?->company_name;
$mobile = $invoice->client?->mobile;
$email = $invoice->client?->email;

$paymentStatusClass = match(true) {
    $remainingAmount <= 0 => 'success',
        $paidAmount > 0 => 'warning',
        default => 'secondary',
    };
    @endphp

    <div class="container-fluid invoice-page">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 invoice-toolbar no-print">

            <div>
                <h4 class="mb-1 fw-semibold">
                    Invoice {{ $invoice->invoice_number }}
                </h4>

                <p class="text-muted mb-0 small">
                    Invoice details and payment summary
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">

                <a
                href="{{ route('admin.invoices.index') }}"
                class="btn btn-light border btn-sm"
                >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

            @if($user && ($isSuperAdmin || $user->can('Invoices Edit')))
            <a
            href="{{ route('admin.invoices.edit', $invoice->id) }}"
            class="btn btn-warning btn-sm"
            >
            <i class="bi bi-pencil-square me-1"></i>
            Edit
        </a>
        @endif

        <button
        type="button"
        class="btn btn-primary btn-sm"
        onclick="window.print()"
        >
        <i class="bi bi-printer me-1"></i>
        Print
    </button>

</div>

</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 small invoice-toolbar no-print" role="alert">

    <i class="bi bi-check-circle me-1"></i>
    {{ session('success') }}

    <button
    type="button"
    class="btn-close"
    data-bs-dismiss="alert"
    ></button>

</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2 small invoice-toolbar no-print" role="alert">

    <i class="bi bi-exclamation-circle me-1"></i>
    {{ session('error') }}

    <button
    type="button"
    class="btn-close"
    data-bs-dismiss="alert"
    ></button>

</div>
@endif

{{-- Everything from here to the closing tag below is the ONLY thing that will print --}}
<div id="invoicePrintArea">

    <div class="invoice-card">

        <div class="invoice-header">

            <div class="row align-items-start">

                <div class="col-md-7">

                    <div class="brand-title">
                        DANMAQ CRM
                    </div>

                    <div class="brand-subtitle">
                        Professional Invoice
                    </div>

                </div>

                <div class="col-md-5 text-md-end mt-3 mt-md-0">

                    <div class="invoice-number">
                        {{ $invoice->invoice_number }}
                    </div>

                    <span class="badge bg-{{ $statusClass }} invoice-status">
                        {{ $invoice->status }}
                    </span>

                </div>

            </div>

        </div>

        <div class="invoice-meta">

            <div class="row g-3">

                <div class="col-md-6">

                    <div class="section-label">
                        BILL TO
                    </div>

                    @if($invoice->client)

                    <div class="client-name">
                        {{ $clientName }}
                    </div>

                    @if($companyName)
                    <div class="client-company">
                        {{ $companyName }}
                    </div>
                    @endif

                    @if($email)
                    <div class="client-detail">
                        <i class="bi bi-envelope me-1"></i>
                        {{ $email }}
                    </div>
                    @endif

                    @if($mobile)
                    <div class="client-detail">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $mobile }}
                    </div>
                    @endif

                    @else

                    <div class="text-muted small">
                        Client deleted
                    </div>

                    @endif

                </div>

                <div class="col-md-6">

                    <div class="invoice-info-box">

                        <div class="info-row">
                            <span>Invoice Date</span>
                            <strong>
                                {{ $invoice->invoice_date?->format('d M Y') ?? '-' }}
                            </strong>
                        </div>

                        <div class="info-row">
                            <span>Due Date</span>
                            <strong>
                                {{ $invoice->due_date?->format('d M Y') ?? '-' }}
                            </strong>
                        </div>

                        @if($invoice->subject)

                        <div class="info-row">
                            <span>Subject</span>
                            <strong>
                                {{ $invoice->subject }}
                            </strong>
                        </div>

                        @endif

                        @if($invoice->quotation)

                        <div class="info-row">
                            <span>Quotation</span>
                            <strong>
                                {{ $invoice->quotation->quotation_number }}
                            </strong>
                        </div>

                        @endif

                        @if($isSuperAdmin && $invoice->creator)

                        <div class="info-row">
                            <span>Added By</span>
                            <strong>
                                {{ trim($invoice->creator->first_name . ' ' . $invoice->creator->last_name) }}
                            </strong>
                        </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        <div class="invoice-items">

            <div class="section-label mb-2">
                INVOICE ITEMS
            </div>

            <div class="table-responsive">

                <table class="table invoice-table">

                    <thead>

                        <tr>
                            <th class="text-center" style="width:5%;">#</th>
                            <th style="width:23%;">Item</th>
                            <th style="width:37%;">Description</th>
                            <th class="text-end" style="width:9%;">Qty</th>
                            <th class="text-end" style="width:13%;">Rate</th>
                            <th class="text-end" style="width:13%;">Amount</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($invoice->items as $item)

                        <tr>

                            <td class="text-center">
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

                            <td class="text-end fw-semibold">
                                ₹{{ number_format((float) $item->amount, 2) }}
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                            colspan="6"
                            class="text-center text-muted py-3"
                            >
                            No invoice items found.
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="row justify-content-end">

    <div class="col-md-5 col-lg-4">

        <div class="summary-box">

            <div class="summary-row">
                <span>Subtotal</span>
                <strong>
                    ₹{{ number_format((float) $invoice->subtotal, 2) }}
                </strong>
            </div>

            <div class="summary-row">

                <span>
                    Discount

                    @if($invoice->discount_type === 'percentage')
                    ({{ number_format($discountValue, 2) }}%)
                    @else
                    (Fixed)
                    @endif
                </span>

                <strong class="text-danger">
                    -₹{{ number_format($discountAmount, 2) }}
                </strong>

            </div>

            <div class="summary-row">

                <span>
                    Tax
                    ({{ number_format($taxPercentage, 2) }}%)
                </span>

                <strong>
                    ₹{{ number_format($taxAmount, 2) }}
                </strong>

            </div>

            <div class="summary-total">

                <span>
                    Grand Total
                </span>

                <strong>
                    ₹{{ number_format((float) $invoice->total, 2) }}
                </strong>

            </div>

        </div>

    </div>

</div>

<div class="payment-summary mt-4">

    <div class="section-label mb-2">
        PAYMENT SUMMARY
    </div>

    <div class="row g-2">

        <div class="col-md-4">

            <div class="payment-box">

                <span>Total Invoice</span>

                <strong>
                    ₹{{ number_format((float) $invoice->total, 2) }}
                </strong>

            </div>

        </div>

        <div class="col-md-4">

            <div class="payment-box">

                <span>Paid Amount</span>

                <strong class="text-success">
                    ₹{{ number_format($paidAmount, 2) }}
                </strong>

            </div>

        </div>

        <div class="col-md-4">

            <div class="payment-box">

                <span>Remaining Due</span>

                <strong class="text-danger">
                    ₹{{ number_format($remainingAmount, 2) }}
                </strong>

            </div>

        </div>

    </div>

    <div class="mt-2">

        <span class="badge bg-{{ $paymentStatusClass }} payment-status">
            @if($remainingAmount <= 0)
            Fully Paid
            @elseif($paidAmount > 0)
            Partially Paid
            @else
            Payment Pending
            @endif
        </span>

    </div>

</div>

@if($invoice->payments->isNotEmpty())

<div class="payment-history mt-4">

    <div class="section-label mb-2">
        PAYMENT HISTORY
    </div>

    <div class="table-responsive">

        <table class="table table-sm payment-table">

            <thead>

                <tr>
                    <th>Payment No.</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Status</th>
                </tr>

            </thead>

            <tbody>

                @foreach($invoice->payments as $payment)

                <tr>

                    <td>
                        {{ $payment->payment_number }}
                    </td>

                    <td>
                        {{ $payment->payment_date?->format('d M Y') ?? '-' }}
                    </td>

                    <td>
                        {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                    </td>

                    <td>
                        {{ $payment->transaction_id ?: '-' }}
                    </td>

                    <td class="text-end fw-semibold">
                        ₹{{ number_format((float) $payment->amount, 2) }}
                    </td>

                    <td class="text-end">
                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                        </span>
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endif

@if($invoice->notes)

<div class="invoice-note mt-4">

    <div class="section-label mb-1">
        NOTES
    </div>

    <div>
        {!! nl2br(e($invoice->notes)) !!}
    </div>

</div>

@endif

@if($invoice->terms)

<div class="invoice-note mt-3">

    <div class="section-label mb-1">
        TERMS & CONDITIONS
    </div>

    <div>
        {!! nl2br(e($invoice->terms)) !!}
    </div>

</div>

@endif

<div class="invoice-footer">

    <div>
        Thank you for your business.
    </div>

    <div>
        Generated by DANMAQ CRM
    </div>

</div>

</div>
{{-- end #invoicePrintArea --}}

</div>

@endsection

@push('styles')

<style>
    .invoice-page {
        font-size: 13px;
    }

    .invoice-toolbar .btn {
        font-size: 12px;
        padding: 6px 10px;
    }

    .invoice-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
    }

    .invoice-header {
        padding: 22px 28px;
        border-bottom: 1px solid #e5e7eb;
    }

    .brand-title {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .brand-subtitle {
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
    }

    .invoice-number {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .invoice-status {
        font-size: 11px !important;
        padding: 5px 9px;
    }

    .invoice-meta {
        padding: 20px 28px;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .8px;
        color: #6b7280;
    }

    .client-name {
        font-size: 15px;
        font-weight: 700;
        margin-top: 5px;
    }

    .client-company {
        font-size: 12px;
        font-weight: 600;
        margin-top: 2px;
    }

    .client-detail {
        color: #6b7280;
        font-size: 11px;
        margin-top: 3px;
    }

    .invoice-info-box {
        max-width: 430px;
        margin-left: auto;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 4px 0;
        border-bottom: 1px dashed #e5e7eb;
        font-size: 11px;
    }

    .info-row:last-child {
        border-bottom: 0;
    }

    .info-row span {
        color: #6b7280;
    }

    .info-row strong {
        text-align: right;
        max-width: 65%;
    }

    .invoice-items {
        padding: 20px 28px 0;
    }

    .invoice-table {
        margin-bottom: 0;
        font-size: 11px;
    }

    .invoice-table th {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .3px;
        background: #f8f9fa;
        padding: 8px 7px;
        white-space: nowrap;
    }

    .invoice-table td {
        padding: 8px 7px;
        vertical-align: middle;
    }

    .summary-box {
        margin-top: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 12px 14px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 5px 0;
        font-size: 12px;
    }

    .summary-row span {
        color: #6b7280;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        border-top: 1px solid #dee2e6;
        margin-top: 6px;
        padding-top: 10px;
        font-size: 15px;
        font-weight: 700;
    }

    .payment-summary,
    .payment-history,
    .invoice-note {
        margin-left: 28px;
        margin-right: 28px;
    }

    .payment-box {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 9px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 11px;
    }

    .payment-box span {
        color: #6b7280;
    }

    .payment-box strong {
        font-size: 13px;
    }

    .payment-status {
        font-size: 10px;
        padding: 4px 8px;
    }

    .payment-table {
        font-size: 10px;
        margin-bottom: 0;
    }

    .payment-table th {
        font-size: 9px;
        text-transform: uppercase;
        background: #f8f9fa;
        padding: 6px;
    }

    .payment-table td {
        padding: 6px;
        vertical-align: middle;
    }

    .payment-table .badge {
        font-size: 9px;
    }

    .invoice-note {
        font-size: 11px;
        color: #6b7280;
        line-height: 1.6;
    }

    .invoice-footer {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        border-top: 1px solid #e5e7eb;
        margin: 24px 28px 0;
        padding: 12px 0 20px;
        color: #9ca3af;
        font-size: 10px;
    }

    @media (max-width: 767.98px) {
        .invoice-header,
        .invoice-meta,
        .invoice-items {
            padding-left: 15px;
            padding-right: 15px;
        }

        .payment-summary,
        .payment-history,
        .invoice-note {
            margin-left: 15px;
            margin-right: 15px;
        }

        .invoice-footer {
            margin-left: 15px;
            margin-right: 15px;
        }

        .invoice-info-box {
            margin-left: 0;
            max-width: none;
        }

        .invoice-table {
            min-width: 700px;
        }

        .payment-table {
            min-width: 650px;
        }

        .invoice-footer {
            flex-direction: column;
        }
    }

    /* =========================================================
       PRINT — A4, isolated content, no scale/blur
       =========================================================
       Strategy: hide EVERYTHING on the page, then make only
       #invoicePrintArea (and everything inside it) visible again,
       and pull it out of wherever it sits in the admin layout
       (sidebar/navbar/wrappers) using position:absolute at the
       top-left of the page. This means we never depend on
       guessing the admin theme's sidebar/navbar class names —
       whatever they are, they get hidden.
    ========================================================= */
    @media print {

        @page {
            size: A4 portrait;
            margin: 14mm 16mm;
        }

        html, body {
            width: 210mm !important;
            height: auto !important;
            background: #fff !important;
            zoom: 1 !important;
            transform: none !important;
            overflow: visible !important;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
            color: #000 !important;
        }

        /* Step 1: hide absolutely everything */
        body * {
            visibility: hidden !important;
        }

        /* Step 2: bring back only the invoice content */
        #invoicePrintArea,
        #invoicePrintArea * {
            visibility: visible !important;
        }

        /* Step 3: detach the invoice from the admin layout flow
           and pin it to the page so no sidebar gutter/scale remains */
           #invoicePrintArea {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            zoom: 1 !important;
        }

        /* Explicit belt-and-braces hide for anything still marked no-print
           or matching common admin theme wrapper names */
           .no-print,
           .invoice-toolbar,
           .admin-sidebar,
           .admin-header,
           .sidebar,
           .navbar,
           .breadcrumb,
           .btn,
           .alert,
           .toast,
           .modal,
           footer {
            display: none !important;
        }

        /* ---- Force every piece of text to a strong, readable dark
           color. This overrides any faint/blue theme colors that
           were leaking through from the admin panel's CSS variables. */
           #invoicePrintArea,
           #invoicePrintArea * {
            color: #000 !important;
            text-shadow: none !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }

        /* Muted/secondary text stays legible but slightly softer than
           pure black, never light gray or blue */
           #invoicePrintArea .brand-subtitle,
           #invoicePrintArea .section-label,
           #invoicePrintArea .client-detail,
           #invoicePrintArea .info-row span,
           #invoicePrintArea .summary-row span,
           #invoicePrintArea .payment-box span,
           #invoicePrintArea .invoice-note,
           #invoicePrintArea .invoice-footer {
            color: #333 !important;
        }

        /* Accent colors kept, but darkened for print contrast */
        #invoicePrintArea .text-success,
        #invoicePrintArea .payment-box .text-success {
            color: #146c2e !important;
        }

        #invoicePrintArea .text-danger,
        #invoicePrintArea .payment-box .text-danger,
        #invoicePrintArea .summary-row .text-danger {
            color: #a3212b !important;
        }

        /* Badges: solid dark background, white text, always legible */
        #invoicePrintArea .badge {
            color: #fff !important;
            border: 1px solid #000 !important;
        }

        .container-fluid {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .invoice-page {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 13px !important;
        }

        .invoice-card {
            width: 100% !important;
            max-width: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .invoice-header {
            padding: 0 0 16px !important;
            border-bottom: 2px solid #000 !important;
        }

        .brand-title {
            font-size: 26px !important;
            font-weight: 800 !important;
        }

        .brand-subtitle {
            font-size: 12px !important;
        }

        .invoice-number {
            font-size: 22px !important;
            font-weight: 800 !important;
        }

        .invoice-status {
            font-size: 11px !important;
            padding: 4px 10px !important;
        }

        .invoice-meta {
            padding: 16px 0 !important;
            border-bottom: 1px solid #999 !important;
        }

        .section-label {
            font-size: 11px !important;
            font-weight: 800 !important;
            letter-spacing: 0.5px !important;
        }

        .client-name {
            font-size: 15px !important;
            font-weight: 800 !important;
        }

        .client-company {
            font-size: 13px !important;
            font-weight: 700 !important;
        }

        .client-detail {
            font-size: 12px !important;
        }

        .info-row {
            font-size: 12px !important;
            padding: 5px 0 !important;
            border-bottom: 1px dashed #bbb !important;
        }

        .info-row strong {
            font-weight: 700 !important;
        }

        .invoice-items {
            padding: 16px 0 0 !important;
        }

        .invoice-table {
            width: 100% !important;
            min-width: 0 !important;
            font-size: 12px !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            border: 1px solid #999 !important;
        }

        .invoice-table th {
            font-size: 11px !important;
            font-weight: 800 !important;
            padding: 8px 7px !important;
            background: #e9e9e9 !important;
            border-bottom: 1px solid #999 !important;
        }

        .invoice-table td {
            padding: 8px 7px !important;
            word-wrap: break-word !important;
            border-bottom: 1px solid #ddd !important;
        }

        .invoice-table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .summary-box {
            margin-top: 14px !important;
            padding: 10px 14px !important;
            border: 1px solid #999 !important;
            width: 100% !important;
        }

        .summary-row {
            font-size: 12px !important;
            padding: 4px 0 !important;
        }

        .summary-total {
            font-size: 16px !important;
            font-weight: 800 !important;
            padding-top: 8px !important;
            border-top: 2px solid #000 !important;
        }

        .payment-summary,
        .payment-history,
        .invoice-note {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .payment-summary {
            margin-top: 16px !important;
        }

        .payment-box {
            padding: 8px 10px !important;
            font-size: 12px !important;
            border: 1px solid #999 !important;
        }

        .payment-box strong {
            font-size: 13px !important;
            font-weight: 800 !important;
        }

        .payment-status {
            font-size: 11px !important;
            font-weight: 700 !important;
        }

        .payment-history {
            margin-top: 16px !important;
        }

        .payment-table {
            width: 100% !important;
            min-width: 0 !important;
            font-size: 11px !important;
            border: 1px solid #999 !important;
        }

        .payment-table th {
            font-size: 10.5px !important;
            font-weight: 800 !important;
            padding: 6px !important;
            background: #e9e9e9 !important;
        }

        .payment-table td {
            padding: 6px !important;
            border-bottom: 1px solid #ddd !important;
        }

        .payment-table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .invoice-note {
            font-size: 12px !important;
            line-height: 1.5 !important;
        }

        .invoice-footer {
            margin: 18px 0 0 !important;
            padding: 10px 0 0 !important;
            font-size: 10.5px !important;
            border-top: 1px solid #999 !important;
        }

        .row {
            --bs-gutter-x: 1rem !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        a {
            color: inherit !important;
            text-decoration: none !important;
        }

        /* Bootstrap icon fonts can rasterize blurry at print DPI —
           hide the small decorative ones so nothing looks smudged */
           .client-detail i,
           .invoice-toolbar i {
            display: none !important;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

@endpush