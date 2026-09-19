@extends('admin.layout.app')
@section('title', 'Edit Lead')
@section('content')
@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');
$statuses = ['New', 'Contacted', 'Qualified', 'Converted', 'Lost'];
$statusColors = ['New' => 'info', 'Contacted' => 'warning', 'Qualified' => 'secondary', 'Converted' => 'success', 'Lost' => 'danger'];
$currentStatus = old('status', $lead->status);
$savedColor = $statusColors[ucfirst(strtolower($lead->status ?? 'New'))] ?? 'dark';
$iconPaths = [
'arrowLeft' => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
'fileText' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
'info' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
'alert' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
];
$icon = function ($name, $size = 16) use ($iconPaths) {
    return '<svg class="le-svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($iconPaths[$name] ?? '') . '</svg>';
};
$creatorName = $lead->creator ? trim($lead->creator->first_name . ' ' . $lead->creator->last_name) : '';
$initial = strtoupper(mb_substr(trim($lead->name ?? '?'), 0, 1));
$followValue = old('follow_up_date', $lead->follow_up_date ? $lead->follow_up_date->format('Y-m-d') : '');
@endphp
<style>
    .le-svg{display:inline-block;flex-shrink:0;vertical-align:middle}
    .le-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px}
    .le-card{margin-bottom:16px}
    .le-card .card-header{display:flex;align-items:center;gap:10px;padding:12px 16px;background:transparent;border-bottom:1px solid var(--bs-border-color-translucent,#e9ebec)}
    .le-card .card-body{padding:16px}
    .le-card-icon{width:34px;height:34px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
    .le-card .card-title{font-size:14px;margin:0;line-height:1.2}
    .le-card .card-subtitle{font-size:11.5px;color:var(--bs-secondary-color);margin:2px 0 0}
    .le-card .form-label{font-size:12px;font-weight:600;margin-bottom:4px}
    .le-card .form-control,.le-card .form-select{font-size:13px}
    .le-status-group{display:flex;flex-wrap:wrap;gap:8px}
    .le-status-group .btn{border-radius:999px;font-size:12px;padding:5px 14px;font-weight:600}
    .le-chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
    .le-chip{border:1px solid var(--bs-border-color);background:var(--bs-body-bg);color:var(--bs-body-color);border-radius:999px;padding:2px 10px;font-size:11px;cursor:pointer;transition:background .15s ease,border-color .15s ease}
    .le-chip:hover{background:var(--bs-primary-bg-subtle);border-color:var(--bs-primary);color:var(--bs-primary)}
    .le-counter{font-size:11px;color:var(--bs-secondary-color);text-align:right;margin-top:4px}
    .le-sticky{position:sticky;top:80px}
    .le-summary{display:flex;align-items:center;gap:12px;padding:12px;border-radius:8px;background:var(--bs-primary-bg-subtle);margin-bottom:14px}
    .le-avatar{width:44px;height:44px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;background:var(--bs-primary);color:#fff;flex-shrink:0}
    .le-pill{display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600}
    .le-info-row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:9px 0;font-size:12.5px;border-bottom:1px solid var(--bs-border-color-translucent,#e9ebec)}
    .le-info-row:last-child{border-bottom:0}
    .le-info-row .value{font-weight:600;text-align:right;word-break:break-word}
    @media (max-width:1199.98px){.le-sticky{position:static}}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="page-title mb-0">Edit Lead</h4>
                    <p class="text-muted mb-0">Update lead information</p>
                </div>
                <a href="{{ route('admin.reports.leads.show', $lead->id) }}" class="btn btn-secondary le-btn">{!! $icon('arrowLeft', 15) !!}Back</a>
            </div>
        </div>
    </div>
    @if($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <span class="pt-1">{!! $icon('alert', 18) !!}</span>
        <div>
            <strong>Form mein kuch galtiyan hain:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    <form action="{{ route('admin.reports.leads.update', $lead->id) }}" method="POST" id="leadEditForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-8">
                <div class="card le-card">
                    <div class="card-header">
                        <span class="le-card-icon bg-primary-subtle text-primary">{!! $icon('user', 18) !!}</span>
                        <div><h5 class="card-title">Lead Information</h5><p class="card-subtitle">Lead ka naam aur company</p></div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="le-name">Lead Name <span class="text-danger">*</span></label>
                                <input type="text" id="le-name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $lead->name) }}" placeholder="Enter lead name" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-company">Company Name</label>
                                <input type="text" id="le-company" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $lead->company_name) }}" placeholder="Enter company name">
                                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-email">Email</label>
                                <input type="email" id="le-email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lead->email) }}" placeholder="Enter email address">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card le-card">
                    <div class="card-header">
                        <span class="le-card-icon bg-success-subtle text-success">{!! $icon('phone', 18) !!}</span>
                        <div><h5 class="card-title">Contact Details</h5><p class="card-subtitle">Lead se kaise baat karni hai</p></div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="le-phone">Phone <span class="text-danger">*</span></label>
                                <input type="tel" id="le-phone" name="phone" class="form-control le-phone @error('phone') is-invalid @enderror" value="{{ old('phone', $lead->phone) }}" placeholder="Enter phone number" inputmode="tel" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-alt-phone">Alternate Phone</label>
                                <input type="tel" id="le-alt-phone" name="alternate_phone" class="form-control le-phone @error('alternate_phone') is-invalid @enderror" value="{{ old('alternate_phone', $lead->alternate_phone) }}" placeholder="Enter alternate phone" inputmode="tel">
                                @error('alternate_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card le-card">
                    <div class="card-header">
                        <span class="le-card-icon bg-warning-subtle text-warning">{!! $icon('briefcase', 18) !!}</span>
                        <div><h5 class="card-title">Lead Details</h5><p class="card-subtitle">Source, service, status aur follow-up</p></div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="le-source">Source</label>
                                <input type="text" id="le-source" name="source" class="form-control @error('source') is-invalid @enderror" value="{{ old('source', $lead->source) }}" placeholder="Enter lead source">
                                @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-service">Service</label>
                                <input type="text" id="le-service" name="service" class="form-control @error('service') is-invalid @enderror" value="{{ old('service', $lead->service) }}" placeholder="Enter service">
                                @error('service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="le-status-group">
                                    @foreach($statuses as $status)
                                    <input type="radio" class="btn-check" name="status" id="le-status-{{ strtolower($status) }}" value="{{ $status }}" autocomplete="off" {{ strtolower((string) $currentStatus) === strtolower($status) ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-{{ $statusColors[$status] }}" for="le-status-{{ strtolower($status) }}">{{ $status }}</label>
                                    @endforeach
                                </div>
                                @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-assigned">Assigned To</label>
                                <select id="le-assigned" name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                    <option value="">Not Assigned</option>
                                    @foreach($users as $item)
                                    <option value="{{ $item->id }}" {{ (string) old('assigned_to', $lead->assigned_to) === (string) $item->id ? 'selected' : '' }}>{{ trim($item->first_name . ' ' . $item->last_name) }}@if($item->email) - {{ $item->email }}@endif</option>
                                    @endforeach
                                </select>
                                @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-budget">Budget</label>
                                <input type="number" id="le-budget" name="budget" class="form-control @error('budget') is-invalid @enderror" value="{{ old('budget', $lead->budget) }}" placeholder="Enter budget" min="0" step="0.01">
                                @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="le-followup">Follow Up Date</label>
                                <input type="date" id="le-followup" name="follow_up_date" class="form-control @error('follow_up_date') is-invalid @enderror" value="{{ $followValue }}">
                                @error('follow_up_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="le-chips">
                                    <button type="button" class="le-chip" data-days="0">Today</button>
                                    <button type="button" class="le-chip" data-days="1">Tomorrow</button>
                                    <button type="button" class="le-chip" data-days="3">+3 days</button>
                                    <button type="button" class="le-chip" data-days="7">+1 week</button>
                                    <button type="button" class="le-chip" data-days="clear">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card le-card">
                    <div class="card-header">
                        <span class="le-card-icon bg-secondary-subtle text-secondary">{!! $icon('fileText', 18) !!}</span>
                        <div><h5 class="card-title">Notes</h5><p class="card-subtitle">Lead ke baare mein zaroori baatein</p></div>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" id="le-notes" rows="5" maxlength="2000" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter notes">{{ old('notes', $lead->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="le-counter"><span id="le-notes-count">0</span> / 2000</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="le-sticky">
                    <div class="card le-card">
                        <div class="card-header">
                            <span class="le-card-icon bg-info-subtle text-info">{!! $icon('info', 18) !!}</span>
                            <div><h5 class="card-title">Lead Summary</h5><p class="card-subtitle">Abhi saved details</p></div>
                        </div>
                        <div class="card-body">
                            <div class="le-summary">
                                <span class="le-avatar">{{ $initial }}</span>
                                <div>
                                    <div class="fw-semibold">{{ $lead->name ?: '-' }}</div>
                                    <span class="le-pill bg-{{ $savedColor }}-subtle text-{{ $savedColor }}">{{ ucfirst($lead->status ?: 'New') }}</span>
                                </div>
                            </div>
                            <div class="le-info-row"><span class="text-muted">Lead ID</span><span class="value">#{{ $lead->id }}</span></div>
                            <div class="le-info-row">
                                <span class="text-muted">Added By</span>
                                <span class="value">
                                    @if($lead->creator){{ $creatorName }}@if($lead->creator->email)<small class="d-block text-muted fw-normal">{{ $lead->creator->email }}</small>@endif
                                    @else<span class="text-muted fw-normal">Unknown</span>@endif
                                </span>
                            </div>
                            <div class="le-info-row"><span class="text-muted">Created At</span><span class="value">{{ $lead->created_at ? $lead->created_at->format('d M Y, h:i A') : '-' }}</span></div>
                            <div class="le-info-row"><span class="text-muted">Last Updated</span><span class="value">{{ $lead->updated_at ? $lead->updated_at->format('d M Y, h:i A') : '-' }}</span></div>
                        </div>
                    </div>
                    <div class="card le-card">
                        <div class="card-body">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1 le-btn" id="le-submit">{!! $icon('save', 16) !!}<span>Update Lead</span></button>
                                <a href="{{ route('admin.reports.leads.show', $lead->id) }}" class="btn btn-light">Cancel</a>
                            </div>
                            <p class="text-muted small mt-3 mb-0"><span class="text-danger">*</span> wale fields zaroori hain.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var followInput = document.getElementById('le-followup');
        function toInputDate(date) {
            return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
        }
        document.querySelectorAll('.le-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var value = chip.getAttribute('data-days');
                if (value === 'clear') { followInput.value = ''; return; }
                var date = new Date();
                date.setDate(date.getDate() + parseInt(value, 10));
                followInput.value = toInputDate(date);
            });
        });
        document.querySelectorAll('.le-phone').forEach(function (input) {
            input.addEventListener('input', function () {
                var cleaned = input.value.replace(/[^0-9+\-()\s]/g, '');
                if (cleaned !== input.value) { input.value = cleaned; }
            });
        });
        var notes = document.getElementById('le-notes');
        var counter = document.getElementById('le-notes-count');
        function updateCount() { counter.textContent = notes.value.length; }
        if (notes && counter) { notes.addEventListener('input', updateCount); updateCount(); }
        var form = document.getElementById('leadEditForm');
        var submitBtn = document.getElementById('le-submit');
        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.querySelector('span').textContent = 'Updating...';
            });
        }
    });
</script>
@endsection