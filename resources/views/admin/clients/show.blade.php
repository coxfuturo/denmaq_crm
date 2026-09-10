@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="page-title-box">

                <div class="page-title-right">

                    <a href="{{ route('admin.clients.edit', $client->id) }}"
                       class="btn btn-warning">
                        <i class="ri-edit-line"></i>
                        Edit
                    </a>

                    <a href="{{ route('admin.clients.index') }}"
                       class="btn btn-secondary">
                        Back
                    </a>

                </div>

                <h4 class="page-title">
                    Client Details
                </h4>

            </div>

        </div>
    </div>

    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <strong>Company Name</strong>
                    <p>{{ $client->company_name }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Contact Person</strong>
                    <p>{{ $client->contact_person }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Email</strong>
                    <p>{{ $client->email ?? '-' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Mobile</strong>
                    <p>{{ $client->mobile }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Alternate Mobile</strong>
                    <p>{{ $client->alternate_mobile ?? '-' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Website</strong>
                    <p>{{ $client->website ?? '-' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Address</strong>
                    <p>
                        {{ $client->address1 ?? '' }}
                        {{ $client->address2 ?? '' }}
                    </p>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>City</strong>
                    <p>{{ $client->city ?? '-' }}</p>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>State</strong>
                    <p>{{ $client->state ?? '-' }}</p>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>Country</strong>
                    <p>{{ $client->country ?? '-' }}</p>
                </div>

                <div class="col-md-3 mb-3">
                    <strong>Pincode</strong>
                    <p>{{ $client->pincode ?? '-' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>GST Number</strong>
                    <p>{{ $client->gst_number ?? '-' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>PAN Number</strong>
                    <p>{{ $client->pan_number ?? '-' }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Status</strong>

                    <p>

                        @if($client->status == 'active')

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Inactive
                            </span>

                        @endif

                    </p>

                </div>

                <div class="col-md-12 mb-3">
                    <strong>Notes</strong>
                    <p>{{ $client->notes ?? '-' }}</p>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
