<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $isSuperAdmin = $user->hasRole('Super Admin');

        $query = Project::with([
            'client',
            'assignedUser',
            'creator'
        ]);

        if (!$isSuperAdmin) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('project_code', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where(
                        'company_name',
                        'like',
                        "%{$search}%"
                    );
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $perPage = (int) $request->get('per_page', 15);

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

        $projects = $query
        ->latest()
        ->paginate($perPage)
        ->withQueryString();

        return view(
            'admin.projects.index',
            compact(
                'projects',
                'perPage'
            )
        );
    }

    public function create()
    {
        $this->checkPermission('Projects Create');

        $clients = Client::where('status', 'active')
        ->orderBy('company_name')
        ->get();

        $users = User::where('status', true)
        ->orderBy('first_name')
        ->get();

        return view(
            'admin.projects.create',
            compact(
                'clients',
                'users'
            )
        );
    }

    public function store(Request $request)
    {
        $this->checkPermission('Projects Create');

        $validated = $request->validate([
            'client_id' => [
                'nullable',
                'exists:clients,id'
            ],
            'project_code' => [
                'required',
                'string',
                'max:100',
                'unique:projects,project_code'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'start_date' => [
                'nullable',
                'date'
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date'
            ],
            'budget' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'priority' => [
                'required',
                'in:low,medium,high,urgent'
            ],
            'status' => [
                'required',
                'in:planning,in_progress,on_hold,completed,cancelled'
            ],
            'assigned_to' => [
                'nullable',
                'exists:users,id'
            ]
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Project::create($validated);

        return redirect()
        ->route('admin.projects.index')
        ->with(
            'success',
            'Project created successfully.'
        );
    }

    public function show(Project $project)
    {
        $this->checkPermission('Projects View');
        $this->checkOwnership($project);

        $project->load([
            'client',
            'assignedUser',
            'creator',
            'updater'
        ]);

        return view(
            'admin.projects.show',
            compact('project')
        );
    }

    public function edit(Project $project)
    {
        $this->checkPermission('Projects Edit');
        $this->checkOwnership($project);

        $clients = Client::where('status', 'active')
        ->orderBy('company_name')
        ->get();

        $users = User::where('status', true)
        ->orderBy('first_name')
        ->get();

        return view(
            'admin.projects.edit',
            compact(
                'project',
                'clients',
                'users'
            )
        );
    }

    public function update(
        Request $request,
        Project $project
    ) {
        $this->checkPermission('Projects Edit');
        $this->checkOwnership($project);

        $validated = $request->validate([
            'client_id' => [
                'nullable',
                'exists:clients,id'
            ],
            'project_code' => [
                'required',
                'string',
                'max:100',
                'unique:projects,project_code,' . $project->id
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'start_date' => [
                'nullable',
                'date'
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date'
            ],
            'budget' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'priority' => [
                'required',
                'in:low,medium,high,urgent'
            ],
            'status' => [
                'required',
                'in:planning,in_progress,on_hold,completed,cancelled'
            ],
            'assigned_to' => [
                'nullable',
                'exists:users,id'
            ]
        ]);

        $validated['updated_by'] = Auth::id();

        $project->update($validated);

        return redirect()
        ->route('admin.projects.index')
        ->with(
            'success',
            'Project updated successfully.'
        );
    }

    public function destroy(Project $project)
    {
        $this->checkPermission('Projects Delete');
        $this->checkOwnership($project);

        $project->delete();

        return redirect()
        ->route('admin.projects.index')
        ->with(
            'success',
            'Project moved to trash successfully.'
        );
    }

    public function trash(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $this->checkPermission('Projects Delete');

        $isSuperAdmin = $user->hasRole('Super Admin');

        $query = Project::onlyTrashed()
        ->with([
            'client',
            'assignedUser',
            'creator'
        ]);

        if (!$isSuperAdmin) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('project_code', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where(
                        'company_name',
                        'like',
                        "%{$search}%"
                    );
                });
            });
        }

        $projects = $query
        ->latest('deleted_at')
        ->paginate(15)
        ->withQueryString();

        return view(
            'admin.projects.trash',
            compact('projects')
        );
    }

    public function restore($id)
    {
        $this->checkPermission('Projects Edit');

        $project = Project::onlyTrashed()
        ->findOrFail($id);

        $this->checkOwnership($project);

        $project->restore();

        return redirect()
        ->route('admin.projects.trash')
        ->with(
            'success',
            'Project restored successfully.'
        );
    }

    public function forceDelete($id)
    {
        $this->checkPermission('Projects Delete');

        $project = Project::onlyTrashed()
        ->findOrFail($id);

        $this->checkOwnership($project);

        $project->forceDelete();

        return redirect()
        ->route('admin.projects.trash')
        ->with(
            'success',
            'Project permanently deleted.'
        );
    }

    private function checkPermission(string $permission): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($user->hasRole('Super Admin')) {
            return;
        }

        if (!$user->can($permission)) {
            abort(
                403,
                'You do not have permission to perform this action.'
            );
        }
    }

    private function checkOwnership(Project $project): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($user->hasRole('Super Admin')) {
            return;
        }

        if ((int) $project->created_by !== (int) $user->id) {
            abort(
                403,
                'You are not allowed to access this project.'
            );
        }
    }
}
