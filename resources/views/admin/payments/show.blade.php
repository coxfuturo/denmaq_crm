@extends('admin.layout.app')

@section('title', 'Payment Details')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('payments.edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('payments.delete'));
@endphp

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="page-title-box d-flex align-items-center justify-content-between">

                <div>
                    <h4 class="page-title mb-1">Payment Details</h4>
                    <p class="text-muted mb-0">
                        View payment information
                    </p>
                </div>

                <div class="d-flex gap-2">

                    @if($canEdit)
                    <a
                    href="{{ route('admin.payments.edit', $payment->id) }}"
                    class="btn btn-warning"
                    >
                    <i class="ri-edit-line me-1"></i>
                    Edit
                </a>
                @endif

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

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    Payment Information
                </h5>

                <div>
                    @switch($payment->status)

                    @case('completed')
                    <span class="badge bg-success">Completed</span>
                    @break

                    @case('pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                    @break

                    @case('failed')
                    <span class="badge bg-danger">Failed</span>
                    @break

                    @case('cancelled')
                    <span class="badge bg-secondary">Cancelled</span>
                    @break

                    @case('refunded')
                    <span class="badge bg-info">Refunded</span>
                    @break

                    @case('partially_refunded')
                    <span class="badge bg-primary">Partially Refunded</span>
                    @break

                    @default
                    <span class="badge bg-secondary">
                        {{ ucwords(str_replace('_', ' ', $payment->status)) }}
                    </span>

                    @endswitch
                </div>
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
                                ₹{{ number_format((float)$payment->amount, 2) }}
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Payment Date
                            </small>

                            <strong>
                                {{ $payment->payment_date ? $payment->payment_date->format('d-m-Y') : '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block mb-1">
                                Payment Method
                            </small>

                            <strong>
                                {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
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
                                {{ $payment->client->name }}
                            </strong>
                            @else
                            <span class="text-muted">N/A</span>
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
                            <span class="text-muted">N/A</span>
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
                                {{ $payment->invoice->invoice_number ?? 'Invoice #'.$payment->invoice->id }}
                            </strong>
                            @else
                            <span class="text-muted">N/A</span>
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
                                {{ trim($payment->creator->first_name.' '.$payment->creator->last_name) }}
                            </strong>

                            @if($payment->creator->email)
                            <small class="text-muted">
                                {{ $payment->creator->email }}
                            </small>
                            @endif

                            @else
                            <span class="text-muted">N/A</span>
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
                    style="max-height: 350px;"
                    >
                </div>

                @elseif($extension === 'pdf')

                <div class="py-4">
                    <i class="ri-file-pdf-line text-danger" style="font-size: 64px;"></i>

                    <h5 class="mt-2">
                        PDF Payment Proof
                    </h5>
                </div>

                @else

                <div class="py-4">
                    <i class="ri-file-line" style="font-size: 64px;"></i>
                </div>

                @endif

                <a
                href="{{ $attachmentUrl }}"
                target="_blank"
                class="btn btn-primary"
                >
                <i class="ri-external-link-line me-1"></i>
                Open Attachment
            </a>

            @else

            <div class="py-5 text-muted">
                <i class="ri-file-forbid-line" style="font-size: 48px;"></i>

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
                <span class="text-muted">Created</span>

                <strong>
                    {{ $payment->created_at ? $payment->created_at->format('d-m-Y H:i') : '-' }}
                </strong>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-muted">Updated</span>

                <strong>
                    {{ $payment->updated_at ? $payment->updated_at->format('d-m-Y H:i') : '-' }}
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