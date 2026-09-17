@extends('admin.layout.app')

@section('title', 'Payment Details')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('payments.edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('payments.delete'));

$statusClass = match($payment->status) {
    'completed' => 'success',
    'pending' => 'warning',
    'failed' => 'danger',
    'cancelled' => 'secondary',
    'refunded' => 'info',
    'partially_refunded' => 'primary',
    default => 'secondary',
};

$clientName = $payment->client?->contact_person ?: '-';
$companyName = $payment->client?->company_name;
$clientMobile = $payment->client?->mobile;
$clientEmail = $payment->client?->email;

$paymentAmount = (float) $payment->amount;

$invoiceTotal = $payment->invoice ? (float) $payment->invoice->total : 0;

$paidBefore = 0;
$remainingAfter = 0;

if ($payment->invoice) {
    $paidBefore = (float) $payment->invoice->payments()
    ->where('id', '<', $payment->id)
        ->where('status', 'completed')
        ->sum('amount');

        $paidBefore = max($paidBefore, 0);

        $remainingAfter = max(
        $invoiceTotal - $paidBefore - $paymentAmount,
        0
        );
        
    }

    $paymentMethod = ucwords(
    str_replace('_', ' ', $payment->payment_method)
    );

    $paymentStatus = ucwords(
    str_replace('_', ' ', $payment->status)
    );
    @endphp

    <div class="container-fluid payment-page">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 payment-toolbar">

            <div>
                <h4 class="page-title mb-1">
                    Payment {{ $payment->payment_number }}
                </h4>

                <p class="text-muted mb-0 small">
                    Payment details and receipt
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">

                <a
                href="{{ route('admin.payments.index') }}"
                class="btn btn-light border btn-sm"
                >
                <i class="ri-arrow-left-line me-1"></i>
                Back
            </a>

            @if($canEdit)

            <a
            href="{{ route('admin.payments.edit', $payment->id) }}"
            class="btn btn-warning btn-sm"
            >
            <i class="ri-edit-line me-1"></i>
            Edit
        </a>

        @endif

        <button
        type="button"
        class="btn btn-primary btn-sm"
        onclick="window.print()"
        >
        <i class="ri-printer-line me-1"></i>
        Print Receipt
    </button>

</div>

</div>

<div class="payment-receipt">

    <div class="receipt-header">

        <div class="row align-items-start">

            <div class="col-md-7">

                <div class="brand-title">
                    DANMAQ CRM
                </div>

                <div class="brand-subtitle">
                    Payment Receipt
                </div>

            </div>

            <div class="col-md-5 text-md-end mt-3 mt-md-0">

                <div class="receipt-number">
                    {{ $payment->payment_number }}
                </div>

                <span class="badge bg-{{ $statusClass }} receipt-status">
                    {{ $paymentStatus }}
                </span>

            </div>

        </div>

    </div>

    <div class="receipt-client-section">

        <div class="row g-4">

            <div class="col-md-6">

                <div class="section-label">
                    RECEIVED FROM
                </div>

                @if($payment->client)

                <div class="client-name">
                    {{ $clientName }}
                </div>

                @if($companyName)
                <div class="client-company">
                    {{ $companyName }}
                </div>
                @endif

                @if($clientEmail)
                <div class="client-detail">
                    <i class="ri-mail-line me-1"></i>
                    {{ $clientEmail }}
                </div>
                @endif

                @if($clientMobile)
                <div class="client-detail">
                    <i class="ri-phone-line me-1"></i>
                    {{ $clientMobile }}
                </div>
                @endif

                @else

                <div class="text-muted small">
                    Client not available
                </div>

                @endif

            </div>

            <div class="col-md-6">

                <div class="receipt-info-box">

                    <div class="info-row">
                        <span>Payment Date</span>
                        <strong>
                            {{ $payment->payment_date?->format('d M Y') ?? '-' }}
                        </strong>
                    </div>

                    <div class="info-row">
                        <span>Payment Method</span>
                        <strong>
                            {{ $paymentMethod }}
                        </strong>
                    </div>

                    @if($payment->invoice)

                    <div class="info-row">
                        <span>Invoice</span>
                        <strong>
                            {{ $payment->invoice->invoice_number }}
                        </strong>
                    </div>

                    @endif

                    @if($payment->transaction_id)

                    <div class="info-row">
                        <span>Transaction ID</span>
                        <strong>
                            {{ $payment->transaction_id }}
                        </strong>
                    </div>

                    @endif

                    @if($payment->bank_account)

                    <div class="info-row">
                        <span>Bank Account</span>
                        <strong>
                            {{ $payment->bank_account }}
                        </strong>
                    </div>

                    @endif

                    @if($isSuperAdmin && $payment->creator)

                    <div class="info-row">
                        <span>Added By</span>
                        <strong>
                            {{ trim($payment->creator->first_name . ' ' . $payment->creator->last_name) }}
                        </strong>
                    </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

    <div class="amount-section">

        <div class="section-label mb-2">
            PAYMENT AMOUNT
        </div>

        <div class="amount-box">

            <span>
                Amount Received
            </span>

            <strong>
                ₹{{ number_format($paymentAmount, 2) }}
            </strong>

        </div>

    </div>

    @if($payment->invoice)

    <div class="invoice-summary-section">

        <div class="section-label mb-2">
            INVOICE PAYMENT SUMMARY
        </div>

        <div class="row g-2">

            <div class="col-md-4">

                <div class="summary-box">

                    <span>
                        Invoice Total
                    </span>

                    <strong>
                        ₹{{ number_format($invoiceTotal, 2) }}
                    </strong>

                </div>

            </div>

            <div class="col-md-4">

                <div class="summary-box">

                    <span>
                        Paid Before
                    </span>

                    <strong>
                        ₹{{ number_format($paidBefore, 2) }}
                    </strong>

                </div>

            </div>

            <div class="col-md-4">

                <div class="summary-box">

                    <span>
                        Remaining Due
                    </span>

                    <strong class="{{ $remainingAfter > 0 ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($remainingAfter, 2) }}
                    </strong>

                </div>

            </div>

        </div>

    </div>

    @endif

    @if($payment->notes)

    <div class="receipt-notes">

        <div class="section-label mb-1">
            NOTES
        </div>

        <div>
            {!! nl2br(e($payment->notes)) !!}
        </div>

    </div>

    @endif

    <div class="receipt-footer">

        <div>
            Thank you for your payment.
        </div>

        <div>
            Generated by DANMAQ CRM
        </div>

    </div>

</div>

<div class="row payment-screen-area mt-4">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">

                <h5 class="card-title mb-0">
                    Payment Information
                </h5>

                <span class="badge bg-{{ $statusClass }}">
                    {{ $paymentStatus }}
                </span>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Payment Number
                            </small>

                            <h5 class="mb-0">
                                {{ $payment->payment_number }}
                            </h5>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Amount
                            </small>

                            <h5 class="mb-0">
                                ₹{{ number_format($paymentAmount, 2) }}
                            </h5>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Payment Date
                            </small>

                            <strong>
                                {{ $payment->payment_date?->format('d-m-Y') ?? '-' }}
                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Payment Method
                            </small>

                            <strong>
                                {{ $paymentMethod }}
                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Transaction ID
                            </small>

                            <strong>
                                {{ $payment->transaction_id ?: '-' }}
                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Bank Account
                            </small>

                            <strong>
                                {{ $payment->bank_account ?: '-' }}
                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Client
                            </small>

                            @if($payment->client)

                            <strong>
                                {{ $clientName }}
                            </strong>

                            @if($companyName)
                            <small class="text-muted d-block mt-1">
                                {{ $companyName }}
                            </small>
                            @endif

                            @else

                            <span class="text-muted">
                                N/A
                            </span>

                            @endif

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Project
                            </small>

                            @if($payment->project)

                            <strong>
                                {{ $payment->project->name }}
                            </strong>

                            @else

                            <span class="text-muted">
                                N/A
                            </span>

                            @endif

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Invoice
                            </small>

                            @if($payment->invoice)

                            <strong>
                                {{ $payment->invoice->invoice_number }}
                            </strong>

                            @else

                            <span class="text-muted">
                                N/A
                            </span>

                            @endif

                        </div>

                    </div>

                    @if($isSuperAdmin)

                    <div class="col-md-6">

                        <div class="border rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Added By
                            </small>

                            @if($payment->creator)

                            <strong class="d-block">
                                {{ trim($payment->creator->first_name . ' ' . $payment->creator->last_name) }}
                            </strong>

                            @if($payment->creator->email)

                            <small class="text-muted">
                                {{ $payment->creator->email }}
                            </small>

                            @endif

                            @else

                            <span class="text-muted">
                                N/A
                            </span>

                            @endif

                        </div>

                    </div>

                    @endif

                    <div class="col-12">

                        <div class="border rounded p-3">

                            <small class="text-muted d-block mb-2">
                                Notes
                            </small>

                            @if($payment->notes)

                            <div style="white-space: pre-line;">
                                {{ $payment->notes }}
                            </div>

                            @else

                            <span class="text-muted">
                                No notes added.
                            </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">

                <h5 class="card-title mb-0">
                    Payment Proof
                </h5>

            </div>

            <div class="card-body text-center">

                @if($payment->attachment)

                @php
                $attachmentUrl = Storage::disk('public')->url($payment->attachment);
                $extension = strtolower(pathinfo($payment->attachment, PATHINFO_EXTENSION));
                @endphp

                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))

                <div class="mb-3">

                    <img
                    src="{{ $attachmentUrl }}"
                    alt="Payment Proof"
                    class="img-fluid rounded border"
                    style="max-height:350px;"
                    >

                </div>

                @elseif($extension === 'pdf')

                <div class="py-4">

                    <i
                    class="ri-file-pdf-line text-danger"
                    style="font-size:64px;"
                    ></i>

                    <h5 class="mt-2">
                        PDF Payment Proof
                    </h5>

                </div>

                @else

                <div class="py-4">

                    <i
                    class="ri-file-line"
                    style="font-size:64px;"
                    ></i>

                </div>

                @endif

                <a
                href="{{ $attachmentUrl }}"
                target="_blank"
                class="btn btn-primary btn-sm"
                >
                <i class="ri-external-link-line me-1"></i>
                Open Attachment
            </a>

            @else

            <div class="py-5 text-muted">

                <i
                class="ri-file-forbid-line"
                style="font-size:48px;"
                ></i>

                <p class="mt-2 mb-0">
                    No payment proof uploaded.
                </p>

            </div>

            @endif

        </div>

    </div>

    <div class="card">

        <div class="card-header">

            <h5 class="card-title mb-0">
                Record Information
            </h5>

        </div>

        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">

                <span class="text-muted">
                    Created
                </span>

                <strong>
                    {{ $payment->created_at?->format('d-m-Y H:i') ?? '-' }}
                </strong>

            </div>

            <div class="d-flex justify-content-between">

                <span class="text-muted">
                    Updated
                </span>

                <strong>
                    {{ $payment->updated_at?->format('d-m-Y H:i') ?? '-' }}
                </strong>

            </div>

        </div>

    </div>

    @if($canDelete)

    <div class="card">

        <div class="card-body">

            <form
            action="{{ route('admin.payments.destroy', $payment->id) }}"
            method="POST"
            onsubmit="return confirm('Are you sure you want to delete this payment?');"
            >

            @csrf
            @method('DELETE')

            <button
            type="submit"
            class="btn btn-danger w-100"
            >
            <i class="ri-delete-bin-line me-1"></i>
            Delete Payment
        </button>

    </form>

</div>

</div>

@endif

</div>

</div>

</div>

@endsection

@push('styles')

<style>
    .payment-page {
        font-size: 13px;
    }

    .payment-toolbar .btn {
        font-size: 12px;
        padding: 6px 11px;
    }

    .payment-receipt {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e2e5e9;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .receipt-header {
        padding: 24px 30px;
        border-bottom: 1px solid #e2e5e9;
    }

    .brand-title {
        font-size: 22px;
        line-height: 1.2;
        font-weight: 700;
    }

    .brand-subtitle {
        margin-top: 3px;
        color: #6c757d;
        font-size: 11px;
    }

    .receipt-number {
        font-size: 19px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 7px;
    }

    .receipt-status {
        font-size: 10px !important;
        padding: 5px 8px;
    }

    .receipt-client-section {
        padding: 20px 30px;
        border-bottom: 1px solid #e2e5e9;
    }

    .section-label {
        color: #6c757d;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .8px;
        line-height: 1.3;
    }

    .client-name {
        margin-top: 6px;
        font-size: 15px;
        font-weight: 700;
    }

    .client-company {
        margin-top: 2px;
        font-size: 11px;
        font-weight: 600;
    }

    .client-detail {
        margin-top: 3px;
        color: #6c757d;
        font-size: 10px;
    }

    .receipt-info-box {
        max-width: 440px;
        margin-left: auto;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 5px 0;
        border-bottom: 1px dashed #e1e4e8;
        font-size: 10px;
    }

    .info-row:last-child {
        border-bottom: 0;
    }

    .info-row span {
        color: #6c757d;
    }

    .info-row strong {
        max-width: 68%;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .amount-section {
        padding: 20px 30px 0;
    }

    .amount-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 15px 18px;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        background: #f8f9fa;
    }

    .amount-box span {
        color: #6c757d;
        font-size: 11px;
    }

    .amount-box strong {
        font-size: 22px;
        font-weight: 700;
    }

    .invoice-summary-section {
        padding: 20px 30px 0;
    }

    .summary-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        min-height: 45px;
        padding: 9px 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .summary-box span {
        color: #6c757d;
        font-size: 10px;
    }

    .summary-box strong {
        font-size: 12px;
    }

    .receipt-notes {
        margin: 20px 30px 0;
        padding-top: 15px;
        border-top: 1px solid #e2e5e9;
        color: #6c757d;
        font-size: 10px;
        line-height: 1.55;
    }

    .receipt-footer {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin: 24px 30px 0;
        padding: 12px 0 18px;
        border-top: 1px solid #e2e5e9;
        color: #8a9199;
        font-size: 9px;
    }

    .payment-screen-area {
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 767.98px) {
        .receipt-header,
        .receipt-client-section,
        .amount-section,
        .invoice-summary-section {
            padding-left: 15px;
            padding-right: 15px;
        }

        .receipt-info-box {
            max-width: none;
            margin-left: 0;
        }

        .receipt-footer,
        .receipt-notes {
            margin-left: 15px;
            margin-right: 15px;
        }

        .receipt-footer {
            flex-direction: column;
        }
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #111 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 10pt !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .payment-toolbar,
        .payment-screen-area,
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

        .container-fluid,
        .payment-page {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .payment-receipt {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .receipt-header {
            padding: 0 0 10mm !important;
            border-bottom: 1px solid #333 !important;
        }

        .brand-title {
            font-size: 20pt !important;
        }

        .brand-subtitle {
            font-size: 9pt !important;
        }

        .receipt-number {
            font-size: 16pt !important;
        }

        .receipt-status {
            padding: 4px 7px !important;
            font-size: 8pt !important;
        }

        .receipt-client-section {
            padding: 7mm 0 !important;
            border-bottom: 1px solid #ccc !important;
        }

        .section-label {
            font-size: 8pt !important;
            color: #555 !important;
        }

        .client-name {
            font-size: 11pt !important;
        }

        .client-company {
            font-size: 9pt !important;
        }

        .client-detail {
            font-size: 8.5pt !important;
        }

        .info-row {
            padding: 3px 0 !important;
            font-size: 8.5pt !important;
            border-bottom: 1px dashed #ccc !important;
        }

        .amount-section {
            padding: 7mm 0 0 !important;
        }

        .amount-box {
            padding: 5mm !important;
            border: 1px solid #999 !important;
            border-radius: 0 !important;
            background: #f5f5f5 !important;
        }

        .amount-box span {
            font-size: 9pt !important;
        }

        .amount-box strong {
            font-size: 17pt !important;
        }

        .invoice-summary-section {
            padding: 7mm 0 0 !important;
        }

        .summary-box {
            min-height: 0 !important;
            padding: 4mm !important;
            border: 1px solid #aaa !important;
            border-radius: 0 !important;
        }

        .summary-box span {
            font-size: 8pt !important;
        }

        .summary-box strong {
            font-size: 10pt !important;
        }

        .receipt-notes {
            margin: 7mm 0 0 !important;
            padding-top: 5mm !important;
            border-top: 1px solid #bbb !important;
            font-size: 8.5pt !important;
            line-height: 1.45 !important;
        }

        .receipt-footer {
            margin: 8mm 0 0 !important;
            padding: 4mm 0 0 !important;
            border-top: 1px solid #bbb !important;
            font-size: 8pt !important;
        }

        .row {
            --bs-gutter-x: 1rem !important;
        }

        a {
            color: inherit !important;
            text-decoration: none !important;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

@endpush
