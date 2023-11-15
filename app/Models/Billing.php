<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'reservation_id', 'amount', 'subject', 'description',
        'date', 'is_paid', 'user_id'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
