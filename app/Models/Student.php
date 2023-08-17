<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guard = 'student';

    protected $fillable = [
        'nis', 'name', 'username', 'password', 'role', 'class', 'room_id'
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

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function StudentSchedules()
    {
        return $this->hasMany('App\Models\StudentSchedule');
    }
}
