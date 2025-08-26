<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $casts = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'status' => 'integer',
        'is_featured' => 'integer'
    ];

    public function translations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function childes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getNameAttribute($name)
    {
        if(auth('admin')->check()||auth('branch')->check())
        {
            return $name;
        }
        return $this->translations[0]->value??$name;
    }

    protected $appends = ['image_fullpath', 'banner_image_fullpath'];

   public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;
    $fallback = asset('assets/admin/img/160x160/img2.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // already an absolute URL? return as-is
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    $storagePath = 'category/' . ltrim($image, '/');

    if (Storage::disk('public')->exists($storagePath)) {
        return Storage::url($storagePath); // => /storage/category/filename.jpg
    }

    return $fallback;
}

public function getBannerImageFullPathAttribute(): string
{
    $image = $this->banner_image ?? null;
    $fallback = asset('assets/admin/img/8_1.png');

    if (empty($image)) {
        return $fallback;
    }

    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    $storagePath = 'category/banner/' . ltrim($image, '/');

    if (Storage::disk('public')->exists($storagePath)) {
        return Storage::url($storagePath); // => /storage/category/banner/filename.png
    }

    return $fallback;
}


    protected static function booted(): void
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
