<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorm_id', 'type_of_room', 'is_aircon', 'furnished_type', 'image'
    ];

    protected $appends = [
        'src'
    ];

    protected $with = [
        'tenant'
    ];

    public function getImageAttribute($value)
    {
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getSrcAttribute()
    {
        return $this->image;
    }

    public function tenant()
    {
        return $this->hasOne(TenantRoom::class, 'room_id', 'room_id');
    }
}
