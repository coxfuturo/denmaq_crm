@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    @php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser && $currentUser->hasRole('Super Admin');
    $canDelete = $isSuperAdmin || $currentUser->can('Clients Delete');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h4 class="mb-1">Client Trash</h4>

            <p class="text-muted mb-0">
                @if($isSuperAdmin)
                Manage all deleted clients
                @else
                Manage your deleted clients
                @endif
            </p>
        </div>

        <a href="{{ route('admin.clients.index') }}"
        class="btn btn-sm btn-primary">

        <i class="bi bi-arrow-left me-1"></i>
        Back to Clients

    </a>

</div>

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show py-2"
role="alert">

{{ session('success') }}

<button type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

@endif

@if($errors->any())

<div class="alert alert-danger alert-dismissible fade show py-2"
role="alert">

<ul class="mb-0">

    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach

</ul>

<button type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

@endif

<div class="card">

    <div class="card-body">

        <form method="GET"
        action="{{ route('admin.clients.trash') }}"
        class="row g-2 mb-4 align-items-end">

        <div class="col-xl-4 col-lg-4 col-md-6">

            <label class="form-label mb-1">
                Search
            </label>

            <input type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control form-control-sm"
            placeholder="Search deleted client...">

        </div>

        @if($isSuperAdmin)

        <div class="col-xl-3 col-lg-3 col-md-6">

            <label class="form-label mb-1">
                Added By
            </label>

            <select name="created_by"
            class="form-select form-select-sm">

            <option value="">
                All Users
            </option>

            @foreach($users as $user)

            <option value="{{ $user->id }}"
                {{ (string) request('created_by') === (string) $user->id ? 'selected' : '' }}>

                {{ trim($user->first_name . ' ' . $user->last_name) }}

            </option>

            @endforeach

        </select>

    </div>

    @endif

    <div class="col-xl-2 col-lg-2 col-md-6">

        <label class="form-label mb-1">
            Per Page
        </label>

        <select name="per_page"
        class="form-select form-select-sm"
        onchange="this.form.submit()">

        @foreach([10,15,25,50,100,200,500] as $limit)

        <option value="{{ $limit }}"
        {{ (int) request('per_page', 15) === $limit ? 'selected' : '' }}>

        {{ $limit }}

    </option>

    @endforeach

</select>

</div>

<div class="col-xl-3 col-lg-3 col-md-6">

    <div class="d-flex gap-2">

        <button type="submit"
        class="btn btn-sm btn-primary">

        <i class="bi bi-search me-1"></i>
        Search

    </button>

    <a href="{{ route('admin.clients.trash') }}"
    class="btn btn-sm btn-secondary">

    <i class="bi bi-arrow-counterclockwise me-1"></i>
    Reset

</a>

</div>

</div>

</form>

<div class="table-responsive">

    <table class="table table-sm table-bordered table-hover align-middle mb-0">

        <thead>

            <tr>

                <th>S.N</th>
                <th>Company</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Location</th>

                @if($isSuperAdmin)
                <th>Added By</th>
                @endif

                <th>Deleted At</th>
                <th width="180">Action</th>

            </tr>

        </thead>

        <tbody>

            @forelse($clients as $client)

            <tr>

                <td>
                    {{ $clients->firstItem() + $loop->index }}
                </td>

                <td>

                    <strong>
                        {{ $client->company_name }}
                    </strong>

                    @if($client->gst_number)

                    <div class="small text-muted">
                        GST: {{ $client->gst_number }}
                    </div>

                    @endif

                    @if($client->pan_number)

                    <div class="small text-muted">
                        PAN: {{ $client->pan_number }}
                    </div>

                    @endif

                </td>

                <td>
                    {{ $client->contact_person ?: '-' }}
                </td>

                <td>
                    {{ $client->email ?: '-' }}
                </td>

                <td>

                    <div>
                        {{ $client->mobile ?: '-' }}
                    </div>

                    @if($client->alternate_mobile)

                    <div class="small text-muted">
                        {{ $client->alternate_mobile }}
                    </div>

                    @endif

                </td>

                <td>

                    @if($client->city || $client->state)

                    {{ $client->city }}

                    @if($client->city && $client->state)
                    ,
                    @endif

                    {{ $client->state }}

                    @else

                    -

                    @endif

                </td>

                @if($isSuperAdmin)

                <td>

                    @if($client->createdBy)

                    @php
                    $creatorName = trim(
                    ($client->createdBy->first_name ?? '') . ' ' .
                    ($client->createdBy->last_name ?? '')
                    );
                    @endphp

                    <div class="d-flex align-items-center gap-2">

                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                        style="width:32px;height:32px;font-size:12px;">

                        {{ strtoupper(substr($client->createdBy->first_name ?? 'U', 0, 1)) }}

                    </div>

                    <div>

                        <strong class="d-block">
                            {{ $creatorName ?: 'Unknown User' }}
                        </strong>

                        @if($client->createdBy->email)

                        <small class="text-muted">
                            {{ $client->createdBy->email }}
                        </small>

                        @endif

                    </div>

                </div>

                @else

                <span class="text-muted">
                    Unknown User
                </span>

                @endif

            </td>

            @endif

            <td>

                @if($client->deleted_at)
                {{ $client->deleted_at->format('d-m-Y H:i') }}
                @else
                -
                @endif

            </td>

            <td>

                @if($canDelete)

                <div class="d-flex gap-1 flex-wrap">

                    <form action="{{ route('admin.clients.restore', $client->id) }}"
                      method="POST">

                      @csrf

                      <button type="submit"
                      class="btn btn-sm btn-success">

                      <i class="bi bi-arrow-counterclockwise me-1"></i>
                      Restore

                  </button>

              </form>

              <form action="{{ route('admin.clients.forceDelete', $client->id) }}"
                  method="POST"
                  onsubmit="return deleteConfirm(this, 'This client will be permanently deleted.')">

                  @csrf
                  @method('DELETE')

                  <button type="submit"
                  class="btn btn-sm btn-danger">

                  <i class="bi bi-trash me-1"></i>
                  Delete

              </button>

          </form>

      </div>

      @else

      <span class="text-muted">
        No Action
    </span>

    @endif

</td>

</tr>

@empty

<tr>

    <td colspan="{{ $isSuperAdmin ? 9 : 8 }}"
    class="text-center py-4">

    <i class="bi bi-trash fs-3 d-block mb-2 text-muted"></i>

    No deleted clients found.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

@if($clients->hasPages())

<div class="d-flex justify-content-end mt-3">

    {{ $clients->appends(request()->query())->links() }}

</div>

@endif

</div>

</div>

</div>

@endsection
