<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','f_name', 'l_name', 'phone', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_phone_verified' => 'integer',
    ];

    protected $appends = ['image_fullpath'];

   public function getImageFullPathAttribute(): string
{
    $image = $this->image ?? null;
    $fallback = asset('assets/admin/img/160x160/img1.jpg');

    if (empty($image)) {
        return $fallback;
    }

    // if already an absolute URL (http/https), return as-is
    if (preg_match('/^https?:\/\//i', $image)) {
        return $image;
    }

    $storagePath = 'profile/' . ltrim($image, '/');

    if (Storage::disk('public')->exists($storagePath)) {
        return Storage::url($storagePath); // ✅ /storage/profile/filename.jpg
    }

    return $fallback;
}


    public function orders(){
        return $this->hasMany(Order::class,'user_id');
    }

    public function addresses(){
        return $this->hasMany(CustomerAddress::class,'user_id');
    }

    public function wishlist_products()
    {
        return $this->hasMany(Wishlist::class,'user_id');
    }
    public function userAccount()
    {
        return $this->morphOne(UserAccount::class, 'accountable');
    }

    public function walletTransactions(){
        return $this->morphMany(WalletTransaction::class, 'walletable');
    }
}
