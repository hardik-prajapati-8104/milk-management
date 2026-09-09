<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@milkdairy.test'],
            [
                'employee_code' => 'EMP00001',
                'name' => 'Super Admin',
                'password' => Hash::make('Password@123'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        $this->command?->warn('Default admin login: admin@milkdairy.test / Password@123 — change this immediately after first login.');
    }
}
