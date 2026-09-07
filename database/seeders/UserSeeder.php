<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Ankesh',
            'last_name' => 'Kumar',
            'email' => 'ankeshcoxfuture@gmail.com',
            'mobile' => '9876543210',
            'password' => 'ankesh@123',
            'type' => 'admin',
            'is_admin' => true,
            'status' => true,
            'profile_image' => null,
        ]);
    }
}