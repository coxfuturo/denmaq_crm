@extends('admin.layout.app')

@section('title', 'Invoice Trash')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');

$can = function ($permission) use ($user, $isSuperAdmin) {
    return $isSuperAdmin || ($user && $user->can($permission));
};
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Invoice Trash</h4>

            <p class="text-muted mb-0">
                Deleted invoices
            </p>
        </div>

        <a
        href="{{ route('admin.invoices.index') }}"
        class="btn btn-light"
        >
        <i class="bi bi-arrow-left"></i>
        Back to Invoices
    </a>

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

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">

    {{ session('error') }}

    <button
    type="button"
    class="btn-close"
    data-bs-dismiss="alert"
    ></button>

</div>
@endif

<div class="card mb-4">

    <div class="card-body">

        <form
        method="GET"
        action="{{ route('admin.invoices.trash') }}"
        >

        <div class="row g-3">

            <div class="col-md-6">

                <label class="form-label">
                    Search
                </label>

                <input
                type="text"
                name="search"
                class="form-control"
                value="{{ request('search') }}"
                placeholder="Invoice no, subject, client..."
                >

            </div>

            @if($isSuperAdmin)

            <div class="col-md-4">

                <label class="form-label">
                    Deleted By
                </label>

                <select
                name="created_by"
                class="form-select"
                >

                <option value="">
                    All Users
                </option>

                @foreach($users as $userItem)

                <option
                value="{{ $userItem->id }}"
                {{ (string) request('created_by') === (string) $userItem->id ? 'selected' : '' }}
                >
                {{ trim($userItem->first_name . ' ' . $userItem->last_name) }}

                @if($userItem->email)
                - {{ $userItem->email }}
                @endif
            </option>

            @endforeach

        </select>

    </div>

    @endif

    <div class="col-md-2 d-flex align-items-end gap-2">

        <button
        type="submit"
        class="btn btn-primary"
        >
        <i class="bi bi-search"></i>
        Search
    </button>

    <a
    href="{{ route('admin.invoices.trash') }}"
    class="btn btn-light"
    >
    <i class="bi bi-arrow-clockwise"></i>
</a>

</div>

</div>

</form>

</div>

</div>

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h5 class="mb-0">
            Deleted Invoices
        </h5>

        <span class="text-muted">
            Total: {{ $invoices->total() }}
        </span>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>#</th>
                        <th>Invoice No.</th>
                        <th>Client</th>
                        <th>Invoice Date</th>
                        <th>Total</th>

                        @if($isSuperAdmin)
                        <th>Added By</th>
                        @endif

                        <th>Deleted At</th>
                        <th class="text-end">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($invoices as $invoice)

                    <tr>

                        <td>
                            {{ $invoices->firstItem() + $loop->index }}
                        </td>

                        <td>

                            <div class="fw-semibold">
                                {{ $invoice->invoice_number }}
                            </div>

                            @if($invoice->subject)

                            <div class="small text-muted">
                                {{ $invoice->subject }}
                            </div>

                            @endif

                        </td>

                        <td>

                            @if($invoice->client)

                            <div class="fw-semibold">
                                {{ $invoice->client->name }}
                            </div>

                            @if($invoice->client->company_name)

                            <div class="small text-muted">
                                {{ $invoice->client->company_name }}
                            </div>

                            @endif

                            @else

                            <span class="text-muted">
                                Client deleted
                            </span>

                            @endif

                        </td>

                        <td>
                            {{ $invoice->invoice_date?->format('d-m-Y') ?? '-' }}
                        </td>

                        <td>

                            <strong>
                                ₹{{ number_format((float) $invoice->total, 2) }}
                            </strong>

                        </td>

                        @if($isSuperAdmin)

                        <td>

                            @if($invoice->creator)

                            <div class="fw-semibold">
                                {{ trim($invoice->creator->first_name . ' ' . $invoice->creator->last_name) }}
                            </div>

                            <div class="small text-muted">
                                {{ $invoice->creator->email }}
                            </div>

                            @else

                            <span class="text-muted">
                                Unknown
                            </span>

                            @endif

                        </td>

                        @endif

                        <td>

                            {{ $invoice->deleted_at?->format('d-m-Y h:i A') ?? '-' }}

                        </td>

                        <td>

                            <div class="d-flex justify-content-end gap-1">

                                @if($can('Invoices Delete'))

                                <form
                                method="POST"
                                action="{{ route('admin.invoices.restore', $invoice->id) }}"
                                onsubmit="return restoreConfirm(this, 'This invoice will be restored from trash.')"
                                class="d-inline"
                                >

                                @csrf
                                @method('PATCH')

                                <button
                                type="submit"
                                class="btn btn-sm btn-outline-success"
                                title="Restore"
                                >
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>

                        </form>

                        <form
                        method="POST"
                        action="{{ route('admin.invoices.forceDelete', $invoice->id) }}"
                        onsubmit="return forceDeleteConfirm(this, 'This invoice will be permanently deleted. This action cannot be undone.')"
                        class="d-inline"
                        >

                        @csrf
                        @method('DELETE')

                        <button
                        type="submit"
                        class="btn btn-sm btn-outline-danger"
                        title="Permanent Delete"
                        >
                        <i class="bi bi-trash3"></i>
                    </button>

                </form>

                @endif

            </div>

        </td>

    </tr>

    @empty

    <tr>

        <td
        colspan="{{ $isSuperAdmin ? 8 : 7 }}"
        class="text-center py-5"
        >

        <div class="text-muted">

            <i class="bi bi-trash fs-2 d-block mb-2"></i>

            No deleted invoices found.

        </div>

    </td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

@if($invoices->hasPages())

<div class="card-footer">
    {{ $invoices->links() }}
</div>

@endif

</div>

</div>

@endsection