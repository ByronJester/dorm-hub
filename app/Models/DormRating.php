<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DormRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate', 'comment', 'dorm_id', 'tenant_id'
    ];
}
