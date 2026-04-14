<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Status permintaan batal: null = tidak ada, 'pending' = menunggu, 'approved' = disetujui, 'rejected' = ditolak
            $table->string('cancel_request_status')->nullable()->after('status');
            $table->text('cancel_reason')->nullable()->after('cancel_request_status');
            $table->text('cancel_reject_reason')->nullable()->after('cancel_reason');
            $table->timestamp('cancel_requested_at')->nullable()->after('cancel_reject_reason');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'cancel_request_status',
                'cancel_reason',
                'cancel_reject_reason',
                'cancel_requested_at',
            ]);
        });
    }
};