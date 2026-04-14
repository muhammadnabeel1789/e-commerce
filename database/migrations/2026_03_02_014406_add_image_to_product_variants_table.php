<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('product_image_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_images')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['product_image_id']);
            $table->dropColumn('product_image_id');
        });
    }
};