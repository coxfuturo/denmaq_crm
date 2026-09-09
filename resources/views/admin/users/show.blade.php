@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">User Details</h4>
            <p class="text-muted mb-0">View user information</p>
        </div>

        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" width="160" height="160" class="rounded-circle" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width:160px;height:160px;font-size:50px;">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        </div>
                    @endif

                    <h5 class="mt-3">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h5>
                </div>

                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>First Name</strong>
                            <div>{{ $user->first_name }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Last Name</strong>
                            <div>{{ $user->last_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Email</strong>
                            <div>{{ $user->email }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Mobile</strong>
                            <div>{{ $user->mobile ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Type</strong>
                            <div>{{ ucfirst($user->type) }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Company</strong>
                            <div>{{ $user->company_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <strong>Role</strong>
                            <div>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-primary">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">No Role</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Admin Access</strong>
                            <div>
                                @if($user->is_admin)
                                    <span class="badge bg-danger">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Status</strong>
                            <div>
                                @if($user->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning text-dark">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Created At</strong>
                            <div>
                                {{ $user->created_at?->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Updated At</strong>
                            <div>
                                {{ $user->updated_at?->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        @if($user->trashed())
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    This user is in trash.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
