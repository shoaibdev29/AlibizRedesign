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
        Schema::create('flash_sale_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('banner_type'); // primary or secondary
            $table->string('image')->nullable();
            $table->foreignId('flash_sale_id')->constrained('flash_sales')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_banners');
    }
};
