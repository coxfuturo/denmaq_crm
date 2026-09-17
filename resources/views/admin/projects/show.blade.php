@extends('admin.layout.app')
@section('content')
@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('Projects Edit'));
$priorityClass = match($project->priority) {
    'low' => 'bg-secondary',
    'medium' => 'bg-primary',
    'high' => 'bg-warning text-dark',
    'urgent' => 'bg-danger',
    default => 'bg-secondary'
};
$statusClass = match($project->status) {
    'planning' => 'bg-secondary',
    'in_progress' => 'bg-primary',
    'on_hold' => 'bg-warning text-dark',
    'completed' => 'bg-success',
    'cancelled' => 'bg-danger',
    default => 'bg-secondary'
};
@endphp

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Project Details</h4>
            <p class="text-muted mb-0">View project information</p>
        </div>
        <div class="d-flex align-items-center gap-1">
            @if($canEdit)
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-warning px-2">
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>
            @endif
            <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-secondary px-2">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                <div>
                    <div class="small text-muted mb-1">Project Code</div>
                    <div class="fw-bold text-primary fs-5">
                        {{ $project->project_code }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="badge {{ $priorityClass }} px-2 py-1">
                        {{ ucfirst($project->priority) }}
                    </span>
                    <span class="badge {{ $statusClass }} px-2 py-1">
                        {{ ucwords(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Project Name</div>
                        <div class="fw-semibold">
                            {{ $project->name }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Client</div>
                        <div class="fw-semibold">
                            {{ $project->client?->company_name ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Assigned To</div>
                        <div class="fw-semibold">
                            @if($project->assignedUser)
                            {{ $project->assignedUser->first_name }} {{ $project->assignedUser->last_name }}
                            @else
                            <span class="text-muted">Unassigned</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Budget</div>
                        <div class="fw-semibold">
                            @if($project->budget !== null)
                            ₹{{ number_format((float) $project->budget, 2) }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Start Date</div>
                        <div class="fw-semibold">
                            {{ $project->start_date?->format('d-m-Y') ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">End Date</div>
                        <div class="fw-semibold">
                            {{ $project->end_date?->format('d-m-Y') ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Priority</div>
                        <div>
                            <span class="badge {{ $priorityClass }} px-2 py-1">
                                {{ ucfirst($project->priority) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Status</div>
                        <div>
                            <span class="badge {{ $statusClass }} px-2 py-1">
                                {{ ucwords(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <div class="small text-muted mb-2">Description</div>
                        <div class="text-body">
                            {!! nl2br(e($project->description ?? '-')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
