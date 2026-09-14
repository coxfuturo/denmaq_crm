@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">

            <div class="page-title-box">
                <h4 class="page-title">Edit Client</h4>
            </div>

        </div>
    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('admin.clients.update', $client->id) }}"
              method="POST">

              @csrf
              @method('PUT')

              <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Company Name
                    </label>

                    <input type="text"
                    name="company_name"
                    class="form-control"
                    value="{{ old('company_name', $client->company_name) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Contact Person
                    </label>

                    <input type="text"
                    name="contact_person"
                    class="form-control"
                    value="{{ old('contact_person', $client->contact_person) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Email
                    </label>

                    <input type="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email', $client->email) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Mobile
                    </label>

                    <input type="text"
                    name="mobile"
                    class="form-control"
                    value="{{ old('mobile', $client->mobile) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Alternate Mobile
                    </label>

                    <input type="text"
                    name="alternate_mobile"
                    class="form-control"
                    value="{{ old('alternate_mobile', $client->alternate_mobile) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Website
                    </label>

                    <input type="url"
                    name="website"
                    class="form-control"
                    value="{{ old('website', $client->website) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Address 1
                    </label>

                    <input type="text"
                    name="address1"
                    class="form-control"
                    value="{{ old('address1', $client->address1) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Address 2
                    </label>

                    <input type="text"
                    name="address2"
                    class="form-control"
                    value="{{ old('address2', $client->address2) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        City
                    </label>

                    <input type="text"
                    name="city"
                    class="form-control"
                    value="{{ old('city', $client->city) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        State
                    </label>

                    <input type="text"
                    name="state"
                    class="form-control"
                    value="{{ old('state', $client->state) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        Country
                    </label>

                    <input type="text"
                    name="country"
                    class="form-control"
                    value="{{ old('country', $client->country) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        Pincode
                    </label>

                    <input type="text"
                    name="pincode"
                    class="form-control"
                    value="{{ old('pincode', $client->pincode) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        GST Number
                    </label>

                    <input type="text"
                    name="gst_number"
                    class="form-control"
                    value="{{ old('gst_number', $client->gst_number) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        PAN Number
                    </label>

                    <input type="text"
                    name="pan_number"
                    class="form-control"
                    value="{{ old('pan_number', $client->pan_number) }}">
                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select name="status" class="form-select">

                        <option value="active"
                        {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                    {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Notes
            </label>

            <textarea name="notes"
            class="form-control"
            rows="4">{{ old('notes', $client->notes) }}</textarea>

        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        Update Client
    </button>

    <a href="{{ route('admin.clients.index') }}"
    class="btn btn-secondary">
    Cancel
</a>

</form>

</div>

</div>

</div>

@endsection
