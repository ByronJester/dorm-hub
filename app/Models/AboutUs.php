<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'sub_title',
        'content',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $with = [
        "user"
    ];
    public function getImagePathAttribute($value)
    {
        if(!$value) return $value;

        return  "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }
}
