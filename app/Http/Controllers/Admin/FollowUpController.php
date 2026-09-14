<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    private function checkPermission(string $permission): void
    {
        $user = Auth::user();

        abort_unless(
            $user &&
            (
                $user->hasRole('Super Admin') ||
                $user->can($permission)
            ),
            403,
            'You do not have permission to perform this action.'
        );
    }

    private function isSuperAdmin(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Super Admin');
    }

    private function checkOwnership(FollowUp $followUp): void
    {
        if ($this->isSuperAdmin()) {
            return;
        }

        abort_unless(
            (int) $followUp->created_by === (int) Auth::id(),
            403,
            'You do not have permission to access this follow up.'
        );
    }

    public function index(Request $request)
    {
        $this->checkPermission('Follow Ups View');

        $query = FollowUp::with([
            'lead',
            'client',
            'assignedUser',
            'creator'
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', Auth::id());
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('priority', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhereHas('lead', function ($leadQuery) use ($search) {
                    $leadQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
                })
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('follow_up_date')) {
            $query->whereDate(
                'follow_up_date',
                $request->follow_up_date
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'follow_up_date',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'follow_up_date',
                '<=',
                $request->to_date
            );
        }

        if ($request->filled('assigned_to')) {
            $query->where(
                'assigned_to',
                $request->assigned_to
            );
        }

        $perPage = in_array(
            (int) $request->per_page,
            [10, 15, 25, 50, 100, 200, 500],
            true
        )
        ? (int) $request->per_page
        : 15;

        $followUps = $query
        ->latest('id')
        ->paginate($perPage)
        ->withQueryString();

        $users = User::where('status', true)
        ->orderBy('first_name')
        ->get();

        return view(
            'admin.followups.index',
            compact('followUps', 'users')
        );
    }

    public function create()
    {
        $this->checkPermission('Follow Ups Create');

        $leads = Lead::orderBy('name')
        ->get();

        $clients = Client::orderBy('company_name')
        ->get();

        $users = User::where('status', true)
        ->orderBy('first_name')
        ->get();

        return view(
            'admin.followups.create',
            compact(
                'leads',
                'clients',
                'users'
            )
        );
    }

    public function store(Request $request)
    {
        $this->checkPermission('Follow Ups Create');

        $validated = $request->validate([
            'lead_id' => [
                'nullable',
                'exists:leads,id'
            ],
            'client_id' => [
                'nullable',
                'exists:clients,id'
            ],
            'assigned_to' => [
                'nullable',
                'exists:users,id'
            ],
            'follow_up_date' => [
                'required',
                'date'
            ],
            'follow_up_time' => [
                'nullable',
                'date_format:H:i'
            ],
            'type' => [
                'required',
                'in:Call,Meeting,Email,WhatsApp,Other'
            ],
            'subject' => [
                'required',
                'string',
                'max:255'
            ],
            'notes' => [
                'nullable',
                'string'
            ],
            'priority' => [
                'required',
                'in:Low,Medium,High'
            ],
            'status' => [
                'required',
                'in:Pending,Completed,Cancelled'
            ],
            'next_follow_up_date' => [
                'nullable',
                'date',
                'after_or_equal:follow_up_date'
            ],
        ]);

        $validated['created_by'] = Auth::id();

        FollowUp::create($validated);

        return redirect()
        ->route('admin.followups.index')
        ->with(
            'success',
            'Follow up created successfully.'
        );
    }

    public function show(string $id)
    {
        $this->checkPermission('Follow Ups View');

        $followUp = FollowUp::with([
            'lead',
            'client',
            'assignedUser',
            'creator',
            'updater'
        ])->findOrFail($id);

        $this->checkOwnership($followUp);

        return view(
            'admin.followups.show',
            compact('followUp')
        );
    }

    public function edit(string $id)
    {
        $this->checkPermission('Follow Ups Edit');

        $followUp = FollowUp::findOrFail($id);

        $this->checkOwnership($followUp);

        $leads = Lead::orderBy('name')
        ->get();

        $clients = Client::orderBy('company_name')
        ->get();

        $users = User::where('status', true)
        ->orderBy('first_name')
        ->get();

        return view(
            'admin.followups.edit',
            compact(
                'followUp',
                'leads',
                'clients',
                'users'
            )
        );
    }

    public function update(Request $request, string $id)
    {
        $this->checkPermission('Follow Ups Edit');

        $followUp = FollowUp::findOrFail($id);

        $this->checkOwnership($followUp);

        $validated = $request->validate([
            'lead_id' => [
                'nullable',
                'exists:leads,id'
            ],
            'client_id' => [
                'nullable',
                'exists:clients,id'
            ],
            'assigned_to' => [
                'nullable',
                'exists:users,id'
            ],
            'follow_up_date' => [
                'required',
                'date'
            ],
            'follow_up_time' => [
                'nullable',
                'date_format:H:i'
            ],
            'type' => [
                'required',
                'in:Call,Meeting,Email,WhatsApp,Other'
            ],
            'subject' => [
                'required',
                'string',
                'max:255'
            ],
            'notes' => [
                'nullable',
                'string'
            ],
            'priority' => [
                'required',
                'in:Low,Medium,High'
            ],
            'status' => [
                'required',
                'in:Pending,Completed,Cancelled'
            ],
            'next_follow_up_date' => [
                'nullable',
                'date',
                'after_or_equal:follow_up_date'
            ],
        ]);

        $validated['updated_by'] = Auth::id();

        $followUp->update($validated);

        return redirect()
        ->route('admin.followups.index')
        ->with(
            'success',
            'Follow up updated successfully.'
        );
    }

    public function changeStatus(Request $request, string $id)
    {
        $this->checkPermission('Follow Ups Edit');

        $request->validate([
            'status' => [
                'required',
                'in:Pending,Completed,Cancelled'
            ],
        ]);

        $followUp = FollowUp::findOrFail($id);

        $this->checkOwnership($followUp);

        $followUp->update([
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
        ->back()
        ->with(
            'success',
            'Follow up status updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $this->checkPermission('Follow Ups Delete');

        $followUp = FollowUp::findOrFail($id);

        $this->checkOwnership($followUp);

        $followUp->delete();

        return redirect()
        ->route('admin.followups.index')
        ->with(
            'success',
            'Follow up moved to trash successfully.'
        );
    }

    public function trash(Request $request)
    {
        $this->checkPermission('Follow Ups Delete');

        $query = FollowUp::onlyTrashed()
        ->with([
            'lead',
            'client',
            'assignedUser',
            'creator',
            'updater'
        ]);

        if (!$this->isSuperAdmin()) {
            $query->where('created_by', Auth::id());
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('priority', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhereHas('lead', function ($leadQuery) use ($search) {
                    $leadQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
                })
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $perPage = in_array(
            (int) $request->per_page,
            [10, 15, 25, 50, 100, 200, 500],
            true
        )
        ? (int) $request->per_page
        : 15;

        $followUps = $query
        ->latest('id')
        ->paginate($perPage)
        ->withQueryString();

        return view(
            'admin.followups.trash',
            compact('followUps')
        );
    }

    public function restore(string $id)
    {
        $this->checkPermission('Follow Ups Delete');

        $followUp = FollowUp::onlyTrashed()
        ->findOrFail($id);

        $this->checkOwnership($followUp);

        $followUp->restore();

        return redirect()
        ->route('admin.followups.trash')
        ->with(
            'success',
            'Follow up restored successfully.'
        );
    }

    public function forceDelete(string $id)
    {
        $this->checkPermission('Follow Ups Delete');

        $followUp = FollowUp::onlyTrashed()
        ->findOrFail($id);

        $this->checkOwnership($followUp);

        $followUp->forceDelete();

        return redirect()
        ->route('admin.followups.trash')
        ->with(
            'success',
            'Follow up permanently deleted.'
        );
    }
}