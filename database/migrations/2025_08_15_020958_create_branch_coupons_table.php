<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->string('coupon_type');
            $table->date('start_date');
            $table->date('expire_date');
            $table->decimal('min_purchase', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->string('discount_type'); // 'amount' or 'percent'
            $table->decimal('discount', 10, 2);
            $table->boolean('status')->default(1);
            $table->integer('limit')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_coupons');
    }
};
