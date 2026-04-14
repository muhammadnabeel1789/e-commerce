<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kurir yang ditugaskan admin untuk mengantarkan pesanan
            $table->foreignId('courier_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->onDelete('set null');

            // Status tugas kurir: null = belum ditugaskan, assigned = sudah ditugaskan,
            // picked_up = barang sudah diambil dari toko, delivered = sudah diantar
            $table->enum('courier_task_status', ['assigned', 'picked_up', 'delivered'])
                  ->nullable()
                  ->after('courier_id');

            // Waktu kurir pick up barang dari toko
            $table->timestamp('picked_up_at')->nullable()->after('courier_task_status');

            // Waktu kurir konfirmasi pengiriman (foto)
            $table->timestamp('delivered_at')->nullable()->after('picked_up_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['courier_id']);
            $table->dropColumn([
                'courier_id',
                'courier_task_status',
                'picked_up_at',
                'delivered_at',
            ]);
        });
    }
};