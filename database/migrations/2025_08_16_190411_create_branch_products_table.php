<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branch_products', function (Blueprint $table) {
            $table->id(); // This automatically creates UNSIGNED BIGINT
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('price', 8, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->decimal('discount', 8, 2)->default(0);
            $table->boolean('set_menu')->default(0);
            $table->integer('wishlist_count')->default(0);
            $table->bigInteger('total_stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_products');
    }
};