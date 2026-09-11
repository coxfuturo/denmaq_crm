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

@endphp

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Edit Role</h2>
        <p class="text-muted mb-0">Update role information and permissions</p>
    </div>

    @if($can('roles.view'))
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
            <i data-feather="arrow-left" style="width: 16px;"></i>
            <span class="ms-1">Back</span>
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i data-feather="check-circle" class="me-2" style="width: 18px;"></i>
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i data-feather="alert-circle" class="me-2" style="width: 18px;"></i>
        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>
@endif

@if(!$can('roles.edit'))

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

            @if($can('roles.view'))
                <a href="{{ route('admin.roles.index') }}"
                   class="btn btn-primary">

                    <i data-feather="arrow-left"
                       style="width: 16px;">
                    </i>

                    <span class="ms-1">
                        Back to Roles
                    </span>

                </a>
            @endif

        </div>
    </div>

@else

    @if($role->name === 'Super Admin')

        <div class="alert alert-warning d-flex align-items-center">

            <i data-feather="shield"
               class="me-2"
               style="width: 20px;">
            </i>

            <div>
                <strong>Super Admin Role</strong>
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

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 py-3">

                <h5 class="mb-0">
                    Role Information
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label for="name"
                               class="form-label">

                            Role Name
                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name) }}"
                               required
                               {{ $role->name === 'Super Admin' ? 'readonly' : '' }}>

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
                               {{ $role->name === 'Super Admin' ? 'readonly' : '' }}>

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
                               {{ $role->name === 'Super Admin' ? 'readonly' : '' }}>

                        <small class="text-muted">
                            Enter Feather icon name.
                        </small>

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
                               {{ $role->name === 'Super Admin' ? 'readonly' : '' }}>

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

                            <input type="checkbox"
                                   name="status"
                                   value="1"
                                   class="form-check-input"
                                   id="status"
                                   {{ old('status', $role->status) ? 'checked' : '' }}
                                   {{ $role->name === 'Super Admin' ? 'disabled' : '' }}>

                            <label class="form-check-label"
                                   for="status">

                                Active

                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div>

                        <h5 class="mb-0">
                            Permissions
                        </h5>

                        <small class="text-muted">
                            Select permissions for this role.
                        </small>

                    </div>

                    @if($role->name !== 'Super Admin')

                        <div class="d-flex gap-2">

                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    id="selectAll">

                                <i data-feather="check-square"
                                   style="width: 15px;">
                                </i>

                                <span class="ms-1">
                                    Select All
                                </span>

                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    id="deselectAll">

                                <i data-feather="square"
                                   style="width: 15px;">
                                </i>

                                <span class="ms-1">
                                    Clear All
                                </span>

                            </button>

                        </div>

                    @endif

                </div>

            </div>

            <div class="card-body">

                @forelse($permissions as $module => $modulePermissions)

                    @php
                        $moduleSlug = \Illuminate\Support\Str::slug($module);
                        $modulePermissionNames = $modulePermissions->pluck('name')->toArray();

                        $selectedCount = count(
                            array_intersect(
                                $modulePermissionNames,
                                $oldPermissions
                            )
                        );

                        $allModuleSelected =
                            count($modulePermissionNames) > 0 &&
                            $selectedCount === count($modulePermissionNames);

                        $someModuleSelected =
                            $selectedCount > 0 &&
                            $selectedCount < count($modulePermissionNames);
                    @endphp

                    <div class="border rounded p-3 mb-3 permission-module">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h6 class="mb-0 text-primary">

                                <i data-feather="folder"
                                   style="width: 16px;">
                                </i>

                                <span class="ms-1">
                                    {{ $module }}
                                </span>

                            </h6>

                            @if($role->name !== 'Super Admin')

                                <div class="form-check">

                                    <input type="checkbox"
                                           class="form-check-input module-checkbox"
                                           data-module="{{ $moduleSlug }}"
                                           id="module_{{ $moduleSlug }}"
                                           {{ $allModuleSelected ? 'checked' : '' }}>

                                    <label class="form-check-label"
                                           for="module_{{ $moduleSlug }}">

                                        Select Module

                                    </label>

                                </div>

                            @endif

                        </div>

                        <div class="row">

                            @foreach($modulePermissions as $permission)

                                @php
                                    $permissionChecked = in_array(
                                        $permission->name,
                                        $oldPermissions,
                                        true
                                    );
                                @endphp

                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">

                                    <div class="border rounded p-2 h-100">

                                        <div class="form-check">

                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->name }}"
                                                   class="form-check-input permission-checkbox permission-{{ $moduleSlug }}"
                                                   id="permission_{{ $permission->id }}"
                                                   {{ $permissionChecked ? 'checked' : '' }}
                                                   {{ $role->name === 'Super Admin' ? 'disabled' : '' }}>

                                            <label class="form-check-label w-100"
                                                   for="permission_{{ $permission->id }}">

                                                <strong>

                                                    {{ $permission->action ?: \Illuminate\Support\Str::headline(\Illuminate\Support\Str::after($permission->name, '.')) }}

                                                </strong>

                                                <small class="d-block text-muted">

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

                    <div class="text-center py-5">

                        <i data-feather="shield-off"
                           style="width: 45px; height: 45px;"
                           class="text-muted mb-3">
                        </i>

                        <h5 class="text-muted">
                            No Permissions Available
                        </h5>

                        <p class="text-muted mb-0">
                            Please create permissions before editing a role.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">

            @if($can('roles.view'))

                <a href="{{ route('admin.roles.index') }}"
                   class="btn btn-light">

                    <i data-feather="x"
                       style="width: 16px;">
                    </i>

                    <span class="ms-1">
                        Cancel
                    </span>

                </a>

            @endif

            @if($role->name !== 'Super Admin')

                <button type="submit"
                        class="btn btn-primary">

                    <i data-feather="save"
                       style="width: 16px;">
                    </i>

                    <span class="ms-1">
                        Update Role
                    </span>

                </button>

            @else

                @if($can('roles.view'))

                    <a href="{{ route('admin.roles.index') }}"
                       class="btn btn-primary">

                        <i data-feather="arrow-left"
                           style="width: 16px;">
                        </i>

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

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAllButton = document.getElementById('selectAll');
    const deselectAllButton = document.getElementById('deselectAll');

    function updateModuleCheckbox(module) {

        const moduleCheckbox = document.querySelector(
            '.module-checkbox[data-module="' + module + '"]'
        );

        const modulePermissions = document.querySelectorAll(
            '.permission-' + module
        );

        const checkedPermissions = document.querySelectorAll(
            '.permission-' + module + ':checked'
        );

        if (!moduleCheckbox) {
            return;
        }

        const total = modulePermissions.length;
        const checked = checkedPermissions.length;

        moduleCheckbox.checked =
            total > 0 && total === checked;

        moduleCheckbox.indeterminate =
            checked > 0 && checked < total;
    }

    function updateAllModuleCheckboxes() {

        document.querySelectorAll('.module-checkbox').forEach(
            function (moduleCheckbox) {

                updateModuleCheckbox(
                    moduleCheckbox.dataset.module
                );

            }
        );
    }

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

    document.querySelectorAll(
        '.module-checkbox'
    ).forEach(function (moduleCheckbox) {

        moduleCheckbox.addEventListener(
            'change',
            function () {

                const module = this.dataset.module;

                document.querySelectorAll(
                    '.permission-' + module
                ).forEach(function (checkbox) {

                    checkbox.checked =
                        moduleCheckbox.checked;

                });

                moduleCheckbox.indeterminate = false;

            }
        );

    });

    document.querySelectorAll(
        '.permission-checkbox'
    ).forEach(function (permissionCheckbox) {

        permissionCheckbox.addEventListener(
            'change',
            function () {

                const moduleClass = Array.from(
                    this.classList
                ).find(function (className) {

                    return className.startsWith('permission-') &&
                           className !== 'permission-checkbox';

                });

                if (!moduleClass) {
                    return;
                }

                const module = moduleClass.replace(
                    'permission-',
                    ''
                );

                updateModuleCheckbox(module);

            }
        );

    });

    updateAllModuleCheckboxes();

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

});
</script>

@endpush
