<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Cek agar tidak error jika kolom sudah ada
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('id'); 
            }
            
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['1', '2', '3', '4'])
                      ->default('1')
                      ->comment('1=menunggu, 2=sudah bayar, 3=kadaluarsa, 4=batal')
                      ->after('status');
            }
            
            // Opsional: Pastikan kolom total ada, jika belum rename/buat
            if (!Schema::hasColumn('orders', 'total') && Schema::hasColumn('orders', 'total_price')) {
                 $table->renameColumn('total_price', 'total');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};