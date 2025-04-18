<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'f_id', 'profile_id', 'user_id', 'amount', 'description', 'type', 'invoice_number',
        'is_paid', 'payment_date', 'for_the_month', 'is_active', 'payment_method'
    ];

    protected $with = [
        'profile', 'user'
    ];

    public function profile(){
        return $this->belongsTo(Profile::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
