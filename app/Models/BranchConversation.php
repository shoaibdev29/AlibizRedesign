<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BranchConversation extends Model
{
    protected $table = 'branch_conversations';

    protected $casts = [
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
        // return array with one fallback image
        return [asset('assets/admin/img/900x400/img1.jpg')];
    }

    foreach ($attachments as $key => $item) {
        if (empty($item)) {
            $attachments[$key] = asset('assets/admin/img/900x400/img1.jpg');
            continue;
        }

        // agar absolute URL already hai (http/https), to as-is return
        if (preg_match('/^https?:\/\//i', $item)) {
            $attachments[$key] = $item;
            continue;
        }

        $storagePath = 'branch_conversation/' . ltrim($item, '/');

        if (Storage::disk('public')->exists($storagePath)) {
            // sahi URL via symlink => /storage/branch_conversation/filename
            $attachments[$key] = Storage::url($storagePath);
        } else {
            $attachments[$key] = asset('assets/admin/img/900x400/img1.jpg');
        }
    }

    return $attachments;
}

}
