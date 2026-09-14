@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    @php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser && $currentUser->hasRole('Super Admin');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Leads</h4>
            <p class="text-muted mb-0">
                @if($isSuperAdmin)
                Manage all system leads
                @else
                Manage your leads
                @endif
            </p>
        </div>

        <div class="d-flex align-items-center gap-1">
            <a href="{{ route('admin.leads.trash') }}" class="btn btn-sm btn-danger px-2">
                <i class="bi bi-trash me-1"></i>
                Trash
            </a>

            <button type="button"
            class="btn btn-sm btn-warning px-2"
            data-bs-toggle="modal"
            data-bs-target="#importLeadModal">
            <i class="bi bi-upload me-1"></i>
            Import
        </button>

        <a href="{{ route('admin.leads.create') }}" class="btn btn-sm btn-primary px-2">
            <i class="bi bi-plus-lg me-1"></i>
            Add Lead
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body">

        <form method="GET"
        action="{{ route('admin.leads.index') }}"
        class="row g-2 mb-3 align-items-end">

        <div class="col-xl-3 col-lg-3 col-md-6">
            <label class="form-label mb-1">Search</label>
            <input type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control form-control-sm"
            placeholder="Search lead, company, phone...">
        </div>

        @if($isSuperAdmin)
        <div class="col-xl-2 col-lg-2 col-md-6">
            <label class="form-label mb-1">Added By</label>

            <select name="created_by" class="form-select form-select-sm">
                <option value="">All Users</option>

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
            <label class="form-label mb-1">Status</label>

            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>

                @foreach($statuses as $status)
                <option value="{{ $status }}"
                {{ request('status') == $status ? 'selected' : '' }}>
                {{ $status }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-xl-2 col-lg-2 col-md-6">
        <label class="form-label mb-1">Source</label>

        <select name="source" class="form-select form-select-sm">
            <option value="">All Sources</option>

            @foreach($sources as $source)
            <option value="{{ $source }}"
            {{ request('source') == $source ? 'selected' : '' }}>
            {{ $source }}
        </option>
        @endforeach
    </select>
</div>

<div class="col-xl-1 col-lg-1 col-md-6">
    <label class="form-label mb-1">Per Page</label>

    <select name="per_page" class="form-select form-select-sm">
        @foreach([10, 15, 25, 50, 100, 200, 500] as $limit)
        <option value="{{ $limit }}"
        {{ (int) request('per_page', 15) === $limit ? 'selected' : '' }}>
        {{ $limit }}
    </option>
    @endforeach
</select>
</div>

<div class="col-xl-2 col-lg-2 col-md-6">
    <div class="d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary px-2">
            <i class="bi bi-search me-1"></i>
            Search
        </button>

        <a href="{{ route('admin.leads.index') }}"
        class="btn btn-sm btn-secondary px-2">
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
                <th>Lead</th>
                <th>Company</th>
                <th>Contact</th>
                <th>Service</th>
                <th>Source</th>

                @if($isSuperAdmin)
                <th>Added By</th>
                @endif

                <th>Assigned To</th>
                <th>Status</th>
                <th>Follow Up</th>
                <th>Budget</th>
                <th>Created</th>
                <th width="100">Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($leads as $lead)
            <tr>
                <td>
                    {{ $leads->firstItem() + $loop->index }}
                </td>

                <td>
                    <div>
                        <strong>{{ $lead->name }}</strong>

                        @if($lead->email)
                        <div class="small text-muted">
                            {{ $lead->email }}
                        </div>
                        @endif
                    </div>
                </td>

                <td>
                    {{ $lead->company_name ?: '-' }}
                </td>

                <td>
                    <div>
                        {{ $lead->phone ?: '-' }}
                    </div>

                    @if($lead->alternate_phone)
                    <div class="small text-muted">
                        {{ $lead->alternate_phone }}
                    </div>
                    @endif
                </td>

                <td>
                    {{ $lead->service ?: '-' }}
                </td>

                <td>
                    {{ $lead->source ?: '-' }}
                </td>

                @if($isSuperAdmin)
                <td>
                    @if($lead->creator)
                    @php
                    $creatorName = trim(
                    ($lead->creator->first_name ?? '') . ' ' .
                    ($lead->creator->last_name ?? '')
                    );
                    @endphp

                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                        style="width:32px;height:32px;font-size:12px;">
                        {{ strtoupper(substr($lead->creator->first_name ?? 'U', 0, 1)) }}
                    </div>

                    <div>
                        <strong class="d-block">
                            {{ $creatorName ?: 'Unknown User' }}
                        </strong>

                        @if($lead->creator->email)
                        <small class="text-muted">
                            {{ $lead->creator->email }}
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
                @if($lead->assignedUser)
                {{ trim(
                $lead->assignedUser->first_name . ' ' .
                $lead->assignedUser->last_name
                ) }}
                @else
                <span class="text-muted">
                    Not Assigned
                </span>
                @endif
            </td>

            <td>
                <form action="{{ route('admin.leads.changeStatus', $lead->id) }}"
                  method="POST">
                  @csrf
                  @method('PATCH')

                  <select name="status"
                  class="form-select form-select-sm"
                  onchange="this.form.submit()">

                  @foreach($statuses as $status)
                  <option value="{{ $status }}"
                  {{ $lead->status === $status ? 'selected' : '' }}>
                  {{ $status }}
              </option>
              @endforeach

          </select>
      </form>
  </td>

  <td>
    @if($lead->follow_up_date)
    {{ \Carbon\Carbon::parse($lead->follow_up_date)->format('d-m-Y') }}
    @else
    -
    @endif
</td>

<td>
    @if($lead->budget !== null)
    ₹{{ number_format((float) $lead->budget, 2) }}
    @else
    -
    @endif
</td>

<td>
    @if($lead->created_at)
    {{ $lead->created_at->format('d-m-Y') }}
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
        <li>
            <a class="dropdown-item"
            href="{{ route('admin.leads.show', $lead->id) }}">
            <i class="bi bi-eye me-1"></i>
            View
        </a>
    </li>

    <li>
        <a class="dropdown-item"
        href="{{ route('admin.leads.edit', $lead->id) }}">
        <i class="bi bi-pencil me-1"></i>
        Edit
    </a>
</li>

<li>
    <form action="{{ route('admin.leads.destroy', $lead->id) }}"
      method="POST"
      onsubmit="return deleteConfirm(this, 'This lead will be moved to trash.')">
      @csrf
      @method('DELETE')

      <button type="submit"
      class="dropdown-item text-danger">
      <i class="bi bi-trash me-1"></i>
      Delete
  </button>
</form>
</li>
</ul>
</div>
</td>
</tr>
@empty
<tr>
    <td colspan="{{ $isSuperAdmin ? 13 : 12 }}"
    class="text-center py-4">
    No leads found.
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

@if($leads->hasPages())
<div class="d-flex justify-content-end mt-3">
    {{ $leads->appends(request()->query())->links() }}
</div>
@endif

</div>
</div>
</div>

<div class="modal fade"
id="importLeadModal"
tabindex="-1"
aria-labelledby="importLeadModalLabel"
aria-hidden="true">

<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header py-2">
            <h5 class="modal-title" id="importLeadModalLabel">
                Import Leads
            </h5>

            <button type="button"
            class="btn-close"
            data-bs-dismiss="modal">
        </button>
    </div>

    <form action="{{ route('admin.leads.import') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf

    <div class="modal-body">

        <div class="alert alert-info py-2">
            <strong>Import Instructions</strong>
            <br>
            Download the sample Excel file and fill lead details according to the given columns.
            <br>
            <br>
            Required fields:
            <strong>Lead Name, Mobile</strong>
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

        <div class="mb-3">
            <small class="text-muted">
                Supported files: .xlsx, .xls, .csv
            </small>
        </div>

        <div>
            <small class="text-muted">
                Please make sure the Excel columns match the required Lead fields.
            </small>
        </div>

    </div>

    <div class="modal-footer py-2">
        <button type="button"
        class="btn btn-sm btn-secondary px-3"
        data-bs-dismiss="modal">
        Close
    </button>

    <button type="submit"
    class="btn btn-sm btn-primary px-3">
    <i class="bi bi-upload me-1"></i>
    Import
</button>
</div>

</form>

</div>
</div>
</div>

@endsection
