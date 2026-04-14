<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            
            // Data Utama
            $table->string('name');
            $table->text('description');
            
            // --- KOLOM BARU (STOK) ---
            $table->integer('stock')->default(0); 
            // -------------------------

            // Data Tambahan
            $table->string('sku')->unique();
            $table->integer('weight'); 
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            
            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Matikan pengecekan foreign key sementara agar bisa drop tabel
        Schema::disableForeignKeyConstraints();
        
        // Hapus tabel
        Schema::dropIfExists('products');
        
        // Hidupkan kembali pengecekan
        Schema::enableForeignKeyConstraints();
    }
};