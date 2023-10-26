<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_application_id', 'owner_id', 'tenant_id', 'dorm_id', 'room_id', 'date'
    ];
}
