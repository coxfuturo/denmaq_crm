<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::updateOrCreate(

            [
                'name' => 'Super Admin',
            ],

            [
                'name_alias' => 'System Administrator',
                'icon' => 'shield',
                'position' => 1,
                'status' => true,
            ]

        );


        /*
        |--------------------------------------------------------------------------
        | Super Admin - All Permissions
        |--------------------------------------------------------------------------
        */

        $superAdmin->permissions()->sync(
            Permission::pluck('id')->toArray()
        );


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin = Role::updateOrCreate(

            [
                'name' => 'Admin',
            ],

            [
                'name_alias' => 'System Admin',
                'icon' => 'user-check',
                'position' => 2,
                'status' => true,
            ]

        );


        $adminPermissions = Permission::whereNotIn(
            'module',
            [
                'Roles',
                'Settings',
            ]
        )->pluck('id')->toArray();


        $admin->permissions()->sync(
            $adminPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | Manager
        |--------------------------------------------------------------------------
        */

        $manager = Role::updateOrCreate(

            [
                'name' => 'Manager',
            ],

            [
                'name_alias' => 'Business Manager',
                'icon' => 'users',
                'position' => 3,
                'status' => true,
            ]

        );


        $managerPermissions = Permission::whereIn(

            'module',

            [
                'Dashboard',
                'Leads',
                'Customers',
                'Contacts',
                'Follow-ups',
                'Tasks',
                'Quotations',
                'Sales Orders',
                'Invoices',
                'Payments',
                'Products',
                'Categories',
                'Brands',
                'Stock',
                'Expenses',
                'Reports',
            ]

        )->pluck('id')->toArray();


        $manager->permissions()->sync(
            $managerPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | Sales Manager
        |--------------------------------------------------------------------------
        */

        $salesManager = Role::updateOrCreate(

            [
                'name' => 'Sales Manager',
            ],

            [
                'name_alias' => 'Sales Head',
                'icon' => 'briefcase',
                'position' => 4,
                'status' => true,
            ]

        );


        $salesManagerPermissions = Permission::whereIn(

            'module',

            [
                'Dashboard',
                'Leads',
                'Customers',
                'Contacts',
                'Follow-ups',
                'Tasks',
                'Quotations',
                'Sales Orders',
                'Products',
                'Reports',
            ]

        )->pluck('id')->toArray();


        $salesManager->permissions()->sync(
            $salesManagerPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | Sales Executive
        |--------------------------------------------------------------------------
        */

        $salesExecutive = Role::updateOrCreate(

            [
                'name' => 'Sales Executive',
            ],

            [
                'name_alias' => 'Sales Staff',
                'icon' => 'user',
                'position' => 5,
                'status' => true,
            ]

        );


        $salesExecutivePermissions = Permission::whereIn(

            'module',

            [
                'Dashboard',
                'Leads',
                'Customers',
                'Contacts',
                'Follow-ups',
                'Tasks',
                'Quotations',
                'Products',
            ]

        )->whereIn(

            'action',

            [
                'view',
                'create',
                'edit',
            ]

        )->pluck('id')->toArray();


        $salesExecutive->permissions()->sync(
            $salesExecutivePermissions
        );


        /*
        |--------------------------------------------------------------------------
        | Accountant
        |--------------------------------------------------------------------------
        */

        $accountant = Role::updateOrCreate(

            [
                'name' => 'Accountant',
            ],

            [
                'name_alias' => 'Accounts Staff',
                'icon' => 'dollar-sign',
                'position' => 6,
                'status' => true,
            ]

        );


        $accountantPermissions = Permission::whereIn(

            'module',

            [
                'Dashboard',
                'Customers',
                'Invoices',
                'Payments',
                'Expenses',
                'Reports',
            ]

        )->whereIn(

            'action',

            [
                'view',
                'create',
                'edit',
                'export',
            ]

        )->pluck('id')->toArray();


        $accountant->permissions()->sync(
            $accountantPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | Support Executive
        |--------------------------------------------------------------------------
        */

        $support = Role::updateOrCreate(

            [
                'name' => 'Support Executive',
            ],

            [
                'name_alias' => 'Support Staff',
                'icon' => 'headphones',
                'position' => 7,
                'status' => true,
            ]

        );


        $supportPermissions = Permission::whereIn(

            'module',

            [
                'Dashboard',
                'Customers',
                'Contacts',
                'Follow-ups',
                'Tasks',
            ]

        )->whereIn(

            'action',

            [
                'view',
                'create',
                'edit',
            ]

        )->pluck('id')->toArray();


        $support->permissions()->sync(
            $supportPermissions
        );


        /*
        |--------------------------------------------------------------------------
        | Viewer
        |--------------------------------------------------------------------------
        */

        $viewer = Role::updateOrCreate(

            [
                'name' => 'Viewer',
            ],

            [
                'name_alias' => 'Read Only',
                'icon' => 'eye',
                'position' => 8,
                'status' => true,
            ]

        );


        $viewerPermissions = Permission::where(

            'action',
            'view'

        )->pluck('id')->toArray();


        $viewer->permissions()->sync(
            $viewerPermissions
        );
    }
}