<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['assignedUser', 'creator']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('alternate_phone', 'like', "%{$search}%")
                    ->orWhere('service', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $sort = $request->get('sort', 'id');

        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'id',
            'name',
            'company_name',
            'email',
            'phone',
            'status',
            'source',
            'follow_up_date',
            'budget',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        $leads = $query->paginate(15)->withQueryString();

        $users = User::where('status', 1)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $statuses = [
            'New',
            'Contacted',
            'Follow Up',
            'Qualified',
            'Proposal',
            'Won',
            'Lost',
        ];

        $sources = [
            'Website',
            'Facebook',
            'Instagram',
            'Google',
            'Reference',
            'Call',
            'Email',
            'Other',
        ];

        return view('admin.leads.index', compact(
            'leads',
            'users',
            'statuses',
            'sources'
        ));
    }

    public function create()
    {
        $users = User::where('status', 1)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $statuses = [
            'New',
            'Contacted',
            'Follow Up',
            'Qualified',
            'Proposal',
            'Won',
            'Lost',
        ];

        $sources = [
            'Website',
            'Facebook',
            'Instagram',
            'Google',
            'Reference',
            'Call',
            'Email',
            'Other',
        ];

        return view('admin.leads.create', compact(
            'users',
            'statuses',
            'sources'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:30',
            'alternate_phone' => 'nullable|string|max:30',
            'source' => 'nullable|string|max:100',
            'service' => 'nullable|string|max:255',
            'status' => 'required|string|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        Lead::create($validated);

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['assignedUser', 'creator']);

        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $users = User::where('status', 1)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $statuses = [
            'New',
            'Contacted',
            'Follow Up',
            'Qualified',
            'Proposal',
            'Won',
            'Lost',
        ];

        $sources = [
            'Website',
            'Facebook',
            'Instagram',
            'Google',
            'Reference',
            'Call',
            'Email',
            'Other',
        ];

        return view('admin.leads.edit', compact(
            'lead',
            'users',
            'statuses',
            'sources'
        ));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:30',
            'alternate_phone' => 'nullable|string|max:30',
            'source' => 'nullable|string|max:100',
            'service' => 'nullable|string|max:255',
            'status' => 'required|string|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $lead->update($validated);

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function changeStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|string|max:100',
        ]);

        $lead->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead status updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead moved to trash successfully.');
    }

    public function trash(Request $request)
    {
        $query = Lead::onlyTrashed()
            ->with(['assignedUser', 'creator']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('alternate_phone', 'like', "%{$search}%")
                    ->orWhere('service', 'like', "%{$search}%");
            });
        }

        $leads = $query
            ->latest('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.leads.trash', compact('leads'));
    }

    public function restore($id)
    {
        $lead = Lead::onlyTrashed()->findOrFail($id);

        $lead->restore();

        return redirect()
            ->route('admin.leads.trash')
            ->with('success', 'Lead restored successfully.');
    }

    public function forceDelete($id)
    {
        $lead = Lead::onlyTrashed()->findOrFail($id);

        $lead->forceDelete();

        return redirect()
            ->route('admin.leads.trash')
            ->with('success', 'Lead permanently deleted.');
    }
}
