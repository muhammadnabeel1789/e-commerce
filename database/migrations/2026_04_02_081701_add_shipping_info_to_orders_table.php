<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk menyimpan info jarak & berat pengiriman.
     * Jalankan: php artisan migrate
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Jarak antara toko ke alamat tujuan (km)
            $table->decimal('shipping_distance_km', 8, 1)->nullable()->after('shipping_cost');

            // Berat total barang (kg)
            $table->decimal('shipping_weight_kg', 8, 2)->nullable()->after('shipping_distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_distance_km', 'shipping_weight_kg']);
        });
    }
};