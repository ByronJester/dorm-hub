<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_application_id', 'tenant_payment_id', 'tenant_id', 'amount',
        'payment_method', 'account_name', 'account_number', 'wallet_name'
    ];
}
