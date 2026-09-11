<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'Super Admin'],
            [
                'guard_name' => 'web',
                'name_alias' => 'System Administrator',
                'icon' => 'shield',
                'position' => 1,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Admin'],
            [
                'guard_name' => 'web',
                'name_alias' => 'System Admin',
                'icon' => 'user-check',
                'position' => 2,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Sales Manager'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Sales Head',
                'icon' => 'briefcase',
                'position' => 3,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Sales Executive'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Sales Staff',
                'icon' => 'user',
                'position' => 4,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Accountant'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Accounts Staff',
                'icon' => 'dollar-sign',
                'position' => 5,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'HR'],
            [
                'guard_name' => 'web',
                'name_alias' => 'HR Staff',
                'icon' => 'people',
                'position' => 6,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Support Executive'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Support Staff',
                'icon' => 'headphones',
                'position' => 7,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Developer'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Developer',
                'icon' => 'code-slash',
                'position' => 8,
                'status' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'Client'],
            [
                'guard_name' => 'web',
                'name_alias' => 'Client User',
                'icon' => 'person',
                'position' => 9,
                'status' => true,
            ]
        );
    }
}
