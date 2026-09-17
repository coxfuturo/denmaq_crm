@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Create Lead</h4>
            <p class="text-muted mb-0">Add a new lead</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-secondary px-2">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger py-2 px-3 mb-3" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header py-2">
            <h5 class="header-title mb-0">Lead Information</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.leads.store') }}" method="POST">
                @csrf

                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label mb-1">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter lead name" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Company Name</label>
                        <input type="text" name="company_name" class="form-control form-control-sm @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="Enter company name">
                        @error('company_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter email">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter phone number" required>
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Alternate Phone</label>
                        <input type="text" name="alternate_phone" class="form-control form-control-sm @error('alternate_phone') is-invalid @enderror" value="{{ old('alternate_phone') }}" placeholder="Enter alternate phone">
                        @error('alternate_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Service</label>
                        <input type="text" name="service" class="form-control form-control-sm @error('service') is-invalid @enderror" value="{{ old('service') }}" placeholder="Enter service">
                        @error('service')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Lead Source</label>
                        <select name="source" class="form-select form-select-sm @error('source') is-invalid @enderror">
                            <option value="">Select Source</option>
                            @foreach($sources as $source)
                            <option value="{{ $source }}" {{ old('source') == $source ? 'selected' : '' }}>
                                {{ $source }}
                            </option>
                            @endforeach
                        </select>
                        @error('source')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', 'New') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Assigned To</label>
                        <select name="assigned_to" class="form-select form-select-sm @error('assigned_to') is-invalid @enderror">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ trim($user->first_name . ' ' . $user->last_name) }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Follow Up Date</label>
                        <input type="date" name="follow_up_date" class="form-control form-control-sm @error('follow_up_date') is-invalid @enderror" value="{{ old('follow_up_date') }}">
                        @error('follow_up_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Budget</label>
                        <input type="number" name="budget" class="form-control form-control-sm @error('budget') is-invalid @enderror" value="{{ old('budget') }}" placeholder="Enter budget" step="0.01" min="0">
                        @error('budget')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label mb-1">Notes</label>
                        <textarea name="notes" rows="4" class="form-control form-control-sm @error('notes') is-invalid @enderror" placeholder="Enter notes">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-1 mt-3">
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-secondary px-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary px-2">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Lead
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
