@extends('admin.layout.app')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');

$canView = $isSuperAdmin || ($user && $user->can('Projects View'));
$canAdd = $isSuperAdmin || ($user && $user->can('Projects Create'));
$canEdit = $isSuperAdmin || ($user && $user->can('Projects Edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('Projects Delete'));
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">Projects</h4>
            <p class="text-muted mb-0">Manage all projects</p>
        </div>

        <div class="d-flex gap-2">

            @if($canDelete)
            <a href="{{ route('admin.projects.trash') }}" class="btn btn-danger">
                <i class="bi bi-trash"></i>
                Trash
            </a>
            @endif

            @if($canAdd)
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Add Project
            </a>
            @endif

        </div>

    </div>

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
            action="{{ route('admin.projects.index') }}"
            class="row g-2 mb-4"
            >

            <div class="col-md-4">

                <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search project..."
                value="{{ request('search') }}"
                >

            </div>

            <div class="col-md-2">

                <select name="status" class="form-select">

                    <option value="">All Status</option>

                    <option
                    value="planning"
                    {{ request('status') == 'planning' ? 'selected' : '' }}
                    >
                    Planning
                </option>

                <option
                value="in_progress"
                {{ request('status') == 'in_progress' ? 'selected' : '' }}
                >
                In Progress
            </option>

            <option
            value="on_hold"
            {{ request('status') == 'on_hold' ? 'selected' : '' }}
            >
            On Hold
        </option>

        <option
        value="completed"
        {{ request('status') == 'completed' ? 'selected' : '' }}
        >
        Completed
    </option>

    <option
    value="cancelled"
    {{ request('status') == 'cancelled' ? 'selected' : '' }}
    >
    Cancelled
</option>

</select>

</div>

<div class="col-md-2">

    <select name="priority" class="form-select">

        <option value="">All Priority</option>

        <option
        value="low"
        {{ request('priority') == 'low' ? 'selected' : '' }}
        >
        Low
    </option>

    <option
    value="medium"
    {{ request('priority') == 'medium' ? 'selected' : '' }}
    >
    Medium
</option>

<option
value="high"
{{ request('priority') == 'high' ? 'selected' : '' }}
>
High
</option>

<option
value="urgent"
{{ request('priority') == 'urgent' ? 'selected' : '' }}
>
Urgent
</option>

</select>

</div>

<div class="col-md-2">

    <select name="per_page" class="form-select">

        @foreach([10, 15, 25, 50, 100, 200, 500] as $number)

        <option
        value="{{ $number }}"
        {{ (int) $perPage === $number ? 'selected' : '' }}
        >
        {{ $number }}
    </option>

    @endforeach

</select>

</div>

<div class="col-md-2">

    <button
    type="submit"
    class="btn btn-primary"
    >
    Search
</button>

<a
href="{{ route('admin.projects.index') }}"
class="btn btn-secondary"
>
Reset
</a>

</div>

</form>

<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle">

        <thead>

            <tr>

                <th>#</th>
                <th>Project Code</th>
                <th>Project Name</th>
                <th>Client</th>
                <th>Assigned To</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Budget</th>
                <th>Priority</th>
                <th>Status</th>
                <th width="180">Action</th>

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
                    <strong>
                        {{ $project->name }}
                    </strong>
                </td>

                <td>
                    {{ $project->client?->company_name ?? '-' }}
                </td>

                <td>

                    @if($project->assignedUser)

                    {{ $project->assignedUser->first_name }}
                    {{ $project->assignedUser->last_name }}

                    @else

                    -

                    @endif

                </td>

                <td>
                    {{ $project->start_date?->format('d-m-Y') ?? '-' }}
                </td>

                <td>
                    {{ $project->end_date?->format('d-m-Y') ?? '-' }}
                </td>

                <td>

                    @if($project->budget !== null)

                    ₹{{ number_format((float) $project->budget, 2) }}

                    @else

                    -

                    @endif

                </td>

                <td>

                    @php
                    $priorityClass = match($project->priority) {
                        'low' => 'bg-secondary',
                        'medium' => 'bg-primary',
                        'high' => 'bg-warning text-dark',
                        'urgent' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    @endphp

                    <span class="badge {{ $priorityClass }}">
                        {{ ucfirst($project->priority) }}
                    </span>

                </td>

                <td>

                    @php
                    $statusClass = match($project->status) {
                        'planning' => 'bg-secondary',
                        'in_progress' => 'bg-primary',
                        'on_hold' => 'bg-warning text-dark',
                        'completed' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    @endphp

                    <span class="badge {{ $statusClass }}">
                        {{ ucwords(str_replace('_', ' ', $project->status)) }}
                    </span>

                </td>

                <td>

                    @if($canView)

                    <a
                    href="{{ route('admin.projects.show', $project) }}"
                    class="btn btn-sm btn-info"
                    title="View"
                    >
                    <i class="bi bi-eye"></i>
                </a>

                @endif

                @if($canEdit)

                <a
                href="{{ route('admin.projects.edit', $project) }}"
                class="btn btn-sm btn-warning"
                title="Edit"
                >
                <i class="bi bi-pencil"></i>
            </a>

            @endif

            @if($canDelete)

            <form
            action="{{ route('admin.projects.destroy', $project) }}"
            method="POST"
            class="d-inline"
            onsubmit="return confirm('Are you sure you want to move this project to trash?');"
            >

            @csrf
            @method('DELETE')

            <button
            type="submit"
            class="btn btn-sm btn-danger"
            title="Delete"
            >
            <i class="bi bi-trash"></i>
        </button>

    </form>

    @endif

    @if(!$canView && !$canEdit && !$canDelete)

    <span class="text-muted">
        No Action
    </span>

    @endif

</td>

</tr>

@empty

<tr>

    <td
    colspan="11"
    class="text-center py-4"
    >
    No projects found.
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
