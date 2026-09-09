@extends('admin.layout.app')

@section('title', 'Edit Role')

@section('content')

<div class="container-fluid">

```
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">
            Edit Role
        </h2>

        <p class="text-muted mb-0">
            Update role information and permissions
        </p>
    </div>

    <a href="{{ route('admin.roles.index') }}"
       class="btn btn-light">

        <i data-feather="arrow-left" style="width: 16px;"></i>

        <span class="ms-1">
            Back
        </span>

    </a>

</div>


{{-- Error Message --}}
@if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show"
         role="alert">

        <i data-feather="alert-circle"
           class="me-2"
           style="width: 18px;">
        </i>

        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

@endif


{{-- Validation Errors --}}
@if($errors->any())

    <div class="alert alert-danger alert-dismissible fade show"
         role="alert">

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

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

@endif


<form action="{{ route('admin.roles.update', $role->id) }}"
      method="POST">

    @csrf

    @method('PUT')


    {{-- ========================================================= --}}
    {{-- Role Information --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="mb-0">
                Role Information
            </h5>

        </div>


        <div class="card-body">

            <div class="row">

                {{-- Role Name --}}
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
                           required>

                    @error('name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Role Alias --}}
                <div class="col-md-6 mb-3">

                    <label for="name_alias"
                           class="form-label">

                        Role Alias

                    </label>

                    <input type="text"
                           name="name_alias"
                           id="name_alias"
                           class="form-control @error('name_alias') is-invalid @enderror"
                           value="{{ old('name_alias', $role->name_alias) }}">

                    @error('name_alias')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Icon --}}
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
                           placeholder="Example: users">

                    <small class="text-muted">
                        Enter Feather icon name.
                    </small>

                    @error('icon')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Position --}}
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
                           min="0">

                    @error('position')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- Status --}}
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
                               {{ old('status', $role->status) ? 'checked' : '' }}>

                        <label class="form-check-label"
                               for="status">

                            Active

                        </label>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Permissions --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        Permissions
                    </h5>

                    <small class="text-muted">
                        Select permissions for this role.
                    </small>

                </div>


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

            </div>

        </div>


        <div class="card-body">

            @php

                $assignedPermissions = $role->permissions
                    ->pluck('id')
                    ->toArray();

                $oldPermissions = old(
                    'permissions',
                    $assignedPermissions
                );

            @endphp


            @forelse($permissions as $module => $modulePermissions)

                @php

                    $moduleSlug = Str::slug($module);

                    $modulePermissionIds = $modulePermissions
                        ->pluck('id')
                        ->toArray();

                    $allModuleSelected =
                        count($modulePermissionIds) > 0 &&
                        count(
                            array_diff(
                                $modulePermissionIds,
                                $oldPermissions
                            )
                        ) === 0;

                @endphp


                <div class="border rounded p-3 mb-3 permission-module">

                    {{-- Module Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h6 class="mb-0 text-primary">

                            <i data-feather="folder"
                               style="width: 16px;">
                            </i>

                            <span class="ms-1">
                                {{ $module }}
                            </span>

                        </h6>


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

                    </div>


                    {{-- Permissions --}}
                    <div class="row">

                        @foreach($modulePermissions as $permission)

                            <div class="col-md-3 col-sm-6 mb-2">

                                <div class="form-check">

                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           class="form-check-input permission-checkbox permission-{{ $moduleSlug }}"
                                           id="permission_{{ $permission->id }}"
                                           {{ in_array($permission->id, $oldPermissions) ? 'checked' : '' }}>

                                    <label class="form-check-label"
                                           for="permission_{{ $permission->id }}">

                                        {{ $permission->name }}

                                    </label>

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


    {{-- ========================================================= --}}
    {{-- Buttons --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-end gap-2 mb-4">

        <a href="{{ route('admin.roles.index') }}"
           class="btn btn-light">

            <i data-feather="x"
               style="width: 16px;">
            </i>

            <span class="ms-1">
                Cancel
            </span>

        </a>


        <button type="submit"
                class="btn btn-primary">

            <i data-feather="save"
               style="width: 16px;">
            </i>

            <span class="ms-1">
                Update Role
            </span>

        </button>

    </div>

</form>
```

</div>

@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const selectAllButton =
        document.getElementById('selectAll');

    const deselectAllButton =
        document.getElementById('deselectAll');


    /*
    |--------------------------------------------------------------------------
    | Update Module Checkbox
    |--------------------------------------------------------------------------
    */

    function updateModuleCheckbox(module) {

        const moduleCheckbox =
            document.querySelector(
                '.module-checkbox[data-module="' + module + '"]'
            );

        const modulePermissions =
            document.querySelectorAll(
                '.permission-' + module
            );

        const checkedPermissions =
            document.querySelectorAll(
                '.permission-' + module + ':checked'
            );


        if (!moduleCheckbox) {
            return;
        }


        moduleCheckbox.checked =
            modulePermissions.length > 0 &&
            modulePermissions.length ===
            checkedPermissions.length;
    }


    /*
    |--------------------------------------------------------------------------
    | Select All
    |--------------------------------------------------------------------------
    */

    if (selectAllButton) {

        selectAllButton.addEventListener('click', function () {

            document
                .querySelectorAll('.permission-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = true;

                });


            document
                .querySelectorAll('.module-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = true;

                });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Clear All
    |--------------------------------------------------------------------------
    */

    if (deselectAllButton) {

        deselectAllButton.addEventListener('click', function () {

            document
                .querySelectorAll('.permission-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = false;

                });


            document
                .querySelectorAll('.module-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = false;

                });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Module Select
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.module-checkbox')
        .forEach(function (moduleCheckbox) {

            moduleCheckbox.addEventListener(
                'change',
                function () {

                    const module =
                        this.dataset.module;


                    document
                        .querySelectorAll(
                            '.permission-' + module
                        )
                        .forEach(function (checkbox) {

                            checkbox.checked =
                                moduleCheckbox.checked;

                        });

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Individual Permission Change
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.permission-checkbox')
        .forEach(function (permissionCheckbox) {

            permissionCheckbox.addEventListener(
                'change',
                function () {

                    const classes =
                        this.className.split(' ');

                    let moduleClass = null;


                    classes.forEach(function (className) {

                        if (
                            className.startsWith('permission-') &&
                            className !== 'permission-checkbox'
                        ) {

                            moduleClass = className;

                        }

                    });


                    if (!moduleClass) {
                        return;
                    }


                    const module =
                        moduleClass.replace(
                            'permission-',
                            ''
                        );


                    updateModuleCheckbox(module);

                }
            );

        });

});

</script>

@endpush
