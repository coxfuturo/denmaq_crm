<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Dashboard',
                'name' => 'Dashboard View',
                'route' => 'admin.dashboard',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Leads
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Leads',
                'name' => 'Leads View',
                'route' => 'admin.leads.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Leads',
                'name' => 'Leads Create',
                'route' => 'admin.leads.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Leads',
                'name' => 'Leads Edit',
                'route' => 'admin.leads.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Leads',
                'name' => 'Leads Delete',
                'route' => 'admin.leads.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Customers
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Customers',
                'name' => 'Customers View',
                'route' => 'admin.customers.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Customers',
                'name' => 'Customers Create',
                'route' => 'admin.customers.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Customers',
                'name' => 'Customers Edit',
                'route' => 'admin.customers.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Customers',
                'name' => 'Customers Delete',
                'route' => 'admin.customers.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Contacts
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Contacts',
                'name' => 'Contacts View',
                'route' => 'admin.contacts.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Contacts',
                'name' => 'Contacts Create',
                'route' => 'admin.contacts.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Contacts',
                'name' => 'Contacts Edit',
                'route' => 'admin.contacts.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Contacts',
                'name' => 'Contacts Delete',
                'route' => 'admin.contacts.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Follow Ups
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Follow Ups',
                'name' => 'Follow Ups View',
                'route' => 'admin.followups.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Follow Ups',
                'name' => 'Follow Ups Create',
                'route' => 'admin.followups.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Follow Ups',
                'name' => 'Follow Ups Edit',
                'route' => 'admin.followups.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Follow Ups',
                'name' => 'Follow Ups Delete',
                'route' => 'admin.followups.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Tasks
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Tasks',
                'name' => 'Tasks View',
                'route' => 'admin.tasks.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Tasks',
                'name' => 'Tasks Create',
                'route' => 'admin.tasks.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Tasks',
                'name' => 'Tasks Edit',
                'route' => 'admin.tasks.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Tasks',
                'name' => 'Tasks Delete',
                'route' => 'admin.tasks.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Quotations
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Quotations',
                'name' => 'Quotations View',
                'route' => 'admin.quotations.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Quotations',
                'name' => 'Quotations Create',
                'route' => 'admin.quotations.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Quotations',
                'name' => 'Quotations Edit',
                'route' => 'admin.quotations.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Quotations',
                'name' => 'Quotations Delete',
                'route' => 'admin.quotations.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Sales Orders
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Sales Orders',
                'name' => 'Sales Orders View',
                'route' => 'admin.orders.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Sales Orders',
                'name' => 'Sales Orders Create',
                'route' => 'admin.orders.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Sales Orders',
                'name' => 'Sales Orders Edit',
                'route' => 'admin.orders.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Sales Orders',
                'name' => 'Sales Orders Delete',
                'route' => 'admin.orders.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Invoices
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Invoices',
                'name' => 'Invoices View',
                'route' => 'admin.invoices.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Invoices',
                'name' => 'Invoices Create',
                'route' => 'admin.invoices.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Invoices',
                'name' => 'Invoices Edit',
                'route' => 'admin.invoices.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Invoices',
                'name' => 'Invoices Delete',
                'route' => 'admin.invoices.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Payments',
                'name' => 'Payments View',
                'route' => 'admin.payments.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Payments',
                'name' => 'Payments Create',
                'route' => 'admin.payments.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Payments',
                'name' => 'Payments Edit',
                'route' => 'admin.payments.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Payments',
                'name' => 'Payments Delete',
                'route' => 'admin.payments.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Products
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Products',
                'name' => 'Products View',
                'route' => 'admin.products.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Products',
                'name' => 'Products Create',
                'route' => 'admin.products.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Products',
                'name' => 'Products Edit',
                'route' => 'admin.products.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Products',
                'name' => 'Products Delete',
                'route' => 'admin.products.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Categories',
                'name' => 'Categories View',
                'route' => 'admin.categories.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Categories',
                'name' => 'Categories Create',
                'route' => 'admin.categories.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Categories',
                'name' => 'Categories Edit',
                'route' => 'admin.categories.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Categories',
                'name' => 'Categories Delete',
                'route' => 'admin.categories.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Brands
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Brands',
                'name' => 'Brands View',
                'route' => 'admin.brands.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Brands',
                'name' => 'Brands Create',
                'route' => 'admin.brands.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Brands',
                'name' => 'Brands Edit',
                'route' => 'admin.brands.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Brands',
                'name' => 'Brands Delete',
                'route' => 'admin.brands.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Stock
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Stock',
                'name' => 'Stock View',
                'route' => 'admin.stock.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Stock',
                'name' => 'Stock Create',
                'route' => 'admin.stock.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Stock',
                'name' => 'Stock Edit',
                'route' => 'admin.stock.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Stock',
                'name' => 'Stock Delete',
                'route' => 'admin.stock.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Expenses
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Expenses',
                'name' => 'Expenses View',
                'route' => 'admin.expenses.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Expenses',
                'name' => 'Expenses Create',
                'route' => 'admin.expenses.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Expenses',
                'name' => 'Expenses Edit',
                'route' => 'admin.expenses.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Expenses',
                'name' => 'Expenses Delete',
                'route' => 'admin.expenses.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Users',
                'name' => 'Users View',
                'route' => 'admin.users.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Users',
                'name' => 'Users Create',
                'route' => 'admin.users.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Users',
                'name' => 'Users Edit',
                'route' => 'admin.users.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Users',
                'name' => 'Users Delete',
                'route' => 'admin.users.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Roles',
                'name' => 'Roles View',
                'route' => 'admin.roles.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Roles',
                'name' => 'Roles Create',
                'route' => 'admin.roles.create',
                'action' => 'create',
                'position' => 2,
                'status' => true,
            ],

            [
                'module' => 'Roles',
                'name' => 'Roles Edit',
                'route' => 'admin.roles.edit',
                'action' => 'edit',
                'position' => 3,
                'status' => true,
            ],

            [
                'module' => 'Roles',
                'name' => 'Roles Delete',
                'route' => 'admin.roles.destroy',
                'action' => 'delete',
                'position' => 4,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Reports',
                'name' => 'Reports View',
                'route' => 'admin.reports.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Reports',
                'name' => 'Reports Export',
                'route' => 'admin.reports.export',
                'action' => 'export',
                'position' => 2,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Activity Logs
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Activity Logs',
                'name' => 'Activity Logs View',
                'route' => 'admin.activity-logs.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],


            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            [
                'module' => 'Settings',
                'name' => 'Settings View',
                'route' => 'admin.settings.index',
                'action' => 'view',
                'position' => 1,
                'status' => true,
            ],

            [
                'module' => 'Settings',
                'name' => 'Settings Edit',
                'route' => 'admin.settings.edit',
                'action' => 'edit',
                'position' => 2,
                'status' => true,
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Insert / Update Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {

            Permission::updateOrCreate(
                [
                    'name' => $permission['name'],
                    'guard_name' => 'web',
                ],
                [
                    'module' => $permission['module'],
                    'route' => $permission['route'],
                    'action' => $permission['action'],
                    'position' => $permission['position'],
                    'status' => $permission['status'],
                ]
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}