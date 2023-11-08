<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'tenant_id', 'dorm_id', 'room_id', 'status', 'move_in',
        'move_out', 'check_date', 'check_time', 'reason', 'expired_at', 'is_active',
        'auto_bill', 'auto_bill_date'
    ];

    protected $appends = [
        'dorm', 'room', 'tenant', 'owner', 'reservation_payment'
    ];

    public function reservation()
    {
        return $this->hasOne(TenantReservation::class);
    }

    public function getDormAttribute()
    {
        return Dorm::where('id', $this->dorm_id)->first();
    }

    public function getRoomAttribute()
    {
        return Room::where('id', $this->room_id)->first();
    }

    public function getOwnerAttribute()
    {
        return User::where('id', $this->owner_id)->first();
    }

    public function getTenantAttribute()
    {
        return User::where('id', $this->tenant_id)->first();
    }

    public function getReservationPaymentAttribute()
    {

        $billing = TenantBilling::where('tenant_application_id', $this->id)->where('description', 'reservation_fee')->first();

        if(!$billing) return null;

        return TenantPayment::where('tenant_billing_id', $billing->id)->first();

    }


}
