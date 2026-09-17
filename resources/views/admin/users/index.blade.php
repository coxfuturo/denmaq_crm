@extends('admin.layout.app')
@section('content')
@php
$authUser=auth()->user();
$isSuperAdmin=$authUser&&$authUser->hasRole('Super Admin');
$canView=$isSuperAdmin||($authUser&&$authUser->can('Users View'));
$canAdd=$isSuperAdmin||($authUser&&$authUser->can('Users Create'));
$canEdit=$isSuperAdmin||($authUser&&$authUser->can('Users Edit'));
$canDelete=$isSuperAdmin||($authUser&&$authUser->can('Users Delete'));
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1"><i class="bi bi-people me-1"></i>Users</h4>
            <p class="text-muted mb-0">Manage system users</p>
        </div>
        @if($canAdd)
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary px-2"><i class="bi bi-plus-lg me-1"></i>Add User</a>
        @endif
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-1">
                    @if($canView)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ $view!=='trash'?'btn-primary':'btn-outline-primary' }} px-2"><i class="bi bi-people me-1"></i>Active Users</a>
                    @endif
                    @if($canDelete)
                    <a href="{{ route('admin.users.index',['view'=>'trash']) }}" class="btn btn-sm {{ $view==='trash'?'btn-danger':'btn-outline-danger' }} px-2"><i class="bi bi-trash me-1"></i>Trash</a>
                    @endif
                </div>
            </div>
            @if($canView)
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-3 align-items-end">
                <input type="hidden" name="view" value="{{ $view }}">
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <label class="form-label mb-1"><i class="bi bi-search me-1"></i>Search</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search user...">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1"><i class="bi bi-person-badge me-1"></i>Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="admin" {{ request('type')=='admin'?'selected':'' }}>Admin</option>
                        <option value="company" {{ request('type')=='company'?'selected':'' }}>Company</option>
                        <option value="customer" {{ request('type')=='customer'?'selected':'' }}>Customer</option>
                    </select>
                </div>
                @if($view!=='trash')
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1"><i class="bi bi-toggle-on me-1"></i>Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status')==='1'?'selected':'' }}>Active</option>
                        <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                @endif
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1"><i class="bi bi-list-ol me-1"></i>Per Page</label>
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach([10,25,50,100,200,500] as $limit)
                        <option value="{{ $limit }}" {{ $perPage==$limit?'selected':'' }}>{{ $limit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary px-2"><i class="bi bi-search me-1"></i>Search</button>
                        <a href="{{ route('admin.users.index',['view'=>$view]) }}" class="btn btn-sm btn-secondary px-2"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                    </div>
                </div>
            </form>
            @endif
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:1%;white-space:nowrap;">S.N</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Type</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th style="width:90px;white-space:nowrap;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td style="width:1%;white-space:nowrap;">{{ $users->firstItem()+$loop->index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_image)
                                    <img src="{{ asset('storage/'.$user->profile_image) }}" width="32" height="32" class="rounded-circle me-2" style="object-fit:cover;" alt="{{ $user->first_name }}">
                                    @else
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;flex-shrink:0;">{{ strtoupper(substr($user->first_name??'U',0,1)) }}</div>
                                    @endif
                                    <div>
                                        <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                        @if($user->company_name)
                                        <div class="small text-muted">{{ $user->company_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile??'-' }}</td>
                            <td>
                                @if($user->type==='admin')
                                <span class="badge bg-danger px-2"><i class="bi bi-shield-lock me-1"></i>Admin</span>
                                @elseif($user->type==='company')
                                <span class="badge bg-info px-2"><i class="bi bi-building me-1"></i>Company</span>
                                @else
                                <span class="badge bg-secondary px-2"><i class="bi bi-person me-1"></i>Customer</span>
                                @endif
                            </td>
                            <td>
                                @forelse($user->roles as $role)
                                <span class="badge bg-primary px-2 me-1"><i class="bi bi-person-check me-1"></i>{{ $role->name }}</span>
                                @empty
                                <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td>
                                @if($user->status)
                                <span class="badge bg-success px-2"><i class="bi bi-check-circle me-1"></i>Active</span>
                                @else
                                <span class="badge bg-warning text-dark px-2"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at?->format('d-m-Y') }}</td>
                            <td>
                                @if($canView||$canEdit||$canDelete)
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-light border dropdown-toggle px-2 py-1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical me-1"></i>Action
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($canView)
                                        <li>
                                            <a class="dropdown-item py-1" href="{{ route('admin.users.show',$user->id) }}">
                                                <i class="bi bi-eye me-2"></i>View
                                            </a>
                                        </li>
                                        @endif
                                        @if($view!=='trash')
                                        @if($canEdit)
                                        <li>
                                            <a class="dropdown-item py-1" href="{{ route('admin.users.edit',$user->id) }}">
                                                <i class="bi bi-pencil-square me-2"></i>Edit
                                            </a>
                                        </li>
                                        @if(!$user->hasRole('Super Admin'))
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        @if($user->status)
                                        <li>
                                            <form action="{{ route('admin.users.status',$user->id) }}" method="POST" onsubmit="return deleteConfirm(this,'This user will be deactivated.')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="0">
                                                <button type="submit" class="dropdown-item py-1 text-warning">
                                                    <i class="bi bi-toggle-off me-2"></i>Deactivate
                                                </button>
                                            </form>
                                        </li>
                                        @else
                                        <li>
                                            <form action="{{ route('admin.users.status',$user->id) }}" method="POST" onsubmit="return deleteConfirm(this,'This user will be activated.')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="dropdown-item py-1 text-success">
                                                    <i class="bi bi-toggle-on me-2"></i>Activate
                                                </button>
                                            </form>
                                            
                                        </li>
                                        @endif
                                        @endif
                                        @endif
                                        @if($canDelete&&!$user->hasRole('Super Admin'))
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.users.destroy',$user->id) }}" method="POST" onsubmit="return deleteConfirm(this,'This user will be moved to trash.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1 text-danger">
                                                    <i class="bi bi-trash3 me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @else
                                        @if($canDelete)
                                        <li>
                                            <form action="{{ route('admin.users.restore', ['id' => $user->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item py-1 text-success">
                                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Restore
                                                </button>
                                            </form>

                                        </li>
                                        @if(!$user->hasRole('Super Admin'))
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.users.forceDelete',$user->id) }}" method="POST" onsubmit="return permanentDeleteConfirm(this,'This user will be permanently deleted. This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1 text-danger">
                                                    <i class="bi bi-trash3-fill me-2"></i>Permanent Delete
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
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-people fs-3 d-block text-muted mb-2"></i>
                                <span class="text-muted">No users found.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection