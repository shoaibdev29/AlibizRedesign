<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $appends = ['image_fullpath'];

    public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;
    $fallback = asset('assets/admin/img/160x160/img1.jpg');

    if (empty($image)) return $fallback;
    if (preg_match('/^https?:\/\//i', $image)) return $image;

    return Storage::disk('public')->exists('admin/'.$image)
        ? asset('storage/admin/'.$image)   // ✅ not storage/app/public
        : $fallback;
}

}
