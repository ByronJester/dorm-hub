<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Dorm extends Model
{
    use HasFactory;

    protected $fillable = [
        'map_address', 'detailed_address', 'lat', 'long', 'property_name',
        'description', 'floors_total', 'rooms_total', 'dorm_image', 'business_permit_image'
    ];

    protected $with = [
        'user', 'rooms', 'rule', 'amenities',
    ];

    protected $appends = [
        'range_from', 'range_to'
    ];

    public function getDormImageAttribute($value)
    {
        // return \LaravelCloudinary::show($value, []);
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getBusinessPermitImageAttribute($value)
    {
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function rule()
    {
        return $this->hasOne(Rule::class);
    }

    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }

    public function getRangeFromAttribute()
    {
        $rooms = Room::where('dorm_id', $this->id)->get();

        $fee = null;

        foreach($rooms as $room) {
            if($fee == null) {
                $fee = $room->fee;
            } else {
                if($room->fee < $fee) {
                    $fee = $room->fee;
                }
            }
        }

        return $fee;
    }

    public function getRangeToAttribute()
    {
        $rooms = Room::where('dorm_id', $this->id)->get();

        $fee = null;

        foreach($rooms as $room) {
            if($fee == null) {
                $fee = $room->fee;
            } else {
                if($room->fee > $fee) {
                    $fee = $room->fee;
                }
            }
        }

        return $fee;
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);

        return $date->isoFormat("LL");
    }
}
