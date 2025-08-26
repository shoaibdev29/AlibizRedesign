<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliveryMan extends Authenticatable
{
    use Notifiable;

    protected $appends = ['image_fullpath', 'identity_image_fullpath'];

   public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;
    $fallback = asset('assets/admin/img/160x160/img1.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // agar full URL (http/https) already stored ho
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    $storagePath = 'delivery-man/' . ltrim($image, '/');

    if (Storage::disk('public')->exists($storagePath)) {
        return Storage::url($storagePath); // => /storage/delivery-man/filename.jpg
    }

    return $fallback;
}

public function getIdentityImageFullPathAttribute()
{
    $value = $this->identity_image ?? [];
    $images = is_array($value) ? $value : json_decode($value, true);

    if (!is_array($images) || empty($images)) {
        return [asset('assets/admin/img/400x400/img2.jpg')];
    }

    foreach ($images as $key => $item) {
        if (empty($item)) {
            $images[$key] = asset('assets/admin/img/400x400/img2.jpg');
            continue;
        }

        // agar already absolute URL hai (CDN, S3, etc.)
        if (preg_match('/^https?:\/\//i', $item)) {
            $images[$key] = $item;
            continue;
        }

        $storagePath = 'delivery-man/' . ltrim($item, '/');

        if (Storage::disk('public')->exists($storagePath)) {
            $images[$key] = Storage::url($storagePath); // => /storage/delivery-man/filename.png
        } else {
            $images[$key] = asset('assets/admin/img/400x400/img2.jpg');
        }
    }

    return $images;
}


    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DMReview::class,'delivery_man_id');
    }

    public function rating(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DMReview::class)
            ->select(DB::raw('avg(rating) average, delivery_man_id'))
            ->groupBy('delivery_man_id');
    }

    /**
     * @return BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
