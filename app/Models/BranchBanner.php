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

   public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;

    // fallback (NO "public/" prefix)
    $fallback = asset('assets/admin/img/160x160/img2.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // already an absolute URL? (CDN/S3 etc.)
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    // consistent storage path for branch banners
    $storagePath = 'branch/banner/' . ltrim($image, '/');

    // check on the "public" disk (maps to storage/app/public)
    if (Storage::disk('public')->exists($storagePath)) {
        // public URL via symlink -> /storage/branch/banner/...
        return Storage::url($storagePath);
    }

    return $fallback;
}

}
?>