<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSchedule extends Model
{
    protected $table = 'student_schedule';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function schedule()
    {
        return $this->belongsTo('App\Models\Schedule');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }
}
