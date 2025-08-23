<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchNewsletter extends Model
{
    protected $table = 'branch_newsletters'; // specify custom table

    protected $fillable = [
        'email',
    ];

    public $timestamps = true; // to have created_at & updated_at
}
