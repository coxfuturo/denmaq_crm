@extends('admin.layout.app')
@section('title','Leads Trash')
@section('content')
@php
$user=auth()->user();
$isSuperAdmin=$user&&$user->hasRole('Super Admin');
$can=function($permission)use($user,$isSuperAdmin){
	return $isSuperAdmin||($user&&$user->can($permission));
};
$statusColors=[
'new'=>'info',
'contacted'=>'warning',
'qualified'=>'secondary',
'converted'=>'success',
'lost'=>'danger'
];
$iconPaths=[
'arrowLeft'=>'<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
'trash'=>'<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
'restore'=>'<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>',
'search'=>'<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
'refresh'=>'<polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>',
'inbox'=>'<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
'check'=>'<polyline points="20 6 9 17 4 12"/>',
'alert'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'
];
$icon=function($name,$size=16)use($iconPaths){
	return '<svg class="lt-svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($iconPaths[$name]??'').'</svg>';
};
$colCount=$isSuperAdmin?8:7;
@endphp
<style>
	.lt-svg{display:inline-block;flex-shrink:0;vertical-align:middle}
	.lt-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px}
	.lt-card{border:1px solid var(--bs-border-color);border-radius:8px;overflow:hidden}
	.lt-card .card-header{padding:10px 14px;border-bottom:1px solid var(--bs-border-color)}
	.lt-card .card-body{padding:0}
	.lt-toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
	.lt-search{position:relative;width:250px;max-width:100%}
	.lt-search .lt-svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--bs-secondary-color);z-index:2}
	.lt-search input{width:100%;height:32px;padding:4px 8px 4px 30px;font-size:12px}
	.lt-toolbar .btn{height:32px;font-size:12px;padding:4px 12px}
	.lt-wrapper{width:100%;overflow-x:auto}
	.lt-table{width:100%;min-width:900px;margin:0;border-collapse:collapse;border-spacing:0;font-size:12px;border:0}
	.lt-table thead th{height:38px;padding:7px 10px;font-size:11px;font-weight:600;white-space:nowrap;vertical-align:middle;background:var(--bs-tertiary-bg,#f3f6f9);border:1px solid var(--bs-border-color);text-align:left}
	.lt-table tbody td{height:50px;padding:6px 10px;white-space:nowrap;vertical-align:middle;line-height:1.25;border:1px solid var(--bs-border-color);background:var(--bs-body-bg)}
	.lt-table tbody tr:hover td{background:var(--bs-tertiary-bg,#f8f9fa)}
	.lt-table th:first-child,.lt-table td:first-child{border-left:0}
	.lt-table th:last-child,.lt-table td:last-child{border-right:0}
	.lt-table thead tr:first-child th{border-top:0}
	.lt-table tbody tr:last-child td{border-bottom:0}
	.lt-avatar{width:30px;height:30px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:600;background:var(--bs-danger-bg-subtle);color:var(--bs-danger)}
	.lt-sub{display:block;font-size:10.5px;color:var(--bs-secondary-color);line-height:1.2}
	.lt-pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600}
	.lt-actions{display:flex;align-items:center;justify-content:center;gap:5px}
	.lt-actions form{display:flex;margin:0;padding:0}
	.lt-action-btn{width:30px;height:30px;padding:0;margin:0;border:0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff!important;cursor:pointer;transition:filter .12s ease}
	.lt-action-btn:hover{filter:brightness(1.1)}
	.lt-action-btn.restore{background:#0ab39c}
	.lt-action-btn.destroy{background:#f06548}
	.lt-action-btn .lt-svg{color:#fff;stroke:#fff}
	.lt-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:10px 14px;border-top:1px solid var(--bs-border-color)}
	.lt-footer .pagination{margin-bottom:0}
	@media(max-width:767.98px){
		.lt-card .card-header{padding:10px}
		.lt-toolbar{width:100%}
		.lt-search{width:100%}
		.lt-toolbar form{width:100%}
		.lt-toolbar form .btn{flex:1}
		.lt-wrapper{overflow-x:auto}
		.lt-table{min-width:900px}
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box d-flex align-items-center justify-content-between flex-wrap gap-2 py-2">
				<div>
					<h4 class="mb-1">Leads Trash</h4>
					<p class="text-muted mb-0">Deleted leads ko restore ya permanently delete karein</p>
				</div>
				<a href="{{ route('admin.reports.leads') }}" class="btn btn-secondary btn-sm lt-btn">
					{!! $icon('arrowLeft',15) !!}
					Back to Leads
				</a>
			</div>
		</div>
	</div>
	
	<div class="card lt-card">
		<div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
			<div>
				<h5 class="card-title mb-1 fs-6">Trashed Leads</h5>
				<p class="text-muted mb-0 small">
					Showing {{ $leads->firstItem()??0 }} to {{ $leads->lastItem()??0 }} of {{ $leads->total() }} leads
				</p>
			</div>
			<div class="lt-toolbar">
				<form method="GET" action="{{ route('admin.reports.leads.trash') }}" class="lt-toolbar">
					<div class="lt-search">
						{!! $icon('search',14) !!}
						<input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, company, email, phone" autocomplete="off">
					</div>
					<button type="submit" class="btn btn-primary lt-btn">
						{!! $icon('search',14) !!}
						Search
					</button>
					@if(request()->filled('search'))
					<a href="{{ route('admin.reports.leads.trash') }}" class="btn btn-light border lt-btn">
						{!! $icon('refresh',14) !!}
						Reset
					</a>
					@endif
				</form>
				@if($can('Leads Force Delete')&&$leads->total()>0)
				<form method="POST" action="{{ route('admin.reports.leads.trash.empty') }}" class="d-flex m-0">
					@csrf
					@method('DELETE')
					<button type="button" class="btn btn-danger lt-btn confirm-btn" data-title="Empty Trash?" data-text="Trash ki saari leads hamesha ke liye delete ho jayengi." data-confirm="Yes, Empty Trash" data-color="#dc3545">
						{!! $icon('trash',14) !!}
						Empty Trash
					</button>
				</form>
				@endif
			</div>
		</div>
		<div class="card-body">
			<div class="lt-wrapper">
				<table class="lt-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Lead</th>
							<th>Company</th>
							<th>Contact</th>
							<th>Status</th>
							@if($isSuperAdmin)
							<th>Added By</th>
							@endif
							<th>Deleted At</th>
							<th class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse($leads as $index=>$lead)
						@php
						$statusKey=strtolower($lead->status??'');
						$color=$statusColors[$statusKey]??'dark';
						$initial=strtoupper(mb_substr(trim($lead->name??'?'),0,1));
						$creatorName=$lead->creator?trim($lead->creator->first_name.' '.$lead->creator->last_name):'';
						@endphp
						<tr>
							<td>{{ $leads->firstItem()+$index }}</td>
							<td>
								<div class="d-flex align-items-center gap-2">
									<span class="lt-avatar">{{ $initial }}</span>
									<div>
										<div class="fw-semibold">{{ $lead->name }}</div>
										@if($lead->email)
										<span class="lt-sub">{{ $lead->email }}</span>
										@endif
									</div>
								</div>
							</td>
							<td>{{ $lead->company_name?:'-' }}</td>
							<td>
								{{ $lead->phone?:'-' }}
								@if($lead->alternate_phone)
								<span class="lt-sub">{{ $lead->alternate_phone }}</span>
								@endif
							</td>
							<td>
								<span class="lt-pill bg-{{ $color }}-subtle text-{{ $color }}">
									{{ $lead->status?:'N/A' }}
								</span>
							</td>
							@if($isSuperAdmin)
							<td>{{ $creatorName!==''?$creatorName:'-' }}</td>
							@endif
							<td>
								{{ $lead->deleted_at?$lead->deleted_at->format('d M Y, h:i A'):'-' }}
								@if($lead->deleted_at)
								<span class="lt-sub">{{ $lead->deleted_at->diffForHumans() }}</span>
								@endif
							</td>
							<td>
								<div class="lt-actions">
									@if($can('Leads Restore'))
									<form method="POST" action="{{ route('admin.reports.leads.restore',$lead->id) }}">
										@csrf
										@method('PATCH')
										<button type="button" class="lt-action-btn restore confirm-btn" title="Restore Lead" data-title="Restore Lead?" data-text="{{ $lead->name }} ko wapas leads list mein bheja jayega." data-confirm="Yes, Restore" data-color="#0ab39c">
											{!! $icon('restore',16) !!}
										</button>
									</form>
									@endif
									@if($can('Leads Force Delete'))
									<form method="POST" action="{{ route('admin.reports.leads.force-delete',$lead->id) }}">
										@csrf
										@method('DELETE')
										<button type="button" class="lt-action-btn destroy confirm-btn" title="Delete Permanently" data-title="Delete Permanently?" data-text="{{ $lead->name }} hamesha ke liye delete ho jayegi. Yeh undo nahi hoga." data-confirm="Yes, Delete" data-color="#dc3545">
											{!! $icon('trash',16) !!}
										</button>
									</form>
									@endif
								</div>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="{{ $colCount }}" class="text-center py-5">
								<div class="text-muted">
									<div class="mb-2">{!! $icon('inbox',40) !!}</div>
									Trash khali hai.
								</div>
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			@if($leads->hasPages())
			<div class="lt-footer">
				<div class="text-muted small">
					Page {{ $leads->currentPage() }} of {{ $leads->lastPage() }}
				</div>
				<div>
					{{ $leads->withQueryString()->links() }}
				</div>
			</div>
			@endif
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	document.addEventListener('DOMContentLoaded',function(){
		document.querySelectorAll('.confirm-btn').forEach(function(button){
			button.addEventListener('click',function(){
				var form=this.closest('form');
				var title=this.getAttribute('data-title')||'Are you sure?';
				var text=this.getAttribute('data-text')||'';
				var confirmText=this.getAttribute('data-confirm')||'Yes';
				var color=this.getAttribute('data-color')||'#0d6efd';
				if(typeof Swal==='undefined'){
					if(confirm(title+'\n\n'+text)){
						form.submit();
					}
					return;
				}
				Swal.fire({
					title:title,
					text:text,
					icon:'warning',
					showCancelButton:true,
					confirmButtonText:confirmText,
					cancelButtonText:'No, Cancel',
					confirmButtonColor:color,
					cancelButtonColor:'#6c757d',
					reverseButtons:true,
					focusCancel:true
				}).then(function(result){
					if(result.isConfirmed){
						form.submit();
					}
				});
			});
		});
	});
</script>
@endsection