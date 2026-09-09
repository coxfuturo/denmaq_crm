@extends('admin.layout.app')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Lead Details</h4>
            <p class="text-muted mb-0">{{ $lead->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i>
                Edit
            </a>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title mb-0">Lead Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Name</div>
                            <div class="fw-semibold">{{ $lead->name }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Company Name</div>
                            <div>{{ $lead->company_name ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Email</div>
                            <div>
                                @if($lead->email)
                                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Phone</div>
                            <div>
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Alternate Phone</div>
                            <div>
                                @if($lead->alternate_phone)
                                    <a href="tel:{{ $lead->alternate_phone }}">{{ $lead->alternate_phone }}</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Service</div>
                            <div>{{ $lead->service ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Source</div>
                            <div>{{ $lead->source ?: '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Status</div>
                            <div>
                                @if($lead->status === 'New')
                                    <span class="badge bg-primary">New</span>
                                @elseif($lead->status === 'Contacted')
                                    <span class="badge bg-info text-dark">Contacted</span>
                                @elseif($lead->status === 'Follow Up')
                                    <span class="badge bg-warning text-dark">Follow Up</span>
                                @elseif($lead->status === 'Qualified')
                                    <span class="badge bg-success">Qualified</span>
                                @elseif($lead->status === 'Proposal')
                                    <span class="badge bg-secondary">Proposal</span>
                                @elseif($lead->status === 'Won')
                                    <span class="badge bg-success">Won</span>
                                @elseif($lead->status === 'Lost')
                                    <span class="badge bg-danger">Lost</span>
                                @else
                                    <span class="badge bg-dark">{{ $lead->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Follow Up Date</div>
                            <div>
                                @if($lead->follow_up_date)
                                    {{ $lead->follow_up_date->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Budget</div>
                            <div>
                                @if($lead->budget !== null)
                                    ₹{{ number_format((float) $lead->budget, 2) }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="text-muted small mb-1">Assigned To</div>
                            <div>
                                @if($lead->assignedUser)
                                    {{ trim($lead->assignedUser->first_name . ' ' . $lead->assignedUser->last_name) }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-1">Notes</div>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($lead->notes ?: '-')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title mb-0">Other Information</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Lead ID</div>
                        <div class="fw-semibold">#{{ $lead->id }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Created By</div>
                        <div>
                            @if($lead->creator)
                                {{ trim($lead->creator->first_name . ' ' . $lead->creator->last_name) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Created At</div>
                        <div>{{ $lead->created_at?->format('d M Y, h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Updated At</div>
                        <div>{{ $lead->updated_at?->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title mb-0">Actions</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i>
                        Edit Lead
                    </a>
                    <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This lead will be deleted.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i>
                            Delete Lead
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
