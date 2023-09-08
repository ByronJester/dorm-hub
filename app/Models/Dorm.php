<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dorm extends Model
{
    use HasFactory;

    protected $fillable = [
        'map_address', 'detailed_address', 'lat', 'long', 'property_name',
        'description', 'floors_total', 'rooms_total', 'dorm_image', 'business_permit_image'
    ];
}
