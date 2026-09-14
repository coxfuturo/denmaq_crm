@extends('admin.layout.app')

@section('content')

@php
$authUser = auth()->user();
$isSuperAdmin = $authUser && $authUser->hasRole('Super Admin');
$canView = $isSuperAdmin || ($authUser && $authUser->can('Users View'));
$canAdd = $isSuperAdmin || ($authUser && $authUser->can('Users Create'));
$canEdit = $isSuperAdmin || ($authUser && $authUser->can('Users Edit'));
$canDelete = $isSuperAdmin || ($authUser && $authUser->can('Users Delete'));
@endphp

<div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Users</h4>
            <p class="text-muted mb-0">Manage system users</p>
        </div>

        @if($canAdd)
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add User
        </a>
        @endif

    </div>

    <div class="card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div class="d-flex align-items-center gap-2">

                    @if($canView)
                    <a href="{{ route('admin.users.index') }}"
                    class="btn {{ $view !== 'trash' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-people me-1"></i>
                    Active Users
                </a>
                @endif

                @if($canDelete)
                <a href="{{ route('admin.users.index', ['view' => 'trash']) }}"
                 class="btn {{ $view === 'trash' ? 'btn-danger' : 'btn-outline-danger' }}">
                 <i class="bi bi-trash me-1"></i>
                 Trash
             </a>
             @endif

         </div>

     </div>

     @if($canView)

     <form method="GET"
     action="{{ route('admin.users.index') }}"
     class="row g-3 mb-4">

     <input type="hidden" name="view" value="{{ $view }}">

     <div class="col-lg-4 col-md-6">
        <label class="form-label">Search</label>
        <input type="text"
        name="search"
        value="{{ $search }}"
        class="form-control"
        placeholder="Search user...">
    </div>

    <div class="col-lg-2 col-md-6">
        <label class="form-label">Type</label>
        <select name="type" class="form-select">
            <option value="">All Types</option>
            <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>
                Admin
            </option>
            <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>
                Company
            </option>
            <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>
                Customer
            </option>
        </select>
    </div>

    @if($view !== 'trash')

    <div class="col-lg-2 col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

    @endif

    <div class="col-lg-2 col-md-6">
        <label class="form-label">Per Page</label>
        <select name="per_page"
        class="form-select"
        onchange="this.form.submit()">
        @foreach([10, 25, 50, 100, 200, 500] as $limit)
        <option value="{{ $limit }}"
        {{ $perPage == $limit ? 'selected' : '' }}>
        {{ $limit }}
    </option>
    @endforeach
</select>
</div>

<div class="col-lg-2 col-md-6">
    <label class="form-label">&nbsp;</label>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search me-1"></i>
            Search
        </button>

        <a href="{{ route('admin.users.index', ['view' => $view]) }}"
         class="btn btn-secondary">
         <i class="bi bi-arrow-clockwise me-1"></i>
         Reset
     </a>
 </div>
</div>

</form>

@endif

<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle mb-0">

        <thead>
            <tr>
                <th style="width: 60px;">S.N</th>
                <th>User</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Type</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th style="width: 110px;">Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse($users as $user)

            <tr>

                <td>
                    {{ $users->firstItem() + $loop->index }}
                </td>

                <td>
                    <div class="d-flex align-items-center">

                        @if($user->profile_image)

                        <img src="{{ asset('storage/' . $user->profile_image) }}"
                        width="40"
                        height="40"
                        class="rounded-circle me-2"
                        style="object-fit: cover;"
                        alt="{{ $user->first_name }}">

                        @else

                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                        style="width:40px;height:40px;flex-shrink:0;">
                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
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

            <td>
                {{ $user->email }}
            </td>

            <td>
                {{ $user->mobile ?? '-' }}
            </td>

            <td>

                @if($user->type === 'admin')

                <span class="badge bg-danger">
                    Admin
                </span>

                @elseif($user->type === 'company')

                <span class="badge bg-info">
                    Company
                </span>

                @else

                <span class="badge bg-secondary">
                    Customer
                </span>

                @endif

            </td>

            <td>

                @forelse($user->roles as $role)

                <span class="badge bg-primary me-1">
                    {{ $role->name }}
                </span>

                @empty

                <span class="text-muted">-</span>

                @endforelse

            </td>

            <td>

                @if($user->status)

                <span class="badge bg-success">
                    Active
                </span>

                @else

                <span class="badge bg-warning text-dark">
                    Inactive
                </span>

                @endif

            </td>

            <td>
                {{ $user->created_at?->format('d-m-Y') }}
            </td>

            <td>

                @if($canView || $canEdit || $canDelete)

                <div class="dropdown">

                    <button type="button"
                    class="btn btn-sm btn-light border dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Action
                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    @if($canView)

                    <li>
                        <a class="dropdown-item"
                        href="{{ route('admin.users.show', $user->id) }}">
                        <i class="bi bi-eye me-2"></i>
                        View
                    </a>
                </li>

                @endif

                @if($view !== 'trash')

                @if($canEdit)

                <li>
                    <a class="dropdown-item"
                    href="{{ route('admin.users.edit', $user->id) }}">
                    <i class="bi bi-pencil me-2"></i>
                    Edit
                </a>
            </li>

            @if(!$user->hasRole('Super Admin'))

            <li>
                <a class="dropdown-item"
                href="{{ route('admin.users.status', $user->id) }}"
                onclick="return statusConfirm(this.href, {{ $user->status ? 'false' : 'true' }}, 'user')">
                <i class="bi bi-toggle-on me-2"></i>
                {{ $user->status ? 'Deactivate' : 'Activate' }}
            </a>
        </li>

        @endif

        @endif

        @if($canDelete && !$user->hasRole('Super Admin'))

        <li>
            <form action="{{ route('admin.users.destroy', $user->id) }}"
              method="POST"
              onsubmit="return deleteConfirm(this, 'This user will be moved to trash.')">

              @csrf
              @method('DELETE')

              <button type="submit"
              class="dropdown-item text-danger">
              <i class="bi bi-trash me-2"></i>
              Delete
          </button>

      </form>
  </li>

  @endif

  @else

  @if($canDelete)

  <li>
    <form action="{{ route('admin.users.restore', $user->id) }}"
      method="GET"
      onsubmit="return restoreConfirm(this, 'This user will be restored.')">

      <button type="submit"
      class="dropdown-item text-success">
      <i class="bi bi-arrow-counterclockwise me-2"></i>
      Restore
  </button>

</form>
</li>

@if(!$user->hasRole('Super Admin'))

<li>
    <form action="{{ route('admin.users.forceDelete', $user->id) }}"
      method="POST"
      onsubmit="return permanentDeleteConfirm(this, 'This user will be permanently deleted. This action cannot be undone.')">

      @csrf
      @method('DELETE')

      <button type="submit"
      class="dropdown-item text-danger">
      <i class="bi bi-trash3 me-2"></i>
      Permanent Delete
  </button>

</form>
</li>

@endif

@endif

@endif

</ul>

</div>

@endif

</td>

</tr>

@empty

<tr>
    <td colspan="9" class="text-center py-5">
        <div class="text-muted">
            <i class="bi bi-people fs-2 d-block mb-2"></i>
            No users found.
        </div>
    </td>
</tr>

@endforelse

</tbody>

</table>

</div>

@if($users->hasPages())

<div class="d-flex justify-content-end mt-4">
    {{ $users->appends(request()->query())->links() }}
</div>

@endif

</div>

</div>

</div>

@endsection
