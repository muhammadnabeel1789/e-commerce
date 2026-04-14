

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size'); // S, M, L, XL, XXL
            $table->string('color');
            $table->string('color_code')->nullable(); // hex code
            $table->integer('stock')->default(0);
            $table->decimal('additional_price', 10, 2)->default(0);
            $table->string('sku_variant')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};