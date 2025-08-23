<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained('branch_flash_sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_flash_sale_products');
    }
};
