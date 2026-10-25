<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@magenta.local'],
            [
                'name' => 'Admin PILARA',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );

        // Akun Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@magenta.local'],
            [
                'name' => 'Super Admin PILARA',
                'password' => Hash::make('SuperAdmin@12345'),
                'role' => 'super_admin',
            ]
        );
    }
}
