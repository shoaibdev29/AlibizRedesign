<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{

    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'set_menu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'wishlist_count' => 'integer',
        'total_stock' => 'integer',
    ];

    protected $appends = ['image_fullpath'];

  public function getImageFullPathAttribute(): array
{
    $raw = $this->image ?? [];

    // Normalize to array
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $images = $decoded;
        } elseif (trim($raw) !== '') {
            // single filename stored as plain string
            $images = [$raw];
        } else {
            $images = [];
        }
    } elseif (is_array($raw)) {
        $images = $raw;
    } else {
        $images = [];
    }

    // Fallback (NO "public/" prefix)
    $fallback = asset('assets/admin/img/160x160/img2.jpg');

    if (empty($images)) {
        return [$fallback];
    }

    $out = [];
    foreach ($images as $item) {
        $item = is_string($item) ? trim($item) : '';
        if ($item === '') {
            $out[] = $fallback;
            continue;
        }

        // already absolute URL?
        if (preg_match('/^https?:\/\//i', $item)) {
            $out[] = $item;
            continue;
        }

        $path = 'product/' . ltrim($item, '/');
        if (Storage::disk('public')->exists($path)) {
            $out[] = Storage::url($path); // /storage/product/filename
        } else {
            $out[] = $fallback;
        }
    }

    // remove dupes & reindex
    return array_values(array_unique($out));
}


    public function translations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class)->latest();
    }

    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id'))
            ->groupBy('product_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
