<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Branch extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'coverage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['image_fullpath'];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

   public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;

    // fallback (no "public/" prefix)
    $fallback = asset('assets/admin/img/160x160/img2.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // agar DB me already full URL hai (e.g., http://... ya https://...), to as-is return karo
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    // check if file exists in storage
    if (Storage::disk('public')->exists('branch/'.$image)) {
        // Storage::url() automatically maps to /storage/branch/filename
        return Storage::url('branch/'.$image);
    }

    return $fallback;
}


    public function delivery_charge_setup()
    {
        return $this->hasOne(DeliveryChargeSetup::class, 'branch_id', 'id');
    }

    public function delivery_charge_by_area()
    {
        return $this->hasMany(DeliveryChargeByArea::class, 'branch_id', 'id')->latest();
    }
}
