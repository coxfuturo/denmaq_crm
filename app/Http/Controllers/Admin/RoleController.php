<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
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
        $this->checkPermission('Roles View');

        if ($request->query('view') === 'trash') {
            $roles = Role::onlyTrashed()
            ->with('permissions')
            ->withCount('users')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        } else {
            $roles = Role::with('permissions')
            ->withCount('users')
            ->whereNull('deleted_at')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        }

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->checkPermission('Roles Create');

        $permissions = Permission::where('guard_name', 'web')
        ->where('status', true)
        ->orderBy('module', 'asc')
        ->orderBy('position', 'asc')
        ->get()
        ->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('Roles Create');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name'
            ],
            'name_alias' => [
                'nullable',
                'string',
                'max:255'
            ],
            'icon' => [
                'nullable',
                'string',
                'max:100'
            ],
            'position' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'status' => [
                'nullable',
                'boolean'
            ],
            'permissions' => [
                'nullable',
                'array'
            ],
            'permissions.*' => [
                'exists:permissions,name'
            ]
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'name_alias' => $validated['name_alias'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'position' => $validated['position'] ?? 0,
            'status' => $request->boolean('status')
        ]);

        $permissionNames = $validated['permissions'] ?? [];

        $permissions = Permission::where('guard_name', 'web')
        ->whereIn('name', $permissionNames)
        ->pluck('name')
        ->toArray();

        $role->syncPermissions($permissions);

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.roles.index')
        ->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission('Roles Edit');

        $role = Role::with('permissions')->findOrFail($id);

        if ($role->trashed()) {
            return redirect()
            ->route('admin.roles.index', [
                'view' => 'trash'
            ])
            ->with(
                'error',
                'Deleted role cannot be edited. Please restore it first.'
            );
        }

        $permissions = Permission::where('guard_name', 'web')
        ->where('status', true)
        ->orderBy('module', 'asc')
        ->orderBy('position', 'asc')
        ->get()
        ->groupBy('module');

        return view(
            'admin.roles.edit',
            compact('role', 'permissions')
        );
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('Roles Edit');

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()
            ->back()
            ->with(
                'error',
                'Super Admin role cannot be modified.'
            );
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $role->id
            ],
            'name_alias' => [
                'nullable',
                'string',
                'max:255'
            ],
            'icon' => [
                'nullable',
                'string',
                'max:100'
            ],
            'position' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'status' => [
                'nullable',
                'boolean'
            ],
            'permissions' => [
                'nullable',
                'array'
            ],
            'permissions.*' => [
                'exists:permissions,name'
            ]
        ]);

        $role->update([
            'name' => $validated['name'],
            'name_alias' => $validated['name_alias'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'position' => $validated['position'] ?? $role->position,
            'status' => $request->boolean('status')
        ]);

        $permissionNames = $validated['permissions'] ?? [];

        $permissions = Permission::where('guard_name', 'web')
        ->whereIn('name', $permissionNames)
        ->pluck('name')
        ->toArray();

        $role->syncPermissions($permissions);

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.roles.index')
        ->with('success', 'Role updated successfully.');
    }

    public function status($id)
    {
        $this->checkPermission('Roles Edit');

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()
            ->back()
            ->with(
                'error',
                'Super Admin status cannot be changed.'
            );
        }

        $role->status = !$role->status;
        $role->save();

        $this->clearPermissionCache();

        return redirect()
        ->back()
        ->with(
            'success',
            'Role status changed successfully.'
        );
    }

    public function changeStatus($id)
    {
        return $this->status($id);
    }

    public function position(Request $request, $id)
    {
        $this->checkPermission('Roles Edit');

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin position cannot be changed.'
            ], 403);
        }

        $validated = $request->validate([
            'position' => [
                'required',
                'integer',
                'min:0'
            ]
        ]);

        $oldPosition = (int) $role->position;
        $newPosition = (int) $validated['position'];

        if ($oldPosition === $newPosition) {
            return response()->json([
                'success' => true,
                'message' => 'Position is already set.',
                'position' => $role->position
            ]);
        }

        DB::transaction(function () use (
            $role,
            $oldPosition,
            $newPosition
        ) {
            $otherRole = Role::whereNull('deleted_at')
            ->where('id', '!=', $role->id)
            ->where('position', $newPosition)
            ->first();

            if ($otherRole) {
                $otherRole->position = $oldPosition;
                $otherRole->save();
            }

            $role->position = $newPosition;
            $role->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Role position updated successfully.',
            'position' => $newPosition
        ]);
    }

    public function destroy($id)
    {
        $this->checkPermission('Roles Delete');

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()
            ->back()
            ->with(
                'error',
                'Super Admin role cannot be deleted.'
            );
        }

        if ($role->users()->exists()) {
            return redirect()
            ->back()
            ->with(
                'error',
                'This role is assigned to users. Please reassign users first.'
            );
        }

        $role->delete();

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.roles.index')
        ->with(
            'success',
            'Role moved to trash successfully.'
        );
    }

    public function restore($id)
    {
        $this->checkPermission('Roles Delete');

        $role = Role::withTrashed()->findOrFail($id);

        if (!$role->trashed()) {
            return redirect()
            ->route('admin.roles.index')
            ->with(
                'error',
                'This role is already active.'
            );
        }

        if ($role->name === 'Super Admin') {
            return redirect()
            ->route('admin.roles.index', [
                'view' => 'trash'
            ])
            ->with(
                'error',
                'Super Admin role does not need to be restored.'
            );
        }

        $role->restore();

        $this->clearPermissionCache();

        return redirect()
        ->route('admin.roles.index', [
            'view' => 'trash'
        ])
        ->with(
            'success',
            'Role restored successfully.'
        );
    }
}
