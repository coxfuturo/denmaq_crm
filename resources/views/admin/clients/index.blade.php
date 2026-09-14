@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    @php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser && $currentUser->hasRole('Super Admin');
    $canCreate = $isSuperAdmin || $currentUser->can('Clients Create');
    $canEdit = $isSuperAdmin || $currentUser->can('Clients Edit');
    $canDelete = $isSuperAdmin || $currentUser->can('Clients Delete');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1">Clients</h4>

            <p class="text-muted mb-0">
                @if($isSuperAdmin)
                Manage all system clients
                @else
                Manage your clients
                @endif
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            @if($canDelete)
            <a href="{{ route('admin.clients.trash') }}"
            class="btn btn-sm btn-danger">
            <i class="bi bi-trash me-1"></i>
            Trash
        </a>
        @endif

        @if($canCreate)
        <button type="button"
        class="btn btn-sm btn-warning"
        data-bs-toggle="modal"
        data-bs-target="#importClientModal">
        <i class="bi bi-upload me-1"></i>
        Import
    </button>

    <a href="{{ route('admin.clients.create') }}"
    class="btn btn-sm btn-primary">
    <i class="bi bi-plus-lg me-1"></i>
    Add Client
</a>
@endif

</div>
</div>

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
        action="{{ route('admin.clients.index') }}"
        class="row g-2 mb-4 align-items-end">

        <div class="col-xl-3 col-lg-3 col-md-6">
            <label class="form-label mb-1">
                Search
            </label>

            <input type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control form-control-sm"
            placeholder="Search client, company, mobile...">
        </div>

        @if($isSuperAdmin)
        <div class="col-xl-2 col-lg-2 col-md-6">
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
            Status
        </label>

        <select name="status"
        class="form-select form-select-sm">

        <option value="">
            All Status
        </option>

        @foreach($statuses as $status)
        <option value="{{ $status }}"
        {{ request('status') == $status ? 'selected' : '' }}>
        {{ ucfirst($status) }}
    </option>
    @endforeach

</select>
</div>

<div class="col-xl-1 col-lg-1 col-md-6">
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

<div class="col-xl-4 col-lg-4 col-md-6">
    <div class="d-flex gap-2">

        <button type="submit"
        class="btn btn-sm btn-primary">

        <i class="bi bi-search me-1"></i>
        Search

    </button>

    <a href="{{ route('admin.clients.index') }}"
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
                <th>Website</th>

                @if($isSuperAdmin)
                <th>Added By</th>
                @endif

                <th>Status</th>
                <th>Created</th>
                <th width="100">Action</th>

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

                <td>

                    @if($client->website)

                    <a href="{{ $client->website }}"
                     target="_blank"
                     rel="noopener noreferrer">

                     {{ $client->website }}

                 </a>

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

            @if($canEdit)

            <form action="{{ route('admin.clients.changeStatus', $client->id) }}"
              method="POST">

              @csrf
              @method('PATCH')

              <select name="status"
              class="form-select form-select-sm"
              onchange="this.form.submit()">

              @foreach($statuses as $status)

              <option value="{{ $status }}"
              {{ $client->status === $status ? 'selected' : '' }}>

              {{ ucfirst($status) }}

          </option>

          @endforeach

      </select>

  </form>

  @else

  @if($client->status === 'active')

  <span class="badge bg-success">
    Active
</span>

@else

<span class="badge bg-secondary">
    Inactive
</span>

@endif

@endif

</td>

<td>

    @if($client->created_at)
    {{ $client->created_at->format('d-m-Y') }}
    @else
    -
    @endif

</td>

<td>

    <div class="dropdown">

        <button class="btn btn-sm btn-light dropdown-toggle px-2"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false">

        Action

    </button>

    <ul class="dropdown-menu">

        @if($currentUser->can('Clients View') || $isSuperAdmin)

        <li>

            <a class="dropdown-item"
            href="{{ route('admin.clients.show', $client->id) }}">

            <i class="bi bi-eye me-1"></i>
            View

        </a>

    </li>

    @endif

    @if($canEdit)

    <li>

        <a class="dropdown-item"
        href="{{ route('admin.clients.edit', $client->id) }}">

        <i class="bi bi-pencil me-1"></i>
        Edit

    </a>

</li>

@endif

@if($canDelete)

<li>

    <form action="{{ route('admin.clients.destroy', $client->id) }}"
      method="POST"
      onsubmit="return deleteConfirm(this, 'This client will be moved to trash.')">

      @csrf
      @method('DELETE')

      <button type="submit"
      class="dropdown-item text-danger">

      <i class="bi bi-trash me-1"></i>
      Delete

  </button>

</form>

</li>

@endif

</ul>

</div>

</td>

</tr>

@empty

<tr>

    <td colspan="{{ $isSuperAdmin ? 12 : 11 }}"
    class="text-center py-4">

    No clients found.

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

@if($canCreate)

<div class="modal fade"
id="importClientModal"
tabindex="-1"
aria-labelledby="importClientModalLabel"
aria-hidden="true">

<div class="modal-dialog">

    <div class="modal-content">

        <div class="modal-header py-2">

            <h5 class="modal-title"
            id="importClientModalLabel">

            Import Clients

        </h5>

        <button type="button"
        class="btn-close"
        data-bs-dismiss="modal">
    </button>

</div>

<form action="{{ route('admin.clients.import') }}"
method="POST"
enctype="multipart/form-data">

@csrf

<div class="modal-body">

    <div class="alert alert-info py-2">

        <strong>
            Import Instructions
        </strong>

        <br>

        Download the sample Excel file and fill client details according to the given columns.

        <br>
        <br>

        Required fields:
        <strong>
            Company Name, Contact Person, Mobile
        </strong>

    </div>

    <div class="mb-3">

        <label class="form-label">
            Select Excel File
        </label>

        <input type="file"
        name="file"
        class="form-control form-control-sm"
        accept=".xlsx,.xls,.csv"
        required>

    </div>

    <div>

        <small class="text-muted">
            Supported files: .xlsx, .xls, .csv
        </small>

    </div>

</div>

<div class="modal-footer py-2">

    <button type="button"
    class="btn btn-sm btn-secondary"
    data-bs-dismiss="modal">

    Close

</button>

<button type="submit"
class="btn btn-sm btn-primary">

<i class="bi bi-upload me-1"></i>
Import

</button>

</div>

</form>

</div>

</div>

</div>

@endif

@endsection
