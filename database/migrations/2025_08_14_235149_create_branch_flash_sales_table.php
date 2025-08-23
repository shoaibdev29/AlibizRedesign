<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('status')->default(0);
            $table->string('image')->default('def.png');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_flash_sales');
    }
};
