@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Add Permission</h4>
            <p class="text-muted mb-0">Create a new system permission</p>
        </div>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Example: leads.view">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Module</label>
                        <input type="text" name="module" value="{{ old('module') }}" class="form-control @error('module') is-invalid @enderror" placeholder="Example: Leads">
                        @error('module')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Route</label>
                        <input type="text" name="route" value="{{ old('route') }}" class="form-control @error('route') is-invalid @enderror" placeholder="Example: admin.leads.index">
                        @error('route')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select @error('action') is-invalid @enderror">
                            <option value="">Select Action</option>
                            <option value="view" {{ old('action') === 'view' ? 'selected' : '' }}>View</option>
                            <option value="create" {{ old('action') === 'create' ? 'selected' : '' }}>Create</option>
                            <option value="edit" {{ old('action') === 'edit' ? 'selected' : '' }}>Edit</option>
                            <option value="delete" {{ old('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                            <option value="status" {{ old('action') === 'status' ? 'selected' : '' }}>Status</option>
                            <option value="import" {{ old('action') === 'import' ? 'selected' : '' }}>Import</option>
                            <option value="export" {{ old('action') === 'export' ? 'selected' : '' }}>Export</option>
                        </select>
                        @error('action')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position</label>
                        <input type="number" name="position" value="{{ old('position', 0) }}" class="form-control @error('position') is-invalid @enderror" min="0">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', 1) ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Save Permission
                </button>

                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection