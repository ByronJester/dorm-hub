<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'relationship',
        'first_name',
        'last_name',
        'contact',
        'image'
    ];

    protected $appends = [
        'name'
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
