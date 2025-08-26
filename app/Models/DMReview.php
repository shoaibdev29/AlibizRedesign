<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DMReview extends Model
{
    protected $casts = [
        'delivery_man_id' => 'integer',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['attachment_fullpath'];

    public function getAttachmentFullPathAttribute()
{
    $value = $this->attachment ?? [];
    $attachments = is_array($value) ? $value : json_decode($value, true);

    if (!is_array($attachments) || empty($attachments)) {
        return [asset('assets/admin/img/400x400/img2.jpg')];
    }

    foreach ($attachments as $key => $item) {
        if (empty($item)) {
            $attachments[$key] = asset('assets/admin/img/400x400/img2.jpg');
            continue;
        }

        // if already a full URL (http/https), keep it as-is
        if (preg_match('/^https?:\/\//i', $item)) {
            $attachments[$key] = $item;
            continue;
        }

        $storagePath = 'review/' . ltrim($item, '/');

        if (Storage::disk('public')->exists($storagePath)) {
            // correct URL => /storage/review/filename.jpg
            $attachments[$key] = Storage::url($storagePath);
        } else {
            $attachments[$key] = asset('assets/admin/img/400x400/img2.jpg');
        }
    }

    return $attachments;
}



    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery_man(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }
}
