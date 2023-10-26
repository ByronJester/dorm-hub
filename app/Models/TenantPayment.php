<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'tenant_billing_id', 'dorm_id', 'room_id', 'amount', 'category',
        'payment_method', 'proof_of_payment', 'status', 'date', 'description'
    ];
}
