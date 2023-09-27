<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'tenant_id', 'dorm_id', 'room_id', 'status', 'is_approved', 'is_active', 'expired_at'
    ];

    protected $with = [
        'tenant'
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function payments()
    {
        return $this->hasMany(TenantPayments::class);
    }
}
