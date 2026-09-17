@extends('admin.layout.app')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Add Project</h4>
            <p class="text-muted mb-0">Create a new project</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-secondary px-2">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
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
            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label mb-1">
                            Project Code
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="project_code" class="form-control form-control-sm @error('project_code') is-invalid @enderror" value="{{ old('project_code') }}" placeholder="PRJ-001" maxlength="100" required>
                        @error('project_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">
                            Project Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter project name" maxlength="255" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Client</label>
                        <select name="client_id" class="form-select form-select-sm @error('client_id') is-invalid @enderror">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Assign To</label>
                        <select name="assigned_to" class="form-select form-select-sm @error('assigned_to') is-invalid @enderror">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Budget</label>
                        <input type="number" name="budget" class="form-control form-control-sm @error('budget') is-invalid @enderror" value="{{ old('budget') }}" placeholder="0.00" min="0" step="0.01">
                        @error('budget')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">
                            Priority
                            <span class="text-danger">*</span>
                        </label>
                        <select name="priority" class="form-select form-select-sm @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">
                            Status
                            <span class="text-danger">*</span>
                        </label>
                        <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                            <option value="planning" {{ old('status', 'planning') === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-1">Description</label>
                        <textarea name="description" rows="4" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Enter project description">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex gap-1 mt-3">
                    <button type="submit" class="btn btn-sm btn-primary px-2">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Project
                    </button>
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-secondary px-2">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
