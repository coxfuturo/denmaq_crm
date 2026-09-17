@extends('admin.layout.app')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$canEdit = $isSuperAdmin || ($user && $user->can('Clients Edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('Clients Delete'));
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Client Details</h4>
            <p class="text-muted mb-0">{{ $client->company_name ?: 'Client Information' }}</p>
        </div>

        <div class="d-flex align-items-center gap-1">
            @if($canEdit)
            <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-warning px-2">
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>
            @endif

            <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-secondary px-2">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    <div class="card mb-3">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">

                <div>
                    <div class="small text-muted mb-1">Company</div>

                    <div class="fw-bold fs-5">
                        {{ $client->company_name ?: '-' }}
                    </div>

                    @if($client->contact_person)
                    <div class="small text-muted mt-1">
                        <i class="bi bi-person me-1"></i>
                        {{ $client->contact_person }}
                    </div>
                    @endif
                </div>

                <div>
                    @if($client->status === 'active')
                    <span class="badge bg-success px-2 py-1">
                        Active
                    </span>
                    @else
                    <span class="badge bg-danger px-2 py-1">
                        Inactive
                    </span>
                    @endif
                </div>

            </div>

            <div class="row g-2">

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Contact Person</div>
                        <div class="fw-semibold">
                            {{ $client->contact_person ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Email</div>

                        @if($client->email)
                        <a href="mailto:{{ $client->email }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-envelope me-1"></i>
                            {{ $client->email }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Mobile</div>

                        @if($client->mobile)
                        <a href="tel:{{ $client->mobile }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $client->mobile }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Alternate Mobile</div>

                        @if($client->alternate_mobile)
                        <a href="tel:{{ $client->alternate_mobile }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $client->alternate_mobile }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Website</div>

                        @if($client->website)
                        <a href="{{ $client->website }}" target="_blank" rel="noopener" class="text-decoration-none fw-semibold">
                            <i class="bi bi-globe me-1"></i>
                            {{ $client->website }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Status</div>

                        @if($client->status === 'active')
                        <span class="badge bg-success px-2 py-1">
                            Active
                        </span>
                        @else
                        <span class="badge bg-danger px-2 py-1">
                            Inactive
                        </span>
                        @endif
                    </div>
                </div>

                <div class="col-12">
                    <div class="border rounded p-3">
                        <div class="small text-muted mb-1">Address</div>

                        <div class="fw-semibold">
                            @if($client->address1 || $client->address2)
                            {{ $client->address1 }}
                            @if($client->address1 && $client->address2)
                            , 
                            @endif
                            {{ $client->address2 }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">City</div>
                        <div class="fw-semibold">{{ $client->city ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">State</div>
                        <div class="fw-semibold">{{ $client->state ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Country</div>
                        <div class="fw-semibold">{{ $client->country ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">Pincode</div>
                        <div class="fw-semibold">{{ $client->pincode ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">GST Number</div>
                        <div class="fw-semibold">
                            {{ $client->gst_number ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-1">PAN Number</div>
                        <div class="fw-semibold">
                            {{ $client->pan_number ?: '-' }}
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="row g-3">

        <div class="col-lg-8">

            <div class="card h-100">

                <div class="card-header py-2">
                    <h5 class="header-title mb-0">
                        <i class="bi bi-journal-text me-1"></i>
                        Notes
                    </h5>
                </div>

                <div class="card-body">
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($client->notes ?: '-')) !!}
                    </div>
                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card mb-3">

                <div class="card-header py-2">
                    <h5 class="header-title mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Other Information
                    </h5>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-muted">Client ID</span>
                        <span class="fw-semibold">#{{ $client->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-muted">Created At</span>
                        <span class="fw-semibold text-end">
                            {{ $client->created_at?->format('d M Y, h:i A') ?? '-' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Updated At</span>
                        <span class="fw-semibold text-end">
                            {{ $client->updated_at?->format('d M Y, h:i A') ?? '-' }}
                        </span>
                    </div>

                </div>

            </div>

            @if($canEdit || $canDelete)
            <div class="card">

                <div class="card-header py-2">
                    <h5 class="header-title mb-0">
                        <i class="bi bi-lightning-charge me-1"></i>
                        Actions
                    </h5>
                </div>

                <div class="card-body">

                    @if($canEdit)
                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-warning w-100 mb-2">
                        <i class="bi bi-pencil me-1"></i>
                        Edit Client
                    </a>
                    @endif

                    @if($canDelete)
                    <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This client will be deleted.')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            <i class="bi bi-trash me-1"></i>
                            Delete Client
                        </button>
                    </form>
                    @endif

                </div>

            </div>
            @endif

        </div>

    </div>

</div>

@endsection
