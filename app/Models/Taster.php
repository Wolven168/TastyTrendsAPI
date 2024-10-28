<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Import JWTSubject

class Taster extends Authenticatable implements CanResetPasswordContract, JWTSubject // Implement JWTSubject
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'user_name',
        'user_id',
        'email',
        'password',
        'user_type',
        'store_id',
        'user_image',
        'phone_num',
        'student_num',
        'favorites',
        'remember_me',
    ];

    // Implement methods required by the JWTSubject interface
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically the ID of the user
    }

    public function getJWTCustomClaims()
    {
        return []; // You can add custom claims here if needed
    }
}
