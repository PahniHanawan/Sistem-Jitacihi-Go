<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $ownerRole = Role::where('role_name', 'owner')->first();
        $adminRole = Role::where('role_name', 'admin')->first();

        User::firstOrCreate(
            ['username' => 'owner'],
            [
                'password' => Hash::make('password'),
                'role_id' => $ownerRole->role_id,
                'full_name' => 'Pemilik Toko (Owner)',
                'status' => 'aktif',
                'email' => 'owner@jitanichigo.com',
            ]
        );

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('password'),
                'role_id' => $adminRole->role_id,
                'full_name' => 'Administrator Toko',
                'status' => 'aktif',
                'email' => 'admin@jitanichigo.com',
            ]
        );
    }
}
