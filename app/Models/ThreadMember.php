<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id', 'user_id'
    ];
}
