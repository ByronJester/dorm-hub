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

    protected $with = [
        'user', 'payment'
    ];

    public function getDormImageAttribute($value)
    {
        return \LaravelCloudinary::show($value, []);
    }

    public function getBusinessPermitImageAttribute($value)
    {
        return \LaravelCloudinary::show($value, []);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
