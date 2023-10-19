<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorm_id', 'methods'
    ];

    protected $appends = [
        'range_from', 'range_to'
    ];

    public function getMethodsAttribute($value)
    {
        return explode(",", $value);
    }

    public function getRangeFromAttribute()
    {
        $rooms = Room::where('dorm_id', $this->dorm_id)->get();

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
        $rooms = Room::where('dorm_id', $this->dorm_id)->get();

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
}
