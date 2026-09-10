@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Permission</h4>
            <p class="text-muted mb-0">Update system permission</p>
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
            <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Permission Name</label>
                        <input type="text" name="name" value="{{ old('name', $permission->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Example: leads.view">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Module</label>
                        <input type="text" name="module" value="{{ old('module', $permission->module) }}" class="form-control @error('module') is-invalid @enderror" placeholder="Example: Leads">
                        @error('module')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Route</label>
                        <input type="text" name="route" value="{{ old('route', $permission->route) }}" class="form-control @error('route') is-invalid @enderror" placeholder="Example: admin.leads.index">
                        @error('route')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select @error('action') is-invalid @enderror">
                            <option value="">Select Action</option>
                            <option value="view" {{ old('action', $permission->action) === 'view' ? 'selected' : '' }}>View</option>
                            <option value="create" {{ old('action', $permission->action) === 'create' ? 'selected' : '' }}>Create</option>
                            <option value="edit" {{ old('action', $permission->action) === 'edit' ? 'selected' : '' }}>Edit</option>
                            <option value="delete" {{ old('action', $permission->action) === 'delete' ? 'selected' : '' }}>Delete</option>
                            <option value="status" {{ old('action', $permission->action) === 'status' ? 'selected' : '' }}>Status</option>
                            <option value="import" {{ old('action', $permission->action) === 'import' ? 'selected' : '' }}>Import</option>
                            <option value="export" {{ old('action', $permission->action) === 'export' ? 'selected' : '' }}>Export</option>
                        </select>
                        @error('action')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position</label>
                        <input type="number" name="position" value="{{ old('position', $permission->position) }}" class="form-control @error('position') is-invalid @enderror" min="0">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $permission->status) ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Permission
                </button>

                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection