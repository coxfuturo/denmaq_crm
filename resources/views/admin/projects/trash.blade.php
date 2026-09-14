@extends('admin.layout.app')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canRestore = $isSuperAdmin || $user->can('projects.update');
$canForceDelete = $isSuperAdmin || $user->can('projects.delete');
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>Project Trash</h4>

        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            Back to Projects
        </a>

    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">

        <div class="card-body">

            <form
            method="GET"
            action="{{ route('admin.projects.trash') }}"
            class="mb-3"
            >
            <div class="input-group">

                <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search deleted projects..."
                value="{{ request('search') }}"
                >

                <button type="submit" class="btn btn-primary">
                    Search
                </button>

            </div>
        </form>

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Deleted At</th>
                        <th width="220">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($projects as $project)

                    <tr>

                        <td>
                            {{ $projects->firstItem() + $loop->index }}
                        </td>

                        <td>
                            {{ $project->project_code }}
                        </td>

                        <td>
                            <strong>{{ $project->name }}</strong>
                        </td>

                        <td>
                            {{ $project->client?->company_name ?? '-' }}
                        </td>

                        <td>
                            {{ $project->deleted_at?->format('d-m-Y H:i') ?? '-' }}
                        </td>

                        <td>

                            @if($canRestore)
                            <form
                            action="{{ route('admin.projects.restore', $project->id) }}"
                            method="POST"
                            class="d-inline"
                            >
                            @csrf

                            <button
                            type="submit"
                            class="btn btn-sm btn-success"
                            onclick="return confirm('Are you sure you want to restore this project?');"
                            >
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Restore
                        </button>

                    </form>
                    @endif

                    @if($canForceDelete)
                    <form
                    action="{{ route('admin.projects.forceDelete', $project->id) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Permanent delete? This cannot be undone.');"
                    >
                    @csrf
                    @method('DELETE')

                    <button
                    type="submit"
                    class="btn btn-sm btn-danger"
                    >
                    <i class="bi bi-trash"></i>
                    Delete Forever
                </button>

            </form>
            @endif

            @if(!$canRestore && !$canForceDelete)
            <span class="text-muted">
                No Action
            </span>
            @endif

        </td>

    </tr>

    @empty

    <tr>
        <td colspan="6" class="text-center py-4">
            Trash is empty.
        </td>
    </tr>

    @endforelse

</tbody>

</table>

</div>

<div class="mt-3">
    {{ $projects->links() }}
</div>

</div>

</div>

</div>

@endsection
