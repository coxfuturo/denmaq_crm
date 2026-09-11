<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    private function checkPermission(string $permission): void
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can($permission),
            403,
            'You do not have permission to perform this action.'
        );
    }

    public function index(Request $request)
    {
        $this->checkPermission('leads.view');

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

        $perPage = (int) $request->get('per_page', 15);

        $allowedPerPage = [10, 15, 25, 50, 100, 200, 500];

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $leads = $query
            ->paginate($perPage)
            ->withQueryString();

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
            'sources',
            'perPage'
        ));
    }

    public function create()
    {
        $this->checkPermission('leads.create');

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
        $this->checkPermission('leads.create');

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
        $this->checkPermission('leads.view');

        $lead->load(['assignedUser', 'creator']);

        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->checkPermission('leads.edit');

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
        $this->checkPermission('leads.edit');

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
        $this->checkPermission('leads.status');

        $validated = $request->validate([
            'status' => 'required|string|max:100',
        ]);

        $lead->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead status updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->checkPermission('leads.delete');

        $lead->delete();

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead moved to trash successfully.');
    }

    public function trash(Request $request)
    {
        $this->checkPermission('leads.restore');

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
        $this->checkPermission('leads.restore');

        $lead = Lead::onlyTrashed()->findOrFail($id);

        $lead->restore();

        return redirect()
            ->route('admin.leads.trash')
            ->with('success', 'Lead restored successfully.');
    }

    public function forceDelete($id)
    {
        $this->checkPermission('leads.force-delete');

        $lead = Lead::onlyTrashed()->findOrFail($id);

        $lead->forceDelete();

        return redirect()
            ->route('admin.leads.trash')
            ->with('success', 'Lead permanently deleted.');
    }
}
