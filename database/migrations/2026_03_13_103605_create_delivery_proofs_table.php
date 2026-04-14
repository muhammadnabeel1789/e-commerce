<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk menyimpan foto bukti pengiriman dari kurir.
     * Kurir wajib upload foto saat:
     * 1. pick_up  — saat mengambil paket dari toko
     * 2. delivered — saat berhasil menyerahkan ke penerima
     */
    public function up(): void
    {
        Schema::create('delivery_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('courier_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Tipe foto: 'pick_up' = ambil barang dari toko, 'delivered' = sudah diterima customer
            $table->enum('type', ['pick_up', 'delivered']);

            // Path file foto (disimpan di storage/app/public/delivery-proofs/)
            $table->string('photo_path');

            // Catatan tambahan dari kurir (opsional)
            $table->text('notes')->nullable();

            // Koordinat GPS saat foto diambil (opsional, dari browser geolocation)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_proofs');
    }
};