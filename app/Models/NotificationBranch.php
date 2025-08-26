<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NotificationBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'type',
    ];

    /**
     * Always return a correct public URL for the image.
     * Works whether DB has full path or just filename.
     */
   public function getImageUrlAttribute()
{
    $fallback = asset('assets/admin/img/icons/upload_img.png');

    if (empty($this->image)) {
        return $fallback;
    }

    // agar DB me already full URL ho (http/https), to use it directly
    if (preg_match('/^https?:\/\//i', $this->image)) {
        return $this->image;
    }

    $storagePath = 'notification/' . ltrim($this->image, '/');

    if (Storage::disk('public')->exists($storagePath)) {
        return Storage::url($storagePath); // => /storage/notification/filename.png
    }

    return $fallback;
}


    // Add this for backward compatibility
    public function getImageFullpathAttribute()
    {
        return $this->image_url;
    }
}
