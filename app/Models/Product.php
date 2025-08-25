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

   public function getImageFullPathAttribute()
{
    $value = $this->image ?? [];
    $images = is_array($value) ? $value : json_decode($value, true);

    if (!is_array($images) || empty($images)) {
        // single safe fallback
        return [asset('assets/admin/img/160x160/img2.jpg')];
    }

    foreach ($images as $key => $item) {
        if (empty($item)) {
            $images[$key] = asset('assets/admin/img/160x160/img2.jpg');
            continue;
        }

        // if already a full URL, keep it
        if (preg_match('/^https?:\/\//i', $item)) {
            $images[$key] = $item;
            continue;
        }

        // storage file exists?
        if (Storage::disk('public')->exists('product/'.$item)) {
            // makes /storage/product/filename.jpg
            $images[$key] = Storage::url('product/'.$item);
        } else {
            // fallback (NO "public/" prefix here)
            $images[$key] = asset('assets/admin/img/160x160/img2.jpg');
        }
    }

    return $images;
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
