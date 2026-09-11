<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
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
            'users.restore',
            'users.force-delete',
            'users.status',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.restore',
            'roles.status',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'permissions.status',
            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',
            'leads.restore',
            'leads.force-delete',
            'leads.status',
            'leads.import',
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'clients.restore',
            'clients.force-delete',
            'clients.status',
            'clients.import',
            'followups.view',
            'followups.create',
            'followups.edit',
            'followups.delete',
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'quotations.view',
            'quotations.create',
            'quotations.edit',
            'quotations.delete',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'reports.leads',
            'reports.sales',
            'reports.users',
            'settings.company',
            'settings.company.update',
            'profile.view',
            'profile.edit',
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

        $superAdmin = Role::findByName('Super Admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::findByName('Admin', 'web');
        $admin->syncPermissions(Permission::all());

        $salesManager = Role::findByName('Sales Manager', 'web');
        $salesManager->syncPermissions([
            'dashboard.view',
            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',
            'leads.status',
            'leads.import',
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.status',
            'clients.import',
            'followups.view',
            'followups.create',
            'followups.edit',
            'projects.view',
            'projects.create',
            'projects.edit',
            'quotations.view',
            'quotations.create',
            'quotations.edit',
            'invoices.view',
            'payments.view',
            'reports.leads',
            'reports.sales',
            'profile.view',
            'profile.edit',
        ]);

        $salesExecutive = Role::findByName('Sales Executive', 'web');
        $salesExecutive->syncPermissions([
            'dashboard.view',
            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.status',
            'clients.view',
            'clients.create',
            'clients.edit',
            'followups.view',
            'followups.create',
            'followups.edit',
            'projects.view',
            'quotations.view',
            'quotations.create',
            'profile.view',
            'profile.edit',
        ]);

        $accountant = Role::findByName('Accountant', 'web');
        $accountant->syncPermissions([
            'dashboard.view',
            'clients.view',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'payments.view',
            'payments.create',
            'payments.edit',
            'reports.sales',
            'profile.view',
            'profile.edit',
        ]);

        $hr = Role::findByName('HR', 'web');
        $hr->syncPermissions([
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.status',
            'reports.users',
            'profile.view',
            'profile.edit',
        ]);

        $support = Role::findByName('Support Executive', 'web');
        $support->syncPermissions([
            'dashboard.view',
            'clients.view',
            'leads.view',
            'followups.view',
            'followups.create',
            'followups.edit',
            'projects.view',
            'profile.view',
            'profile.edit',
        ]);

        $developer = Role::findByName('Developer', 'web');
        $developer->syncPermissions([
            'dashboard.view',
            'projects.view',
            'projects.create',
            'projects.edit',
            'profile.view',
            'profile.edit',
        ]);

        $client = Role::findByName('Client', 'web');
        $client->syncPermissions([
            'dashboard.view',
            'projects.view',
            'invoices.view',
            'payments.view',
            'profile.view',
            'profile.edit',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Roles and permissions created successfully.');
    }
}
