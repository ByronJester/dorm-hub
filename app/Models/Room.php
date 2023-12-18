<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorm_id', 'type_of_room', 'is_aircon', 'furnished_type','furnished_desc', 'image', 'status'
    ];

    protected $appends = [
        'src', 'has_active_application_reservation'
    ];

    public function getImageAttribute($value)
    {
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getSrcAttribute()
    {
        return $this->image;
    }

    public function getHasActiveApplicationReservationAttribute()
    {
        $application = Application::where('room_id', $this->id)->where('is_active', true)->first();
        $reservation = Reservation::where('room_id', $this->id)->where('is_active', true)->first();

        if($application || $reservation) {
            return true;
        }

        return false;
    }
}
