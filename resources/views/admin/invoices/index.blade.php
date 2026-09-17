@extends('admin.layout.app')

@section('content')

<style>
    .action-icon {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:32px;
        height:32px;
        border-radius:4px;
        border:1px solid transparent;
        text-decoration:none;
        font-size:17px;
        line-height:1;
        cursor:pointer;
        background:#fff;
        padding:0;
    }

    .view-icon {
        color:#0d6efd;
        border-color:#0d6efd;
    }

    .view-icon:hover {
        color:#fff;
        background:#0d6efd;
    }

    .edit-icon {
        color:#f0ad00;
        border-color:#f0ad00;
    }

    .edit-icon:hover {
        color:#fff;
        background:#f0ad00;
    }

    .delete-icon {
        color:#dc3545;
        border-color:#dc3545;
    }

    .delete-icon:hover {
        color:#fff;
        background:#dc3545;
    }

    .delete-form {
        display:inline-flex;
        margin:0;
    }
</style>

<div class="container-fluid">

    @php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser && $currentUser->hasRole('Super Admin');
    $can = function ($permission) use ($currentUser, $isSuperAdmin) {
        return $isSuperAdmin || ($currentUser && $currentUser->can($permission));
    };
    $clientLabel = function ($client) {
        if (!$client) {
            return 'Client deleted';
        }
        $attributes = $client->getAttributes();
        $name = trim(
        ($attributes['first_name'] ?? '') . ' ' .
        ($attributes['last_name'] ?? '')
        );
        if ($name === '') {
            $name =
            ($attributes['company_name'] ?? '') ?:
            ($attributes['email'] ?? ('Client #' . $client->id));
        }
        return $name;
    };
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Invoices</h4>
            <p class="text-muted mb-0">
                @if($isSuperAdmin)
                Manage all system invoices
                @else
                Manage your invoices
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center gap-1">
            @if($can('Invoices Delete'))
            <a href="{{ route('admin.invoices.trash') }}" class="btn btn-sm btn-danger px-2">
                <i class="bi bi-trash me-1"></i>
                Trash
            </a>
            @endif
            @if($can('Invoices Create'))
            <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-primary px-2">
                <i class="bi bi-plus-lg me-1"></i>
                Add Invoice
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="GET" action="{{ route('admin.invoices.index') }}" class="row g-2 mb-3 align-items-end">

                <div class="col-xl-3 col-lg-3 col-md-6">
                    <label class="form-label mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Invoice, subject, client...">
                </div>

                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1">Client</label>
                    <select name="client_id" class="form-select form-select-sm">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                        @php
                        $clientName = $clientLabel($client);
                        $clientAttributes = $client->getAttributes();
                        $companyName = $clientAttributes['company_name'] ?? '';
                        @endphp
                        <option value="{{ $client->id }}" {{ (string) request('client_id') === (string) $client->id ? 'selected' : '' }}>
                            {{ $clientName }}
                            @if($companyName && $clientName !== $companyName)
                            - {{ $companyName }}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                @if($isSuperAdmin)

                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1">Added By</label>
                    <select name="created_by" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string) request('created_by') === (string) $user->id ? 'selected' : '' }}>
                            {{ trim($user->first_name . ' ' . $user->last_name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-xl-1 col-lg-1 col-md-6">
                    <label class="form-label mb-1">Per Page</label>
                    <select name="per_page" class="form-select form-select-sm">
                        @foreach([10,15,25,50,100,200,500] as $page)
                        <option value="{{ $page }}" {{ (int) request('per_page', $perPage) === $page ? 'selected' : '' }}>
                            {{ $page }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-6">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary px-2">
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-secondary px-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>
                    </div>
                </div>

            </form>

            <div class="table-responsive">

                <table class="table table-sm table-bordered table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th style="width:1%;white-space:nowrap;">S.N</th>
                            <th>Invoice</th>
                            <th>Client</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            @if($isSuperAdmin)
                            <th>Added By</th>
                            @endif
                            <th width="130">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($invoices as $invoice)

                        @php
                        $clientName = $clientLabel($invoice->client);
                        $statusClass = match($invoice->status) {
                            'Paid' => 'success',
                            'Sent' => 'primary',
                            'Partially Paid' => 'info',
                            'Overdue' => 'danger',
                            'Cancelled' => 'dark',
                            default => 'secondary',
                        };
                        @endphp

                        <tr>

                            <td style="width:1%;white-space:nowrap;">
                                {{ $invoices->firstItem() + $loop->index }}
                            </td>

                            <td>
                                @if($can('Invoices View'))
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-primary text-decoration-none fw-semibold">
                                    {{ $invoice->invoice_number }}
                                </a>
                                @else
                                <span class="fw-semibold">
                                    {{ $invoice->invoice_number }}
                                </span>
                                @endif

                                @if($invoice->subject)

                                <div class="small text-muted">
                                    {{ $invoice->subject }}
                                </div>
                                @endif
                            </td>

                            <td>
                                @if($invoice->client)

                                <div>
                                    <strong>{{ $clientName }}</strong>
                                </div>

                                @php
                                $clientAttributes = $invoice->client->getAttributes();
                                $companyName = $clientAttributes['company_name'] ?? '';
                                $clientEmail = $clientAttributes['email'] ?? '';
                                @endphp

                                @if($companyName)

                                <div class="small text-muted">
                                    {{ $companyName }}
                                </div>
                                @elseif($clientEmail)
                                <div class="small text-muted">
                                    {{ $clientEmail }}
                                </div>
                                @endif

                                @else

                                <span class="text-danger">
                                    Client deleted
                                </span>

                                @endif

                            </td>

                            <td>
                                @if($invoice->invoice_date)
                                {{ $invoice->invoice_date->format('d-m-Y') }}
                                @else
                                -
                                @endif
                            </td>

                            <td>
                                @if($invoice->due_date)

                                {{ $invoice->due_date->format('d-m-Y') }}

                                @if($invoice->status === 'Overdue')

                                <div class="small text-danger">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Overdue
                                </div>
                                @endif

                                @else

                                *

                                @endif

                            </td>

                            <td>
                                <strong>
                                    ₹{{ number_format((float) $invoice->total, 2) }}
                                </strong>

                                <div class="small text-muted">
                                    Sub ₹{{ number_format((float) $invoice->subtotal, 2) }}
                                </div>
                            </td>

                            <td>

                                @if($can('Invoices Status'))

                                <form action="{{ route('admin.invoices.changeStatus', $invoice->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">

                                        @foreach($statuses as $status)

                                        <option value="{{ $status }}" {{ $invoice->status === $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>

                                        @endforeach

                                    </select>

                                </form>

                                @else

                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $invoice->status }}
                                </span>

                                @endif

                            </td>

                            @if($isSuperAdmin)

                            <td>

                                @if($invoice->creator)

                                @php
                                $creatorName = trim(
                                ($invoice->creator->first_name ?? '') . ' ' .
                                ($invoice->creator->last_name ?? '')
                                );
                                @endphp

                                <div>
                                    <strong>
                                        {{ $creatorName ?: 'Unknown User' }}
                                    </strong>
                                </div>

                                @if($invoice->creator->email)

                                <div class="small text-muted">
                                    {{ $invoice->creator->email }}
                                </div>
                                @endif

                                @else

                                <span class="text-muted">
                                    Unknown User
                                </span>

                                @endif

                            </td>

                            @endif

                            <td>

                                <div class="d-flex align-items-center gap-1">

                                    @if($can('Invoices View'))

                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="action-icon view-icon" title="View">
                                        👁
                                    </a>

                                    @endif

                                    @if($can('Invoices Edit'))

                                    <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="action-icon edit-icon" title="Edit">
                                        ✎
                                    </a>

                                    @endif

                                    @if($can('Invoices Delete'))

                                    <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="delete-form" onsubmit="return deleteConfirm(this, 'This invoice will be moved to trash.')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="action-icon delete-icon" title="Delete">
                                            🗑
                                        </button>

                                    </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="{{ $isSuperAdmin ? 9 : 8 }}" class="text-center py-4">
                                No invoices found.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($invoices->hasPages())

            <div class="d-flex justify-content-end mt-3">
                {{ $invoices->appends(request()->query())->links() }}
            </div>

            @endif

        </div>
    </div>

</div>

@endsection
