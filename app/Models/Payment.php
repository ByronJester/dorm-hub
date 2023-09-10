<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorm_id', 'deposit', 'advance', 'fee', 'methods'
    ];

    public function getMethodsAttribute($value)
    {
        return explode(",", $value);
    }
}
