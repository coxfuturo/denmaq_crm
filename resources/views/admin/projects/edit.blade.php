@extends('admin.layout.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Project</h4>

        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Project Code <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        name="project_code"
                        class="form-control @error('project_code') is-invalid @enderror"
                        value="{{ old('project_code', $project->project_code) }}"
                        required
                        >

                        @error('project_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Project Name <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $project->name) }}"
                        required
                        >

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Client
                        </label>

                        <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                            <option value="">Select Client</option>

                            @foreach($clients as $client)
                            <option
                            value="{{ $client->id }}"
                            {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}
                            >
                            {{ $client->company_name }}
                        </option>
                        @endforeach
                    </select>

                    @error('client_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Assign To
                    </label>

                    <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                        <option value="">Select User</option>

                        @foreach($users as $user)
                        <option
                        value="{{ $user->id }}"
                        {{ old('assigned_to', $project->assigned_to) == $user->id ? 'selected' : '' }}
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

            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Start Date
                </label>

                <input
                type="date"
                name="start_date"
                class="form-control @error('start_date') is-invalid @enderror"
                value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                >

                @error('start_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">
                    End Date
                </label>

                <input
                type="date"
                name="end_date"
                class="form-control @error('end_date') is-invalid @enderror"
                value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                >

                @error('end_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    Budget
                </label>

                <input
                type="number"
                step="0.01"
                min="0"
                name="budget"
                class="form-control @error('budget') is-invalid @enderror"
                value="{{ old('budget', $project->budget) }}"
                >

                @error('budget')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    Priority <span class="text-danger">*</span>
                </label>

                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                    <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>
                        Low
                    </option>

                    <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>
                        Medium
                    </option>

                    <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>
                        High
                    </option>

                    <option value="urgent" {{ old('priority', $project->priority) == 'urgent' ? 'selected' : '' }}>
                        Urgent
                    </option>
                </select>

                @error('priority')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    Status <span class="text-danger">*</span>
                </label>

                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>
                        Planning
                    </option>

                    <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>
                        In Progress
                    </option>

                    <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>
                        On Hold
                    </option>

                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>

                    <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>
                        Cancelled
                    </option>
                </select>

                @error('status')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">
                    Description
                </label>

                <textarea
                name="description"
                rows="5"
                class="form-control @error('description') is-invalid @enderror"
                >{{ old('description', $project->description) }}</textarea>

                @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            Update Project
        </button>

        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>
</div>
</div>

@endsection
