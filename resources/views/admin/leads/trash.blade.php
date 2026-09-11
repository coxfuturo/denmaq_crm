@extends('admin.layout.app')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Lead Trash</h4>
            <p class="text-muted mb-0">Manage deleted leads</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Leads
        </a>
    </div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Lead</th>
                        <th>Company</th>
                        <th>Phone</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $leads->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $lead->name }}</strong>
                                @if($lead->email)
                                    <div class="small text-muted">{{ $lead->email }}</div>
                                @endif
                            </td>
                            <td>{{ $lead->company_name ?: '-' }}</td>
                            <td>{{ $lead->phone }}</td>
                            <td>{{ $lead->service ?: '-' }}</td>
                            <td>{{ $lead->status }}</td>
                            <td>{{ $lead->deleted_at?->format('d-m-Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form action="{{ route('admin.leads.restore', $lead->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                                            Restore
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.leads.forceDelete', $lead->id) }}" method="POST" onsubmit="return deleteConfirm(this, 'This lead will be permanently deleted.')">
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
                                No deleted leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $leads->links() }}
        </div>
    </div>
</div>
</div>
@endsection
