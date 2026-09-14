@extends('admin.layout.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Follow Ups Trash</h4>
            <p class="text-muted mb-0">Manage deleted follow ups</p>
        </div>

        <a href="{{ route('admin.followups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Follow Ups
        </a>
    </div>
    
    <div class="card mb-3">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.followups.trash') }}">

                <div class="row g-3">

                    <div class="col-md-5">
                        <label class="form-label">Search</label>

                        <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search deleted follow ups..."
                        value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>

                        <select name="status" class="form-select">
                            <option value="">All Status</option>

                            <option
                            value="Pending"
                            {{ request('status') === 'Pending' ? 'selected' : '' }}
                            >
                            Pending
                        </option>

                        <option
                        value="Completed"
                        {{ request('status') === 'Completed' ? 'selected' : '' }}
                        >
                        Completed
                    </option>

                    <option
                    value="Cancelled"
                    {{ request('status') === 'Cancelled' ? 'selected' : '' }}
                    >
                    Cancelled
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Per Page</label>

            <select name="per_page" class="form-select">

                @foreach([10,15,25,50,100,200,500] as $value)

                <option
                value="{{ $value }}"
                {{ (int) request('per_page', 15) === $value ? 'selected' : '' }}
                >
                {{ $value }}
            </option>

            @endforeach

        </select>
    </div>

    <div class="col-md-2 d-flex align-items-end gap-2">

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i>
        </button>

        <a
        href="{{ route('admin.followups.trash') }}"
        class="btn btn-secondary"
        title="Reset"
        >
        <i class="bi bi-arrow-clockwise"></i>
    </a>

</div>

</div>

</form>

</div>

</div>

<div class="card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lead / Client</th>
                        <th>Subject</th>
                        <th>Follow Up Date</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($followUps as $followUp)

                    <tr>

                        <td>
                            {{ $followUps->firstItem() + $loop->index }}
                        </td>

                        <td>

                            @if($followUp->lead)

                            <strong>Lead:</strong>
                            {{ $followUp->lead->name }}

                            @if($followUp->lead->company_name)
                            <br>
                            <small class="text-muted">
                                {{ $followUp->lead->company_name }}
                            </small>
                            @endif

                            @elseif($followUp->client)

                            <strong>Client:</strong>
                            {{ $followUp->client->company_name }}

                            @if($followUp->client->contact_person)
                            <br>
                            <small class="text-muted">
                                {{ $followUp->client->contact_person }}
                            </small>
                            @endif

                            @else

                            <span class="text-muted">
                                N/A
                            </span>

                            @endif

                        </td>

                        <td>

                            <strong>
                                {{ $followUp->subject }}
                            </strong>

                            @if($followUp->notes)

                            <br>

                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($followUp->notes, 60) }}
                            </small>

                            @endif

                        </td>

                        <td>

                            {{ $followUp->follow_up_date?->format('d-m-Y') }}

                            @if($followUp->follow_up_time)

                            <br>

                            <small class="text-muted">
                                {{ $followUp->follow_up_time->format('H:i') }}
                            </small>

                            @endif

                        </td>

                        <td>

                            @if($followUp->type === 'Call')

                            <span class="badge bg-primary">
                                Call
                            </span>

                            @elseif($followUp->type === 'Meeting')

                            <span class="badge bg-info">
                                Meeting
                            </span>

                            @elseif($followUp->type === 'Email')

                            <span class="badge bg-secondary">
                                Email
                            </span>

                            @elseif($followUp->type === 'WhatsApp')

                            <span class="badge bg-success">
                                WhatsApp
                            </span>

                            @else

                            <span class="badge bg-dark">
                                {{ $followUp->type }}
                            </span>

                            @endif

                        </td>

                        <td>

                            @if($followUp->priority === 'High')

                            <span class="badge bg-danger">
                                High
                            </span>

                            @elseif($followUp->priority === 'Medium')

                            <span class="badge bg-warning text-dark">
                                Medium
                            </span>

                            @else

                            <span class="badge bg-success">
                                Low
                            </span>

                            @endif

                        </td>

                        <td>

                            @if($followUp->status === 'Pending')

                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>

                            @elseif($followUp->status === 'Completed')

                            <span class="badge bg-success">
                                Completed
                            </span>

                            @else

                            <span class="badge bg-danger">
                                Cancelled
                            </span>

                            @endif

                        </td>

                        <td>

                            {{ $followUp->deleted_at?->format('d-m-Y H:i') }}

                        </td>

                        <td>

                            <div class="d-flex gap-1">

                                <form
                                action="{{ route('admin.followups.restore', $followUp->id) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to restore this follow up?')"
                                >

                                @csrf
                                @method('PATCH')

                                <button
                                type="submit"
                                class="btn btn-sm btn-success"
                                title="Restore"
                                >
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>

                        </form>

                        <form
                        action="{{ route('admin.followups.forceDelete', $followUp->id) }}"
                        method="POST"
                        onsubmit="return confirm('This follow up will be permanently deleted. Are you sure?')"
                        >

                        @csrf
                        @method('DELETE')

                        <button
                        type="submit"
                        class="btn btn-sm btn-danger"
                        title="Permanent Delete"
                        >
                        <i class="bi bi-trash3"></i>
                    </button>

                </form>

            </div>

        </td>

    </tr>

    @empty

    <tr>

        <td colspan="9" class="text-center py-5">

            <div class="text-muted">

                <i class="bi bi-trash fs-1"></i>

                <p class="mb-0 mt-2">
                    No deleted follow ups found.
                </p>

            </div>

        </td>

    </tr>

    @endforelse

</tbody>

</table>

</div>

@if($followUps->hasPages())

<div class="d-flex justify-content-between align-items-center mt-3">

    <div class="text-muted">
        Showing
        {{ $followUps->firstItem() ?? 0 }}
        to
        {{ $followUps->lastItem() ?? 0 }}
        of
        {{ $followUps->total() }}
        deleted follow ups
    </div>

    <div>
        {{ $followUps->links() }}
    </div>

</div>

@endif

</div>

</div>

</div>

@endsection
