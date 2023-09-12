<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'dorm_id', 'short_term', 'mix_gender', 'curfew', 'curfew_hours', 'minimum_stay', 'rules'
    ];

    public function getRulesAttribute($value)
    {
        return explode(",", $value);
    }
}
