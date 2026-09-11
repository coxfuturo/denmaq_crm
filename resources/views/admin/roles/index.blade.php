@extends('admin.layout.app')

@section('title', 'Roles List')

@section('content')

@php
$user = auth()->user();

$isSuperAdmin = $user && $user->hasRole('Super Admin');

$can = function ($permission) use ($user, $isSuperAdmin) {
    return $isSuperAdmin || ($user && $user->can($permission));
};

@endphp

<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-2 mt-2">
        <h5 class="mb-0">Roles List</h5>
        <small class="text-muted">Manage roles and permissions</small>
    </div>

    @if($can('Roles Create'))

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <span class="me-1">+</span>
            Add Role
        </a>

    @endif
</div>

<div class="card shadow-sm">
    <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            Roles
            <span class="text-muted">({{ $roles->count() }})</span>
        </h6>

        @if(request('view') == 'trash')

            @if($can('Roles View'))

                <a href="{{ route('admin.roles.index') }}" class="btn btn-success btn-sm">
                    <span class="me-1">←</span>
                    Active
                </a>

            @endif

        @else

            @if($can('Roles Delete'))

                <a href="{{ route('admin.roles.index', ['view' => 'trash']) }}" class="btn btn-secondary btn-sm">
                    <span class="me-1">🗑</span>
                    Trash
                </a>

            @endif

        @endif

    </div>

    <div class="card-body p-2">

        <div class="table-responsive">

            <table class="table table-bordered table-hover table-sm align-middle mb-0 compact-table">

                <thead class="table-light">

                    <tr>

                        <th width="20">S.n</th>

                        <th>Role</th>

                        <th>Description</th>

                        <th width="100" class="text-center">
                            Position
                        </th>

                        <th width="85" class="text-center">
                            Status
                        </th>

                        <th width="110" class="text-center">
                            Permissions
                        </th>

                        <th width="120">
                            Created By
                        </th>

                        <th width="120">
                            Created At
                        </th>

                        <th width="100" class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($roles as $role)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>

                                <div class="d-flex align-items-center">

                                    <div>

                                        <strong class="role-name">
                                            {{ $role->name }}
                                        </strong>

                                        @if($role->name_alias)

                                            <div>
                                                <small class="text-muted">
                                                    {{ $role->name_alias }}
                                                </small>
                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>

                            <td>

                                <span
                                    class="description-text"
                                    title="{{ $role->description }}"
                                >
                                    {{ $role->description ?: 'Not Available' }}
                                </span>

                            </td>

                            <td class="text-center">

                                @if($role->deleted_at)

                                    <span class="text-muted">
                                        -
                                    </span>

                                @elseif($can('Roles Edit') && $role->name !== 'Super Admin')

                                    <div class="position-box">

                                        <button
                                            type="button"
                                            class="position-btn"
                                            onclick="changePosition({{ $role->id }}, 'down')"
                                        >
                                            −
                                        </button>

                                        <input
                                            type="text"
                                            id="position{{ $role->id }}"
                                            value="{{ $role->position ?? 0 }}"
                                            readonly
                                        >

                                        <button
                                            type="button"
                                            class="position-btn"
                                            onclick="changePosition({{ $role->id }}, 'up')"
                                        >
                                            +
                                        </button>

                                    </div>

                                @else

                                    <span>
                                        {{ $role->position ?? 0 }}
                                    </span>

                                @endif

                            </td>

                            <td class="text-center">

                                @if($role->deleted_at)

                                    <span class="badge bg-secondary">
                                        Deleted
                                    </span>

                                @elseif($can('Roles Edit') && $role->name !== 'Super Admin')

                                    <label class="status-toggle">

                                        <input
                                            type="checkbox"
                                            {{ $role->status == 1 ? 'checked' : '' }}
                                            onchange="toggleStatus(this, '{{ route('admin.roles.status', $role->id) }}')"
                                        >

                                        <span class="status-slider"></span>

                                    </label>

                                @else

                                    @if($role->status == 1)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    @endif

                                @endif

                            </td>

                            <td class="text-center">

                                @if($can('Roles View'))

                                    <button
                                        type="button"
                                        onclick="openTableJs({{ $role->id }})"
                                        class="permission-btn"
                                    >
                                        🔑

                                        <span>
                                            {{ $role->permissions->count() }}
                                        </span>

                                    </button>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $role->createdBy?->name ?? 'System' }}
                            </td>

                            <td>

                                @if($role->created_at)

                                    {{ $role->created_at->format('d-m-Y H:i') }}

                                @else

                                    -

                                @endif

                            </td>

                            <td class="text-center">

                                <div class="action-buttons">

                                    @if($role->deleted_at)

                                        @if($can('Roles Delete'))

                                            <a
                                                href="#"
                                                class="action-icon restore-icon"
                                                title="Restore"
                                                onclick="return confirmAlert(
                                                    'Are you sure you want to restore this role?',
                                                    function() {
                                                        window.location.href = '{{ route('admin.roles.restore', $role->id) }}';
                                                    }
                                                )"
                                            >
                                                ↻
                                            </a>

                                        @else

                                            <span class="text-muted">
                                                -
                                            </span>

                                        @endif

                                    @else

                                        @if($can('Roles Edit'))

                                            <a
                                                href="{{ route('admin.roles.edit', $role->id) }}"
                                                class="action-icon edit-icon"
                                                title="Edit"
                                            >
                                                ✎
                                            </a>

                                        @endif

                                        @if($can('Roles Delete') && $role->name !== 'Super Admin')

                                            <form
                                                action="{{ route('admin.roles.destroy', $role->id) }}"
                                                method="POST"
                                                class="delete-form"
                                                onsubmit="return confirmForm(this, 'This role will be moved to trash.')"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="action-icon delete-icon"
                                                    title="Delete"
                                                >
                                                    🗑
                                                </button>

                                            </form>

                                        @elseif($role->name === 'Super Admin')

                                            <span class="badge bg-secondary">
                                                Protected
                                            </span>

                                        @endif

                                    @endif

                                </div>

                            </td>

                        </tr>

                        @if($can('Roles View'))

                            <tr
                                id="openTable{{ $role->id }}"
                                class="d-none permission-row"
                            >

                                <td colspan="9">

                                    <div class="permission-wrapper">

                                        <div class="permission-title">

                                            🔑 Permissions of

                                            <strong>
                                                {{ $role->name }}
                                            </strong>

                                        </div>

                                        <table class="table table-bordered table-sm mb-0">

                                            <thead class="table-light">

                                                <tr>

                                                    <th width="40">
                                                        #
                                                    </th>

                                                    <th>
                                                        Permission
                                                    </th>

                                                    <th>
                                                        Description
                                                    </th>

                                                    <th width="80" class="text-center">
                                                        Status
                                                    </th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                @forelse($role->permissions as $permission)

                                                    <tr>

                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>

                                                        <td>
                                                            🔒
                                                            {{ $permission->name }}
                                                        </td>

                                                        <td>
                                                            {{ $permission->description ?: 'Not Available' }}
                                                        </td>

                                                        <td class="text-center">

                                                            @if($permission->status ?? true)

                                                                <span class="badge bg-success">
                                                                    Active
                                                                </span>

                                                            @else

                                                                <span class="badge bg-danger">
                                                                    Inactive
                                                                </span>

                                                            @endif

                                                        </td>

                                                    </tr>

                                                @empty

                                                    <tr>

                                                        <td
                                                            colspan="4"
                                                            class="text-center text-muted"
                                                        >
                                                            No permissions assigned.
                                                        </td>

                                                    </tr>

                                                @endforelse

                                            </tbody>

                                        </table>

                                    </div>

                                </td>

                            </tr>

                        @endif

                    @empty

                        <tr>

                            <td colspan="9" class="text-center py-4">

                                <div class="text-muted mb-2">
                                    No Roles Found
                                </div>

                                @if(request('view') != 'trash' && $can('Roles Create'))

                                    <a
                                        href="{{ route('admin.roles.create') }}"
                                        class="btn btn-primary btn-sm"
                                    >
                                        + Add Role
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

</div>

</div>

<style>

    .compact-table {
        font-size: 13px;
    }

    .compact-table th {
        font-size: 12px;
        font-weight: 600;
        padding: 7px 8px !important;
        white-space: nowrap;
    }

    .compact-table td {
        padding: 6px 8px !important;
        vertical-align: middle;
    }

    .role-icon {
        width: 28px;
        height: 28px;
        min-width: 28px;
        border-radius: 50%;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .role-name {
        font-size: 13px;
    }

    .description-text {
        display: block;
        max-width: 180px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .position-box {
        display: inline-flex;
        align-items: center;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
        height: 28px;
    }

    .position-box input {
        width: 35px;
        height: 26px;
        border: 0;
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        text-align: center;
        font-size: 12px;
        padding: 0;
    }

    .position-btn {
        width: 25px;
        height: 27px;
        border: 0;
        background: #f8f9fa;
        cursor: pointer;
        font-size: 15px;
        line-height: 25px;
    }

    .position-btn:hover {
        background: #e9ecef;
    }

    .status-toggle {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 22px;
        cursor: pointer;
        margin: 0;
    }

    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .status-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #dc3545;
        border-radius: 22px;
        transition: 0.3s;
    }

    .status-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        top: 3px;
        background-color: #fff;
        border-radius: 50%;
        transition: 0.3s;
    }

    .status-toggle input:checked + .status-slider {
        background-color: #198754;
    }

    .status-toggle input:checked + .status-slider:before {
        transform: translateX(20px);
    }

    .status-toggle input:disabled + .status-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .permission-btn {
        border: 1px solid #6c757d;
        background: #6c757d;
        color: #fff;
        border-radius: 4px;
        font-size: 11px;
        padding: 4px 7px;
        cursor: pointer;
    }

    .permission-btn:hover {
        background: #5c636a;
    }

    .action-buttons {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .action-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        text-decoration: none;
        border: 1px solid;
        background: #fff;
        font-size: 17px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .edit-icon {
        color: #0d6efd;
        border-color: #0d6efd;
    }

    .edit-icon:hover {
        background: #0d6efd;
        color: #fff;
    }

    .delete-icon {
        color: #dc3545;
        border-color: #dc3545;
    }

    .delete-icon:hover {
        background: #dc3545;
        color: #fff;
    }

    .restore-icon {
        color: #198754;
        border-color: #198754;
    }

    .restore-icon:hover {
        background: #198754;
        color: #fff;
    }

    .delete-form {
        display: inline-block;
        margin: 0;
    }

    .permission-row td {
        background: #f8f9fa;
    }

    .permission-wrapper {
        padding: 10px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .permission-title {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .permission-wrapper table {
        font-size: 12px;
    }

    .permission-wrapper th,
    .permission-wrapper td {
        padding: 5px 7px !important;
    }

    .badge {
        font-size: 10px;
        font-weight: 500;
    }

</style>

<script>

    function openTableJs(id) {

        const currentTable = document.getElementById('openTable' + id);

        if (!currentTable) {
            return;
        }

        document.querySelectorAll('.permission-row').forEach(function(row) {

            if (row !== currentTable) {
                row.classList.add('d-none');
            }

        });

        currentTable.classList.toggle('d-none');
    }

</script>

@endsection
