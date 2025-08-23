<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchFlashSaleProduct extends Model
{
    use HasFactory;

    protected $table = 'branch_flash_sale_products'; // Make sure you have this table

    protected $fillable = [
        'flash_sale_id',
        'product_id',
    ];

    public function flashSale()
    {
        return $this->belongsTo(BranchFlashSale::class, 'flash_sale_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }
}
