<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guard = 'student';

    protected $fillable = [
        'username', 'password', 'role', 'class'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function StudentSchedules()
    {
        return $this->hasMany('App\Models\StudentSchedule');
    }
}
