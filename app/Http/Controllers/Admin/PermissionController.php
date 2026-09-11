<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

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

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
            'module' => [
                'required',
                'string',
                'max:255',
            ],
            'route' => [
                'required',
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
        ]);

        $validated['guard_name'] = 'web';
        $validated['position'] = $request->input('position', 0);
        $validated['status'] = $request->boolean('status');

        Permission::create($validated);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'module' => [
                'required',
                'string',
                'max:255',
            ],
            'route' => [
                'required',
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
        ]);

        $validated['guard_name'] = 'web';
        $validated['position'] = $request->input('position', 0);
        $validated['status'] = $request->boolean('status');

        $permission->update($validated);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        if ($permission->roles()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'This permission is assigned to a role. Please remove it from the role first.');
        }

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    public function status(string $id)
    {
        $permission = Permission::findOrFail($id);

        $permission->status = !$permission->status;
        $permission->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->back()
            ->with('success', 'Permission status updated successfully.');
    }
}