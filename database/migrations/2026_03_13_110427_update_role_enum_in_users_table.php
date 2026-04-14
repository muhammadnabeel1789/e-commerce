<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum role agar mencakup 'kurir'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'kurir') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        // Rollback ke enum semula (tanpa kurir)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer'");
    }
};