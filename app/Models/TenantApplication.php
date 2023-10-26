<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'tenant_id', 'dorm_id', 'room_id', 'status', 'move_in',
        'move_out', 'check_date', 'check_time', 'reason', 'expired_at', 'is_active'
    ];
}
