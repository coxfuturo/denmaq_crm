@extends('admin.layout.app')

@section('title', 'Payments')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canView = $isSuperAdmin || ($user && $user->can('Payments View'));
$canCreate = $isSuperAdmin || ($user && $user->can('Payments Create'));
$canEdit = $isSuperAdmin || ($user && $user->can('Payments Edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('Payments Delete'));
@endphp

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

    .search-btn,
    .reset-btn {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:5px;
        white-space:nowrap;
    }

    .search-btn i,
    .reset-btn i {
        font-size:15px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="page-title mb-1">Payments</h4>
            <small class="text-muted">{{ $isSuperAdmin ? 'Manage all payments' : 'Manage your payments' }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($canDelete)
            <a href="{{ route('admin.payments.trash') }}" class="btn btn-sm btn-danger" title="Trash">
                <i class="ri-delete-bin-line"></i>
            </a>
            @endif
            @if($canCreate)
            <a href="{{ route('admin.payments.create') }}" class="btn btn-sm btn-primary" title="Add Payment">
                <i class="ri-add-line"></i>
            </a>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.payments.index') }}">
                <div class="row g-2">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search payment / transaction / client">
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <select name="client_id" class="form-select form-select-sm">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}{{ $client->contact_person ? ' - '.$client->contact_person : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <select name="payment_method" class="form-select form-select-sm">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="upi" {{ request('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="debit_card" {{ request('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                            <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-xl-1 col-lg-2 col-md-6">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            <option value="partially_refunded" {{ request('status') == 'partially_refunded' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Date From">
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Date To">
                    </div>

                    @if($isSuperAdmin)

                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <select name="created_by" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            @foreach($users as $item)
                            <option value="{{ $item->id }}" {{ request('created_by') == $item->id ? 'selected' : '' }}>
                                {{ trim($item->first_name.' '.$item->last_name) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-auto">
                        <select name="per_page" class="form-select form-select-sm">
                            @foreach([10,15,25,50,100] as $perPage)
                            <option value="{{ $perPage }}" {{ (int)request('per_page', 10) === $perPage ? 'selected' : '' }}>
                                {{ $perPage }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-1">
                            <button type="submit" class="btn btn-sm btn-primary search-btn">
                                <i class="ri-search-line"></i>
                                <span>Search</span>
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary reset-btn">
                                <i class="ri-refresh-line"></i>
                                <span>Reset</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="45">#</th>
                            <th>Payment</th>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Invoice</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Status</th>
                            @if($isSuperAdmin)
                            <th>Added By</th>
                            @endif
                            <th width="130">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $loop->index }}</td>

                            <td>
                                <strong>{{ $payment->payment_number }}</strong>
                                @if($payment->transaction_id)
                                <small class="d-block text-muted">{{ $payment->transaction_id }}</small>
                                @endif
                            </td>

                            <td>
                                @if($payment->client)
                                <strong>{{ $payment->client->company_name }}</strong>
                                @if($payment->client->contact_person)
                                <small class="d-block text-muted">{{ $payment->client->contact_person }}</small>
                                @endif
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($payment->project)
                                {{ $payment->project->name }}
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td>
                                @if($payment->invoice)
                                {{ $payment->invoice->invoice_number ?? '#'.$payment->invoice->id }}
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td>
                                <strong>₹{{ number_format((float)$payment->amount, 2) }}</strong>
                            </td>

                            <td>
                                {{ $payment->payment_date ? $payment->payment_date->format('d-m-Y') : '-' }}
                            </td>

                            <td>
                                {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>

                            <td>
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
                                <span class="badge bg-primary">Partial</span>
                                @break
                                @default
                                <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $payment->status)) }}</span>
                                @endswitch
                            </td>

                            @if($isSuperAdmin)

                            <td>
                                @if($payment->creator)
                                <strong>{{ trim($payment->creator->first_name.' '.$payment->creator->last_name) }}</strong>
                                @if($payment->creator->email)
                                <small class="d-block text-muted">{{ $payment->creator->email }}</small>
                                @endif
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            @endif

                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @if($canView)
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="action-icon view-icon" title="View">
                                        👁
                                    </a>
                                    @endif

                                    @if($canEdit) <a href="{{ route('admin.payments.edit', $payment->id) }}" class="action-icon edit-icon" title="Edit">
                                    ✎ </a>
                                    @endif

                                    @if($canDelete)

                                    <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" class="delete-form" onsubmit="return confirmForm(this, 'This payment will be moved to trash.')">
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
                            <td colspan="{{ $isSuperAdmin ? 11 : 10 }}" class="text-center py-4">
                                <i class="ri-wallet-3-line fs-2 d-block text-muted mb-2"></i>
                                <span class="text-muted">No payments found.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())

            <div class="p-2 border-top">
                {{ $payments->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
