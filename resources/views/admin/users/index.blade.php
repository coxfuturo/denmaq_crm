@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Users</h4>
            <p class="text-muted mb-0">Manage system users</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Add User
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn {{ $view !== 'trash' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Active Users
                    </a>
                    <a href="{{ route('admin.users.index', ['view' => 'trash']) }}" class="btn {{ $view === 'trash' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Trash
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-4">
                <input type="hidden" name="view" value="{{ $view }}">

                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search user...">
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>Company</option>
                        <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>

                @if($view !== 'trash')
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                @endif

                <div class="col-md-2">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach([10, 25, 50, 100, 200, 500] as $limit)
                            <option value="{{ $limit }}" {{ $perPage == $limit ? 'selected' : '' }}>
                                {{ $limit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.users.index', ['view' => $view]) }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Type</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->profile_image)
                                            <img src="{{ asset('storage/' . $user->profile_image) }}" width="40" height="40" class="rounded-circle me-2" style="object-fit: cover;" alt="{{ $user->first_name }}">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;">
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>
                                                {{ $user->first_name }}
                                                {{ $user->last_name }}
                                            </strong>
                                            @if($user->company_name)
                                                <div class="small text-muted">
                                                    {{ $user->company_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile ?? '-' }}</td>
                                <td>
                                    @if($user->type === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->type === 'company')
                                        <span class="badge bg-info">Company</span>
                                    @else
                                        <span class="badge bg-secondary">Customer</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-primary">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($user->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at?->format('d-m-Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                    View
                                                </a>
                                            </li>
                                            @if($view !== 'trash')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.status', $user->id) }}" onclick="return statusConfirm(this.href, {{ $user->status ? 'false' : 'true' }}, 'user')">
                                                        {{ $user->status ? 'Deactivate' : 'Activate' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This user will be moved to trash.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="GET" onsubmit="return restoreConfirm(this, 'This user will be restored.')">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            Restore
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" onsubmit="return permanentDeleteConfirm(this, 'This user will be permanently deleted. This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            Permanent Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
