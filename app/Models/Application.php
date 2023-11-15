<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'tenant_id', 'dorm_id', 'room_id', 'status', 'move_in',
        'is_approved', 'is_active'
    ];

    public function dorm()
    {
        return $this->belongsTo(Dorm::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
