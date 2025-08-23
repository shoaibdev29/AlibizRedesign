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
        Schema::create('branch_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('banner_type', ['primary', 'secondary']);
            $table->enum('item_type', ['product', 'category'])->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();

            // Optional foreign keys (if you want constraints)
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_banners');
    }
};
