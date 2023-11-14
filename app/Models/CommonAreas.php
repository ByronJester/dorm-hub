<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonAreas extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorm_id', 'image'
    ];

    protected $appends = [
        'src'
    ];

    public function getImageAttribute($value)
    {
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getSrcAttribute()
    {
        return $this->image;
    }
}
