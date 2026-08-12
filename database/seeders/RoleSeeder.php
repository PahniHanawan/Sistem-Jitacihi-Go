<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['role_name' => 'owner']);
        Role::firstOrCreate(['role_name' => 'admin']);
    }
}
