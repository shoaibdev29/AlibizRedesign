<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // branch user id
            $table->text('message')->nullable();
            $table->text('reply')->nullable();
            $table->json('attachment')->nullable();
            $table->boolean('checked')->default(false);
            $table->boolean('is_reply')->default(false);
            $table->timestamps();

            // Optional foreign key if you have a branch_users table
            // $table->foreign('user_id')->references('id')->on('branch_users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_conversations');
    }
};
