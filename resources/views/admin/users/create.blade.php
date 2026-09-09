@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Add User</h4>
            <p class="text-muted mb-0">Create a new user</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-control" maxlength="13">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">User Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="customer" {{ old('type', 'customer') === 'customer' ? 'selected' : '' }}>
                                Customer
                            </option>
                            <option value="company" {{ old('type') === 'company' ? 'selected' : '' }}>
                                Company
                            </option>
                            <option value="admin" {{ old('type') === 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6" id="companyNameBox">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <small class="text-muted">
                            JPG, JPEG, PNG, WEBP. Maximum 2MB.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="is_admin" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">
                                Admin Access
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('type');
    const companyNameBox = document.getElementById('companyNameBox');

    function toggleCompanyName() {
        if (type.value === 'company') {
            companyNameBox.style.display = 'block';
        } else {
            companyNameBox.style.display = 'none';
        }
    }

    toggleCompanyName();

    type.addEventListener('change', toggleCompanyName);
});
</script>
@endsection
