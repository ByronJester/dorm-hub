<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_path',
    ];

    public function getImagePathAttribute($value)
    {
        if(!$value) return $value;

        return  "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }
}
