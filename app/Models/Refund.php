<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_payment_id', 'amount', 'payment_method', 'wallet_name',
        'account_name', 'account_number', 'proof_of_refund'
    ];

    public function getProofOfRefundAttribute($value)
    {
        if(!$value) return $value;

        return  "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }
}
