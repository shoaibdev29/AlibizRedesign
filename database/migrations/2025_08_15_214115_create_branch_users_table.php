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
        Schema::create('branch_users', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->tinyInteger('is_phone_verified')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('email_verification_token', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('cm_firebase_token', 255)->nullable();
            $table->string('temporary_token', 255)->nullable();
            $table->tinyInteger('login_hit_count')->default(0);
            $table->tinyInteger('is_temp_blocked')->default(0);
            $table->timestamp('temp_block_time')->nullable();
            $table->string('login_medium', 255)->default('general');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_users');
    }
};
