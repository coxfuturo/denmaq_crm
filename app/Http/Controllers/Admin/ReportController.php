<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function checkPermission(string $permission): void
    {
        $user=auth()->user();

        abort_unless(
            $user&&(
                $user->hasRole('Super Admin')||
                $user->can($permission)
            ),
            403,
            'You do not have permission to access this page.'
        );
    }

    private function isSuperAdmin(): bool
    {
        $user=auth()->user();

        return $user&&$user->hasRole('Super Admin');
    }

    private function leadQuery()
    {
        $user=auth()->user();

        $query=Lead::with([
            'assignedUser',
            'creator'
        ]);

        if(!$this->isSuperAdmin()){
            $query->where('created_by',$user->id);
        }

        return $query;
    }

    public function leadReport(Request $request)
    {
        $this->checkPermission('Leads View');

        $query=$this->leadQuery();

        if($request->filled('search')){
            $search=trim($request->search);

            $query->where(function($q)use($search){
                $q->where('name','like','%'.$search.'%')
                ->orWhere('company_name','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orWhere('phone','like','%'.$search.'%')
                ->orWhere('alternate_phone','like','%'.$search.'%');
            });
        }

        if($request->filled('status')){
            $query->where('status',$request->status);
        }

        if($request->filled('source')){
            $query->where('source',$request->source);
        }

        if($request->filled('service')){
            $query->where('service',$request->service);
        }

        if($request->filled('assigned_to')){
            $query->where('assigned_to',$request->assigned_to);
        }

        if($this->isSuperAdmin()&&$request->filled('created_by')){
            $query->where('created_by',$request->created_by);
        }

        if($request->filled('follow_up_from')){
            $query->whereDate('follow_up_date','>=',$request->follow_up_from);
        }

        if($request->filled('follow_up_to')){
            $query->whereDate('follow_up_date','<=',$request->follow_up_to);
        }

        if($request->filled('created_from')){
            $query->whereDate('created_at','>=',$request->created_from);
        }

        if($request->filled('created_to')){
            $query->whereDate('created_at','<=',$request->created_to);
        }

        $totalLeads=(clone $query)->count();

        $newLeads=(clone $query)
        ->where('status','New')
        ->count();

        $contactedLeads=(clone $query)
        ->where('status','Contacted')
        ->count();

        $qualifiedLeads=(clone $query)
        ->where('status','Qualified')
        ->count();

        $convertedLeads=(clone $query)
        ->where('status','Converted')
        ->count();

        $lostLeads=(clone $query)
        ->where('status','Lost')
        ->count();

        $perPage=(int)$request->get('per_page',15);

        if(!in_array($perPage,[10,15,25,50,100,200,500],true)){
            $perPage=15;
        }

        $leads=$query
        ->latest('id')
        ->paginate($perPage)
        ->withQueryString();

        $users=User::orderBy('first_name')
        ->orderBy('last_name')
        ->get();

        $statuses=Lead::query()
        ->whereNotNull('status')
        ->where('status','!=','')
        ->distinct()
        ->orderBy('status')
        ->pluck('status');

        $sources=Lead::query()
        ->whereNotNull('source')
        ->where('source','!=','')
        ->distinct()
        ->orderBy('source')
        ->pluck('source');

        $services=Lead::query()
        ->whereNotNull('service')
        ->where('service','!=','')
        ->distinct()
        ->orderBy('service')
        ->pluck('service');

        return view('admin.reports.leads.index',compact(
            'leads',
            'users',
            'statuses',
            'sources',
            'services',
            'totalLeads',
            'newLeads',
            'contactedLeads',
            'qualifiedLeads',
            'convertedLeads',
            'lostLeads'
        ));
    }

    public function leadCreate()
    {
        $this->checkPermission('Leads Create');

        $users=User::orderBy('first_name')
        ->orderBy('last_name')
        ->get();

        return view('admin.reports.leads.create',compact('users'));
    }

    public function leadStore(Request $request)
    {
        $this->checkPermission('Leads Create');

        $validated=$request->validate([
            'name'=>['required','string','max:255'],
            'company_name'=>['nullable','string','max:255'],
            'email'=>['nullable','email','max:255'],
            'phone'=>['required','string','max:50'],
            'alternate_phone'=>['nullable','string','max:50'],
            'source'=>['nullable','string','max:255'],
            'service'=>['nullable','string','max:255'],
            'status'=>['required','string','max:100'],
            'assigned_to'=>['nullable','exists:users,id'],
            'follow_up_date'=>['nullable','date'],
            'budget'=>['nullable','numeric','min:0'],
            'notes'=>['nullable','string']
        ]);

        $validated['created_by']=auth()->id();

        Lead::create($validated);

        return redirect()
        ->route('admin.reports.leads')
        ->with('success','Lead created successfully.');
    }

    public function leadShow($id)
    {
        $this->checkPermission('Leads View');

        $lead=$this->leadQuery()
        ->where('id',$id)
        ->firstOrFail();

        return view('admin.reports.leads.show',compact('lead'));
    }

    public function leadEdit($id)
    {
        $this->checkPermission('Leads Edit');

        $lead=$this->leadQuery()
        ->where('id',$id)
        ->firstOrFail();

        $users=User::orderBy('first_name')
        ->orderBy('last_name')
        ->get();

        return view('admin.reports.leads.edit',compact('lead','users'));
    }

    public function leadUpdate(Request $request,$id)
    {
        $this->checkPermission('Leads Edit');

        $lead=$this->leadQuery()
        ->where('id',$id)
        ->firstOrFail();

        $validated=$request->validate([
            'name'=>['required','string','max:255'],
            'company_name'=>['nullable','string','max:255'],
            'email'=>['nullable','email','max:255'],
            'phone'=>['required','string','max:50'],
            'alternate_phone'=>['nullable','string','max:50'],
            'source'=>['nullable','string','max:255'],
            'service'=>['nullable','string','max:255'],
            'status'=>['required','string','max:100'],
            'assigned_to'=>['nullable','exists:users,id'],
            'follow_up_date'=>['nullable','date'],
            'budget'=>['nullable','numeric','min:0'],
            'notes'=>['nullable','string']
        ]);

        $lead->update($validated);

        return redirect()
        ->route('admin.reports.leads')
        ->with('success','Lead updated successfully.');
    }

    public function leadDelete($id)
    {
        $this->checkPermission('Leads Delete');

        $lead=$this->leadQuery()
        ->where('id',$id)
        ->firstOrFail();

        $lead->delete();

        return redirect()
        ->route('admin.reports.leads')
        ->with('success','Lead deleted successfully.');
    }

    public function leadTrash()
    {
        $this->checkPermission('Leads Restore');

        $query=Lead::onlyTrashed()
        ->with([
            'assignedUser',
            'creator'
        ]);

        if(!$this->isSuperAdmin()){
            $query->where('created_by',auth()->id());
        }

        $leads=$query
        ->latest('deleted_at')
        ->paginate(15)
        ->withQueryString();

        return view('admin.reports.leads.trash',compact('leads'));
    }

    public function leadRestore($id)
    {
        $this->checkPermission('Leads Restore');

        $query=Lead::onlyTrashed();

        if(!$this->isSuperAdmin()){
            $query->where('created_by',auth()->id());
        }

        $lead=$query
        ->where('id',$id)
        ->firstOrFail();

        $lead->restore();

        return redirect()
        ->route('admin.reports.leads.trash')
        ->with('success','Lead restored successfully.');
    }

    public function leadForceDelete($id)
    {
        $this->checkPermission('Leads Force Delete');

        $query=Lead::onlyTrashed();

        if(!$this->isSuperAdmin()){
            $query->where('created_by',auth()->id());
        }

        $lead=$query
        ->where('id',$id)
        ->firstOrFail();

        $lead->forceDelete();

        return redirect()
        ->route('admin.reports.leads.trash')
        ->with('success','Lead permanently deleted.');
    }

    public function leadEmptyTrash()
    {
        $this->checkPermission('Leads Force Delete');

        if(!$this->isSuperAdmin()){
            abort(403,'Only Super Admin can empty trash.');
        }

        Lead::onlyTrashed()->forceDelete();

        return redirect()
        ->route('admin.reports.leads.trash')
        ->with('success','All trashed leads permanently deleted successfully.');
    }

    public function leadChangeStatus(Request $request,$id)
    {
        $this->checkPermission('Leads Status');

        $request->validate([
            'status'=>['required','string','max:100']
        ]);

        $lead=$this->leadQuery()
        ->where('id',$id)
        ->firstOrFail();

        $lead->update([
            'status'=>$request->status
        ]);

        return redirect()
        ->back()
        ->with('success','Lead status updated successfully.');
    }
}