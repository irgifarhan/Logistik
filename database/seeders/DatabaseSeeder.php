<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SatkerSeeder::class,
            MultiRoleUsersSeeder::class,
            DummyDataSeeder::class,
            KategoriSeeder::class,
            SatuanSeeder::class,
            GudangSeeder::class,
            BarangSeeder::class,
        ]);
    }
}
