<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'f_id', 'profile_id', 'amount', 'description', 'type', 'invoice_number',
        'is_paid', 'payment_date', 'for_the_month', 'is_active', 'payment_method'
    ];

}
