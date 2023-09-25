<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPayments extends Model
{
    use HasFactory;

    protected $appends = [
        'display_date'
    ];

    public function getDisplayDateAttribute()
    {
        $date = strtotime($this->date);
        $year = date('Y', $date);
        $month = date('F', $date);

        return "$month $year";
    }
}
