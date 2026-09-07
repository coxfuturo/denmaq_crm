@extends('admin.layout.app')

@section('title', 'Roles')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">Roles</h2>
            <p class="text-muted mb-0">
                Manage system roles and permissions
            </p>
        </div>

        <a href="{{ route('admin.roles.create') }}"
           class="btn btn-primary">
            + Add Role
        </a>

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($roles as $role)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $role->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $role->users_count }}
                                </td>

                                <td>
                                    {{ $role->permissions->count() }}
                                </td>

                                <td>

                                    <a
                                        href="{{ route('admin.roles.edit', $role->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Edit
                                    </a>


                                    @if($role->name !== 'Super Admin')

                                        <form
                                            action="{{ route('admin.roles.destroy', $role->id) }}"
                                            method="POST"
                                            class="d-inline"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure?')"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No roles found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
