<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $casts = [
        'product_id' => 'integer',
        'category_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    protected $appends = ['image_fullpath'];

    public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;

    // fallback image (NO "public/" prefix here)
    $fallback = asset('assets/admin/img/160x160/img2.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // if full URL is stored in DB, just return it
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    // check in storage disk and return proper public URL
    if (Storage::disk('public')->exists('banner/'.$image)) {
        // generates: /storage/banner/filename.jpg
        return Storage::url('banner/'.$image);
    }

    return $fallback;
}

}
