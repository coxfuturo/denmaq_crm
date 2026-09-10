@extends('admin.layout.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1">Clients</h4>
            <p class="text-muted mb-0">Manage system clients</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.clients.trash') }}" class="btn btn-sm btn-danger">
                <i class="bi bi-trash me-1"></i>Trash
            </a>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#importClientModal">
                <i class="bi bi-upload me-1"></i>Import
            </button>
            <a href="{{ route('admin.clients.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Client
            </a>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
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
            <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search client...">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach([10,15,25,50,100,200,500] as $limit)
                            <option value="{{ $limit }}" {{ (int) request('per_page', 15) === $limit ? 'selected' : '' }}>
                                {{ $limit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Company</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Location</th>
                            <th>Website</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ $clients->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $client->company_name }}</strong>
                                    @if($client->gst_number)
                                        <div class="small text-muted">GST: {{ $client->gst_number }}</div>
                                    @endif
                                </td>
                                <td>{{ $client->contact_person ?: '-' }}</td>
                                <td>{{ $client->email ?: '-' }}</td>
                                <td>
                                    <div>{{ $client->mobile }}</div>
                                    @if($client->alternate_mobile)
                                        <div class="small text-muted">{{ $client->alternate_mobile }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($client->city || $client->state)
                                        {{ $client->city }}{{ $client->city && $client->state ? ', ' : '' }}{{ $client->state }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($client->website)
                                        <a href="{{ $client->website }}" target="_blank">{{ $client->website }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.clients.changeStatus', $client->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ $client->status === $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>{{ $client->created_at?->format('d-m-Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.clients.show', $client->id) }}">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.clients.edit', $client->id) }}">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This client will be moved to trash.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">No clients found.</td>
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
<div class="modal fade" id="importClientModal" tabindex="-1" aria-labelledby="importClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importClientModalLabel">Import Clients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.clients.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Import Instructions</strong><br>
                        Download the sample Excel file and fill client details according to the given columns.
                        <br><br>
                        Required fields:
                        <strong>Company Name, Contact Person, Mobile</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Excel File</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Supported files: .xlsx, .xls, .csv</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-upload me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection