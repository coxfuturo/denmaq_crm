<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Exports\ClientsExport;
use App\Exports\ClientsSampleExport;
use App\Imports\ClientsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('alternate_mobile', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('gst_number', 'like', '%' . $search . '%')
                    ->orWhere('pan_number', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $perPage = (int) $request->get('per_page', 15);
        $allowedPerPage = [10,15,25,50,100,200,500];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = [
            'company_name',
            'contact_person',
            'email',
            'mobile',
            'city',
            'state',
            'status',
            'created_at',
            'updated_at'
        ];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }
        $clients = $query
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
        $statuses = [
            'active',
            'inactive'
        ];
        return view('admin.clients.index', compact('clients', 'statuses'));
    }

    public function export()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);
        Excel::import(new ClientsImport, $request->file('file'));
        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Clients imported successfully.');
    }

    public function sample()
    {
        return Excel::download(new ClientsSampleExport, 'clients-sample.xlsx');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:20',
            'alternate_mobile' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        Client::create([
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'pincode' => $request->pincode,
            'website' => $request->website,
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'notes' => $request->notes,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);
        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);
        $client = Client::findOrFail($id);
        $client->status = $request->status;
        $client->updated_by = Auth::id();
        $client->save();
        return redirect()
            ->back()
            ->with('success', 'Client status updated successfully.');
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:20',
            'alternate_mobile' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $client->update([
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'pincode' => $request->pincode,
            'website' => $request->website,
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'notes' => $request->notes,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);
        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function trash(Request $request)
    {
        $query = Client::onlyTrashed()->with('user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('alternate_mobile', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('gst_number', 'like', '%' . $search . '%')
                    ->orWhere('pan_number', 'like', '%' . $search . '%');
            });
        }
        $perPage = (int) $request->get('per_page', 15);
        $allowedPerPage = [10,15,25,50,100,200,500];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }
        $clients = $query
            ->latest('deleted_at')
            ->paginate($perPage)
            ->withQueryString();
        return view('admin.clients.trash', compact('clients'));
    }

    public function restore($id)
    {
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->restore();
        return redirect()
            ->route('admin.clients.trash')
            ->with('success', 'Client restored successfully.');
    }

    public function forceDelete($id)
    {
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->forceDelete();
        return redirect()
            ->route('admin.clients.trash')
            ->with('success', 'Client permanently deleted successfully.');
    }
}