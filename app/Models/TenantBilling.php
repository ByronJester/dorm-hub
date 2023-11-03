<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantBilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'tenant_application_id', 'first_name', 'last_name', 'phone_number', 'amount',
        'description', 'date', 'is_paid'
    ];
}
