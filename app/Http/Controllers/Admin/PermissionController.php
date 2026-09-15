<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
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

    private function clearPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function index(Request $request)
    {
        $this->checkPermission('Permissions View');

        $query = Permission::query()
        ->where('guard_name', 'web');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('module', 'like', '%' . $search . '%')
                ->orWhere('route', 'like', '%' . $search . '%')
                ->orWhere('action', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permissions = $query
        ->orderBy('module', 'ASC')
        ->orderBy('position', 'ASC')
        ->orderBy('id', 'ASC')
        ->paginate(20)
        ->withQueryString();

        return view(
            'admin.permissions.index',
            compact('permissions')
        );
    }

    public function create()
    {
        $this->checkPermission('Permissions Create');

        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission('Permissions Create');

        $validated = $request->validate([
            'module' => [
                'required',
                'string',
                'max:255',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')
                ->where(function ($query) {
                    return $query->where('guard_name', 'web');
                }),
            ],
            'route' => [
                'nullable',
                'string',
                'max:255',
            ],
            'action' => [
                'required',
                'string',
                'max:100',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['guard_name'] = 'web';
        $validated['position'] = $request->input('position', 0);
        $validated['status'] = $request->boolean('status');

        Permission::create($validated);

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.permissions.index')
        ->with(
            'success',
            'Permission created successfully.'
        );
    }

    public function edit(string $id)
    {
        $this->checkPermission('Permissions Edit');

        $permission = Permission::where('guard_name', 'web')
        ->findOrFail($id);

        return view(
            'admin.permissions.edit',
            compact('permission')
        );
    }

    public function update(Request $request, string $id)
    {
        $this->checkPermission('Permissions Edit');

        $permission = Permission::where('guard_name', 'web')
        ->findOrFail($id);

        $validated = $request->validate([
            'module' => [
                'required',
                'string',
                'max:255',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')
                ->where(function ($query) {
                    return $query->where('guard_name', 'web');
                })
                ->ignore($permission->id),
            ],
            'route' => [
                'nullable',
                'string',
                'max:255',
            ],
            'action' => [
                'required',
                'string',
                'max:100',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['guard_name'] = 'web';
        $validated['position'] = $request->input('position', 0);
        $validated['status'] = $request->boolean('status');

        $permission->update($validated);

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.permissions.index')
        ->with(
            'success',
            'Permission updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $this->checkPermission('Permissions Delete');

        $permission = Permission::where('guard_name', 'web')
        ->findOrFail($id);

        if ($permission->roles()->exists()) {
            return redirect()
            ->back()
            ->with(
                'error',
                'This permission is assigned to a role. Please remove it from the role first.'
            );
        }

        $permission->delete();

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.permissions.index')
        ->with(
            'success',
            'Permission deleted successfully.'
        );
    }

    public function status(string $id)
    {
        $this->checkPermission('Permissions Edit');

        $permission = Permission::where('guard_name', 'web')
        ->findOrFail($id);

        $permission->status = !$permission->status;
        $permission->save();

        $this->clearPermissionCache();

        return redirect()
        ->back()
        ->with(
            'success',
            'Permission status updated successfully.'
        );
    }

    public function changeStatus(string $id)
    {
        return $this->status($id);
    }
}
