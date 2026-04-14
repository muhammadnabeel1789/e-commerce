<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();

            // ── Status pesanan sesuai alur controller
            // Midtrans : pending → paid (otomatis webhook) → processing → shipped → completed
            // COD      : pending → processing → shipped → completed
            // Bisa dibatalkan dari: pending, paid, processing
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'])
                  ->default('pending');

            // ── Harga (TANPA diskon)
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // ── Pembayaran Midtrans
            $table->string('payment_method')->nullable();
            // payment_status: diisi otomatis oleh Midtrans webhook
            $table->enum('payment_status', [
                'unpaid',    // belum bayar
                'pending',   // menunggu konfirmasi bank (transfer)
                'paid',      // sudah lunas
                'challenge', // dicurigai fraud, perlu review Midtrans
                'failed',    // gagal
                'expire',    // kedaluwarsa
                'deny',      // ditolak
                'cancel',    // dibatalkan
            ])->default('unpaid');
            $table->string('snap_token')->nullable(); // token Midtrans Snap

            // ── Alamat pengiriman (disimpan langsung untuk histori)
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('shipping_address');
            $table->string('district')->nullable();
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            // ── Kurir & Resi
            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();

            // ── Catatan opsional dari customer
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};