<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchCoupon extends Model
{
    use HasFactory;

    protected $table = 'branch_coupons';

    protected $fillable = [
        'title',
        'code',
        'coupon_type',
        'start_date',
        'expire_date',
        'min_purchase',
        'max_discount',
        'discount_type',
        'discount',
        'status',
        'created_at',
        'updated_at',
        'limit'
    ];
}
