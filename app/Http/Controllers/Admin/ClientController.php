<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Exports\ClientsExport;
use App\Exports\ClientsSampleExport;
use App\Imports\ClientsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    private function checkPermission(string $permission): void
    {
        $user = auth()->user();

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

    private function checkClientOwnership(Client $client): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->hasRole('Super Admin')) {
            return;
        }

        abort_unless(
            (int) $client->created_by === (int) $user->id,
            403,
            'You are not allowed to access this client.'
        );
    }

    public function index(Request $request)
    {
        $this->checkPermission('Clients View');

        $user = auth()->user();

        $query = Client::with([
            'user',
            'createdBy',
            'updatedBy'
        ]);

        if (!$user->hasRole('Super Admin')) {
            $query->where(
                'created_by',
                $user->id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('alternate_mobile', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('state', 'like', "%{$search}%")
                ->orWhere('gst_number', 'like', "%{$search}%")
                ->orWhere('pan_number', 'like', "%{$search}%")
                ->orWhereHas('createdBy', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ["%{$search}%"]
                    );
                });
            });
        }

        if (
            $user->hasRole('Super Admin') &&
            $request->filled('created_by')
        ) {
            $query->where(
                'created_by',
                $request->created_by
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $perPage = (int) $request->get(
            'per_page',
            15
        );

        $allowedPerPage = [
            10,
            15,
            25,
            50,
            100,
            200,
            500
        ];

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $sort = $request->get(
            'sort',
            'created_at'
        );

        $direction = $request->get(
            'direction',
            'desc'
        );

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

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
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

        $users = User::where('status', 1)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

        return view(
            'admin.clients.index',
            compact(
                'clients',
                'statuses',
                'users',
                'perPage'
            )
        );
    }

    public function export()
    {
        $this->checkPermission('Clients View');

        return Excel::download(
            new ClientsExport,
            'clients.xlsx'
        );
    }

    public function import(Request $request)
    {
        $this->checkPermission('Clients Create');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        Excel::import(
            new ClientsImport,
            $request->file('file')
        );

        return redirect()
        ->route('admin.clients.index')
        ->with(
            'success',
            'Clients imported successfully.'
        );
    }

    public function sample()
    {
        $this->checkPermission('Clients Create');

        return Excel::download(
            new ClientsSampleExport,
            'clients-sample.xlsx'
        );
    }

    public function create()
    {
        $this->checkPermission('Clients Create');

        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission('Clients Create');

        $validated = $request->validate([
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

        $validated['created_by'] = Auth::id();

        Client::create($validated);

        return redirect()
        ->route('admin.clients.index')
        ->with(
            'success',
            'Client created successfully.'
        );
    }

    public function show(Client $client)
    {
        $this->checkPermission('Clients View');

        $this->checkClientOwnership($client);

        $client->load([
            'user',
            'createdBy',
            'updatedBy'
        ]);

        return view(
            'admin.clients.show',
            compact('client')
        );
    }

    public function changeStatus(
        Request $request,
        Client $client
    ) {
        $this->checkPermission('Clients Edit');

        $this->checkClientOwnership($client);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $client->update([
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()
        ->back()
        ->with(
            'success',
            'Client status updated successfully.'
        );
    }

    public function edit(Client $client)
    {
        $this->checkPermission('Clients Edit');

        $this->checkClientOwnership($client);

        return view(
            'admin.clients.edit',
            compact('client')
        );
    }

    public function update(
        Request $request,
        Client $client
    ) {
        $this->checkPermission('Clients Edit');

        $this->checkClientOwnership($client);

        $validated = $request->validate([
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

        $validated['updated_by'] = Auth::id();

        $client->update($validated);

        return redirect()
        ->route('admin.clients.index')
        ->with(
            'success',
            'Client updated successfully.'
        );
    }

    public function destroy(Client $client)
    {
        $this->checkPermission('Clients Delete');

        $this->checkClientOwnership($client);

        $client->delete();

        return redirect()
        ->route('admin.clients.index')
        ->with(
            'success',
            'Client moved to trash successfully.'
        );
    }

    public function trash(Request $request)
    {
        $this->checkPermission('Clients Delete');

        $user = auth()->user();

        $query = Client::onlyTrashed()
        ->with([
            'user',
            'createdBy',
            'updatedBy'
        ]);

        if (!$user->hasRole('Super Admin')) {
            $query->where(
                'created_by',
                $user->id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('contact_person', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('alternate_mobile', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('state', 'like', "%{$search}%")
                ->orWhere('gst_number', 'like', "%{$search}%")
                ->orWhere('pan_number', 'like', "%{$search}%")
                ->orWhereHas('createdBy', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ["%{$search}%"]
                    );
                });
            });
        }

        if (
            $user->hasRole('Super Admin') &&
            $request->filled('created_by')
        ) {
            $query->where(
                'created_by',
                $request->created_by
            );
        }

        $perPage = (int) $request->get(
            'per_page',
            15
        );

        $allowedPerPage = [
            10,
            15,
            25,
            50,
            100,
            200,
            500
        ];

        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $clients = $query
        ->latest('deleted_at')
        ->paginate($perPage)
        ->withQueryString();

        $users = User::where('status', 1)
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

        return view(
            'admin.clients.trash',
            compact(
                'clients',
                'users',
                'perPage'
            )
        );
    }

    public function restore($id)
    {
        $this->checkPermission('Clients Delete');

        $client = Client::onlyTrashed()
        ->findOrFail($id);

        $this->checkClientOwnership($client);

        $client->restore();

        return redirect()
        ->route('admin.clients.trash')
        ->with(
            'success',
            'Client restored successfully.'
        );
    }

    public function forceDelete($id)
    {
        $this->checkPermission('Clients Delete');

        $client = Client::onlyTrashed()
        ->findOrFail($id);

        $this->checkClientOwnership($client);

        $client->forceDelete();

        return redirect()
        ->route('admin.clients.trash')
        ->with(
            'success',
            'Client permanently deleted successfully.'
        );
    }
}