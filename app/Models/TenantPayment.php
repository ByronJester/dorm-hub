<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TenantPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'tenant_billing_id', 'dorm_id', 'room_id', 'amount', 'category',
        'payment_method', 'proof_of_payment', 'status', 'date', 'description'
    ];

    protected $appends = [
        'display_date', 'display_created_date'
    ];

    public function getDisplayDateAttribute()
    {
        $date = Carbon::parse($this->date);


        return $date->isoFormat('LL');
    }

    public function getDisplayCreatedDateAttribute()
    {
        $date = Carbon::parse($this->created_at);


        return $date->isoFormat('LL');
    }
}
