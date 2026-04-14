<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel payments digunakan sebagai LOG transaksi dari Midtrans
        // Status pembayaran utama tetap di tabel orders (kolom payment_status)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            $table->string('transaction_id')->nullable();   // ID transaksi dari Midtrans
            $table->string('payment_method')->nullable();   // gopay, bank_transfer, credit_card, dll
            $table->decimal('amount', 10, 2);

            // Status sesuai notifikasi Midtrans
            $table->enum('status', ['pending', 'paid', 'failed', 'challenge', 'expire', 'deny', 'cancel'])
                  ->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('payload')->nullable(); // simpan raw response Midtrans jika perlu debug

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};