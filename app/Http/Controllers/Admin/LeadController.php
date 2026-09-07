<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of leads.
     */
    public function index(Request $request)
    {
        $query = Lead::with([
            'assignedUser',
            'creator'
        ]);

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Assigned employee filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $leads = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('admin.leads.index', compact(
            'leads',
            'users'
        ));
    }


    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.leads.create', compact('users'));
    }


    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'max:20'
            ],

            'alternate_phone' => [
                'nullable',
                'string',
                'max:20'
            ],

            'source' => [
                'nullable',
                'string',
                'max:100'
            ],

            'service' => [
                'nullable',
                'string',
                'max:255'
            ],

            'status' => [
                'required',
                'string',
                'max:100'
            ],

            'assigned_to' => [
                'nullable',
                'exists:users,id'
            ],

            'follow_up_date' => [
                'nullable',
                'date'
            ],

            'budget' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'notes' => [
                'nullable',
                'string'
            ],
        ]);


        // Current logged-in user
        $validated['created_by'] = auth()->id();


        Lead::create($validated);


        return redirect()
            ->route('admin.leads.index')
            ->with(
                'success',
                'Lead created successfully.'
            );
    }


    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        $lead->load([
            'assignedUser',
            'creator'
        ]);

        return view(
            'admin.leads.show',
            compact('lead')
        );
    }


    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead)
    {
        $users = User::orderBy('name')->get();

        return view(
            'admin.leads.edit',
            compact(
                'lead',
                'users'
            )
        );
    }


    /**
     * Update the specified lead.
     */
    public function update(Request $request,Lead $lead) {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'max:20'
            ],

            'alternate_phone' => [
                'nullable',
                'string',
                'max:20'
            ],

            'source' => [
                'nullable',
                'string',
                'max:100'
            ],

            'service' => [
                'nullable',
                'string',
                'max:255'
            ],

            'status' => [
                'required',
                'string',
                'max:100'
            ],

            'assigned_to' => [
                'nullable',
                'exists:users,id'
            ],

            'follow_up_date' => [
                'nullable',
                'date'
            ],

            'budget' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'notes' => [
                'nullable',
                'string'
            ],

        ]);


        $lead->update($validated);


        return redirect()
            ->route('admin.leads.index')
            ->with(
                'success',
                'Lead updated successfully.'
            );
    }


    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('admin.leads.index')
            ->with(
                'success',
                'Lead deleted successfully.'
            );
    }
}
