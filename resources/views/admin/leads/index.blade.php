@extends('admin.layout.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Leads</h4>
            <p class="text-muted mb-0">Manage system leads</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.leads.trash') }}" class="btn btn-danger">
                <i class="bi bi-trash"></i>
                Trash
            </a>
            <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Add Lead
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.leads.index') }}" class="row g-2 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search lead...">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="source" class="form-select">
                        <option value="">All Sources</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                {{ $source }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="assigned_to" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ trim($user->first_name . ' ' . $user->last_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach([10,15,25,50,100,200,500] as $limit)
                            <option value="{{ $limit }}" {{ (int) request('per_page', 15) === $limit ? 'selected' : '' }}>
                                {{ $limit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Lead</th>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Service</th>
                            <th>Source</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Follow Up</th>
                            <th>Budget</th>
                            <th>Created</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td>{{ $leads->firstItem() + $loop->index }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $lead->name }}</strong>
                                        @if($lead->email)
                                            <div class="small text-muted">{{ $lead->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $lead->company_name ?: '-' }}</td>
                                <td>
                                    <div>{{ $lead->phone }}</div>
                                    @if($lead->alternate_phone)
                                        <div class="small text-muted">{{ $lead->alternate_phone }}</div>
                                    @endif
                                </td>
                                <td>{{ $lead->service ?: '-' }}</td>
                                <td>{{ $lead->source ?: '-' }}</td>
                                <td>
                                    @if($lead->assignedUser)
                                        {{ trim($lead->assignedUser->first_name . ' ' . $lead->assignedUser->last_name) }}
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.leads.changeStatus', $lead->id) }}" method="POST">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ $lead->status === $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($lead->follow_up_date)
                                        {{ $lead->follow_up_date->format('d-m-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($lead->budget !== null)
                                        ₹{{ number_format((float) $lead->budget, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $lead->created_at?->format('d-m-Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.leads.show', $lead->id) }}">
                                                    View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.leads.edit', $lead->id) }}">
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This lead will be moved to trash.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-1"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    No leads found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $leads->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
