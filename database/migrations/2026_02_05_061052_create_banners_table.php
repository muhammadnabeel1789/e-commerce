<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('banners', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable(); // Optional: Name of the banner
        $table->string('image');             // Path to the banner image
        $table->string('position')->default('hero'); // 'hero', 'footer', etc.
        $table->boolean('is_active')->default(true);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
