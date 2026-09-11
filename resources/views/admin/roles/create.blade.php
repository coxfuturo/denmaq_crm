@extends('admin.layout.app')

@section('title', 'Create Role')

@section('content')

@php
    $user = auth()->user();
    $isSuperAdmin = $user && $user->hasRole('Super Admin');

    $can = function ($permission) use ($user, $isSuperAdmin) {
        return $isSuperAdmin || ($user && $user->can($permission));
    };
@endphp

<div class="container-fluid role-page">

    <div class="page-header d-flex justify-content-between align-items-center">

        <div>
            <h2 class="page-title">Create Role</h2>
            <p class="page-description">
                Create a new role and assign permissions
            </p>
        </div>

        @if($can('roles.view'))
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

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif

    @if(!$can('roles.create'))

        <div class="alert alert-danger">
            You do not have permission to create roles.
        </div>

    @else

        <form
            action="{{ route('admin.roles.store') }}"
            method="POST"
        >

            @csrf

            <div class="card mb-4">

                <div class="card-header">

                    <h5>Role Information</h5>

                    <small>
                        Enter basic information for this role.
                    </small>

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
                                value="{{ old('name') }}"
                                placeholder="Enter role name"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
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
                                value="{{ old('name_alias') }}"
                                placeholder="Enter role alias"
                            >

                            @error('name_alias')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
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
                                value="{{ old('icon') }}"
                                placeholder="Example: users"
                            >

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

                            <label for="position" class="form-label">
                                Position
                            </label>

                            <input
                                type="number"
                                name="position"
                                id="position"
                                class="form-control @error('position') is-invalid @enderror"
                                value="{{ old('position', 0) }}"
                                min="0"
                            >

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

                                <input
                                    type="checkbox"
                                    name="status"
                                    value="1"
                                    class="form-check-input"
                                    id="status"
                                    {{ old('status', 1) ? 'checked' : '' }}
                                >

                                <label
                                    class="form-check-label"
                                    for="status"
                                >
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

                            <h5>Permissions</h5>

                            <small>
                                Select permissions for this role.
                            </small>

                        </div>

                        <div class="permission-actions d-flex gap-2">

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="selectAll"
                            >

                                <i data-feather="check-square"></i>

                                <span class="ms-1">
                                    Select All
                                </span>

                            </button>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                id="deselectAll"
                            >

                                <i data-feather="square"></i>

                                <span class="ms-1">
                                    Clear All
                                </span>

                            </button>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @forelse($permissions as $module => $modulePermissions)

                        @php
                            $moduleSlug = \Illuminate\Support\Str::slug($module ?: 'other-permissions');
                            $permissionNames = $modulePermissions->pluck('name')->toArray();
                            $oldPermissions = old('permissions', []);

                            $moduleSelected =
                                count($permissionNames) > 0 &&
                                count(array_diff($permissionNames, $oldPermissions)) === 0;
                        @endphp

                        <div class="permission-module">

                            <div class="module-header">

                                <h6 class="module-title">

                                    <i data-feather="folder"></i>

                                    <span>
                                        {{ $module ?: 'Other Permissions' }}
                                    </span>

                                </h6>

                                <div class="module-select">

                                    <input
                                        type="checkbox"
                                        class="form-check-input module-checkbox"
                                        data-module="{{ $moduleSlug }}"
                                        id="module_{{ $moduleSlug }}"
                                        {{ $moduleSelected ? 'checked' : '' }}
                                    >

                                    <label for="module_{{ $moduleSlug }}">
                                        Select Module
                                    </label>

                                </div>

                            </div>

                            <div class="row g-2">

                                @foreach($modulePermissions as $permission)

                                    @php
                                        $permissionLabel = $permission->action
                                            ?: \Illuminate\Support\Str::headline(
                                                str_replace('.', ' ', $permission->name)
                                            );
                                    @endphp

                                    <div class="col-xl-3 col-lg-4 col-md-6">

                                        <div class="permission-item">

                                            <div class="form-check">

                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->name }}"
                                                    class="form-check-input permission-checkbox permission-{{ $moduleSlug }}"
                                                    id="permission_{{ $permission->id }}"
                                                    {{ in_array($permission->name, $oldPermissions, true) ? 'checked' : '' }}
                                                >

                                                <label
                                                    class="form-check-label"
                                                    for="permission_{{ $permission->id }}"
                                                >

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
                                Please create permissions before creating a role.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

            <div class="form-footer">

                @if($can('roles.view'))

                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="btn btn-light"
                    >

                        <i data-feather="x"></i>

                        <span class="ms-1">
                            Cancel
                        </span>

                    </a>

                @endif

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i data-feather="save"></i>

                    <span class="ms-1">
                        Create Role
                    </span>

                </button>

            </div>

        </form>

    @endif

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

    .permission-module {
        border: 1px solid #dee2e6;
        border-radius: 7px;
        margin-bottom: 15px;
        overflow: hidden;
    }

    .permission-module:last-child {
        margin-bottom: 0;
    }

    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 14px;
    }

    .module-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .module-title svg {
        width: 16px;
        height: 16px;
    }

    .module-select {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #495057;
    }

    .module-select label {
        cursor: pointer;
        margin: 0;
    }

    .permission-module .row {
        padding: 12px;
    }

    .permission-item {
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 9px 10px;
        background: #fff;
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

        .module-header {
            align-items: flex-start;
            gap: 10px;
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

<script>

document.addEventListener('DOMContentLoaded', function() {

    const selectAll = document.getElementById('selectAll');
    const deselectAll = document.getElementById('deselectAll');

    const modules = document.querySelectorAll('.module-checkbox');
    const permissions = document.querySelectorAll('.permission-checkbox');

    function updateModule(module) {

        const items = document.querySelectorAll(
            '.permission-' + module
        );

        const checked = document.querySelectorAll(
            '.permission-' + module + ':checked'
        );

        const checkbox = document.querySelector(
            '.module-checkbox[data-module="' + module + '"]'
        );

        if (!checkbox) {
            return;
        }

        checkbox.checked =
            items.length > 0 &&
            items.length === checked.length;

        checkbox.indeterminate =
            checked.length > 0 &&
            checked.length < items.length;
    }

    selectAll?.addEventListener('click', function() {

        permissions.forEach(function(item) {
            item.checked = true;
        });

        modules.forEach(function(item) {
            item.checked = true;
            item.indeterminate = false;
        });

    });

    deselectAll?.addEventListener('click', function() {

        permissions.forEach(function(item) {
            item.checked = false;
        });

        modules.forEach(function(item) {
            item.checked = false;
            item.indeterminate = false;
        });

    });

    modules.forEach(function(module) {

        module.addEventListener('change', function() {

            const items = document.querySelectorAll(
                '.permission-' + this.dataset.module
            );

            items.forEach(function(item) {
                item.checked = module.checked;
            });

            module.indeterminate = false;

        });

        updateModule(module.dataset.module);

    });

    permissions.forEach(function(permission) {

        permission.addEventListener('change', function() {

            const moduleClass = Array.from(
                this.classList
            ).find(function(item) {

                return item.startsWith('permission-') &&
                    item !== 'permission-checkbox';

            });

            if (!moduleClass) {
                return;
            }

            const module = moduleClass.replace(
                'permission-',
                ''
            );

            updateModule(module);

        });

    });

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

});

</script>

@endsection
