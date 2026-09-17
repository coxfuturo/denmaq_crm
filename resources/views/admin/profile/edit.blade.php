@extends('admin.layout.app')

@section('title', 'My Profile')

@section('content')

@php
$userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
$initial = strtoupper(substr($user->first_name ?? 'U', 0, 1));
@endphp

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">My Profile</h4>
            <p class="text-muted mb-0">
                Manage your account information
            </p>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        @if($user->profile_image)

                        <img
                        id="profilePreview"
                        src="{{ asset('storage/' . $user->profile_image) }}"
                        alt="{{ $userName ?: 'User' }}"
                        class="rounded-circle shadow-sm"
                        style="width:120px;height:120px;object-fit:cover;">

                        @else

                        <div
                        id="profileInitial"
                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow-sm"
                        style="width:120px;height:120px;font-size:42px;">

                        {{ $initial }}

                    </div>

                    <img
                    id="profilePreview"
                    src=""
                    alt="Profile Preview"
                    class="rounded-circle shadow-sm d-none"
                    style="width:120px;height:120px;object-fit:cover;">

                    @endif

                </div>

                <h5 class="mb-1">
                    {{ $userName ?: 'User' }}
                </h5>

                <p class="text-muted mb-3">
                    {{ $user->email }}
                </p>

                @if($user->type)

                <span class="badge bg-primary px-3 py-2">
                    {{ $user->type }}
                </span>

                @endif

                @if($user->company_name)

                <div class="mt-3">
                    <small class="text-muted d-block">
                        Company
                    </small>

                    <span class="fw-semibold">
                        {{ $user->company_name }}
                    </span>
                </div>

                @endif

                @if($user->mobile)

                <div class="mt-3">
                    <small class="text-muted d-block">
                        Mobile
                    </small>

                    <span class="fw-semibold">
                        {{ $user->mobile }}
                    </span>
                </div>

                @endif

            </div>

        </div>

    </div>

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0">
                    Profile Information
                </h5>

            </div>

            <div class="card-body p-4">

                <form
                action="{{ route('admin.profile.update') }}"
                method="POST"
                enctype="multipart/form-data"
                id="profileForm">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label for="first_name" class="form-label">
                            First Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="form-control @error('first_name') is-invalid @enderror"
                        value="{{ old('first_name', $user->first_name) }}"
                        maxlength="100"
                        autocomplete="given-name"
                        required>

                        @error('first_name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="last_name" class="form-label">
                            Last Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="form-control @error('last_name') is-invalid @enderror"
                        value="{{ old('last_name', $user->last_name) }}"
                        maxlength="100"
                        autocomplete="family-name"
                        required>

                        @error('last_name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="email" class="form-label">
                            Email
                            <span class="text-danger">*</span>
                        </label>

                        <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        maxlength="255"
                        autocomplete="email"
                        required>

                        @error('email')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="mobile" class="form-label">
                            Mobile
                            <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        id="mobile"
                        name="mobile"
                        class="form-control @error('mobile') is-invalid @enderror"
                        value="{{ old('mobile', $user->mobile) }}"
                        maxlength="20"
                        autocomplete="tel"
                        required>

                        @error('mobile')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-12 mb-3">

                        <label for="company_name" class="form-label">
                            Company Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        class="form-control @error('company_name') is-invalid @enderror"
                        value="{{ old('company_name', $user->company_name) }}"
                        maxlength="255"
                        autocomplete="organization"
                        required>

                        @error('company_name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-12 mb-3">

                        <label for="profile_image" class="form-label">
                            Profile Image
                        </label>

                        <input
                        type="file"
                        id="profile_image"
                        name="profile_image"
                        class="form-control @error('profile_image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                        <small class="text-muted d-block mt-1">
                            JPG, JPEG, PNG or WEBP. Maximum file size: 2 MB.
                        </small>

                        @error('profile_image')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="password" class="form-label">
                            New Password
                        </label>

                        <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        minlength="8"
                        autocomplete="new-password">

                        <small class="text-muted d-block mt-1">
                            Leave blank if you do not want to change password.
                        </small>

                        @error('password')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="password_confirmation" class="form-label">
                            Confirm New Password
                        </label>

                        <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password">

                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-light">

                    <i class="bi bi-arrow-left me-1"></i>
                    Cancel

                </a>

                <button
                type="submit"
                class="btn btn-primary"
                id="profileSubmitButton">

                <span
                id="profileSubmitSpinner"
                class="spinner-border spinner-border-sm me-1 d-none"
                role="status"
                aria-hidden="true">
            </span>

            <i
            id="profileSubmitIcon"
            class="bi bi-check-lg me-1">
        </i>

        <span id="profileSubmitText">
            Update Profile
        </span>

    </button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

@if($errors->any())

<script>
    document.addEventListener('DOMContentLoaded', function () {
        errorAlert(@json($errors->first()));
    });
</script>

@endif

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('profileForm');
        const submitButton = document.getElementById('profileSubmitButton');
        const submitSpinner = document.getElementById('profileSubmitSpinner');
        const submitIcon = document.getElementById('profileSubmitIcon');
        const submitText = document.getElementById('profileSubmitText');

        const firstName = document.getElementById('first_name');
        const lastName = document.getElementById('last_name');
        const email = document.getElementById('email');
        const mobile = document.getElementById('mobile');
        const companyName = document.getElementById('company_name');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const profileImage = document.getElementById('profile_image');
        const profilePreview = document.getElementById('profilePreview');
        const profileInitial = document.getElementById('profileInitial');

        if (profileImage) {

            profileImage.addEventListener('change', function () {

                const file = this.files[0];

                if (!file) {
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                const maxSize = 2 * 1024 * 1024;

                if (!allowedTypes.includes(file.type)) {

                    this.value = '';

                    errorAlert('Please select a JPG, JPEG, PNG or WEBP image.');

                    return;
                }

                if (file.size > maxSize) {

                    this.value = '';

                    errorAlert('Profile image cannot be larger than 2 MB.');

                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {

                    if (profilePreview) {

                        profilePreview.src = event.target.result;
                        profilePreview.classList.remove('d-none');

                    }

                    if (profileInitial) {

                        profileInitial.classList.add('d-none');

                    }

                };

                reader.readAsDataURL(file);

            });

        }

        if (form) {

            form.addEventListener('submit', function (event) {

                event.preventDefault();

                const firstNameValue = firstName.value.trim();
                const lastNameValue = lastName.value.trim();
                const emailValue = email.value.trim();
                const mobileValue = mobile.value.trim();
                const companyValue = companyName.value.trim();
                const passwordValue = password.value;
                const confirmationValue = passwordConfirmation.value;

                if (!firstNameValue) {

                    errorAlert('First name is required.');
                    firstName.focus();

                    return;
                }

                if (firstNameValue.length > 100) {

                    errorAlert('First name cannot exceed 100 characters.');
                    firstName.focus();

                    return;
                }

                if (!lastNameValue) {

                    errorAlert('Last name is required.');
                    lastName.focus();

                    return;
                }

                if (lastNameValue.length > 100) {

                    errorAlert('Last name cannot exceed 100 characters.');
                    lastName.focus();

                    return;
                }

                if (!emailValue) {

                    errorAlert('Email address is required.');
                    email.focus();

                    return;
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailPattern.test(emailValue)) {

                    errorAlert('Please enter a valid email address.');
                    email.focus();

                    return;
                }

                if (!mobileValue) {

                    errorAlert('Mobile number is required.');
                    mobile.focus();

                    return;
                }

                if (mobileValue.length > 20) {

                    errorAlert('Mobile number cannot exceed 20 characters.');
                    mobile.focus();

                    return;
                }

                if (!companyValue) {

                    errorAlert('Company name is required.');
                    companyName.focus();

                    return;
                }

                if (companyValue.length > 255) {

                    errorAlert('Company name cannot exceed 255 characters.');
                    companyName.focus();

                    return;
                }

                if (passwordValue !== '') {

                    if (passwordValue.length < 8) {

                        errorAlert('Password must be at least 8 characters.');
                        password.focus();

                        return;
                    }

                    if (passwordValue !== confirmationValue) {

                        errorAlert('Password confirmation does not match.');
                        passwordConfirmation.focus();

                        return;
                    }

                }

                submitButton.disabled = true;

                submitSpinner.classList.remove('d-none');
                submitIcon.classList.add('d-none');
                submitText.textContent = 'Updating...';

                form.submit();

            });

        }

    });

</script>

@endsection