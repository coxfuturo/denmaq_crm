@extends('admin.layout.app')

@section('title', 'Edit Role')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');

$can = function ($permission) use ($user, $isSuperAdmin) {
    return $isSuperAdmin || ($user && $user->can($permission));
};

$assignedPermissions = $role->permissions->pluck('name')->toArray();
$oldPermissions = old('permissions', $assignedPermissions);
$isSuperAdminRole = $role->name === 'Super Admin';

$allowedPermissions = [
'Dashboard View',
'Users View',
'Users Create',
'Users Edit',
'Users Delete',
'Roles View',
'Roles Create',
'Roles Edit',
'Roles Delete',
'Permissions View',
'Permissions Create',
'Permissions Edit',
'Permissions Delete',
'Leads View',
'Leads Create',
'Leads Edit',
'Leads Delete',
'Clients View',
'Clients Create',
'Clients Edit',
'Clients Delete',
'Follow Ups View',
'Follow Ups Create',
'Follow Ups Edit',
'Follow Ups Delete',
'Projects View',
'Projects Create',
'Projects Edit',
'Projects Delete',
'Quotations View',
'Quotations Create',
'Quotations Edit',
'Quotations Delete',
'Invoices View',
'Invoices Create',
'Invoices Edit',
'Invoices Delete',
'Payments View',
'Payments Create',
'Payments Edit',
'Payments Delete',
'Lead Reports View',
'Sales Reports View',
'User Reports View',
'Company Settings View',
'Company Settings Edit',
'Profile View',
'Profile Edit'
];

$permissionCollection = collect($permissions)
->flatten(1)
->filter(function ($permission) use ($allowedPermissions) {
    return $permission &&
    isset($permission->name) &&
    in_array($permission->name, $allowedPermissions, true);
})
->unique('name')
->values();

$permissionModules = $permissionCollection
->groupBy(function ($permission) {
    return $permission->module ?: 'Other';
})
->sortKeys();

$moduleOrder = [
'Dashboard',
'Users',
'Roles',
'Permissions',
'Leads',
'Clients',
'Follow Ups',
'Projects',
'Quotations',
'Invoices',
'Payments',
'Reports',
'Company Settings',
'Profile'
];

$permissionModules = $permissionModules->sortBy(function ($permissions, $module) use ($moduleOrder) {
    $position = array_search($module, $moduleOrder, true);
    return $position === false ? 999 : $position;
});
@endphp

<div class="container-fluid role-page">

    <div class="page-header d-flex justify-content-between align-items-center">

        <div>
            <h2 class="page-title">Edit Role</h2>
            <p class="page-description">Update role information and permissions</p>
        </div>

        @if($can('Roles View'))
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
            <i data-feather="arrow-left"></i>
            <span class="ms-1">Back</span>
        </a>
        @endif

    </div>

    @if($errors->any())

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <div class="d-flex">

            <i data-feather="alert-circle" class="me-2"></i>

            <div>

                <strong>Please fix the following errors:</strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif

    @if(!$can('Roles Edit'))

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <i data-feather="lock" style="width:50px;height:50px;" class="text-danger mb-3"></i>

            <h4 class="text-danger">Access Denied</h4>

            <p class="text-muted mb-3">You do not have permission to edit roles.</p>

            @if($can('Roles View'))

            <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">

                <i data-feather="arrow-left"></i>

                <span class="ms-1">Back to Roles</span>

            </a>

            @endif

        </div>

    </div>

    @else

    @if($isSuperAdminRole)

    <div class="alert alert-warning d-flex align-items-center">

        <i data-feather="shield" class="me-2" style="width:20px;"></i>

        <div>

            <strong>Super Admin Role</strong>

            <div class="small">Super Admin role cannot be modified.</div>

        </div>

    </div>

    @endif

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card mb-4">

            <div class="card-header">

                <h5>Role Information</h5>

                <small>Update basic information for this role.</small>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label for="name" class="form-label">
                            Role Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role->name) }}"
                        placeholder="Enter role name"
                        required
                        {{ $isSuperAdminRole ? 'readonly' : '' }}
                        >

                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="name_alias" class="form-label">
                            Role Alias
                        </label>

                        <input
                        type="text"
                        name="name_alias"
                        id="name_alias"
                        class="form-control @error('name_alias') is-invalid @enderror"
                        value="{{ old('name_alias', $role->name_alias) }}"
                        placeholder="Enter role alias"
                        {{ $isSuperAdminRole ? 'readonly' : '' }}
                        >

                        @error('name_alias')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="icon" class="form-label">
                            Icon
                        </label>

                        <input
                        type="text"
                        name="icon"
                        id="icon"
                        class="form-control @error('icon') is-invalid @enderror"
                        value="{{ old('icon', $role->icon) }}"
                        placeholder="Example: users"
                        {{ $isSuperAdminRole ? 'readonly' : '' }}
                        >

                        <div class="form-text">
                            Use Feather icon name such as users, shield or user.
                        </div>

                        @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="col-md-3 mb-3">

                        <label for="position" class="form-label">
                            Position
                        </label>

                        <input
                        type="number"
                        name="position"
                        id="position"
                        class="form-control @error('position') is-invalid @enderror"
                        value="{{ old('position', $role->position) }}"
                        min="0"
                        {{ $isSuperAdminRole ? 'readonly' : '' }}
                        >

                        @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label d-block">
                            Status
                        </label>

                        <div class="form-check form-switch mt-2">

                            <input
                            type="checkbox"
                            name="status"
                            value="1"
                            class="form-check-input"
                            id="status"
                            {{ old('status', $role->status) ? 'checked' : '' }}
                            {{ $isSuperAdminRole ? 'disabled' : '' }}
                            >

                            <label class="form-check-label" for="status">
                                Active
                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="card mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div>
                    <h5>Permissions</h5>
                    <small>Select permissions for this role.</small>
                </div>

                @if(!$isSuperAdminRole && $permissionCollection->count())

                <div class="permission-actions">

                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">
                        Select All
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllPermissions">
                        Clear All
                    </button>

                </div>

                @endif

            </div>

            <div class="card-body">

                @if($permissionCollection->count())

                @foreach($permissionModules as $moduleName => $modulePermissions)

                @if($moduleName !== 'Other')

                <div class="permission-module">

                    <div class="permission-module-title d-flex justify-content-between align-items-center">

                        <span>
                            {{ \Illuminate\Support\Str::headline($moduleName) }}
                        </span>

                        @if(!$isSuperAdminRole)

                        <button
                        type="button"
                        class="btn btn-sm btn-link p-0 module-toggle"
                        data-module="{{ \Illuminate\Support\Str::slug($moduleName) }}"
                        >
                        Select All
                    </button>

                    @endif

                </div>

                <div class="row g-2">

                    @foreach($modulePermissions as $permission)

                    @php
                    $permissionChecked = in_array($permission->name, $oldPermissions, true);
                    $moduleSlug = \Illuminate\Support\Str::slug($moduleName);
                    @endphp

                    <div class="col-xl-3 col-lg-4 col-md-6">

                        <div class="permission-item">

                            <div class="form-check">

                                <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                class="form-check-input permission-checkbox permission-module-{{ $moduleSlug }}"
                                id="permission_{{ $permission->id }}"
                                {{ $permissionChecked ? 'checked' : '' }}
                                {{ $isSuperAdminRole ? 'disabled' : '' }}
                                >

                                <label
                                class="form-check-label"
                                for="permission_{{ $permission->id }}"
                                >

                                <strong>
                                    {{ $permission->name }}
                                </strong>

                            </label>

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

        @endif

        @endforeach

        @else

        <div class="empty-permission">

            <i data-feather="shield-off"></i>

            <h5>No Permissions Available</h5>

            <p>Please create permissions before editing a role.</p>

        </div>

        @endif

    </div>

</div>

<div class="form-footer">

    @if($can('Roles View'))

    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">

        <i data-feather="x"></i>

        <span class="ms-1">Cancel</span>

    </a>

    @endif

    @if(!$isSuperAdminRole)

    <button type="submit" class="btn btn-primary">

        <i data-feather="save"></i>

        <span class="ms-1">Update Role</span>

    </button>

    @else

    @if($can('Roles View'))

    <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">

        <i data-feather="arrow-left"></i>

        <span class="ms-1">Back to Roles</span>

    </a>

    @endif

    @endif

</div>

</form>

@endif

</div>

<style>

    .role-page {
        padding-bottom:30px;
    }

    .page-header {
        margin-bottom:20px;
    }

    .page-title {
        font-size:22px;
        font-weight:600;
        margin-bottom:4px;
    }

    .page-description {
        color:#6c757d;
        margin-bottom:0;
    }

    .card {
        border:1px solid #e5e7eb;
        border-radius:8px;
    }

    .card-header {
        background:#f8f9fa;
        padding:15px 18px;
    }

    .card-header h5 {
        margin-bottom:3px;
        font-size:16px;
        font-weight:600;
    }

    .card-header small {
        color:#6c757d;
    }

    .permission-actions {
        display:flex;
        gap:6px;
    }

    .permission-module {
        margin-bottom:25px;
    }

    .permission-module:last-child {
        margin-bottom:0;
    }

    .permission-module-title {
        font-size:15px;
        font-weight:600;
        color:#343a40;
        padding-bottom:8px;
        margin-bottom:10px;
        border-bottom:1px solid #e9ecef;
    }

    .module-toggle {
        font-size:12px;
        text-decoration:none;
    }

    .permission-item {
        border:1px solid #e9ecef;
        border-radius:5px;
        padding:10px;
        background:#ffffff;
        height:100%;
    }

    .permission-item:hover {
        background:#f8f9fa;
        border-color:#ced4da;
    }

    .permission-item .form-check {
        min-height:auto;
        margin:0;
    }

    .permission-item .form-check-label {
        font-size:13px;
        cursor:pointer;
        word-break:break-word;
        display:block;
    }

    .permission-item .form-check-input {
        cursor:pointer;
    }

    .empty-permission {
        text-align:center;
        padding:50px 20px;
        color:#6c757d;
    }

    .empty-permission svg {
        width:45px;
        height:45px;
        margin-bottom:10px;
    }

    .empty-permission h5 {
        margin-bottom:5px;
    }

    .empty-permission p {
        margin-bottom:0;
    }

    .form-footer {
        display:flex;
        justify-content:flex-end;
        gap:10px;
        padding-bottom:20px;
    }

    @media(max-width:767px) {

        .page-header {
            align-items:flex-start !important;
            gap:10px;
        }

        .form-footer {
            justify-content:stretch;
        }

        .form-footer .btn {
            flex:1;
        }

        .permission-actions {
            flex-direction:column;
        }

    }

</style>

@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function () {

        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        const checkboxes = document.querySelectorAll('.permission-checkbox');

        const selectAllButton = document.getElementById('selectAllPermissions');
        const clearAllButton = document.getElementById('clearAllPermissions');

        if (selectAllButton) {
            selectAllButton.addEventListener('click', function () {

                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = true;
                });

            });
        }

        if (clearAllButton) {
            clearAllButton.addEventListener('click', function () {

                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });

            });
        }

        document.querySelectorAll('.module-toggle').forEach(function (button) {

            button.addEventListener('click', function () {

                const module = this.getAttribute('data-module');
                const moduleCheckboxes = document.querySelectorAll('.permission-module-' + module);

                let allChecked = true;

                moduleCheckboxes.forEach(function (checkbox) {
                    if (!checkbox.checked) {
                        allChecked = false;
                    }
                });

                moduleCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = !allChecked;
                });

                this.textContent = allChecked ? 'Select All' : 'Clear All';

            });

        });

    });

</script>

@endpush

@endsection
