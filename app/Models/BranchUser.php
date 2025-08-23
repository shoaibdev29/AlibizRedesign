<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class BranchUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'branch_users'; // specify custom table

    protected $fillable = [
        'f_name',
        'l_name',
        'phone',
        'email',
        'password',
        'image',
        'cm_firebase_token',
        'temporary_token',
        'login_medium'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_phone_verified' => 'integer',
    ];

    protected $appends = ['image_fullpath'];

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('public/assets/admin/img/160x160/img1.jpg');

        if (!is_null($image) && Storage::disk('public')->exists('profile/' . $image)) {
            $path = asset('storage/app/public/profile/' . $image);
        }

        return $path;
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'user_id'); // you may later change to branch orders if separate
    }
}
