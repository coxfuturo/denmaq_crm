@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Add Follow Up</h4>
            <p class="text-muted mb-0">Create a new follow up</p>
        </div>

        <a href="{{ route('admin.followups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">

        <div class="card-body">

            <form action="{{ route('admin.followups.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Lead</label>

                        <select name="lead_id" class="form-select @error('lead_id') is-invalid @enderror">
                            <option value="">Select Lead</option>

                            @foreach($leads as $lead)
                            <option
                            value="{{ $lead->id }}"
                            {{ old('lead_id') == $lead->id ? 'selected' : '' }}
                            >
                            {{ $lead->name }}

                            @if($lead->company_name)
                            - {{ $lead->company_name }}
                            @endif
                        </option>
                        @endforeach
                    </select>

                    @error('lead_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Client</label>

                    <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                        <option value="">Select Client</option>

                        @foreach($clients as $client)
                        <option
                        value="{{ $client->id }}"
                        {{ old('client_id') == $client->id ? 'selected' : '' }}
                        >
                        {{ $client->company_name }}

                        @if($client->contact_person)
                        - {{ $client->contact_person }}
                        @endif
                    </option>
                    @endforeach
                </select>

                @error('client_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Assigned To</label>

                <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                    <option value="">Select User</option>

                    @foreach($users as $user)
                    <option
                    value="{{ $user->id }}"
                    {{ old('assigned_to') == $user->id ? 'selected' : '' }}
                    >
                    {{ $user->first_name }} {{ $user->last_name }}
                </option>
                @endforeach
            </select>

            @error('assigned_to')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Follow Up Date
                <span class="text-danger">*</span>
            </label>

            <input
            type="date"
            name="follow_up_date"
            class="form-control @error('follow_up_date') is-invalid @enderror"
            value="{{ old('follow_up_date', date('Y-m-d')) }}"
            required
            >

            @error('follow_up_date')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Follow Up Time</label>

            <input
            type="time"
            name="follow_up_time"
            class="form-control @error('follow_up_time') is-invalid @enderror"
            value="{{ old('follow_up_time') }}"
            >

            @error('follow_up_time')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Type
                <span class="text-danger">*</span>
            </label>

            <select
            name="type"
            class="form-select @error('type') is-invalid @enderror"
            required
            >
            <option value="Call" {{ old('type', 'Call') === 'Call' ? 'selected' : '' }}>
                Call
            </option>

            <option value="Meeting" {{ old('type') === 'Meeting' ? 'selected' : '' }}>
                Meeting
            </option>

            <option value="Email" {{ old('type') === 'Email' ? 'selected' : '' }}>
                Email
            </option>

            <option value="WhatsApp" {{ old('type') === 'WhatsApp' ? 'selected' : '' }}>
                WhatsApp
            </option>

            <option value="Other" {{ old('type') === 'Other' ? 'selected' : '' }}>
                Other
            </option>
        </select>

        @error('type')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">
            Subject
            <span class="text-danger">*</span>
        </label>

        <input
        type="text"
        name="subject"
        class="form-control @error('subject') is-invalid @enderror"
        placeholder="Enter follow up subject"
        value="{{ old('subject') }}"
        required
        >

        @error('subject')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">
            Priority
            <span class="text-danger">*</span>
        </label>

        <select
        name="priority"
        class="form-select @error('priority') is-invalid @enderror"
        required
        >
        <option value="Low" {{ old('priority', 'Medium') === 'Low' ? 'selected' : '' }}>
            Low
        </option>

        <option value="Medium" {{ old('priority', 'Medium') === 'Medium' ? 'selected' : '' }}>
            Medium
        </option>

        <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>
            High
        </option>
    </select>

    @error('priority')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

<div class="col-md-4">
    <label class="form-label">
        Status
        <span class="text-danger">*</span>
    </label>

    <select
    name="status"
    class="form-select @error('status') is-invalid @enderror"
    required
    >
    <option value="Pending" {{ old('status', 'Pending') === 'Pending' ? 'selected' : '' }}>
        Pending
    </option>

    <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>
        Completed
    </option>

    <option value="Cancelled" {{ old('status') === 'Cancelled' ? 'selected' : '' }}>
        Cancelled
    </option>
</select>

@error('status')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
</div>

<div class="col-md-4">
    <label class="form-label">
        Next Follow Up Date
    </label>

    <input
    type="date"
    name="next_follow_up_date"
    class="form-control @error('next_follow_up_date') is-invalid @enderror"
    value="{{ old('next_follow_up_date') }}"
    >

    @error('next_follow_up_date')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

<div class="col-md-12">
    <label class="form-label">Notes</label>

    <textarea
    name="notes"
    class="form-control @error('notes') is-invalid @enderror"
    rows="5"
    placeholder="Enter follow up notes..."
    >{{ old('notes') }}</textarea>

    @error('notes')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

</div>

<div class="mt-4 d-flex gap-2">

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i>
        Save Follow Up
    </button>

    <a
    href="{{ route('admin.followups.index') }}"
    class="btn btn-secondary"
    >
    Cancel
</a>

</div>

</form>

</div>

</div>

</div>

@endsection
