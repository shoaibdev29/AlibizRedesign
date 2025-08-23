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
        Schema::create('notification_branches', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('description', 255);
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1); // 1=active, 0=inactive
            $table->string('type')->default('general'); // optional, e.g., general or branch-specific
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_branches');
    }
};
