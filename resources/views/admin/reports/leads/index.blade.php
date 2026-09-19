@extends('admin.layout.app')

@section('title','Leads Report')

@section('content')

@php
$user=auth()->user();
$isSuperAdmin=$user&&$user->hasRole('Super Admin');
$can=function($permission)use($user,$isSuperAdmin){
    return $isSuperAdmin||($user&&$user->can($permission));
};

$iconPaths=[
'eye'=>'<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
'edit'=>'<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
'trash'=>'<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/><path d="M9 6V4h6v2"/>',
'plus'=>'<path d="M12 5v14M5 12h14"/>',
'filter'=>'<path d="M4 5h16M7 12h10M10 19h4"/>',
'search'=>'<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
'refresh'=>'<path d="M20 11a8.1 8.1 0 0 0-14.8-3L3 11"/><path d="M3 5v6h6"/><path d="M4 13a8.1 8.1 0 0 0 14.8 3L21 13"/><path d="M21 19v-6h-6"/>',
'users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
'plusCircle'=>'<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
'phone'=>'<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"/>',
'userCheck'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>',
'checkCircle'=>'<circle cx="12" cy="12" r="9"/><polyline points="8 12 11 15 16 9"/>',
'xCircle'=>'<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>',
'columns'=>'<rect x="3" y="4" width="18" height="16" rx="1"/><path d="M9 4v16M15 4v16"/>',
'inbox'=>'<path d="M4 4h16v16H4z"/><path d="M4 13h4l2 3h4l2-3h4"/>',
'check'=>'<polyline points="20 6 9 17 4 12"/>',
'alert'=>'<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/>',
'sort'=>'<path d="m7 15 5 5 5-5M7 9l5-5 5 5"/>'
];

$icon=function($name,$size=16)use($iconPaths){
    return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'.$iconPaths[$name].'</svg>';
};

$summaryCards=[
['key'=>'all','title'=>'Total Leads','value'=>$totalLeads??$leads->total(),'icon'=>'users','class'=>'primary'],
['key'=>'New','title'=>'New','value'=>$newLeads??0,'icon'=>'inbox','class'=>'info'],
['key'=>'Contacted','title'=>'Contacted','value'=>$contactedLeads??0,'icon'=>'phone','class'=>'warning'],
['key'=>'Qualified','title'=>'Qualified','value'=>$qualifiedLeads??0,'icon'=>'userCheck','class'=>'primary'],
['key'=>'Converted','title'=>'Converted','value'=>$convertedLeads??0,'icon'=>'checkCircle','class'=>'success'],
['key'=>'Lost','title'=>'Lost','value'=>$lostLeads??0,'icon'=>'xCircle','class'=>'danger']
];

$toggleColumns=[
'name'=>'Lead',
'company'=>'Company',
'phone'=>'Phone',
'source'=>'Source',
'service'=>'Service',
'status'=>'Status',
'assigned'=>'Assigned To',
'created'=>'Created At'
];

$filterKeys=[
'search'=>'Search',
'status'=>'Status',
'source'=>'Source',
'service'=>'Service',
'assigned_to'=>'Assigned',
'created_by'=>'Added By',
'follow_up_from'=>'Follow Up From',
'follow_up_to'=>'Follow Up To',
'created_from'=>'Created From',
'created_to'=>'Created To'
];

$activeFilters=[];
foreach($filterKeys as $key=>$label){
    if(request()->filled($key)){
        $activeFilters[]=$label;
    }
}
@endphp

<style>
    .lead-report-page .card{border:1px solid var(--bs-border-color);border-radius:8px;box-shadow:none}
    .lead-report-page .summary-card{min-height:82px}
    .lead-report-page .summary-icon{width:38px;height:38px;border-radius:7px;display:flex;align-items:center;justify-content:center}
    .lead-report-page .summary-value{font-size:20px;font-weight:700;line-height:1}
    .lead-report-page .summary-title{font-size:12px;color:var(--bs-secondary-color);margin-bottom:5px}
    .lead-report-page .filter-header{cursor:pointer}
    .lead-report-page .filter-header .filter-arrow{transition:.2s}
    .lead-report-page .filter-header.collapsed .filter-arrow{transform:rotate(-90deg)}
    .lead-report-page .form-label{font-size:12px;font-weight:600;margin-bottom:5px}
    .lead-report-page .form-control,.lead-report-page .form-select{height:36px;font-size:13px;border-radius:6px}
    .lead-report-page .btn-sm{font-size:12px;padding:6px 10px;border-radius:6px}
    .lead-report-page .table-wrapper{overflow:auto;border:1px solid var(--bs-border-color);border-radius:7px}
    .lead-report-page .lead-table{width:100%;margin:0;border-collapse:collapse;border-spacing:0;min-width:1100px}
    .lead-report-page .lead-table th{font-size:12px;font-weight:700;white-space:nowrap;background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);padding:9px 10px;vertical-align:middle}
    .lead-report-page .lead-table td{font-size:12px;border:1px solid var(--bs-border-color);padding:8px 10px;vertical-align:middle;background:var(--bs-body-bg)}
    .lead-report-page .lead-table tbody tr:hover td{background:var(--bs-tertiary-bg)}
    .lead-report-page .lead-table .sticky-actions{position:sticky;right:0;z-index:3;border-left:1px solid var(--bs-border-color);box-shadow:none;background:var(--bs-body-bg)}
    .lead-report-page .lead-table thead .sticky-actions{z-index:5;background:var(--bs-tertiary-bg)}
    .lead-report-page .lead-table tbody tr:hover .sticky-actions{background:var(--bs-tertiary-bg)}
    .lead-report-page .lead-table .sort-btn{border:0;background:transparent;padding:0;margin:0;font:inherit;color:inherit;display:inline-flex;align-items:center;gap:5px;cursor:pointer}
    .lead-report-page .action-btn{width:29px;height:29px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:5px}
    .lead-report-page .status-select{height:29px!important;min-width:105px;font-size:11px!important;padding:2px 25px 2px 7px!important}
    .lead-report-page .lead-name{font-weight:600}
    .lead-report-page .lead-sub{font-size:10px;color:var(--bs-secondary-color);margin-top:2px}
    .lead-report-page .filter-summary{font-size:11px;color:var(--bs-secondary-color)}
    .lead-report-page .column-menu{min-width:210px;padding:8px}
    .lead-report-page .column-menu label{font-size:12px;display:flex;align-items:center;gap:7px;padding:4px 2px;cursor:pointer}
    .lead-report-page .quick-search{max-width:250px}
    .lead-report-page .empty-state{padding:40px 20px;text-align:center;color:var(--bs-secondary-color)}
    @media(max-width:767.98px){
        .lead-report-page .summary-card{min-height:75px}
        .lead-report-page .page-actions{width:100%}
        .lead-report-page .page-actions .btn{flex:1}
        .lead-report-page .quick-search{max-width:none;width:100%}
        .lead-report-page .table-wrapper{border:0;overflow:visible}
        .lead-report-page .lead-table{min-width:0;display:block}
        .lead-report-page .lead-table thead{display:none}
        .lead-report-page .lead-table tbody{display:block}
        .lead-report-page .lead-table tr{display:block;border:1px solid var(--bs-border-color);border-radius:7px;margin-bottom:10px;overflow:hidden}
        .lead-report-page .lead-table td{display:flex;justify-content:space-between;align-items:center;gap:15px;border:0;border-bottom:1px solid var(--bs-border-color);padding:8px 10px;text-align:right}
        .lead-report-page .lead-table td:last-child{border-bottom:0}
        .lead-report-page .lead-table td::before{content:attr(data-label);font-weight:600;text-align:left;color:var(--bs-secondary-color)}
        .lead-report-page .lead-table td.col-empty{display:flex!important}
        .lead-report-page .lead-table .sticky-actions{position:static;border-left:0!important;box-shadow:none}
        .lead-report-page .lead-table .sticky-actions::before{display:none}
    }
</style>

<div class="container-fluid lead-report-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="mb-1">Leads Report</h4>
            <div class="text-muted small">Manage and review leads</div>
        </div>
        <div class="d-flex align-items-center gap-2 page-actions">
            @if($can('Leads Restore'))
            <a href="{{ route('admin.reports.leads.trash') }}" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1">
                {!! $icon('trash',15) !!}
                Trash
            </a>
            @endif
            @if($can('Leads Create'))
            <a href="{{ route('admin.reports.leads.create') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                {!! $icon('plus',15) !!}
                Add Lead
            </a>
            @endif
        </div>
    </div>

    <div class="row g-2 mb-3">
        @foreach($summaryCards as $card)
        @php
        $isActive=$card['key']==='all'
        ?request()->missing('status')
        :request('status')===$card['key'];
        @endphp
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ $card['key']==='all'?route('admin.reports.leads'):route('admin.reports.leads',['status'=>$card['key']]) }}" class="text-decoration-none">
                <div class="card summary-card h-100 {{ $isActive?'border-primary':'' }}">
                    <div class="card-body p-2 d-flex align-items-center gap-2">
                        <div class="summary-icon bg-{{ $card['class'] }}-subtle text-{{ $card['class'] }}">
                            {!! $icon($card['icon'],18) !!}
                        </div>
                        <div>
                            <div class="summary-title">{{ $card['title'] }}</div>
                            <div class="summary-value text-body">{{ number_format($card['value']) }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="card mb-3">
        <div class="card-header py-2 d-flex justify-content-between align-items-center filter-header collapsed" data-bs-toggle="collapse" data-bs-target="#leadFilters" aria-expanded="false">
            <div class="d-flex align-items-center gap-2">
                <span>{!! $icon('filter',15) !!}</span>
                <span class="fw-semibold small">Filters</span>
                @if(count($activeFilters))
                <span class="badge bg-primary">{{ count($activeFilters) }}</span>
                @endif
            </div>
            <span class="filter-arrow">{!! $icon('sort',15) !!}</span>
        </div>
        <div id="leadFilters" class="collapse">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.reports.leads') }}">
                    <div class="row g-2">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, company, email, phone">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                @foreach(($statuses??[]) as $status)
                                <option value="{{ $status }}" {{ request('status')===$status?'selected':'' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Source</label>
                            <select name="source" class="form-select">
                                <option value="">All</option>
                                @foreach(($sources??[]) as $source)
                                <option value="{{ $source }}" {{ request('source')===$source?'selected':'' }}>{{ $source }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Service</label>
                            <select name="service" class="form-select">
                                <option value="">All</option>
                                @foreach(($services??[]) as $service)
                                <option value="{{ $service }}" {{ request('service')===$service?'selected':'' }}>{{ $service }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">All</option>
                                @foreach(($users??[]) as $filterUser)
                                <option value="{{ $filterUser->id }}" {{ (string)request('assigned_to')===(string)$filterUser->id?'selected':'' }}>
                                    {{ trim($filterUser->first_name.' '.$filterUser->last_name) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if($isSuperAdmin)
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Added By</label>
                            <select name="created_by" class="form-select">
                                <option value="">All</option>
                                @foreach(($users??[]) as $filterUser)
                                <option value="{{ $filterUser->id }}" {{ (string)request('created_by')===(string)$filterUser->id?'selected':'' }}>
                                    {{ trim($filterUser->first_name.' '.$filterUser->last_name) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Follow Up From</label>
                            <input type="date" name="follow_up_from" value="{{ request('follow_up_from') }}" class="form-control">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Follow Up To</label>
                            <input type="date" name="follow_up_to" value="{{ request('follow_up_to') }}" class="form-control">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Created From</label>
                            <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control">
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label">Created To</label>
                            <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control">
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 align-items-end mt-2">
                            <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                {!! $icon('search',14) !!}
                                Filter
                            </button>
                            <a href="{{ route('admin.reports.leads') }}" class="btn btn-light border btn-sm d-inline-flex align-items-center gap-1">
                                {!! $icon('refresh',14) !!}
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="fw-semibold small">Lead List</div>
                    <div class="filter-summary">
                        {{ $leads->total() }} total records
                        @if(count($activeFilters))
                        · {{ implode(', ',$activeFilters) }}
                        @endif
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group input-group-sm quick-search">
                        <span class="input-group-text">{!! $icon('search',14) !!}</span>
                        <input type="text" id="quickSearch" class="form-control" placeholder="Search current page">
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-light border btn-sm dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                            {!! $icon('columns',14) !!}
                            Columns
                        </button>
                        <div class="dropdown-menu dropdown-menu-end column-menu" id="columnMenu">
                            @foreach($toggleColumns as $columnKey=>$columnTitle)
                            <label>
                                <input type="checkbox" class="column-toggle" data-column="{{ $columnKey }}" checked>
                                {{ $columnTitle }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="lead-table" id="leadTable">
                <thead>
                    <tr>
                        <th data-column="index">#</th>
                        <th data-column="name">
                            <button type="button" class="sort-btn" data-sort="name">
                                Lead {!! $icon('sort',12) !!}
                            </button>
                        </th>
                        <th data-column="company">
                            <button type="button" class="sort-btn" data-sort="company">
                                Company {!! $icon('sort',12) !!}
                            </button>
                        </th>
                        <th data-column="phone">Phone</th>
                        <th data-column="source">Source</th>
                        <th data-column="service">Service</th>
                        <th data-column="status">Status</th>
                        <th data-column="assigned">Assigned To</th>
                        <th data-column="created">Created At</th>
                        @if($isSuperAdmin)
                        <th data-column="created_by">Added By</th>
                        @endif
                        <th class="sticky-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="leadTableBody">
                    @forelse($leads as $index=>$lead)
                    <tr>
                        <td data-label="#" data-column="index">
                            {{ $leads->firstItem()+$index }}
                        </td>
                        <td data-label="Lead" data-column="name" data-sort-value="{{ strtolower($lead->name??'') }}">
                            <div class="lead-name">{{ $lead->name }}</div>
                            @if($lead->email)
                            <div class="lead-sub">{{ $lead->email }}</div>
                            @endif
                        </td>
                        <td data-label="Company" data-column="company" data-sort-value="{{ strtolower($lead->company_name??'') }}">
                            {{ $lead->company_name?:'—' }}
                        </td>
                        <td data-label="Phone" data-column="phone" data-sort-value="{{ $lead->phone??'' }}">
                            {{ $lead->phone?:'—' }}
                        </td>
                        <td data-label="Source" data-column="source" data-sort-value="{{ strtolower($lead->source??'') }}">
                            {{ $lead->source?:'—' }}
                        </td>
                        <td data-label="Service" data-column="service" data-sort-value="{{ strtolower($lead->service??'') }}">
                            {{ $lead->service?:'—' }}
                        </td>
                        <td data-label="Status" data-column="status" data-sort-value="{{ strtolower($lead->status??'') }}">
                            @if($can('Leads Status'))
                            <form method="POST" action="{{ route('admin.reports.leads.status',$lead->id) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select status-select" onchange="this.form.submit()">
                                    @foreach(($statuses??['New','Contacted','Qualified','Converted','Lost']) as $status)
                                    <option value="{{ $status }}" {{ $lead->status===$status?'selected':'' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary">{{ $lead->status?:'—' }}</span>
                            @endif
                        </td>
                        <td data-label="Assigned To" data-column="assigned" data-sort-value="{{ strtolower(optional($lead->assignedUser)->first_name.' '.optional($lead->assignedUser)->last_name) }}">
                            @if($lead->assignedUser)
                            {{ trim($lead->assignedUser->first_name.' '.$lead->assignedUser->last_name) }}
                            @else
                            —
                            @endif
                        </td>
                        <td data-label="Created At" data-column="created" data-sort-value="{{ optional($lead->created_at)->timestamp }}">
                            {{ optional($lead->created_at)->format('d M Y') }}
                        </td>
                        @if($isSuperAdmin)
                        <td data-label="Added By" data-column="created_by" data-sort-value="{{ strtolower(optional($lead->creator)->first_name.' '.optional($lead->creator)->last_name) }}">
                            @if($lead->creator)
                            {{ trim($lead->creator->first_name.' '.$lead->creator->last_name) }}
                            @else
                            —
                            @endif
                        </td>
                        @endif
                        <td data-label="Actions" class="sticky-actions">
                            <div class="d-flex justify-content-end gap-1">
                                @if($can('Leads View'))
                                <a href="{{ route('admin.reports.leads.show',$lead->id) }}" class="btn btn-light border action-btn" title="View">
                                    {!! $icon('eye',14) !!}
                                </a>
                                @endif
                                @if($can('Leads Edit'))
                                <a href="{{ route('admin.reports.leads.edit',$lead->id) }}" class="btn btn-warning-subtle text-warning border action-btn" title="Edit">
                                    {!! $icon('edit',14) !!}
                                </a>
                                @endif
                                @if($can('Leads Delete'))
                                <form method="POST" action="{{ route('admin.reports.leads.delete',$lead->id) }}" class="delete-form d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger-subtle text-danger border action-btn delete-btn" title="Delete">
                                        {!! $icon('trash',14) !!}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isSuperAdmin?11:10 }}" class="col-empty">
                            <div class="empty-state w-100">
                                <div class="mb-2">{!! $icon('inbox',28) !!}</div>
                                <div class="fw-semibold">No leads found</div>
                                <div class="small">Try changing your filters or add a new lead.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
        <div class="card-footer py-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Showing {{ $leads->firstItem()??0 }} to {{ $leads->lastItem()??0 }} of {{ $leads->total() }}
                </div>
                <div>
                    {{ $leads->withQueryString()->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded',function(){
        const table=document.getElementById('leadTable');
        const quickSearch=document.getElementById('quickSearch');
        const columnMenu=document.getElementById('columnMenu');
        const storageKey='denmaq_report_leads_columns';

        document.querySelectorAll('.delete-btn').forEach(function(button){
            button.addEventListener('click',function(){
                const form=this.closest('.delete-form');
                Swal.fire({
                    title:'Delete Lead?',
                    text:'This lead will be moved to trash.',
                    icon:'warning',
                    showCancelButton:true,
                    confirmButtonText:'Yes, Delete',
                    cancelButtonText:'Cancel',
                    reverseButtons:true
                }).then(function(result){
                    if(result.isConfirmed){
                        form.submit();
                    }
                });
            });
        });

        if(quickSearch){
            quickSearch.addEventListener('input',function(){
                const value=this.value.toLowerCase().trim();
                let count=0;
                document.querySelectorAll('#leadTableBody tr').forEach(function(row){
                    if(row.querySelector('.empty-state'))return;
                    const match=row.innerText.toLowerCase().includes(value);
                    row.style.display=match?'':'none';
                    if(match)count++;
                });
            });
        }

        function applyColumnState(){
            let state={};
            try{
                state=JSON.parse(localStorage.getItem(storageKey)||'{}');
            }catch(e){
                state={};
            }
            document.querySelectorAll('.column-toggle').forEach(function(input){
                const key=input.dataset.column;
                const visible=state[key]!==false;
                input.checked=visible;
                document.querySelectorAll('[data-column="'+key+'"]').forEach(function(cell){
                    cell.style.display=visible?'':'none';
                });
            });
        }

        document.querySelectorAll('.column-toggle').forEach(function(input){
            input.addEventListener('change',function(){
                let state={};
                try{
                    state=JSON.parse(localStorage.getItem(storageKey)||'{}');
                }catch(e){
                    state={};
                }
                state[this.dataset.column]=this.checked;
                localStorage.setItem(storageKey,JSON.stringify(state));
                applyColumnState();
            });
        });

        applyColumnState();

        document.querySelectorAll('.sort-btn').forEach(function(button){
            button.addEventListener('click',function(){
                const key=this.dataset.sort;
                const tbody=document.getElementById('leadTableBody');
                const rows=Array.from(tbody.querySelectorAll('tr')).filter(function(row){
                    return !row.querySelector('.empty-state');
                });
                const currentDirection=this.dataset.direction==='asc'?'desc':'asc';
                this.dataset.direction=currentDirection;
                rows.sort(function(a,b){
                    const cellA=a.querySelector('[data-column="'+key+'"]');
                    const cellB=b.querySelector('[data-column="'+key+'"]');
                    const valueA=(cellA?.dataset.sortValue||cellA?.innerText||'').trim().toLowerCase();
                    const valueB=(cellB?.dataset.sortValue||cellB?.innerText||'').trim().toLowerCase();
                    const numberA=parseFloat(valueA);
                    const numberB=parseFloat(valueB);
                    if(!Number.isNaN(numberA)&&!Number.isNaN(numberB)&&valueA!==''&&valueB!==''){
                        return currentDirection==='asc'?numberA-numberB:numberB-numberA;
                    }
                    return currentDirection==='asc'
                    ?valueA.localeCompare(valueB)
                    :valueB.localeCompare(valueA);
                });
                rows.forEach(function(row){
                    tbody.appendChild(row);
                });
            });
        });

        document.querySelectorAll('#leadFilters select,#leadFilters input[type="date"]').forEach(function(element){
            element.addEventListener('change',function(){
                const form=this.closest('form');
                if(form){
                    form.submit();
                }
            });
        });
    });
</script>

@endsection