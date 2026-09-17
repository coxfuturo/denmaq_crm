<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = Permission::where('guard_name', 'web')->get();

        $superAdmin = Role::updateOrCreate(
            ['name' => 'Super Admin'],
            [
                'guard_name' => 'web',
                'name_alias' => 'System Administrator',
                'icon' => 'shield',
                'position' => 1,
                'status' => true,
            ]
        );

        $superAdmin->syncPermissions($permissions);

        $admin = Role::updateOrCreate(
            ['name' => 'Admin'],
            [
                'guard_name' => 'web',
                'name_alias' => 'System Admin',
                'icon' => 'user-check',
                'position' => 2,
                'status' => true,
            ]
        );

        $admin->syncPermissions($permissions);

        $salesManager = Role::updateOrCreate(
            ['name' => 'Sales Manager'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Sales Head',
                'icon' => 'briefcase',
                'position' => 3,
                'status' => true,
            ]
        );

        $salesManager->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Leads',
                'Clients',
                'Follow Ups',
                'Projects',
                'Quotations',
                'Invoices',
                'Payments',
                'Sales Reports',
            ])
        );

        $salesExecutive = Role::updateOrCreate(
            ['name' => 'Sales Executive'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Sales Staff',
                'icon' => 'user',
                'position' => 4,
                'status' => true,
            ]
        );

        $salesExecutive->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Leads',
                'Clients',
                'Follow Ups',
                'Projects',
                'Quotations',
            ])
        );

        $accountant = Role::updateOrCreate(
            ['name' => 'Accountant'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Accounts Staff',
                'icon' => 'dollar-sign',
                'position' => 5,
                'status' => true,
            ]
        );

        $accountant->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Clients',
                'Quotations',
                'Invoices',
                'Payments',
                'Sales Reports',
            ])
        );

        $hr = Role::updateOrCreate(
            ['name' => 'HR'],
            [
                'guard_name' => 'web',
                'name_alias' => 'HR Staff',
                'icon' => 'people',
                'position' => 6,
                'status' => true,
            ]
        );

        $hr->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Users',
                'User Reports',
            ])
        );

        $supportExecutive = Role::updateOrCreate(
            ['name' => 'Support Executive'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Support Staff',
                'icon' => 'headphones',
                'position' => 7,
                'status' => true,
            ]
        );

        $supportExecutive->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Clients',
                'Follow Ups',
            ])
        );

        $developer = Role::updateOrCreate(
            ['name' => 'Developer'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Developer',
                'icon' => 'code-slash',
                'position' => 8,
                'status' => true,
            ]
        );

        $developer->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Users',
                'Roles',
                'Permissions',
                'Projects',
            ])
        );

        $client = Role::updateOrCreate(
            ['name' => 'Client'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Client User',
                'icon' => 'person',
                'position' => 9,
                'status' => true,
            ]
        );

        $client->syncPermissions(
            $permissions->whereIn('module', [
                'Dashboard',
                'Profile',
            ])
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}