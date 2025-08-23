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
        if (!$this->image) {
            return asset('public/assets/admin/img/icons/upload_img.png');
        }

        $path = asset('storage/app/public/notification/' . $this->image);

        // Verify file exists (optional)
        if (!Storage::disk('public')->exists('notification/' . $this->image)) {
            return asset('public/assets/admin/img/icons/upload_img.png');
        }

        return $path;
    }

    // Add this for backward compatibility
    public function getImageFullpathAttribute()
    {
        return $this->image_url;
    }
}
