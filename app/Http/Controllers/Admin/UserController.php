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
    private function isSuperAdmin(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('Super Admin');
    }

    private function checkPermission(string $permission): void
    {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->hasRole('Super Admin') ||
                $user->can($permission)
            ),
            403
        );
    }

    private function checkCreatePermission(): void
    {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->hasRole('Super Admin') ||
                $user->can('Users Create')
            ),
            403
        );
    }

    private function checkUserAccess(User $user): void
    {
        $currentUser = auth()->user();

        abort_unless($currentUser, 403);

        if ($currentUser->hasRole('Super Admin')) {
            return;
        }

        abort_unless(
            (int) $currentUser->id === (int) $user->id,
            403
        );
    }

    public function index(Request $request)
    {
        $this->checkPermission('Users View');

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('Super Admin');

        $view = $request->query('view', 'active');

        $query = $view === 'trash'
        ? User::onlyTrashed()
        : User::query();

        if (!$isSuperAdmin) {
            $query->where('id', $currentUser->id);
        }

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
            'created_at'
        ];

        $sort = $request->query('sort', 'id');
        $direction = $request->query('direction', 'desc');

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        $allowedPerPage = [
            10,
            25,
            50,
            100,
            200,
            500
        ];

        $perPage = (int) $request->query('per_page', 10);

        if (!in_array($perPage, $allowedPerPage, true)) {
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
        $this->checkCreatePermission();

        $roles = Role::where('status', true)
        ->where('guard_name', 'web')
        ->orderBy('position')
        ->orderBy('name')
        ->get();

        if (!$this->isSuperAdmin()) {
            $roles = collect();
        }

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->checkCreatePermission();

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('Super Admin');

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255'
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:13'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed'
            ],
            'type' => [
                'required',
                'in:admin,company,customer'
            ],
            'is_admin' => [
                'nullable',
                'boolean'
            ],
            'status' => [
                'required',
                'boolean'
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
            'role' => [
                'nullable',
                'exists:roles,name'
            ]
        ]);

        if (!$isSuperAdmin) {
            $validated['is_admin'] = false;
            $validated['type'] = $currentUser->type;
            unset($validated['role']);
        } else {
            $validated['is_admin'] = $request->boolean('is_admin');

            if ($validated['type'] === 'admin') {
                $validated['is_admin'] = true;
            }
        }

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request
            ->file('profile_image')
            ->store('users', 'public');
        }

        $role = null;

        if ($isSuperAdmin && $request->filled('role')) {
            $role = Role::where('name', $request->role)
            ->where('guard_name', 'web')
            ->where('status', true)
            ->first();

            if (!$role) {
                return back()
                ->withInput()
                ->with('error', 'Selected role is invalid or inactive.');
            }
        }

        unset($validated['role']);

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
        $this->checkPermission('Users View');

        $user = User::withTrashed()
        ->with('roles')
        ->findOrFail($id);

        $this->checkUserAccess($user);

        return view('admin.users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $this->checkPermission('Users Edit');

        $user = User::findOrFail($id);

        $this->checkUserAccess($user);

        $roles = Role::where('status', true)
        ->where('guard_name', 'web')
        ->orderBy('position')
        ->orderBy('name')
        ->get();

        if (!$this->isSuperAdmin()) {
            $roles = collect();
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $this->checkPermission('Users Edit');

        $user = User::findOrFail($id);

        $this->checkUserAccess($user);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('Super Admin');

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255'
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:13'
            ],
            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed'
            ],
            'type' => [
                'required',
                'in:admin,company,customer'
            ],
            'is_admin' => [
                'nullable',
                'boolean'
            ],
            'status' => [
                'required',
                'boolean'
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
            'role' => [
                'nullable',
                'exists:roles,name'
            ]
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

        if ($isSuperAdmin) {
            $validated['is_admin'] = $request->boolean('is_admin');

            if ($validated['type'] === 'admin') {
                $validated['is_admin'] = true;
            }
        } else {
            $validated['is_admin'] = $user->is_admin;
            $validated['type'] = $user->type;
            unset($validated['role']);
        }

        $role = null;

        if ($isSuperAdmin && $request->filled('role')) {
            $role = Role::where('name', $request->role)
            ->where('guard_name', 'web')
            ->where('status', true)
            ->first();

            if (!$role) {
                return back()
                ->withInput()
                ->with('error', 'Selected role is invalid or inactive.');
            }
        }

        unset($validated['role']);

        $user->update($validated);

        if ($isSuperAdmin) {
            if ($role) {
                $user->syncRoles([$role]);
            } else {
                $user->syncRoles([]);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
        ->route('admin.users.index')
        ->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $this->checkPermission('Users Delete');

        $user = User::findOrFail($id);

        $this->checkUserAccess($user);

        if ($user->hasRole('Super Admin')) {
            return redirect()
            ->back()
            ->with('error', 'Super Admin cannot be deleted.');
        }

        $user->delete();

        return redirect()
        ->route('admin.users.index')
        ->with('success', 'User moved to trash successfully.');
    }

    public function restore(string $id)
    {
        $this->checkPermission('Users Delete');

        $user = User::onlyTrashed()
        ->findOrFail($id);

        $this->checkUserAccess($user);

        $user->restore();

        return redirect()
        ->route('admin.users.index', [
            'view' => 'trash'
        ])
        ->with('success', 'User restored successfully.');
    }

    public function forceDelete(string $id)
    {
        $this->checkPermission('Users Delete');

        $user = User::onlyTrashed()
        ->findOrFail($id);

        $this->checkUserAccess($user);

        if ($user->hasRole('Super Admin')) {
            return redirect()
            ->back()
            ->with('error', 'Super Admin cannot be permanently deleted.');
        }

        if (
            $user->profile_image &&
            Storage::disk('public')->exists($user->profile_image)
        ) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->forceDelete();

        return redirect()
        ->route('admin.users.index', [
            'view' => 'trash'
        ])
        ->with('success', 'User permanently deleted.');
    }

    public function status(string $id)
    {
        $this->checkPermission('Users Edit');

        $user = User::findOrFail($id);

        $this->checkUserAccess($user);

        if ($user->hasRole('Super Admin')) {
            return redirect()
            ->back()
            ->with('error', 'Super Admin status cannot be changed.');
        }

        $user->status = !$user->status;
        $user->save();

        return redirect()
        ->back()
        ->with('success', 'User status updated successfully.');
    }

    public function changeStatus(string $id)
    {
        return $this->status($id);
    }
}
