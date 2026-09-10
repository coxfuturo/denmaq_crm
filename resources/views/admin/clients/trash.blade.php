@extends('admin.layout.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Client Trash</h4>
            <p class="text-muted mb-0">Manage deleted clients</p>
        </div>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Clients
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.clients.trash') }}" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search deleted client...">
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
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Search
                    </button>
                    <a href="{{ route('admin.clients.trash') }}" class="btn btn-secondary">
                        Reset
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
                            <th>Deleted At</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ $clients->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $client->company_name }}</strong>
                                    @if($client->gst_number)
                                        <div class="small text-muted">
                                            GST: {{ $client->gst_number }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $client->contact_person ?: '-' }}</td>
                                <td>{{ $client->email ?: '-' }}</td>
                                <td>
                                    {{ $client->mobile ?: '-' }}
                                    @if($client->alternate_mobile)
                                        <div class="small text-muted">
                                            {{ $client->alternate_mobile }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($client->city || $client->state)
                                        {{ $client->city }}
                                        {{ $client->city && $client->state ? ', ' : '' }}
                                        {{ $client->state }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{ $client->deleted_at?->format('d-m-Y H:i') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form action="{{ route('admin.clients.restore', $client->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                                Restore
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.clients.forceDelete', $client->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This client will be permanently deleted.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash me-1"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-trash fs-3 d-block mb-2 text-muted"></i>
                                    No deleted clients found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
