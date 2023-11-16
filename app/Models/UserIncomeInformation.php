<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIncomeInformation extends Model
{
    use HasFactory;

    protected $table = 'user_income_informations';

    protected $fillable = [
        'source_of_income', 'monthly_icome', 'proof', 'monthly_expenses'
    ];

    public function getProofAttribute($value)
    {
        return  "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }
}
