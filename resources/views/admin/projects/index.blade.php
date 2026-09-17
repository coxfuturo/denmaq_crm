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
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Projects</h4>
            <p class="text-muted mb-0">Manage all projects</p>
        </div>
        <div class="d-flex align-items-center gap-1">
            @if($canDelete)
            <a href="{{ route('admin.projects.trash') }}" class="btn btn-sm btn-danger px-2">
                <i class="bi bi-trash me-1"></i>
                Trash
            </a>
            @endif
            @if($canAdd)
            <a href="{{ route('admin.projects.create') }}" class="btn btn-sm btn-primary px-2">
                <i class="bi bi-plus-lg me-1"></i>
                Add Project
            </a>
            @endif
        </div>
    </div>
    @if($errors->any())
    <div class="alert alert-danger py-2">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-2 mb-3 align-items-end">
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <label class="form-label mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search project..." value="{{ request('search') }}">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All Priority</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <label class="form-label mb-1">Per Page</label>
                    <select name="per_page" class="form-select form-select-sm">
                        @foreach([10,15,25,50,100,200,500] as $number)
                        <option value="{{ $number }}" {{ (int) $perPage === $number ? 'selected' : '' }}>{{ $number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary px-2">
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-secondary px-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:1%;white-space:nowrap;">S.N</th>
                            <th>Project Code</th>
                            <th>Project Name</th>
                            <th>Client</th>
                            <th>Assigned To</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Budget</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th style="width:90px;white-space:nowrap;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td style="width:1%;white-space:nowrap;">
                                {{ $projects->firstItem() + $loop->index }}
                            </td>
                            <td class="text-nowrap">
                                {{ $project->project_code }}
                            </td>
                            <td>
                                <strong>{{ $project->name }}</strong>
                            </td>
                            <td>
                                {{ $project->client?->company_name ?? '-' }}
                            </td>
                            <td>
                                @if($project->assignedUser)
                                {{ $project->assignedUser->first_name }} {{ $project->assignedUser->last_name }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                {{ $project->start_date?->format('d-m-Y') ?? '-' }}
                            </td>
                            <td class="text-nowrap">
                                {{ $project->end_date?->format('d-m-Y') ?? '-' }}
                            </td>
                            <td class="text-nowrap">
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
                                <span class="badge {{ $priorityClass }} px-2">
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
                                <span class="badge {{ $statusClass }} px-2">
                                    {{ ucwords(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($canView || $canEdit || $canDelete)
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-light border dropdown-toggle px-2 py-1" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($canView)
                                        <li>
                                            <a class="dropdown-item py-1" href="{{ route('admin.projects.show', $project) }}">
                                                <i class="bi bi-eye me-1"></i>
                                                View
                                            </a>
                                        </li>
                                        @endif
                                        @if($canEdit)
                                        <li>
                                            <a class="dropdown-item py-1" href="{{ route('admin.projects.edit', $project) }}">
                                                <i class="bi bi-pencil me-1"></i>
                                                Edit
                                            </a>
                                        </li>
                                        @endif
                                        @if($canDelete)
                                        <li>
                                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" onsubmit="return deleteConfirm(this, 'This project will be moved to trash.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-1 text-danger">
                                                    <i class="bi bi-trash me-1"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                No projects found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($projects->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $projects->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
