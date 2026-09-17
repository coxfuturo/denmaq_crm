@extends('admin.layout.app')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('Leads Edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('Leads Delete'));

$statusClass = match($lead->status) {
    'New' => 'bg-primary',
    'Contacted' => 'bg-info text-dark',
    'Follow Up' => 'bg-warning text-dark',
    'Qualified' => 'bg-success',
    'Proposal' => 'bg-secondary',
    'Won' => 'bg-success',
    'Lost' => 'bg-danger',
    default => 'bg-dark'
};
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Lead Details</h4>
            <p class="text-muted mb-0">{{ $lead->name }}</p>
        </div>

        <div class="d-flex align-items-center gap-1">
            @if($canEdit)
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn btn-sm btn-warning px-2">
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>
            @endif

            <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-secondary px-2">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success py-2 px-3 mb-3">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger py-2 px-3 mb-3">
        {{ session('error') }}
    </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                <div>
                    <div class="small text-muted mb-1">Lead</div>
                    <div class="fw-bold fs-5">{{ $lead->name }}</div>

                    @if($lead->company_name)
                    <div class="small text-muted mt-1">
                        <i class="bi bi-building me-1"></i>
                        {{ $lead->company_name }}
                    </div>
                    @endif
                </div>

                <div>
                    <span class="badge {{ $statusClass }} px-2 py-1">
                        {{ $lead->status }}
                    </span>
                </div>
            </div>

            <div class="row g-2">

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Email</div>
                        @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-envelope me-1"></i>
                            {{ $lead->email }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Phone</div>
                        @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $lead->phone }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Alternate Phone</div>
                        @if($lead->alternate_phone)
                        <a href="tel:{{ $lead->alternate_phone }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $lead->alternate_phone }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Service</div>
                        <div class="fw-semibold">
                            {{ $lead->service ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Lead Source</div>
                        <div class="fw-semibold">
                            {{ $lead->source ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Assigned To</div>
                        <div class="fw-semibold">
                            @if($lead->assignedUser)
                            {{ trim($lead->assignedUser->first_name . ' ' . $lead->assignedUser->last_name) }}
                            @else
                            <span class="text-muted">Not Assigned</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Follow Up Date</div>
                        <div class="fw-semibold">
                            @if($lead->follow_up_date)
                            {{ $lead->follow_up_date->format('d M Y') }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Budget</div>
                        <div class="fw-semibold">
                            @if($lead->budget !== null)
                            ₹{{ number_format((float) $lead->budget, 2) }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="row g-3">

        <div class="col-lg-8">

            <div class="card h-100">
                <div class="card-header py-2">
                    <h5 class="header-title mb-0">
                        <i class="bi bi-journal-text me-1"></i>
                        Notes
                    </h5>
                </div>

                <div class="card-body">
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($lead->notes ?: '-')) !!}
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="card mb-3">
                <div class="card-header py-2">
                    <h5 class="header-title mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Other Information
                    </h5>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-muted">Lead ID</span>
                        <span class="fw-semibold">#{{ $lead->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2 gap-2">
                        <span class="small text-muted">Created By</span>
                        <span class="fw-semibold text-end">
                            @if($lead->creator)
                            {{ trim($lead->creator->first_name . ' ' . $lead->creator->last_name) }}
                            @else
                            -
                            @endif
                        </span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2 gap-2">
                        <span class="small text-muted">Created At</span>
                        <span class="fw-semibold text-end">
                            {{ $lead->created_at?->format('d M Y, h:i A') }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between gap-2">
                        <span class="small text-muted">Updated At</span>
                        <span class="fw-semibold text-end">
                            {{ $lead->updated_at?->format('d M Y, h:i A') }}
                        </span>
                    </div>

                </div>
            </div>

            @if($canEdit || $canDelete)
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="header-title mb-0">
                        <i class="bi bi-lightning-charge me-1"></i>
                        Actions
                    </h5>
                </div>

                <div class="card-body">

                    @if($canEdit)
                    <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn btn-sm btn-warning w-100 mb-2">
                        <i class="bi bi-pencil me-1"></i>
                        Edit Lead
                    </a>
                    @endif

                    @if($canDelete)
                    <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This lead will be deleted.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            <i class="bi bi-trash me-1"></i>
                            Delete Lead
                        </button>
                    </form>
                    @endif

                </div>
            </div>
            @endif

        </div>

    </div>

</div>

@endsection
