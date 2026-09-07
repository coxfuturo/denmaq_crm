<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard
            'dashboard.view',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Permissions
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Clients
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',

            // Leads
            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',

            // Sales
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',

            // Projects
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',

            // Documents
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',

            // Invoices
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',

            // Payments
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',

            // Reports
            'reports.view',

            // Analytics
            'analytics.view',

            // Settings
            'settings.view',
            'settings.edit',

        ];


        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [

            'Super Admin',
            'Admin',
            'Sales Manager',
            'Sales Executive',
            'Accountant',
            'HR',
            'Support Executive',
            'Developer',
            'Client',

        ];


        foreach ($roles as $role) {

            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Super Admin - All Permissions
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::findByName('Super Admin');

        $superAdmin->syncPermissions(
            Permission::all()
        );


        /*
        |--------------------------------------------------------------------------
        | Admin - All Permissions
        |--------------------------------------------------------------------------
        */

        $admin = Role::findByName('Admin');

        $admin->syncPermissions(
            Permission::all()
        );


        /*
        |--------------------------------------------------------------------------
        | Sales Manager
        |--------------------------------------------------------------------------
        */

        $salesManager = Role::findByName('Sales Manager');

        $salesManager->syncPermissions([

            'dashboard.view',

            'clients.view',
            'clients.create',
            'clients.edit',

            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',

            'sales.view',
            'sales.create',
            'sales.edit',

            'projects.view',

            'documents.view',
            'documents.create',

            'invoices.view',

            'payments.view',

            'reports.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Sales Executive
        |--------------------------------------------------------------------------
        */

        $salesExecutive = Role::findByName('Sales Executive');

        $salesExecutive->syncPermissions([

            'dashboard.view',

            'clients.view',
            'clients.create',
            'clients.edit',

            'leads.view',
            'leads.create',
            'leads.edit',

            'sales.view',
            'sales.create',

            'documents.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Accountant
        |--------------------------------------------------------------------------
        */

        $accountant = Role::findByName('Accountant');

        $accountant->syncPermissions([

            'dashboard.view',

            'clients.view',

            'sales.view',

            'invoices.view',
            'invoices.create',
            'invoices.edit',

            'payments.view',
            'payments.create',
            'payments.edit',

            'reports.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | HR
        |--------------------------------------------------------------------------
        */

        $hr = Role::findByName('HR');

        $hr->syncPermissions([

            'dashboard.view',

            'users.view',
            'users.create',
            'users.edit',

            'reports.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Support Executive
        |--------------------------------------------------------------------------
        */

        $support = Role::findByName('Support Executive');

        $support->syncPermissions([

            'dashboard.view',

            'clients.view',

            'projects.view',

            'documents.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Developer
        |--------------------------------------------------------------------------
        */

        $developer = Role::findByName('Developer');

        $developer->syncPermissions([

            'dashboard.view',

            'projects.view',
            'projects.create',
            'projects.edit',

            'documents.view',
            'documents.create',
            'documents.edit',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Client
        |--------------------------------------------------------------------------
        */

        $client = Role::findByName('Client');

        $client->syncPermissions([

            'dashboard.view',

            'projects.view',

            'documents.view',

            'invoices.view',

            'payments.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Success Message
        |--------------------------------------------------------------------------
        */

        $this->command->info(
            'Roles and permissions created successfully.'
        );
    }
}
