<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'user_type',
        'username',
        'password',
        'id_picture',
        'selfie_id_picture',
        'status',
        'image',
        'bio',
        'pk',
        'sk',
        'bank_name',
        'subscription',
        'account_name',
        'account_number',
        'first_logged_in'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'name'
    ];

    protected $with = [
        // 'income_information'
    ];

    public function getIdPictureAttribute($value)
    {
        if(!$value) return $value;
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getSelfieIdPictureAttribute($value)
    {
        if(!$value) return $value;
        return "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getImageAttribute($value)
    {
        if(!$value) return $value;

        return  "http://res.cloudinary.com/dcmgsini6/image/upload/" . $value;
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // public function income_information()
    // {
    //     return $this->hasOne(UserIncomeInformation::class);
    // }
}
