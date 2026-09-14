@extends('admin.layout.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Edit Follow Up</h4>
            <p class="text-muted mb-0">Update follow up details</p>
        </div>

        <a href="{{ route('admin.followups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('admin.followups.update', $followUp->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Lead</label>

                        <select name="lead_id" class="form-select">
                            <option value="">Select Lead</option>

                            @foreach($leads as $lead)
                            <option
                            value="{{ $lead->id }}"
                            {{ old('lead_id', $followUp->lead_id) == $lead->id ? 'selected' : '' }}
                            >
                            {{ $lead->name }}

                            @if($lead->company_name)
                            - {{ $lead->company_name }}
                            @endif
                        </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Client</label>

                    <select name="client_id" class="form-select">
                        <option value="">Select Client</option>

                        @foreach($clients as $client)
                        <option
                        value="{{ $client->id }}"
                        {{ old('client_id', $followUp->client_id) == $client->id ? 'selected' : '' }}
                        >
                        {{ $client->company_name }}

                        @if($client->contact_person)
                        - {{ $client->contact_person }}
                        @endif
                    </option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Assigned To</label>

                <select name="assigned_to" class="form-select">
                    <option value="">Select User</option>

                    @foreach($users as $user)
                    <option
                    value="{{ $user->id }}"
                    {{ old('assigned_to', $followUp->assigned_to) == $user->id ? 'selected' : '' }}
                    >
                    {{ $user->first_name }} {{ $user->last_name }}
                </option>
                @endforeach

            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Follow Up Date <span class="text-danger">*</span>
            </label>

            <input
            type="date"
            name="follow_up_date"
            class="form-control"
            value="{{ old('follow_up_date', $followUp->follow_up_date?->format('Y-m-d')) }}"
            required
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Follow Up Time</label>

            <input
            type="time"
            name="follow_up_time"
            class="form-control"
            value="{{ old('follow_up_time', $followUp->follow_up_time ? $followUp->follow_up_time->format('H:i') : '') }}"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Type <span class="text-danger">*</span>
            </label>

            <select name="type" class="form-select" required>

                <option
                value="Call"
                {{ old('type', $followUp->type) === 'Call' ? 'selected' : '' }}
                >
                Call
            </option>

            <option
            value="Meeting"
            {{ old('type', $followUp->type) === 'Meeting' ? 'selected' : '' }}
            >
            Meeting
        </option>

        <option
        value="Email"
        {{ old('type', $followUp->type) === 'Email' ? 'selected' : '' }}
        >
        Email
    </option>

    <option
    value="WhatsApp"
    {{ old('type', $followUp->type) === 'WhatsApp' ? 'selected' : '' }}
    >
    WhatsApp
</option>

<option
value="Other"
{{ old('type', $followUp->type) === 'Other' ? 'selected' : '' }}
>
Other
</option>

</select>
</div>

<div class="col-md-8">
    <label class="form-label">
        Subject <span class="text-danger">*</span>
    </label>

    <input
    type="text"
    name="subject"
    class="form-control"
    placeholder="Enter follow up subject"
    value="{{ old('subject', $followUp->subject) }}"
    required
    >
</div>

<div class="col-md-4">
    <label class="form-label">
        Priority <span class="text-danger">*</span>
    </label>

    <select name="priority" class="form-select" required>

        <option
        value="Low"
        {{ old('priority', $followUp->priority) === 'Low' ? 'selected' : '' }}
        >
        Low
    </option>

    <option
    value="Medium"
    {{ old('priority', $followUp->priority) === 'Medium' ? 'selected' : '' }}
    >
    Medium
</option>

<option
value="High"
{{ old('priority', $followUp->priority) === 'High' ? 'selected' : '' }}
>
High
</option>

</select>
</div>

<div class="col-md-4">
    <label class="form-label">
        Status <span class="text-danger">*</span>
    </label>

    <select name="status" class="form-select" required>

        <option
        value="Pending"
        {{ old('status', $followUp->status) === 'Pending' ? 'selected' : '' }}
        >
        Pending
    </option>

    <option
    value="Completed"
    {{ old('status', $followUp->status) === 'Completed' ? 'selected' : '' }}
    >
    Completed
</option>

<option
value="Cancelled"
{{ old('status', $followUp->status) === 'Cancelled' ? 'selected' : '' }}
>
Cancelled
</option>

</select>
</div>

<div class="col-md-4">
    <label class="form-label">Next Follow Up Date</label>

    <input
    type="date"
    name="next_follow_up_date"
    class="form-control"
    value="{{ old('next_follow_up_date', $followUp->next_follow_up_date?->format('Y-m-d')) }}"
    >
</div>

<div class="col-md-12">
    <label class="form-label">Notes</label>

    <textarea
    name="notes"
    class="form-control"
    rows="5"
    placeholder="Enter follow up notes..."
    >{{ old('notes', $followUp->notes) }}</textarea>
</div>

</div>

<div class="mt-4 d-flex gap-2">

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Update Follow Up
    </button>

    <a href="{{ route('admin.followups.index') }}" class="btn btn-secondary">
        Cancel
    </a>

</div>

</form>

</div>

</div>

</div>

@endsection
