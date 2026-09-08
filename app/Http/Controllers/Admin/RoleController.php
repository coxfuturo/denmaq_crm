<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('view') === 'trash') {
            $roles = Role::onlyTrashed()
                ->with('permissions')
                ->withCount('users')
                ->orderBy('position', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();
        } else {
            $roles = Role::with('permissions')
                ->withCount('users')
                ->whereNull('deleted_at')
                ->orderBy('position', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();
        }

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('status', true)
            ->orderBy('module', 'ASC')
            ->orderBy('position', 'ASC')
            ->get()
            ->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
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
            ]
        ]);

        $role = Role::create([
            'name' => $request->name,
            'name_alias' => $request->name_alias,
            'icon' => $request->icon,
            'position' => $request->position ?? 0,
            'status' => $request->has('status') ? 1 : 0
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->trashed()) {
            return redirect()
                ->route('admin.roles.index', ['view' => 'trash'])
                ->with('error', 'Deleted role cannot be edited. Please restore it first.');
        }

        $permissions = Permission::where('status', true)
            ->orderBy('module', 'ASC')
            ->orderBy('position', 'ASC')
            ->get()
            ->groupBy('module');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()
                ->back()
                ->with('error', 'Super Admin role cannot be modified.');
        }

        $request->validate([
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
            ]
        ]);

        $role->update([
            'name' => $request->name,
            'name_alias' => $request->name_alias,
            'icon' => $request->icon,
            'position' => $request->position ?? $role->position,
            'status' => $request->has('status') ? 1 : 0
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function status($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()
                ->back()
                ->with('error', 'Super Admin status cannot be changed.');
        }

        $role->status = $role->status == 1 ? 0 : 1;
        $role->save();

        return redirect()
            ->back()
            ->with('success', 'Role status changed successfully.');
    }

    public function position(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Super Admin position cannot be changed.'
            ], 403);
        }

        $request->validate([
            'position' => [
                'required',
                'integer',
                'min:0'
            ]
        ]);

        $oldPosition = (int) $role->position;
        $newPosition = (int) $request->position;

        if ($oldPosition === $newPosition) {
            return response()->json([
                'success' => true,
                'message' => 'Position is already set.',
                'position' => $role->position
            ]);
        }

        $otherRole = Role::whereNull('deleted_at')
            ->where('id', '!=', $role->id)
            ->where('position', $newPosition)
            ->first();

        DB::transaction(function () use ($role, $otherRole, $oldPosition, $newPosition) {
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
            'position' => $role->position
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            return redirect()
                ->back()
                ->with('error', 'Super Admin role cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'This role is assigned to users. Please reassign users first.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function restore($id)
    {
        $role = Role::withTrashed()->findOrFail($id);

        if (!$role->trashed()) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'This role is already active.');
        }

        $role->restore();

        return redirect()
            ->route('admin.roles.index', ['view' => 'trash'])
            ->with('success', 'Role restored successfully.');
    }
}
