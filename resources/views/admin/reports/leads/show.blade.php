@extends('admin.layout.app')
@section('title', 'Lead Details')
@section('content')
@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$can = function ($permission) use ($user, $isSuperAdmin) {
    return $isSuperAdmin || ($user && $user->can($permission));
};
$statuses = ['New', 'Contacted', 'Qualified', 'Converted', 'Lost'];
$statusColors = ['new' => 'info', 'contacted' => 'warning', 'qualified' => 'secondary', 'converted' => 'success', 'lost' => 'danger'];
$statusKey = strtolower($lead->status ?? 'new');
$statusColor = $statusColors[$statusKey] ?? 'dark';
$iconPaths = [
'arrowLeft' => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>',
'trash' => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
'mail' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
'info' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
'refresh' => '<polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>',
'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
'fileText' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
'check' => '<polyline points="20 6 9 17 4 12"/>',
'alert' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
];
$icon = function ($name, $size = 16) use ($iconPaths) {
    return '<svg class="ls-svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($iconPaths[$name] ?? '') . '</svg>';
};
$assignedName = $lead->assignedUser ? trim($lead->assignedUser->first_name . ' ' . $lead->assignedUser->last_name) : '';
$creatorName = $lead->creator ? trim($lead->creator->first_name . ' ' . $lead->creator->last_name) : '';
$initial = strtoupper(mb_substr(trim($lead->name ?? '?'), 0, 1));
$followBadge = 'bg-light text-body';
$followNote = '';
if ($lead->follow_up_date && !in_array($statusKey, ['converted', 'lost'], true)) {
    if ($lead->follow_up_date->isToday()) {
        $followBadge = 'bg-warning-subtle text-warning';
        $followNote = 'Today';
    } elseif ($lead->follow_up_date->isPast()) {
        $followBadge = 'bg-danger-subtle text-danger';
        $followNote = 'Overdue';
    }
}
@endphp
<style>
    .ls-svg{display:inline-block;flex-shrink:0;vertical-align:middle}
    .ls-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px}
    .ls-card{margin-bottom:16px}
    .ls-card .card-header{display:flex;align-items:center;gap:10px;padding:12px 16px;background:transparent;border-bottom:1px solid var(--bs-border-color-translucent,#e9ebec)}
    .ls-card .card-body{padding:16px}
    .ls-card-icon{width:34px;height:34px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
    .ls-card .card-title{font-size:14px;margin:0}
    .ls-hero{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
    .ls-avatar{width:64px;height:64px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;background:var(--bs-primary-bg-subtle);color:var(--bs-primary);flex-shrink:0}
    .ls-hero-info{flex:1;min-width:200px}
    .ls-hero-info h4{margin:0 0 2px;font-size:20px}
    .ls-hero-actions{display:flex;gap:8px;flex-wrap:wrap}
    .ls-hero-actions .btn{font-size:12px;padding:6px 12px}
    .ls-pill{display:inline-flex;align-items:center;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:600}
    .ls-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 24px}
    .ls-field .ls-label{display:block;font-size:11.5px;color:var(--bs-secondary-color);margin-bottom:3px}
    .ls-field .ls-value{font-size:14px;font-weight:500;word-break:break-word}
    .ls-field a{text-decoration:none}
    .ls-field a:hover{text-decoration:underline}
    .ls-notes{border:1px solid var(--bs-border-color);border-radius:8px;padding:14px;background:var(--bs-tertiary-bg,#f8f9fa);font-size:13.5px;line-height:1.6}
    .ls-side-row{padding:10px 0;border-bottom:1px solid var(--bs-border-color-translucent,#e9ebec)}
    .ls-side-row:first-child{padding-top:0}
    .ls-side-row:last-child{border-bottom:0;padding-bottom:0}
    .ls-side-row .ls-label{display:block;font-size:11.5px;color:var(--bs-secondary-color);margin-bottom:2px}
    .ls-status-group{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}
    .ls-status-group .btn{border-radius:999px;font-size:12px;padding:5px 14px;font-weight:600}
    .ls-sticky{position:sticky;top:80px}
    @media (max-width:1199.98px){.ls-sticky{position:static}}
    @media (max-width:575.98px){.ls-grid{grid-template-columns:1fr}}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="page-title mb-0">Lead Details</h4>
                    <p class="text-muted mb-0">View complete lead information</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.reports.leads') }}" class="btn btn-secondary ls-btn">{!! $icon('arrowLeft', 15) !!}Back</a>
                    @if($can('Leads Edit'))
                    <a href="{{ route('admin.reports.leads.edit', $lead->id) }}" class="btn btn-warning ls-btn">{!! $icon('edit', 15) !!}Edit</a>
                    @endif
                    @if($can('Leads Delete'))
                    <form action="{{ route('admin.reports.leads.delete', $lead->id) }}" method="POST" class="d-inline delete-lead-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger ls-btn delete-lead-btn" data-name="{{ $lead->name }}">{!! $icon('trash', 15) !!}Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card ls-card">
                <div class="card-body">
                    <div class="ls-hero">
                        <span class="ls-avatar">{{ $initial }}</span>
                        <div class="ls-hero-info">
                            <h4>{{ $lead->name ?: '-' }}</h4>
                            <p class="text-muted mb-2">{{ $lead->company_name ?: 'No company' }}</p>
                            <span class="ls-pill bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">{{ ucfirst($lead->status ?: 'New') }}</span>
                        </div>
                        <div class="ls-hero-actions">
                            @if($lead->phone)
                            <a href="tel:{{ $lead->phone }}" class="btn btn-success ls-btn">{!! $icon('phone', 14) !!}Call</a>
                            @endif
                            @if($lead->email)
                            <a href="mailto:{{ $lead->email }}" class="btn btn-info ls-btn">{!! $icon('mail', 14) !!}Email</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card ls-card">
                <div class="card-header">
                    <span class="ls-card-icon bg-primary-subtle text-primary">{!! $icon('user', 18) !!}</span>
                    <h5 class="card-title">Lead Information</h5>
                </div>
                <div class="card-body">
                    <div class="ls-grid">
                        <div class="ls-field"><span class="ls-label">Lead Name</span><div class="ls-value">{{ $lead->name ?: '-' }}</div></div>
                        <div class="ls-field"><span class="ls-label">Company Name</span><div class="ls-value">{{ $lead->company_name ?: '-' }}</div></div>
                        <div class="ls-field"><span class="ls-label">Email</span><div class="ls-value">@if($lead->email)<a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>@else-@endif</div></div>
                        <div class="ls-field"><span class="ls-label">Phone</span><div class="ls-value">@if($lead->phone)<a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>@else-@endif</div></div>
                        <div class="ls-field"><span class="ls-label">Alternate Phone</span><div class="ls-value">@if($lead->alternate_phone)<a href="tel:{{ $lead->alternate_phone }}">{{ $lead->alternate_phone }}</a>@else-@endif</div></div>
                        <div class="ls-field"><span class="ls-label">Source</span><div class="ls-value">@if($lead->source)<span class="badge bg-light text-dark">{{ $lead->source }}</span>@else-@endif</div></div>
                        <div class="ls-field"><span class="ls-label">Service</span><div class="ls-value">{{ $lead->service ?: '-' }}</div></div>
                        <div class="ls-field"><span class="ls-label">Status</span><div class="ls-value"><span class="ls-pill bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">{{ ucfirst($lead->status ?: 'New') }}</span></div></div>
                        <div class="ls-field"><span class="ls-label">Follow Up Date</span><div class="ls-value">@if($lead->follow_up_date)<span class="ls-pill {{ $followBadge }}">{{ $lead->follow_up_date->format('d M Y') }}@if($followNote)&nbsp;&middot;&nbsp;{{ $followNote }}@endif</span>@else-@endif</div></div>
                        <div class="ls-field"><span class="ls-label">Budget</span><div class="ls-value">@if($lead->budget !== null)₹{{ number_format((float) $lead->budget, 2) }}@else-@endif</div></div>
                    </div>
                </div>
            </div>
            <div class="card ls-card">
                <div class="card-header">
                    <span class="ls-card-icon bg-secondary-subtle text-secondary">{!! $icon('fileText', 18) !!}</span>
                    <h5 class="card-title">Notes</h5>
                </div>
                <div class="card-body">
                    @if($lead->notes)
                    <div class="ls-notes">{!! nl2br(e($lead->notes)) !!}</div>
                    @else
                    <p class="text-muted mb-0">No notes available.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ls-sticky">
                <div class="card ls-card">
                    <div class="card-header">
                        <span class="ls-card-icon bg-info-subtle text-info">{!! $icon('info', 18) !!}</span>
                        <h5 class="card-title">Lead Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="ls-side-row"><span class="ls-label">Lead ID</span><strong>#{{ $lead->id }}</strong></div>
                        <div class="ls-side-row">
                            <span class="ls-label">Assigned To</span>
                            @if($lead->assignedUser)
                            <strong>{{ $assignedName }}</strong>
                            @if($lead->assignedUser->email)<small class="d-block text-muted">{{ $lead->assignedUser->email }}</small>@endif
                            @else
                            <span class="text-muted">Not Assigned</span>
                            @endif
                        </div>
                        <div class="ls-side-row">
                            <span class="ls-label">Added By</span>
                            @if($lead->creator)
                            <strong>{{ $creatorName }}</strong>
                            @if($lead->creator->email)<small class="d-block text-muted">{{ $lead->creator->email }}</small>@endif
                            @else
                            <span class="text-muted">Unknown</span>
                            @endif
                        </div>
                        <div class="ls-side-row"><span class="ls-label">Created At</span><span>{{ $lead->created_at ? $lead->created_at->format('d M Y, h:i A') : '-' }}</span></div>
                        <div class="ls-side-row"><span class="ls-label">Last Updated</span><span>{{ $lead->updated_at ? $lead->updated_at->format('d M Y, h:i A') : '-' }}</span></div>
                    </div>
                </div>
                @if($can('Leads Status'))
                <div class="card ls-card">
                    <div class="card-header">
                        <span class="ls-card-icon bg-warning-subtle text-warning">{!! $icon('refresh', 18) !!}</span>
                        <h5 class="card-title">Change Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.reports.leads.status', $lead->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="ls-status-group">
                                @foreach($statuses as $status)
                                <input type="radio" class="btn-check" name="status" id="ls-status-{{ strtolower($status) }}" value="{{ $status }}" autocomplete="off" {{ $statusKey === strtolower($status) ? 'checked' : '' }} required>
                                <label class="btn btn-outline-{{ $statusColors[strtolower($status)] }}" for="ls-status-{{ strtolower($status) }}">{{ $status }}</label>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary w-100 ls-btn">{!! $icon('save', 15) !!}Update Status</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-lead-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var form = this.closest('.delete-lead-form');
                var leadName = this.getAttribute('data-name') || 'this lead';
                if (typeof Swal === 'undefined') {
                    if (confirm('Delete ' + leadName + '?')) { form.submit(); }
                    return;
                }
                Swal.fire({
                    title: 'Delete Lead?',
                    text: 'Are you sure you want to delete ' + leadName + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'No, Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (result) {
                    if (result.isConfirmed) { form.submit(); }
                });
            });
        });
    });
</script>
@endsection