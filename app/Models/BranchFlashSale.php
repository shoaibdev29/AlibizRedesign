<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\FlashSaleProduct;

class BranchFlashSale extends Model
{
    use HasFactory;

    protected $table = 'branch_flash_sales'; // Make sure you have this table

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'status',
        'image',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
    ];

    // Relationship with flash sale products
    public function products()
    {
        return $this->hasMany(BranchFlashSaleProduct::class, 'flash_sale_id', 'id');
    }
}
