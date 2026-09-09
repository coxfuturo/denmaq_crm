@extends('admin.layout.app')

@section('title', 'Create Role')

@section('content')

<div class="container-fluid role-page">

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Create Role</h2>
        <p class="page-description">Create a new role and assign permissions</p>
    </div>

    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
        <i data-feather="arrow-left"></i>
        <span class="ms-1">Back</span>
    </a>
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

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf

    <div class="card mb-4">
        <div class="card-header">
            <h5>Role Information</h5>
            <small>Enter basic information for this role.</small>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        Role Name <span class="text-danger">*</span>
                    </label>

                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Enter role name"
                        required>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name_alias" class="form-label">Role Alias</label>

                    <input type="text" name="name_alias" id="name_alias"
                        class="form-control @error('name_alias') is-invalid @enderror"
                        value="{{ old('name_alias') }}"
                        placeholder="Enter role alias">

                    @error('name_alias')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="icon" class="form-label">Icon</label>

                    <input type="text" name="icon" id="icon"
                        class="form-control @error('icon') is-invalid @enderror"
                        value="{{ old('icon') }}"
                        placeholder="Example: users">

                    <div class="form-text">Use Feather icon name such as users, shield or user.</div>

                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="position" class="form-label">Position</label>

                    <input type="number" name="position" id="position"
                        class="form-control @error('position') is-invalid @enderror"
                        value="{{ old('position', 0) }}"
                        min="0">

                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label d-block">Status</label>

                    <div class="form-check form-switch mt-2">
                        <input type="checkbox"
                            name="status"
                            value="1"
                            class="form-check-input"
                            id="status"
                            {{ old('status', 1) ? 'checked' : '' }}>

                        <label class="form-check-label" for="status">Active</label>
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
                    <small>Select permissions for this role.</small>
                </div>

                <div class="permission-actions d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                        <i data-feather="check-square"></i>
                        <span class="ms-1">Select All</span>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                        <i data-feather="square"></i>
                        <span class="ms-1">Clear All</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            @forelse($permissions as $module => $modulePermissions)

                @php
                    $moduleSlug = Str::slug($module);
                @endphp

                <div class="permission-module">

                    <div class="module-header">
                        <h6 class="module-title">
                            <i data-feather="folder"></i>
                            <span>{{ $module }}</span>
                        </h6>

                        <div class="module-select">
                            <input type="checkbox"
                                class="form-check-input module-checkbox"
                                data-module="{{ $moduleSlug }}"
                                id="module_{{ $moduleSlug }}">

                            <label for="module_{{ $moduleSlug }}">
                                Select Module
                            </label>
                        </div>
                    </div>

                    <div class="row g-2">
                        @foreach($modulePermissions as $permission)

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="permission-item">
                                    <div class="form-check">
                                        <input type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            class="form-check-input permission-checkbox permission-{{ $moduleSlug }}"
                                            id="permission_{{ $permission->id }}"
                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>

                                        <label class="form-check-label"
                                            for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
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
                    <h5>No Permissions Available</h5>
                    <p>Please create permissions before creating a role.</p>
                </div>

            @endforelse
        </div>
    </div>

    <div class="form-footer">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
            <i data-feather="x"></i>
            <span class="ms-1">Cancel</span>
        </a>

        <button type="submit" class="btn btn-primary">
            <i data-feather="save"></i>
            <span class="ms-1">Create Role</span>
        </button>
    </div>

</form>

</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const deselectAll = document.getElementById('deselectAll');
    const modules = document.querySelectorAll('.module-checkbox');
    const permissions = document.querySelectorAll('.permission-checkbox');

    function updateModule(module) {
        const items = document.querySelectorAll('.permission-' + module);
        const checked = document.querySelectorAll('.permission-' + module + ':checked');
        const checkbox = document.querySelector('.module-checkbox[data-module="' + module + '"]');

        if (checkbox) {
            checkbox.checked = items.length > 0 && items.length === checked.length;
        }
    }

    selectAll?.addEventListener('click', function() {
        permissions.forEach(item => item.checked = true);
        modules.forEach(item => item.checked = true);
    });

    deselectAll?.addEventListener('click', function() {
        permissions.forEach(item => item.checked = false);
        modules.forEach(item => item.checked = false);
    });

    modules.forEach(module => {
        module.addEventListener('change', function() {
            document.querySelectorAll('.permission-' + this.dataset.module)
                .forEach(item => item.checked = this.checked);
        });

        updateModule(module.dataset.module);
    });

    permissions.forEach(permission => {
        permission.addEventListener('change', function() {
            const module = [...this.classList]
                .find(item => item.startsWith('permission-') && item !== 'permission-checkbox')
                ?.replace('permission-', '');

            if (module) {
                updateModule(module);
            }
        });
    });

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
