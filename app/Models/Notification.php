<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Notification extends Model
{

    protected $casts = [
        'status' => 'integer',
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
    $fallback = asset('assets/admin/img/160x160/img1.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // agar DB me already full URL ho (http/https), to as-is return kare
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    $storagePath = 'notification/' . ltrim($image, '/');

    if (Storage::disk('public')->exists($storagePath)) {
        // sahi public URL => /storage/notification/filename.jpg
        return Storage::url($storagePath);
    }

    return $fallback;
}

}
