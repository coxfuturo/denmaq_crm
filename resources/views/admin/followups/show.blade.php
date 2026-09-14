@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Follow Up Details</h4>
            <p class="text-muted mb-0">View follow up information</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.followups.edit', $followUp->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>

            <a href="{{ route('admin.followups.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-8">

            <div class="card mb-3">

                <div class="card-header">
                    <h5 class="mb-0">Follow Up Information</h5>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="text-muted d-block">Subject</label>
                            <strong>{{ $followUp->subject }}</strong>
                        </div>

                        <div class="col-md-3">
                            <label class="text-muted d-block">Type</label>

                            @if($followUp->type === 'Call')
                            <span class="badge bg-primary">Call</span>
                            @elseif($followUp->type === 'Meeting')
                            <span class="badge bg-info">Meeting</span>
                            @elseif($followUp->type === 'Email')
                            <span class="badge bg-secondary">Email</span>
                            @elseif($followUp->type === 'WhatsApp')
                            <span class="badge bg-success">WhatsApp</span>
                            @else
                            <span class="badge bg-dark">{{ $followUp->type }}</span>
                            @endif
                        </div>

                        <div class="col-md-3">
                            <label class="text-muted d-block">Priority</label>

                            @if($followUp->priority === 'High')
                            <span class="badge bg-danger">High</span>
                            @elseif($followUp->priority === 'Medium')
                            <span class="badge bg-warning text-dark">Medium</span>
                            @else
                            <span class="badge bg-success">Low</span>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted d-block">Follow Up Date</label>

                            <strong>
                                {{ $followUp->follow_up_date?->format('d-m-Y') }}
                            </strong>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted d-block">Follow Up Time</label>

                            @if($followUp->follow_up_time)
                            <strong>
                                {{ $followUp->follow_up_time->format('H:i') }}
                            </strong>
                            @else
                            <span class="text-muted">Not specified</span>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted d-block">Status</label>

                            @if($followUp->status === 'Pending')
                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>
                            @elseif($followUp->status === 'Completed')
                            <span class="badge bg-success">
                                Completed
                            </span>
                            @else
                            <span class="badge bg-danger">
                                Cancelled
                            </span>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted d-block">Next Follow Up Date</label>

                            @if($followUp->next_follow_up_date)
                            <strong>
                                {{ $followUp->next_follow_up_date->format('d-m-Y') }}
                            </strong>
                            @else
                            <span class="text-muted">
                                Not specified
                            </span>
                            @endif
                        </div>

                        <div class="col-md-12">
                            <label class="text-muted d-block">Notes</label>

                            @if($followUp->notes)
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($followUp->notes)) !!}
                            </div>
                            @else
                            <span class="text-muted">
                                No notes available.
                            </span>
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card mb-3">

                <div class="card-header">
                    <h5 class="mb-0">Lead / Client</h5>
                </div>

                <div class="card-body">

                    @if($followUp->lead)

                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Lead Name
                        </label>

                        <strong>
                            {{ $followUp->lead->name }}
                        </strong>
                    </div>

                    @if($followUp->lead->company_name)
                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Company
                        </label>

                        {{ $followUp->lead->company_name }}
                    </div>
                    @endif

                    @if($followUp->lead->email)
                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Email
                        </label>

                        {{ $followUp->lead->email }}
                    </div>
                    @endif

                    @if($followUp->lead->phone)
                    <div class="mb-0">
                        <label class="text-muted d-block">
                            Phone
                        </label>

                        {{ $followUp->lead->phone }}
                    </div>
                    @endif

                    @elseif($followUp->client)

                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Client Company
                        </label>

                        <strong>
                            {{ $followUp->client->company_name }}
                        </strong>
                    </div>

                    @if($followUp->client->contact_person)
                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Contact Person
                        </label>

                        {{ $followUp->client->contact_person }}
                    </div>
                    @endif

                    @if($followUp->client->email)
                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Email
                        </label>

                        {{ $followUp->client->email }}
                    </div>
                    @endif

                    @if($followUp->client->mobile)
                    <div class="mb-0">
                        <label class="text-muted d-block">
                            Mobile
                        </label>

                        {{ $followUp->client->mobile }}
                    </div>
                    @endif

                    @else

                    <span class="text-muted">
                        No lead or client assigned.
                    </span>

                    @endif

                </div>

            </div>

            <div class="card mb-3">

                <div class="card-header">
                    <h5 class="mb-0">Assigned User</h5>
                </div>

                <div class="card-body">

                    @if($followUp->assignedUser)

                    <strong>
                        {{ $followUp->assignedUser->first_name }}
                        {{ $followUp->assignedUser->last_name }}
                    </strong>

                    @if($followUp->assignedUser->email)
                    <div class="text-muted mt-1">
                        {{ $followUp->assignedUser->email }}
                    </div>
                    @endif

                    @if($followUp->assignedUser->mobile)
                    <div class="text-muted mt-1">
                        {{ $followUp->assignedUser->mobile }}
                    </div>
                    @endif

                    @else

                    <span class="text-muted">
                        Unassigned
                    </span>

                    @endif

                </div>

            </div>

            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">Record Information</h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Created By
                        </label>

                        @if($followUp->creator)
                        {{ $followUp->creator->first_name }}
                        {{ $followUp->creator->last_name }}
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Created At
                        </label>

                        {{ $followUp->created_at?->format('d-m-Y H:i') }}
                    </div>

                    <div class="mb-3">
                        <label class="text-muted d-block">
                            Last Updated By
                        </label>

                        @if($followUp->updater)
                        {{ $followUp->updater->first_name }}
                        {{ $followUp->updater->last_name }}
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </div>

                    <div>
                        <label class="text-muted d-block">
                            Updated At
                        </label>

                        {{ $followUp->updated_at?->format('d-m-Y H:i') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
