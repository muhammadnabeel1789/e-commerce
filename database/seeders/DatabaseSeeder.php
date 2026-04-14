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
        // HAPUS ATAU KOMENTARI BARIS BAWAAN INI:
        // User::factory(10)->create();

        // HAPUS JUGA YANG INI JIKA ADA:
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // GANTI DENGAN INI (Panggil Seeder Manual Kita):
        $this->call([
            RoleSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
        ]);
    }
}