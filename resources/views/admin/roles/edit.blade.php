@extends('admin.layout.app')

@section('title', 'Edit Role')

@section('content')

@php
$user = auth()->user();

$isSuperAdmin = $user && $user->hasRole('Super Admin');

$can = function ($permission) use ($user, $isSuperAdmin) {
    return $isSuperAdmin || ($user && $user->can($permission));
};

$assignedPermissions = $role->permissions
->pluck('name')
->toArray();

$oldPermissions = old('permissions', $assignedPermissions);

$isSuperAdminRole = $role->name === 'Super Admin';
@endphp

<div class="container-fluid role-page">

    ```
    <div class="page-header d-flex justify-content-between align-items-center">

        <div>
            <h2 class="page-title">
                Edit Role
            </h2>

            <p class="page-description">
                Update role information and permissions
            </p>
        </div>

        @if($can('Roles View'))

        <a href="{{ route('admin.roles.index') }}"
        class="btn btn-light">

        <i data-feather="arrow-left"></i>

        <span class="ms-1">
            Back
        </span>

    </a>

    @endif

</div>

@if($errors->any())

<div class="alert alert-danger alert-dismissible fade show"
role="alert">

<div class="d-flex">

    <i data-feather="alert-circle"
    class="me-2"></i>

    <div>

        <strong>
            Please fix the following errors:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

</div>

<button type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

@endif

@if(!$can('Roles Edit'))

<div class="card border-0 shadow-sm">

    <div class="card-body text-center py-5">

        <i data-feather="lock"
        style="width: 50px; height: 50px;"
        class="text-danger mb-3">
    </i>

    <h4 class="text-danger">
        Access Denied
    </h4>

    <p class="text-muted mb-3">
        You do not have permission to edit roles.
    </p>

    @if($can('Roles View'))

    <a href="{{ route('admin.roles.index') }}"
    class="btn btn-primary">

    <i data-feather="arrow-left"></i>

    <span class="ms-1">
        Back to Roles
    </span>

</a>

@endif

</div>

</div>

@else

@if($isSuperAdminRole)

<div class="alert alert-warning d-flex align-items-center">

    <i data-feather="shield"
    class="me-2"
    style="width: 20px;">
</i>

<div>

    <strong>
        Super Admin Role
    </strong>

    <div class="small">
        Super Admin role cannot be modified.
    </div>

</div>

</div>

@endif

<form action="{{ route('admin.roles.update', $role->id) }}"
    method="POST">

    @csrf

    @method('PUT')

    <div class="card mb-4">

        <div class="card-header">

            <h5>
                Role Information
            </h5>

            <small>
                Update basic information for this role.
            </small>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label for="name"
                    class="form-label">

                    Role Name

                    <span class="text-danger">
                        *
                    </span>

                </label>

                <input type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $role->name) }}"
                placeholder="Enter role name"
                required
                {{ $isSuperAdminRole ? 'readonly' : '' }}>

                @error('name')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

                @enderror

            </div>

            <div class="col-md-6 mb-3">

                <label for="name_alias"
                class="form-label">

                Role Alias

            </label>

            <input type="text"
            name="name_alias"
            id="name_alias"
            class="form-control @error('name_alias') is-invalid @enderror"
            value="{{ old('name_alias', $role->name_alias) }}"
            placeholder="Enter role alias"
            {{ $isSuperAdminRole ? 'readonly' : '' }}>

            @error('name_alias')

            <div class="invalid-feedback">
                {{ $message }}
            </div>

            @enderror

        </div>

        <div class="col-md-6 mb-3">

            <label for="icon"
            class="form-label">

            Icon

        </label>

        <input type="text"
        name="icon"
        id="icon"
        class="form-control @error('icon') is-invalid @enderror"
        value="{{ old('icon', $role->icon) }}"
        placeholder="Example: users"
        {{ $isSuperAdminRole ? 'readonly' : '' }}>

        <div class="form-text">
            Use Feather icon name such as users, shield or user.
        </div>

        @error('icon')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>

    <div class="col-md-3 mb-3">

        <label for="position"
        class="form-label">

        Position

    </label>

    <input type="number"
    name="position"
    id="position"
    class="form-control @error('position') is-invalid @enderror"
    value="{{ old('position', $role->position) }}"
    min="0"
    {{ $isSuperAdminRole ? 'readonly' : '' }}>

    @error('position')

    <div class="invalid-feedback">
        {{ $message }}
    </div>

    @enderror

</div>

<div class="col-md-3 mb-3">

    <label class="form-label d-block">
        Status
    </label>

    <div class="form-check form-switch mt-2">

        <input type="hidden"
        name="status"
        value="0">

        <input type="checkbox"
        name="status"
        value="1"
        class="form-check-input"
        id="status"
        {{ old('status', $role->status) ? 'checked' : '' }}
        {{ $isSuperAdminRole ? 'disabled' : '' }}>

        <label class="form-check-label"
        for="status">

        Active

    </label>

</div>

</div>

</div>

</div>

</div>

<div class="card mb-4">

    <div class="card-header">

        <div class="permissions-header d-flex justify-content-between align-items-center">

            <div>

                <h5>
                    Permissions
                </h5>

                <small>
                    Select permissions for this role.
                </small>

            </div>

            @if(!$isSuperAdminRole)

            <div class="permission-actions d-flex gap-2">

                <button type="button"
                class="btn btn-sm btn-outline-primary"
                id="selectAll">

                <i data-feather="check-square"></i>

                <span class="ms-1">
                    Select All
                </span>

            </button>

            <button type="button"
            class="btn btn-sm btn-outline-secondary"
            id="deselectAll">

            <i data-feather="square"></i>

            <span class="ms-1">
                Clear All
            </span>

        </button>

    </div>

    @endif

</div>

</div>

<div class="card-body">

    @forelse($permissions->groupBy(function ($permission) {
        return $permission->module ?: 'Other';
    }) as $module => $modulePermissions)

    @php
    $moduleName = $module ?: 'Other';

    $moduleSlug = \Illuminate\Support\Str::slug($moduleName);

    $modulePermissionNames = $modulePermissions
    ->pluck('name')
    ->toArray();

    $selectedCount = count(
    array_intersect(
    $modulePermissionNames,
    $oldPermissions
    )
    );

    $allModuleSelected =
    count($modulePermissionNames) > 0 &&
    $selectedCount === count($modulePermissionNames);
    @endphp

    <div class="permission-section">

        <div class="section-header">

            <div class="section-title-wrapper">

                <div class="section-icon">
                    <i data-feather="layers"></i>
                </div>

                <div>

                    <h5 class="section-title">
                        {{ $moduleName }}
                    </h5>

                    <span class="section-description">
                        Manage {{ $moduleName }} permissions
                    </span>

                </div>

            </div>

            @if(!$isSuperAdminRole)

            <div class="section-select">

                <input type="checkbox"
                class="form-check-input module-checkbox"
                data-module="{{ $moduleSlug }}"
                id="module_{{ $moduleSlug }}"
                {{ $allModuleSelected ? 'checked' : '' }}>

                <label for="module_{{ $moduleSlug }}">
                    Select All
                </label>

            </div>

            @endif

        </div>

        <div class="row g-2 p-3">

            @foreach($modulePermissions as $permission)

            @php
            $permissionChecked = in_array(
            $permission->name,
            $oldPermissions,
            true
            );

            $permissionLabel = $permission->action
            ?: \Illuminate\Support\Str::headline(
            str_replace(
            ['.', '_', '-'],
            ' ',
            $permission->name
            )
            );
            @endphp

            <div class="col-xl-3 col-lg-4 col-md-6">

                <div class="permission-item">

                    <div class="form-check">

                        <input type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->name }}"
                        class="form-check-input permission-checkbox permission-{{ $moduleSlug }}"
                        data-module="{{ $moduleSlug }}"
                        id="permission_{{ $permission->id }}"
                        {{ $permissionChecked ? 'checked' : '' }}
                        {{ $isSuperAdminRole ? 'disabled' : '' }}>

                        <label class="form-check-label"
                        for="permission_{{ $permission->id }}">

                        <strong>
                            {{ $permissionLabel }}
                        </strong>

                        <small class="permission-name">
                            {{ $permission->name }}
                        </small>

                    </label>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

@empty

<div class="empty-permission">

    <i data-feather="shield-off"></i>

    <h5>
        No Permissions Available
    </h5>

    <p>
        Please create permissions before editing a role.
    </p>

</div>

@endforelse

</div>

</div>

<div class="form-footer">

    @if($can('Roles View'))

    <a href="{{ route('admin.roles.index') }}"
    class="btn btn-light">

    <i data-feather="x"></i>

    <span class="ms-1">
        Cancel
    </span>

</a>

@endif

@if(!$isSuperAdminRole)

<button type="submit"
class="btn btn-primary">

<i data-feather="save"></i>

<span class="ms-1">
    Update Role
</span>

</button>

@else

@if($can('Roles View'))

<a href="{{ route('admin.roles.index') }}"
class="btn btn-primary">

<i data-feather="arrow-left"></i>

<span class="ms-1">
    Back to Roles
</span>

</a>

@endif

@endif

</div>

</form>

@endif
```

</div>

<style>
    .role-page {
        padding-bottom: 30px;
    }

    .page-header {
        margin-bottom: 20px;
    }

    .page-title {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .page-description {
        color: #6c757d;
        margin-bottom: 0;
    }

    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    .card-header {
        background: #f8f9fa;
        padding: 15px 18px;
    }

    .card-header h5 {
        margin-bottom: 3px;
        font-size: 16px;
        font-weight: 600;
    }

    .card-header small {
        color: #6c757d;
    }

    .permission-section {
        border: 1px solid #dfe3e8;
        border-radius: 8px;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .permission-section:last-child {
        margin-bottom: 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f1f5f9;
        border-bottom: 1px solid #dfe3e8;
        padding: 14px 16px;
    }

    .section-title-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-icon {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        background: #ffffff;
        border: 1px solid #dee2e6;
    }

    .section-icon svg {
        width: 17px;
        height: 17px;
    }

    .section-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
    }

    .section-description {
        display: block;
        margin-top: 2px;
        font-size: 11px;
        color: #6c757d;
    }

    .section-select {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        color: #495057;
    }

    .section-select label {
        cursor: pointer;
        margin: 0;
        font-weight: 500;
    }

    .permission-item {
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 9px 10px;
        background: #ffffff;
        height: 100%;
    }

    .permission-item:hover {
        background: #f8f9fa;
        border-color: #ced4da;
    }

    .permission-item .form-check {
        min-height: auto;
        margin: 0;
    }

    .permission-item .form-check-label {
        font-size: 13px;
        cursor: pointer;
        word-break: break-word;
        display: block;
    }

    .permission-item .form-check-input {
        cursor: pointer;
    }

    .permission-name {
        display: block;
        color: #6c757d;
        font-size: 10px;
        margin-top: 2px;
    }

    .permission-actions {
        flex-wrap: wrap;
    }

    .empty-permission {
        text-align: center;
        padding: 50px 20px;
        color: #6c757d;
    }

    .empty-permission svg {
        width: 45px;
        height: 45px;
        margin-bottom: 10px;
    }

    .empty-permission h5 {
        margin-bottom: 5px;
    }

    .empty-permission p {
        margin-bottom: 0;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-bottom: 20px;
    }

    @media(max-width: 767px) {

        .page-header {
            align-items: flex-start !important;
            gap: 10px;
        }

        .permissions-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }

        .section-header {
            align-items: flex-start;
            gap: 12px;
        }

        .permission-actions {
            width: 100%;
        }

        .form-footer {
            justify-content: stretch;
        }

        .form-footer .btn {
            flex: 1;
        }

    }
</style>

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const selectAllButton = document.getElementById('selectAll');
        const deselectAllButton = document.getElementById('deselectAll');

        function getModulePermissions(module) {

            return document.querySelectorAll(
                '.permission-' + module
                );

        }

        function updateModuleCheckbox(module) {

            const moduleCheckbox = document.querySelector(
                '.module-checkbox[data-module="' + module + '"]'
                );

            if (!moduleCheckbox) {
                return;
            }

            const permissions = getModulePermissions(module);

            const total = permissions.length;

            let checked = 0;

            permissions.forEach(function (checkbox) {

                if (checkbox.checked) {
                    checked++;
                }

            });

            moduleCheckbox.checked =
            total > 0 &&
            checked === total;

            moduleCheckbox.indeterminate =
            checked > 0 &&
            checked < total;

        }

        document.querySelectorAll('.module-checkbox').forEach(function (moduleCheckbox) {

            moduleCheckbox.addEventListener('change', function () {

                const module = this.dataset.module;

                getModulePermissions(module).forEach(function (checkbox) {

                    checkbox.checked =
                    moduleCheckbox.checked;

                });

                updateModuleCheckbox(module);

            });

        });

        document.querySelectorAll('.permission-checkbox').forEach(function (permissionCheckbox) {

            permissionCheckbox.addEventListener('change', function () {

                updateModuleCheckbox(
                    this.dataset.module
                    );

            });

        });

        if (selectAllButton) {

            selectAllButton.addEventListener('click', function () {

                document.querySelectorAll(
                    '.permission-checkbox'
                    ).forEach(function (checkbox) {

                        checkbox.checked = true;

                    });

                    document.querySelectorAll(
                        '.module-checkbox'
                        ).forEach(function (checkbox) {

                            checkbox.checked = true;
                            checkbox.indeterminate = false;

                        });

                    });

        }

        if (deselectAllButton) {

            deselectAllButton.addEventListener('click', function () {

                document.querySelectorAll(
                    '.permission-checkbox'
                    ).forEach(function (checkbox) {

                        checkbox.checked = false;

                    });

                    document.querySelectorAll(
                        '.module-checkbox'
                        ).forEach(function (checkbox) {

                            checkbox.checked = false;
                            checkbox.indeterminate = false;

                        });

                    });

        }

        document.querySelectorAll('.module-checkbox').forEach(function (checkbox) {

            updateModuleCheckbox(
                checkbox.dataset.module
                );

        });

        if (typeof feather !== 'undefined') {
            feather.replace();
        }

    });
</script>

@endpush

@endsection
