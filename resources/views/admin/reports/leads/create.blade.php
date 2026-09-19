@extends('admin.layout.app')

@section('title', 'Create Lead')

@section('content')

@php
$user = auth()->user();
$isSuperAdmin = $user && $user->hasRole('Super Admin');

$statuses = ['New', 'Contacted', 'Qualified', 'Converted', 'Lost'];

$statusColors = [
'New'       => 'info',
'Contacted' => 'warning',
'Qualified' => 'secondary',
'Converted' => 'success',
'Lost'      => 'danger',
];

$iconPaths = [
'arrowLeft' => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
'userPlus'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>',
'user'      => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
'phone'     => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
'fileText'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
'info'      => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
'alert'     => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
'save'      => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
'calendar'  => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
];

$icon = function ($name, $size = 16) use ($iconPaths) {
    return '<svg class="lf-svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
    . ($iconPaths[$name] ?? '') . '</svg>';
};

$creatorName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
$creatorInitial = strtoupper(mb_substr($creatorName !== '' ? $creatorName : 'U', 0, 1));
@endphp

<style>
    .lf-svg {
        display: inline-block;
        flex-shrink: 0;
        vertical-align: middle;
    }

    .lf-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .lf-card {
        margin-bottom: 16px;
    }

    .lf-card .card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: transparent;
        border-bottom: 1px solid var(--bs-border-color-translucent, #e9ebec);
    }

    .lf-card-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .lf-card .card-title {
        font-size: 14px;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .lf-card .card-subtitle {
        font-size: 11.5px;
        color: var(--bs-secondary-color);
        margin: 2px 0 0;
    }

    .lf-card .card-body {
        padding: 16px;
    }

    .lf-card .form-label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .lf-card .form-control,
    .lf-card .form-select {
        font-size: 13px;
    }

    .lf-status-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .lf-status-group .btn {
        border-radius: 999px;
        font-size: 12px;
        padding: 5px 14px;
        font-weight: 600;
    }

    .lf-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .lf-chip {
        border: 1px solid var(--bs-border-color);
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-radius: 999px;
        padding: 2px 10px;
        font-size: 11px;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease;
    }

    .lf-chip:hover {
        background: var(--bs-primary-bg-subtle);
        border-color: var(--bs-primary);
        color: var(--bs-primary);
    }

    .lf-counter {
        font-size: 11px;
        color: var(--bs-secondary-color);
        text-align: right;
        margin-top: 4px;
    }

    .lf-sticky {
        position: sticky;
        top: 80px;
    }

    .lf-creator {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        background: var(--bs-primary-bg-subtle);
        margin-bottom: 14px;
    }

    .lf-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        background: var(--bs-primary);
        color: #fff;
        flex-shrink: 0;
    }

    .lf-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 9px 0;
        font-size: 12.5px;
        border-bottom: 1px solid var(--bs-border-color-translucent, #e9ebec);
    }

    .lf-info-row:last-child {
        border-bottom: 0;
    }

    .lf-info-row .value {
        font-weight: 600;
        text-align: right;
        word-break: break-all;
    }

    .lf-note {
        font-size: 12px;
        color: var(--bs-secondary-color);
        margin: 12px 0 0;
    }

    @media (max-width: 1199.98px) {
        .lf-sticky {
            position: static;
        }
    }
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="mb-1">Create Lead</h4>
                    <p class="text-muted mb-0">Add a new lead</p>
                </div>

                <a href="{{ route('admin.reports.leads') }}" class="btn btn-light lf-btn">
                    {!! $icon('arrowLeft', 16) !!}
                    Back to Leads
                </a>
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

    <form method="POST" action="{{ route('admin.reports.leads.store') }}" id="leadCreateForm">
        @csrf

        <div class="row">
            <div class="col-xl-8">
                <div class="card lf-card">
                    <div class="card-header">
                        <span class="lf-card-icon bg-primary-subtle text-primary">{!! $icon('user', 18) !!}</span>
                        <div>
                            <h5 class="card-title">Lead Information</h5>
                            <p class="card-subtitle">Lead ka naam aur company</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="lf-name">
                                    Lead Name <span class="text-danger">*</span>
                                </label>
                                <input
                                type="text"
                                id="lf-name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Enter lead name"
                                required
                                autofocus
                                >
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="lf-company">Company Name</label>
                                <input
                                type="text"
                                id="lf-company"
                                name="company_name"
                                class="form-control @error('company_name') is-invalid @enderror"
                                value="{{ old('company_name') }}"
                                placeholder="Enter company name"
                                >
                                @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="lf-email">Email</label>
                                <input
                                type="email"
                                id="lf-email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="Enter email address"
                                >
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card lf-card">
                    <div class="card-header">
                        <span class="lf-card-icon bg-success-subtle text-success">{!! $icon('phone', 18) !!}</span>
                        <div>
                            <h5 class="card-title">Contact Details</h5>
                            <p class="card-subtitle">Lead se kaise baat karni hai</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="lf-phone">
                                    Phone <span class="text-danger">*</span>
                                </label>
                                <input
                                type="tel"
                                id="lf-phone"
                                name="phone"
                                class="form-control lf-phone @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}"
                                placeholder="Enter phone number"
                                inputmode="tel"
                                required
                                >
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="lf-alt-phone">Alternate Phone</label>
                                <input
                                type="tel"
                                id="lf-alt-phone"
                                name="alternate_phone"
                                class="form-control lf-phone @error('alternate_phone') is-invalid @enderror"
                                value="{{ old('alternate_phone') }}"
                                placeholder="Enter alternate phone"
                                inputmode="tel"
                                >
                                @error('alternate_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card lf-card">
                    <div class="card-header">
                        <span class="lf-card-icon bg-warning-subtle text-warning">{!! $icon('briefcase', 18) !!}</span>
                        <div>
                            <h5 class="card-title">Lead Details</h5>
                            <p class="card-subtitle">Source, service, status aur follow-up</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="lf-source">Source</label>
                                <input
                                type="text"
                                id="lf-source"
                                name="source"
                                class="form-control @error('source') is-invalid @enderror"
                                value="{{ old('source') }}"
                                placeholder="Website, Facebook, Referral, etc."
                                >
                                @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="lf-service">Service</label>
                                <input
                                type="text"
                                id="lf-service"
                                name="service"
                                class="form-control @error('service') is-invalid @enderror"
                                value="{{ old('service') }}"
                                placeholder="Enter required service"
                                >
                                @error('service')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>

                                <div class="lf-status-group">
                                    @foreach($statuses as $status)
                                    <input
                                    type="radio"
                                    class="btn-check"
                                    name="status"
                                    id="lf-status-{{ strtolower($status) }}"
                                    value="{{ $status }}"
                                    autocomplete="off"
                                    {{ old('status', 'New') == $status ? 'checked' : '' }}
                                    required
                                    >
                                    <label class="btn btn-outline-{{ $statusColors[$status] }}" for="lf-status-{{ strtolower($status) }}">
                                        {{ $status }}
                                    </label>
                                    @endforeach
                                </div>

                                @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="lf-assigned">Assigned To</label>
                                <select
                                id="lf-assigned"
                                name="assigned_to"
                                class="form-select @error('assigned_to') is-invalid @enderror"
                                >
                                <option value="">Select User</option>

                                @foreach($users as $reportUser)
                                <option value="{{ $reportUser->id }}" {{ old('assigned_to') == $reportUser->id ? 'selected' : '' }}>
                                    {{ trim($reportUser->first_name . ' ' . $reportUser->last_name) }}
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="lf-budget">Budget</label>
                            <input
                            type="number"
                            id="lf-budget"
                            name="budget"
                            class="form-control @error('budget') is-invalid @enderror"
                            value="{{ old('budget') }}"
                            placeholder="Enter budget"
                            min="0"
                            step="0.01"
                            >
                            @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="lf-followup">Follow Up Date</label>
                            <input
                            type="date"
                            id="lf-followup"
                            name="follow_up_date"
                            class="form-control @error('follow_up_date') is-invalid @enderror"
                            value="{{ old('follow_up_date') }}"
                            >
                            @error('follow_up_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="lf-chips">
                                <button type="button" class="lf-chip" data-days="0">Today</button>
                                <button type="button" class="lf-chip" data-days="1">Tomorrow</button>
                                <button type="button" class="lf-chip" data-days="3">+3 days</button>
                                <button type="button" class="lf-chip" data-days="7">+1 week</button>
                                <button type="button" class="lf-chip" data-days="clear">Clear</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card lf-card">
                <div class="card-header">
                    <span class="lf-card-icon bg-secondary-subtle text-secondary">{!! $icon('fileText', 18) !!}</span>
                    <div>
                        <h5 class="card-title">Notes</h5>
                        <p class="card-subtitle">Lead ke baare mein zaroori baatein</p>
                    </div>
                </div>

                <div class="card-body">
                    <textarea
                    name="notes"
                    id="lf-notes"
                    rows="5"
                    maxlength="2000"
                    class="form-control @error('notes') is-invalid @enderror"
                    placeholder="Enter lead notes"
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="lf-counter"><span id="lf-notes-count">0</span> / 2000</div>
                </div>
            </div>

        </div>

        <div class="col-xl-4">
            <div class="lf-sticky">

                <div class="card lf-card">
                    <div class="card-header">
                        <span class="lf-card-icon bg-info-subtle text-info">{!! $icon('info', 18) !!}</span>
                        <div>
                            <h5 class="card-title">Creator Information</h5>
                            <p class="card-subtitle">Yeh lead automatically aapke naam se judegi</p>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="lf-creator">
                            <span class="lf-avatar">{{ $creatorInitial }}</span>
                            <div>
                                <div class="fw-semibold">{{ $creatorName !== '' ? $creatorName : 'User' }}</div>
                                <small class="text-muted">Added By</small>
                            </div>
                        </div>

                        <div class="lf-info-row">
                            <span class="text-muted">Created By</span>
                            <span class="value">{{ $creatorName !== '' ? $creatorName : '-' }}</span>
                        </div>

                        <div class="lf-info-row">
                            <span class="text-muted">Email</span>
                            <span class="value">{{ $user->email }}</span>
                        </div>

                        <div class="lf-info-row">
                            <span class="text-muted">Role</span>
                            <span class="value">{{ $isSuperAdmin ? 'Super Admin' : 'User' }}</span>
                        </div>

                    </div>
                </div>

                <div class="card lf-card">
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 lf-btn" id="lf-submit">
                                {!! $icon('save', 16) !!}
                                <span>Save Lead</span>
                            </button>

                            <a href="{{ route('admin.reports.leads') }}" class="btn btn-light">Cancel</a>
                        </div>

                        <p class="lf-note mb-0">
                            <span class="text-danger">*</span> wale fields zaroori hain.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

</form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        var followInput = document.getElementById('lf-followup');

        function toInputDate(date) {
            var y = date.getFullYear();
            var m = String(date.getMonth() + 1).padStart(2, '0');
            var d = String(date.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        }

        document.querySelectorAll('.lf-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var value = chip.getAttribute('data-days');

                if (value === 'clear') {
                    followInput.value = '';
                    return;
                }

                var date = new Date();
                date.setDate(date.getDate() + parseInt(value, 10));
                followInput.value = toInputDate(date);
            });
        });

        document.querySelectorAll('.lf-phone').forEach(function (input) {
            input.addEventListener('input', function () {
                var cleaned = input.value.replace(/[^0-9+\-()\s]/g, '');
                if (cleaned !== input.value) {
                    input.value = cleaned;
                }
            });
        });

        var notes = document.getElementById('lf-notes');
        var counter = document.getElementById('lf-notes-count');

        function updateCount() {
            counter.textContent = notes.value.length;
        }

        if (notes && counter) {
            notes.addEventListener('input', updateCount);
            updateCount();
        }

        /* 4. Double submit se bachao */
        var form = document.getElementById('leadCreateForm');
        var submitBtn = document.getElementById('lf-submit');

        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                if (!form.checkValidity()) {
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.querySelector('span').textContent = 'Saving...';
            });
        }

    });
</script>

@endsection