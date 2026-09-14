@extends('admin.layout.app')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || $user->can('projects.update');
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>Project Details</h4>

        <div class="d-flex gap-2">

            @if($canEdit)
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i>
                Edit
            </a>
            @endif

            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                Back
            </a>

        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <strong>Project Code</strong>
                    <div>
                        {{ $project->project_code }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Project Name</strong>
                    <div>
                        {{ $project->name }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Client</strong>
                    <div>
                        {{ $project->client?->company_name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Assigned To</strong>
                    <div>

                        @if($project->assignedUser)
                        {{ $project->assignedUser->first_name }}
                        {{ $project->assignedUser->last_name }}
                        @else
                        -
                        @endif

                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Start Date</strong>
                    <div>
                        {{ $project->start_date?->format('d-m-Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>End Date</strong>
                    <div>
                        {{ $project->end_date?->format('d-m-Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Budget</strong>
                    <div>

                        @if($project->budget !== null)
                        ₹{{ number_format((float) $project->budget, 2) }}
                        @else
                        -
                        @endif

                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Priority</strong>
                    <div>
                        {{ ucfirst($project->priority) }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Status</strong>
                    <div>
                        {{ ucwords(str_replace('_', ' ', $project->status)) }}
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>Description</strong>

                    <div class="mt-2">
                        {!! nl2br(e($project->description ?? '-')) !!}
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
