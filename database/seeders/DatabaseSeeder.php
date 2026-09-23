<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // 1. Buat role
            UserSeeder::class,      // 2. Buat user
            CriteriaSeeder::class,  // 3. Buat kriteria
            ProductSeeder::class,   // 4. Buat produk + assessment
        ]);
    }
}