@extends('admin.layout.app')

@section('content')

@php
$user = auth()->user();

$isSuperAdmin = $user && $user->hasRole('Super Admin');

$canView = $isSuperAdmin || ($user && $user->can('Follow Ups View'));
$canAdd = $isSuperAdmin || ($user && $user->can('Follow Ups Create'));
$canEdit = $isSuperAdmin || ($user && $user->can('Follow Ups Edit'));
$canDelete = $isSuperAdmin || ($user && $user->can('Follow Ups Delete'));

@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">Follow Ups</h4>
            <p class="text-muted mb-0">Manage all follow ups</p>
        </div>

        <div class="d-flex gap-2">

            @if($canDelete)
            <a href="{{ route('admin.followups.trash') }}" class="btn btn-danger">
                <i class="bi bi-trash"></i>
                Trash
            </a>
            @endif

            @if($canAdd)
            <a href="{{ route('admin.followups.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Add Follow Up
            </a>
            @endif

        </div>

    </div>

    <div class="card mb-3">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.followups.index') }}">

                <div class="row g-3">

                    <div class="col-md-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search follow up..."
                        value="{{ request('search') }}"
                        >

                    </div>

                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option
                            value="Pending"
                            {{ request('status') == 'Pending' ? 'selected' : '' }}
                            >
                            Pending
                        </option>

                        <option
                        value="Completed"
                        {{ request('status') == 'Completed' ? 'selected' : '' }}
                        >
                        Completed
                    </option>

                    <option
                    value="Cancelled"
                    {{ request('status') == 'Cancelled' ? 'selected' : '' }}
                    >
                    Cancelled
                </option>

            </select>

        </div>

        <div class="col-md-2">

            <label class="form-label">
                Type
            </label>

            <select name="type" class="form-select">

                <option value="">
                    All Types
                </option>

                <option
                value="Call"
                {{ request('type') == 'Call' ? 'selected' : '' }}
                >
                Call
            </option>

            <option
            value="Meeting"
            {{ request('type') == 'Meeting' ? 'selected' : '' }}
            >
            Meeting
        </option>

        <option
        value="Email"
        {{ request('type') == 'Email' ? 'selected' : '' }}
        >
        Email
    </option>

    <option
    value="WhatsApp"
    {{ request('type') == 'WhatsApp' ? 'selected' : '' }}
    >
    WhatsApp
</option>

<option
value="Other"
{{ request('type') == 'Other' ? 'selected' : '' }}
>
Other
</option>

</select>

</div>

<div class="col-md-2">

    <label class="form-label">
        Priority
    </label>

    <select name="priority" class="form-select">

        <option value="">
            All Priority
        </option>

        <option
        value="Low"
        {{ request('priority') == 'Low' ? 'selected' : '' }}
        >
        Low
    </option>

    <option
    value="Medium"
    {{ request('priority') == 'Medium' ? 'selected' : '' }}
    >
    Medium
</option>

<option
value="High"
{{ request('priority') == 'High' ? 'selected' : '' }}
>
High
</option>

</select>

</div>

<div class="col-md-3">

    <label class="form-label">
        Assigned User
    </label>

    <select name="assigned_to" class="form-select">

        <option value="">
            All Users
        </option>

        @foreach($users as $userItem)

        <option
        value="{{ $userItem->id }}"
        {{ request('assigned_to') == $userItem->id ? 'selected' : '' }}
        >
        {{ $userItem->first_name }}
        {{ $userItem->last_name }}
    </option>

    @endforeach

</select>

</div>

<div class="col-md-2">

    <label class="form-label">
        Follow Up Date
    </label>

    <input
    type="date"
    name="follow_up_date"
    class="form-control"
    value="{{ request('follow_up_date') }}"
    >

</div>

<div class="col-md-2">

    <label class="form-label">
        From Date
    </label>

    <input
    type="date"
    name="from_date"
    class="form-control"
    value="{{ request('from_date') }}"
    >

</div>

<div class="col-md-2">

    <label class="form-label">
        To Date
    </label>

    <input
    type="date"
    name="to_date"
    class="form-control"
    value="{{ request('to_date') }}"
    >

</div>

<div class="col-md-2">

    <label class="form-label">
        Per Page
    </label>

    <select name="per_page" class="form-select">

        @foreach([10, 15, 25, 50, 100, 200, 500] as $value)

        <option
        value="{{ $value }}"
        {{ (int) request('per_page', 15) === $value ? 'selected' : '' }}
        >
        {{ $value }}
    </option>

    @endforeach

</select>

</div>

<div class="col-md-4 d-flex align-items-end gap-2">

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-search"></i>
        Search
    </button>

    <a
    href="{{ route('admin.followups.index') }}"
    class="btn btn-secondary"
    >
    <i class="bi bi-arrow-clockwise"></i>
    Reset
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
                        <th>Date</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Next Follow Up</th>
                        <th width="170">Action</th>

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

                            <div>

                                <strong>Lead:</strong>
                                {{ $followUp->lead->name }}

                                @if($followUp->lead->company_name)

                                <br>

                                <small class="text-muted">
                                    {{ $followUp->lead->company_name }}
                                </small>

                                @endif

                            </div>

                            @endif

                            @if($followUp->client)

                            <div class="mt-1">

                                <strong>Client:</strong>
                                {{ $followUp->client->company_name }}

                                @if($followUp->client->contact_person)

                                <br>

                                <small class="text-muted">
                                    {{ $followUp->client->contact_person }}
                                </small>

                                @endif

                            </div>

                            @endif

                            @if(!$followUp->lead && !$followUp->client)

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

                            @if($followUp->assignedUser)

                            {{ $followUp->assignedUser->first_name }}
                            {{ $followUp->assignedUser->last_name }}

                            @else

                            <span class="text-muted">
                                Unassigned
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

                            @if($followUp->next_follow_up_date)

                            {{ $followUp->next_follow_up_date->format('d-m-Y') }}

                            @else

                            <span class="text-muted">
                                N/A
                            </span>

                            @endif

                        </td>

                        <td>

                            <div class="d-flex gap-1">

                                @if($canView)

                                <a
                                href="{{ route('admin.followups.show', $followUp->id) }}"
                                class="btn btn-sm btn-info"
                                title="View"
                                >
                                <i class="bi bi-eye"></i>
                            </a>

                            @endif

                            @if($canEdit)

                            <a
                            href="{{ route('admin.followups.edit', $followUp->id) }}"
                            class="btn btn-sm btn-warning"
                            title="Edit"
                            >
                            <i class="bi bi-pencil"></i>
                        </a>

                        <button
                        type="button"
                        class="btn btn-sm btn-success"
                        title="Change Status"
                        data-bs-toggle="modal"
                        data-bs-target="#statusModal{{ $followUp->id }}"
                        >
                        <i class="bi bi-check-circle"></i>
                    </button>

                    @endif

                    @if($canDelete)

                    <form
                    action="{{ route('admin.followups.destroy', $followUp->id) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to move this follow up to trash?')"
                    >

                    @csrf
                    @method('DELETE')

                    <button
                    type="submit"
                    class="btn btn-sm btn-danger"
                    title="Delete"
                    >
                    <i class="bi bi-trash"></i>
                </button>

            </form>

            @endif

        </div>

    </td>

</tr>

@if($canEdit)

<div
class="modal fade"
id="statusModal{{ $followUp->id }}"
tabindex="-1"
aria-hidden="true"
>

<div class="modal-dialog">

    <div class="modal-content">

        <div class="modal-header">

            <h5 class="modal-title">
                Change Follow Up Status
            </h5>

            <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            ></button>

        </div>

        <form
        action="{{ route('admin.followups.status', $followUp->id) }}"
        method="POST"
        >

        @csrf
        @method('PATCH')

        <div class="modal-body">

            <div class="mb-3">

                <label class="form-label">
                    Status
                </label>

                <select
                name="status"
                class="form-select"
                required
                >

                <option
                value="Pending"
                {{ $followUp->status === 'Pending' ? 'selected' : '' }}
                >
                Pending
            </option>

            <option
            value="Completed"
            {{ $followUp->status === 'Completed' ? 'selected' : '' }}
            >
            Completed
        </option>

        <option
        value="Cancelled"
        {{ $followUp->status === 'Cancelled' ? 'selected' : '' }}
        >
        Cancelled
    </option>

</select>

</div>

</div>

<div class="modal-footer">

    <button
    type="button"
    class="btn btn-secondary"
    data-bs-dismiss="modal"
    >
    Close
</button>

<button
type="submit"
class="btn btn-primary"
>
Update Status
</button>

</div>

</form>

</div>

</div>

</div>

@endif

@empty

<tr>

    <td colspan="10" class="text-center py-4">

        <div class="text-muted">

            <i class="bi bi-inbox fs-2"></i>

            <p class="mb-0 mt-2">
                No follow ups found.
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
        follow ups

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
