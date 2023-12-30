<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription',
        'amount',
        'invoice_number',
        'is_paid'
    ];

    protected $with = ['user'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
