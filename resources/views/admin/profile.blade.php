@extends('admin.layout.app')

@section('title', 'My Profile')

@section('content')

<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- Page Heading --}}
    <div class="page-heading mb-4">

        <div class="page-heading-copy">

            <span class="page-icon">
                <i class="bi bi-person-circle"></i>
            </span>

            <div>
                <p class="eyebrow mb-1">
                    Account
                </p>

                <h1 class="h3 mb-1">
                    My Profile
                </h1>

                <p class="text-muted mb-0">
                    Manage your personal information and account settings.
                </p>
            </div>

        </div>

    </div>


    <div class="row g-3">
        <div class="col-12 col-lg-4">

            <div class="panel h-100">

                <div class="text-center p-4">

                    {{-- Profile Image --}}
                    @if($user->profile_image)

                    <img
                    src="{{ asset('storage/' . $user->profile_image) }}"
                    alt="{{ $user->first_name }}"
                    class="rounded-circle mb-3"
                    style="
                    width: 120px;
                    height: 120px;
                    object-fit: cover;
                    border: 4px solid #f1f3f5;
                    "
                    >

                    @else

                    <div
                    class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                    style="
                    width: 120px;
                    height: 120px;
                    font-size: 42px;
                    font-weight: 600;
                    "
                    >
                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                </div>

                @endif


                <h4 class="mb-1">
                    {{ $user->first_name }}
                    {{ $user->last_name }}
                </h4>

                <p class="text-muted mb-2">
                    {{ $user->email }}
                </p>


                {{-- User Type --}}
                @if($user->type)

                <span class="badge text-bg-primary">
                    {{ ucfirst($user->type) }}
                </span>

                @endif


                {{-- Status --}}
                @if($user->status)

                <div class="mt-3">
                    <span class="badge text-bg-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Active
                    </span>
                </div>

                @else

                <div class="mt-3">
                    <span class="badge text-bg-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        Inactive
                    </span>
                </div>

                @endif

            </div>

        </div>

    </div>

    <div class="col-12 col-lg-8">

        <div class="panel">

            <div class="panel-header">

                <div>

                    <h2 class="h5 mb-1 section-title">

                        <i class="bi bi-person"></i>

                        <span>
                            Profile Information
                        </span>

                    </h2>

                    <p class="text-muted mb-0">
                        Update your personal information.
                    </p>

                </div>

            </div>


            <div class="p-4">

                <form
                action="{{ route('admin.profile.update') }}"
                method="POST"
                enctype="multipart/form-data"
                >
                @csrf
                <div class="row g-3">
                    {{-- First Name --}}
                    <div class="col-md-6">

                        <label
                        for="first_name"
                        class="form-label"
                        >
                        First Name
                    </label>

                    <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    class="form-control @error('first_name') is-invalid @enderror"
                    value="{{ old('first_name', $user->first_name) }}"
                    required
                    >

                    @error('first_name')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                    @enderror

                </div>


                {{-- Last Name --}}
                <div class="col-md-6">

                    <label
                    for="last_name"
                    class="form-label"
                    >
                    Last Name
                </label>

                <input
                type="text"
                id="last_name"
                name="last_name"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{ old('last_name', $user->last_name) }}"
                >

                @error('last_name')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

                @enderror

            </div>


            {{-- Email --}}
            <div class="col-md-6">

                <label
                for="email"
                class="form-label"
                >
                Email
            </label>

            <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email) }}"
            required
            >

            @error('email')

            <div class="invalid-feedback">
                {{ $message }}
            </div>

            @enderror

        </div>


        {{-- Mobile --}}
        <div class="col-md-6">

            <label
            for="mobile"
            class="form-label"
            >
            Mobile
        </label>

        <input
        type="text"
        id="mobile"
        name="mobile"
        class="form-control @error('mobile') is-invalid @enderror"
        value="{{ old('mobile', $user->mobile) }}"
        maxlength="13"
        >

        @error('mobile')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Profile Image --}}
    <div class="col-12">

        <label
        for="profile_image"
        class="form-label"
        >
        Profile Image
    </label>

    <input
    type="file"
    id="profile_image"
    name="profile_image"
    class="form-control @error('profile_image') is-invalid @enderror"
    accept=".jpg,.jpeg,.png,.webp"
    >

    <div class="form-text">
        JPG, JPEG, PNG or WEBP. Maximum size 2MB.
    </div>

    @error('profile_image')

    <div class="invalid-feedback">
        {{ $message }}
    </div>

    @enderror

</div>


{{-- Divider --}}
<div class="col-12">
    <hr class="my-2">
</div>


{{-- Password --}}
<div class="col-md-6">

    <label
    for="password"
    class="form-label"
    >
    New Password
</label>

<input
type="password"
id="password"
name="password"
class="form-control @error('password') is-invalid @enderror"
autocomplete="new-password"
>

<div class="form-text">
    Leave blank if you don't want to change it.
</div>

@error('password')

<div class="invalid-feedback">
    {{ $message }}
</div>

@enderror

</div>


{{-- Confirm Password --}}
<div class="col-md-6">

    <label
    for="password_confirmation"
    class="form-label"
    >
    Confirm Password
</label>

<input
type="password"
id="password_confirmation"
name="password_confirmation"
class="form-control"
autocomplete="new-password"
>

</div>


{{-- Submit --}}
<div class="col-12 mt-4">

    <button
    type="submit"
    class="btn btn-primary"
    >
    <i class="bi bi-check-circle me-1"></i>
    Update Profile
</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

@endsection
