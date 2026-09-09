<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getUserRoles')) {

    function getUserRoles()
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()
            ->roles()
            ->where('roles.status', 1)
            ->orderBy('roles.position', 'ASC')
            ->get();
    }
}

if (!function_exists('getAssignedPermissions')) {

    function getAssignedPermissions($roleId)
    {
        return Permission::whereHas('roles', function ($query) use ($roleId) {

            $query->where('roles.id', $roleId);

        })
        ->where('permissions.status', 1)
        ->orderBy('permissions.module', 'ASC')
        ->orderBy('permissions.position', 'ASC')
        ->get();
    }
}