<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner', 'tenant', 'dorm_id', 'room_id', 'check_date', 'check_time', 'date', 'expired_at',
    ];

    public function dorm()
    {
        return $this->belongsTo(Dorm::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function owner_user()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function tenant_user()
    {
        return $this->belongsTo(User::class, 'tenant');
    }
}
