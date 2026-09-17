@extends('admin.layout.app')

@section('title', 'Quotation Trash')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');

$canRestore = $isSuperAdmin || ($user && $user->can('Quotations Restore'));
$canForceDelete = $isSuperAdmin || ($user && $user->can('Quotations Force Delete'));
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Quotation Trash</h4>
            <p class="text-muted mb-0">Manage deleted quotations.</p>
        </div>

        <a href="{{ route('admin.quotations.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Quotations
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quotation No.</th>
                            <th>Subject</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Deleted At</th>

                            @if($isSuperAdmin)
                            <th>Added By</th>
                            @endif

                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($quotations as $quotation)

                        <tr>

                            <td>
                                {{ $quotations->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>{{ $quotation->quotation_number }}</strong>
                            </td>

                            <td>
                                {{ $quotation->subject ?: '-' }}
                            </td>

                            <td>
                                @if($quotation->client)
                                {{ $quotation->client->company_name ?: ($quotation->client->contact_person ?? '-') }}
                                @else
                                -
                                @endif
                            </td>

                            <td>
                                ₹{{ number_format((float) $quotation->total, 2) }}
                            </td>

                            <td>
                                {{ $quotation->deleted_at ? $quotation->deleted_at->format('d M Y, h:i A') : '-' }}
                            </td>

                            @if($isSuperAdmin)
                            <td>
                                @if($quotation->creator)
                                {{ trim($quotation->creator->first_name . ' ' . $quotation->creator->last_name) }}
                                <small class="d-block text-muted">
                                    {{ $quotation->creator->email }}
                                </small>
                                @else
                                -
                                @endif
                            </td>
                            @endif

                            <td>

                                <div class="d-flex gap-1">

                                    @if($canRestore)
                                    <form action="{{ route('admin.quotations.restore', $quotation->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                            Restore
                                        </button>
                                    </form>
                                    @endif

                                    @if($canForceDelete)
                                    <form action="{{ route('admin.quotations.forceDelete', $quotation->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('This quotation will be permanently deleted. This action cannot be undone. Continue?')">
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </button>
                                </form>
                                @endif

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="{{ $isSuperAdmin ? 8 : 7 }}" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-trash3 fs-3 d-block mb-2"></i>
                                No deleted quotations found.
                            </div>
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($quotations->hasPages())
        <div class="mt-3">
            {{ $quotations->links() }}
        </div>
        @endif

    </div>
</div>

</div>

@endsection