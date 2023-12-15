<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner', 'tenant', 'dorm_id', 'room_id', 'status', 'move_in', 'move_out', 'reason', 'reason_description',
        'is_active', 'auto_bill', 'auto_bill_date', 'profile_id'
    ];

    protected $appends = [
        'profile'
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

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function getProfileAttribute()
    {
        return Profile::where('id', $this->profile_id)->first();
    }
}
