<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'first_name' => 'admin',
                'last_name' => 'denmaq',
                'mobile' => '9876543210',
                'password' => Hash::make('12345678'),
                'type' => 'admin',
                'is_admin' => true,
                'status' => true,
                'profile_image' => null,
            ]
        );

        $role = Role::firstOrCreate(
            [
                'name' => 'Super Admin',
            ],
            [
                'guard_name' => 'web',
                'name_alias' => 'Super Admin',
                'position' => 0,
                'status' => true,
            ]
        );

        $user->syncRoles([$role]);
    }
}