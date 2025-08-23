<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchReview extends Model
{
    protected $table = 'branch_reviews';

    protected $casts = [
        'branch_product_id' => 'integer',
        'user_id' => 'integer',
        'order_id' => 'integer',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BranchProduct::class, 'branch_product_id');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
