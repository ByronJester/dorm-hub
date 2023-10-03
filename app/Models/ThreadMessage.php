<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ThreadMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id', 'thread_member_id', 'user_id', 'message', 'files'
    ];

    protected $appends = [
        'display_date'
    ];

    public function getDisplayDateAttribute()
    {
        $date = Carbon::parse($this->created_at);

        return $date->isoFormat('LLL');
    }
}
