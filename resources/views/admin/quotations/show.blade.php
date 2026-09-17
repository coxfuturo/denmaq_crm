@extends('admin.layout.app')

@section('title', 'Quotation ' . $quotation->quotation_number)

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('Quotations Edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('Quotations Delete'));
$canStatus = $isSuperAdmin || ($user && $user->can('Quotations Status'));
$discountValue = (float) ($quotation->discount_value ?? 0);
$discountAmount = (float) ($quotation->discount_amount ?? 0);
@endphp

<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

        <div>
            <h4 class="mb-1">
                {{ $quotation->quotation_number }}
            </h4>

            <p class="text-muted mb-0">
                {{ $quotation->subject }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a
            href="{{ route('admin.quotations.index') }}"
            class="btn btn-light border"
            >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

        @if($canEdit)
        <a
        href="{{ route('admin.quotations.edit', $quotation->id) }}"
        class="btn btn-primary"
        >
        <i class="bi bi-pencil me-1"></i>
        Edit
    </a>
    @endif

</div>

</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}

    <button
    type="button"
    class="btn-close"
    data-bs-dismiss="alert"
    ></button>
</div>
@endif

<div class="card mb-4">

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-6">

                <h6 class="text-muted mb-2">
                    Bill To
                </h6>

                @if($quotation->client)

                <h5 class="mb-1">
                    {{ $quotation->client->company_name }}
                </h5>

                @if($quotation->client->contact_person)
                <div>
                    {{ $quotation->client->contact_person }}
                </div>
                @endif

                @if($quotation->client->email)
                <div class="text-muted">
                    {{ $quotation->client->email }}
                </div>
                @endif

                @if($quotation->client->mobile)
                <div class="text-muted">
                    {{ $quotation->client->mobile }}
                </div>
                @endif

                @else

                <span class="text-muted">
                    Client information unavailable
                </span>

                @endif

            </div>

            <div class="col-md-6">

                <div class="row g-3">

                    <div class="col-6">
                        <div class="text-muted small">
                            Quotation Number
                        </div>

                        <strong>
                            {{ $quotation->quotation_number }}
                        </strong>
                    </div>

                    <div class="col-6">
                        <div class="text-muted small">
                            Status
                        </div>

                        @if($canStatus)

                        <form
                        action="{{ route('admin.quotations.changeStatus', $quotation->id) }}"
                        method="POST"
                        >
                        @csrf
                        @method('PATCH')

                        <select
                        name="status"
                        class="form-select form-select-sm"
                        onchange="this.form.submit()"
                        >
                        @foreach(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'] as $status)
                        <option
                        value="{{ $status }}"
                        {{ $quotation->status === $status ? 'selected' : '' }}
                        >
                        {{ $status }}
                    </option>
                    @endforeach
                </select>
            </form>

            @else

            <span class="badge bg-secondary">
                {{ $quotation->status }}
            </span>

            @endif
        </div>

        <div class="col-6">
            <div class="text-muted small">
                Quotation Date
            </div>

            <strong>
                {{ $quotation->quotation_date?->format('d M Y') ?? '-' }}
            </strong>
        </div>

        <div class="col-6">
            <div class="text-muted small">
                Valid Until
            </div>

            <strong>
                {{ $quotation->valid_until?->format('d M Y') ?? '-' }}
            </strong>
        </div>

    </div>

</div>

</div>

</div>

</div>

<div class="card mb-4">

    <div class="card-header">
        <h5 class="mb-0">
            Quotation Items
        </h5>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered align-middle mb-0">

                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th width="120">Qty</th>
                        <th width="150">Rate</th>
                        <th width="170">Amount</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($quotation->items as $item)

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

                        <td>
                            {{ number_format((float) $item->quantity, 2) }}
                        </td>

                        <td>
                            ₹{{ number_format((float) $item->rate, 2) }}
                        </td>

                        <td>
                            <strong>
                                ₹{{ number_format((float) $item->amount, 2) }}
                            </strong>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td
                        colspan="6"
                        class="text-center py-4 text-muted"
                        >
                        No quotation items found.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>

<div class="row g-4 mb-4">

    <div class="col-lg-7">

        @if($quotation->notes || $quotation->terms)

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">
                    Notes & Terms
                </h5>
            </div>

            <div class="card-body">

                @if($quotation->notes)
                <div class="mb-4">

                    <h6>
                        Notes
                    </h6>

                    <div
                    class="text-muted"
                    style="white-space: pre-line;"
                    >
                    {{ $quotation->notes }}
                </div>

            </div>
            @endif

            @if($quotation->terms)
            <div>

                <h6>
                    Terms & Conditions
                </h6>

                <div
                class="text-muted"
                style="white-space: pre-line;"
                >
                {{ $quotation->terms }}
            </div>

        </div>
        @endif

    </div>

</div>

@else

<div class="card">

    <div class="card-body text-muted">
        No notes or terms added.
    </div>

</div>

@endif

</div>

<div class="col-lg-5">

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Summary
            </h5>
        </div>

        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <span>
                    Subtotal
                </span>

                <strong>
                    ₹{{ number_format((float) $quotation->subtotal, 2) }}
                </strong>
            </div>

            <div class="d-flex justify-content-between mb-3">

                <span>
                    Discount

                    @if($quotation->discount_type === 'percentage')
                    ({{ number_format($discountValue, 2) }}%)
                    @endif
                </span>

                <strong class="text-danger">
                    -₹{{ number_format($discountAmount, 2) }}
                </strong>

            </div>

            <div class="d-flex justify-content-between mb-3">

                <span>
                    Tax
                </span>

                <strong>
                    ₹{{ number_format((float) $quotation->tax, 2) }}
                </strong>

            </div>

            <hr>

            <div class="d-flex justify-content-between fs-5">

                <span>
                    Grand Total
                </span>

                <strong>
                    ₹{{ number_format((float) $quotation->total, 2) }}
                </strong>

            </div>

        </div>

    </div>

</div>

</div>

@if($isSuperAdmin)

<div class="card mb-4">

    <div class="card-body">

        <h6 class="mb-3">
            Created Information
        </h6>

        <div class="row g-3">

            <div class="col-md-4">

                <div class="text-muted small">
                    Added By
                </div>

                @if($quotation->creator)

                <strong>
                    {{ trim($quotation->creator->first_name . ' ' . $quotation->creator->last_name) }}
                </strong>

                @if($quotation->creator->email)
                <div class="small text-muted">
                    {{ $quotation->creator->email }}
                </div>
                @endif

                @else

                <span class="text-muted">
                    Unknown
                </span>

                @endif

            </div>

            <div class="col-md-4">

                <div class="text-muted small">
                    Created
                </div>

                <strong>
                    {{ $quotation->created_at?->format('d M Y, h:i A') ?? '-' }}
                </strong>

            </div>

            <div class="col-md-4">

                <div class="text-muted small">
                    Last Updated
                </div>

                <strong>
                    {{ $quotation->updated_at?->format('d M Y, h:i A') ?? '-' }}
                </strong>

            </div>

        </div>

    </div>

</div>

@endif

@if($canDelete)

<div class="d-flex justify-content-end">

    <form
    action="{{ route('admin.quotations.destroy', $quotation->id) }}"
    method="POST"
    onsubmit="return deleteConfirm(this, 'This quotation will be moved to trash.')"
    >
    @csrf
    @method('DELETE')

    <button
    type="submit"
    class="btn btn-outline-danger"
    >
    <i class="bi bi-trash me-1"></i>
    Delete Quotation
</button>

</form>

</div>

@endif

</div>

@endsection