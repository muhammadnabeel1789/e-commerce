<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // <--- WAJIB DITAMBAHKAN

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun ADMIN
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Cek berdasarkan email
            [
                'name' => 'Admin Toko',
                'password' => Hash::make('123456789'), // <--- WAJIB PAKE HASH
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(), // Biar tidak diminta verifikasi email
            ]
        );

        // 2. Buat Akun CUSTOMER
        User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer Test',
                'password' => Hash::make('123456789'), // <--- WAJIB PAKE HASH
                'role' => 'customer',
                'phone' => '089876543210',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Buat Akun KURIR Reguler
        User::updateOrCreate(
            ['email' => 'kurir@gmail.com'],
            [
                'name'              => 'kurir Reguler',
                'password'          => Hash::make('123456789'),
                'role'              => 'kurir',
                'phone'             => '0811754847854',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
    }
}