<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'reservation_id', 'billing_id', 'payment_method', 'proof_of_payment',
        'status', 'description', 'date', 'user_id'
    ];

    protected $appends = [
        'display_date', 'display_created_date'
    ];

    protected $with = [
        'billing'
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

    public function getProofOfPaymentAttribute($value)
    {
        if(!$value) return null;

        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}
