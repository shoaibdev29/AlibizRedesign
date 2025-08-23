<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BranchBanner extends Model
{
    protected $casts = [
        'product_id' => 'integer',
        'category_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    protected $appends = ['image_fullpath'];

    public function getImageFullPathAttribute()
    {
        $image = $this->image ?? null;
        $path = asset('public/assets/admin/img/160x160/img2.jpg');

        if (!is_null($image)) {
            // Use consistent storage path for branch banners
            $storagePath = 'branch/banner/' . $image;
            
            if (Storage::disk('public')->exists($storagePath)) {
                $path = asset('storage/app/public/' . $storagePath);
            }
        }
        
        return $path;
    }
}
?>