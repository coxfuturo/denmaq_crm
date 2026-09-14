@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Company Settings</h4>
            <p class="text-muted mb-0">
                Manage your company information.
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}

        <button type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close">
    </button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <strong>Please fix the following errors:</strong>

    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card shadow-sm border-0">

    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="bi bi-building me-2"></i>
            Company Information
        </h5>
    </div>

    <div class="card-body">

        <form action="{{ route('admin.settings.company.update') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        <div class="row g-4">

            {{-- Company Logo --}}
            <div class="col-12">

                <label class="form-label fw-semibold">
                    Company Logo
                </label>

                <div class="d-flex align-items-center gap-3">

                    <div>
                     @if($company && $company->logo)
                     <img
                     src="{{ asset('storage/' . $company->logo) }}"
                     alt="{{ $company->name }}"
                     >
                     @endif

                 </div>

                 <div class="flex-grow-1">

                    <input type="file"
                    name="logo"
                    class="form-control @error('logo') is-invalid @enderror"
                    accept=".jpg,.jpeg,.png,.webp">

                    <small class="text-muted">
                        JPG, JPEG, PNG or WEBP. Maximum size: 2MB.
                    </small>

                    @error('logo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                </div>

            </div>

        </div>


        {{-- Company Name --}}
        <div class="col-md-6">

            <label for="name" class="form-label">
                Company Name <span class="text-danger">*</span>
            </label>

            <input type="text"
            id="name"
            name="name"
            value="{{ old('name', $company?->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Enter company name"
            required>

            @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- Email --}}
        <div class="col-md-6">

            <label for="email" class="form-label">
                Company Email
            </label>

            <input type="email"
            id="email"
            name="email"
            value="{{ old('email', $company?->email) }}"
            class="form-control @error('email') is-invalid @enderror"
            placeholder="company@example.com">

            @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- Phone --}}
        <div class="col-md-6">

            <label for="phone" class="form-label">
                Phone
            </label>

            <input type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $company?->phone) }}"
            class="form-control @error('phone') is-invalid @enderror"
            placeholder="Enter company phone">

            @error('phone')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- Website --}}
        <div class="col-md-6">

            <label for="website" class="form-label">
                Website
            </label>

            <input type="url"
            id="website"
            name="website"
            value="{{ old('website', $company?->website) }}"
            class="form-control @error('website') is-invalid @enderror"
            placeholder="https://example.com">

            @error('website')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- Address --}}
        <div class="col-12">

            <label for="address" class="form-label">
                Address
            </label>

            <textarea id="address"
            name="address"
            rows="3"
            class="form-control @error('address') is-invalid @enderror"
            placeholder="Enter company address">{{ old('address', $company?->address) }}</textarea>

            @error('address')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- City --}}
        <div class="col-md-3">

            <label for="city" class="form-label">
                City
            </label>

            <input type="text"
            id="city"
            name="city"
            value="{{ old('city', $company?->city) }}"
            class="form-control @error('city') is-invalid @enderror"
            placeholder="City">

            @error('city')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- State --}}
        <div class="col-md-3">

            <label for="state" class="form-label">
                State
            </label>

            <input type="text"
            id="state"
            name="state"
            value="{{ old('state', $company?->state) }}"
            class="form-control @error('state') is-invalid @enderror"
            placeholder="State">

            @error('state')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- Country --}}
        <div class="col-md-3">

            <label for="country" class="form-label">
                Country
            </label>

            <input type="text"
            id="country"
            name="country"
            value="{{ old('country', $company?->country) }}"
            class="form-control @error('country') is-invalid @enderror"
            placeholder="Country">

            @error('country')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>


        {{-- Postal Code --}}
        <div class="col-md-3">

            <label for="postal_code" class="form-label">
                Postal Code
            </label>

            <input type="text"
            id="postal_code"
            name="postal_code"
            value="{{ old('postal_code', $company?->postal_code) }}"
            class="form-control @error('postal_code') is-invalid @enderror"
            placeholder="Postal code">

            @error('postal_code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror

        </div>

    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-end">

        <button type="submit"
        class="btn btn-primary">

        <i class="bi bi-check2-circle me-1"></i>
        Save Company Settings

    </button>

</div>

</form>

</div>

</div>

</div>

@endsection
