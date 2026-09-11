<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.view'),
            403
        );

        $view = $request->query('view', 'active');

        $query = $view === 'trash'
            ? User::onlyTrashed()
            : User::query();

        $search = trim($request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $view !== 'trash') {
            $query->where('status', $request->status);
        }

        $allowedSorts = [
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile',
            'type',
            'company_name',
            'status',
            'created_at',
        ];

        $sort = $request->query('sort', 'id');
        $direction = $request->query('direction', 'desc');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        $allowedPerPage = [10, 25, 50, 100, 200, 500];

        $perPage = (int) $request->query('per_page', 10);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $users = $query
            ->with('roles')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'view',
            'search',
            'sort',
            'direction',
            'perPage'
        ));
    }

    public function create()
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.create'),
            403
        );

        $roles = Role::where('status', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.create'),
            403
        );

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'max:13'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'type' => ['required', 'in:admin,company,customer'],
            'is_admin' => ['nullable', 'boolean'],
            'status' => ['required', 'boolean'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'role' => ['nullable', 'exists:roles,name'],
        ]);

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request
                ->file('profile_image')
                ->store('users', 'public');
        }

        $validated['is_admin'] = $request->boolean('is_admin');

        if ($validated['type'] === 'admin') {
            $validated['is_admin'] = true;
        }

        $role = $request->filled('role')
            ? Role::where('name', $request->role)
                ->where('guard_name', 'web')
                ->where('status', true)
                ->first()
            : null;

        if ($request->filled('role') && !$role) {
            return back()
                ->withInput()
                ->with('error', 'Selected role is invalid or inactive.');
        }

        $user = User::create($validated);

        if ($role) {
            $user->assignRole($role);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.view'),
            403
        );

        $user = User::withTrashed()
            ->with('roles')
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function edit(string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.edit'),
            403
        );

        $user = User::findOrFail($id);

        $roles = Role::where('status', true)
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.edit'),
            403
        );

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id
            ],
            'mobile' => ['nullable', 'string', 'max:13'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'type' => ['required', 'in:admin,company,customer'],
            'is_admin' => ['nullable', 'boolean'],
            'status' => ['required', 'boolean'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'role' => ['nullable', 'exists:roles,name'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_image')) {
            if (
                $user->profile_image &&
                Storage::disk('public')->exists($user->profile_image)
            ) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $validated['profile_image'] = $request
                ->file('profile_image')
                ->store('users', 'public');
        }

        $validated['is_admin'] = $request->boolean('is_admin');

        if ($validated['type'] === 'admin') {
            $validated['is_admin'] = true;
        }

        $role = $request->filled('role')
            ? Role::where('name', $request->role)
                ->where('guard_name', 'web')
                ->where('status', true)
                ->first()
            : null;

        if ($request->filled('role') && !$role) {
            return back()
                ->withInput()
                ->with('error', 'Selected role is invalid or inactive.');
        }

        $user->update($validated);

        if ($role) {
            $user->syncRoles([$role]);
        } else {
            $user->syncRoles([]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.delete'),
            403
        );

        $user = User::findOrFail($id);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User moved to trash successfully.');
    }

    public function restore(string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.restore'),
            403
        );

        $user = User::onlyTrashed()
            ->findOrFail($id);

        $user->restore();

        return redirect()
            ->route('admin.users.index', ['view' => 'trash'])
            ->with('success', 'User restored successfully.');
    }

    public function forceDelete(string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.force-delete'),
            403
        );

        $user = User::onlyTrashed()
            ->findOrFail($id);

        if (
            $user->profile_image &&
            Storage::disk('public')->exists($user->profile_image)
        ) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->forceDelete();

        return redirect()
            ->route('admin.users.index', ['view' => 'trash'])
            ->with('success', 'User permanently deleted.');
    }

    public function status(string $id)
    {
        abort_unless(
            auth()->user()->hasRole('Super Admin') ||
            auth()->user()->can('users.status'),
            403
        );

        $user = User::findOrFail($id);

        $user->status = !$user->status;
        $user->save();

        return redirect()
            ->back()
            ->with('success', 'User status updated successfully.');
    }
}