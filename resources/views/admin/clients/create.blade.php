@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Add Client</h4>
            <p class="text-muted mb-0">Create a new client</p>
        </div>

        <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-secondary px-2">
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
            <h5 class="header-title mb-0">Client Information</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.clients.store') }}" method="POST">
                @csrf

                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label mb-1">Company Name</label>
                        <input type="text" name="company_name" class="form-control form-control-sm @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="Enter company name">
                        @error('company_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control form-control-sm @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" placeholder="Enter contact person">
                        @error('contact_person')
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
                        <label class="form-label mb-1">Mobile</label>
                        <input type="text" name="mobile" class="form-control form-control-sm @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="Enter mobile">
                        @error('mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Alternate Mobile</label>
                        <input type="text" name="alternate_mobile" class="form-control form-control-sm @error('alternate_mobile') is-invalid @enderror" value="{{ old('alternate_mobile') }}" placeholder="Enter alternate mobile">
                        @error('alternate_mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Website</label>
                        <input type="url" name="website" class="form-control form-control-sm @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://example.com">
                        @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Address 1</label>
                        <input type="text" name="address1" class="form-control form-control-sm @error('address1') is-invalid @enderror" value="{{ old('address1') }}" placeholder="Enter address">
                        @error('address1')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Address 2</label>
                        <input type="text" name="address2" class="form-control form-control-sm @error('address2') is-invalid @enderror" value="{{ old('address2') }}" placeholder="Enter address 2">
                        @error('address2')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">City</label>
                        <input type="text" name="city" class="form-control form-control-sm @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Enter city">
                        @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">State</label>
                        <input type="text" name="state" class="form-control form-control-sm @error('state') is-invalid @enderror" value="{{ old('state') }}" placeholder="Enter state">
                        @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">Country</label>
                        <input type="text" name="country" class="form-control form-control-sm @error('country') is-invalid @enderror" value="{{ old('country') }}" placeholder="Enter country">
                        @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">Pincode</label>
                        <input type="text" name="pincode" class="form-control form-control-sm @error('pincode') is-invalid @enderror" value="{{ old('pincode') }}" placeholder="Enter pincode">
                        @error('pincode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">GST Number</label>
                        <input type="text" name="gst_number" class="form-control form-control-sm @error('gst_number') is-invalid @enderror" value="{{ old('gst_number') }}" placeholder="Enter GST number">
                        @error('gst_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">PAN Number</label>
                        <input type="text" name="pan_number" class="form-control form-control-sm @error('pan_number') is-invalid @enderror" value="{{ old('pan_number') }}" placeholder="Enter PAN number">
                        @error('pan_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('status')
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
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-secondary px-2">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-sm btn-primary px-2">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Client
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection
