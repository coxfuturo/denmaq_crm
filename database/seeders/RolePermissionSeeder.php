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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'reports.view',
            'analytics.view',
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

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

        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->syncPermissions(
            Permission::all()
        );

        $admin = Role::findByName('Admin');
        $admin->syncPermissions(
            Permission::all()
        );

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

        $hr = Role::findByName('HR');
        $hr->syncPermissions([
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'reports.view',
        ]);

        $support = Role::findByName('Support Executive');
        $support->syncPermissions([
            'dashboard.view',
            'clients.view',
            'projects.view',
            'documents.view',
        ]);

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

        $client = Role::findByName('Client');
        $client->syncPermissions([
            'dashboard.view',
            'projects.view',
            'documents.view',
            'invoices.view',
            'payments.view',
        ]);

        $this->command->info(
            'Roles and permissions created successfully.'
        );
    }
}