<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = ['id'];

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\Supervisor');
    }

    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }

    public function studentSchedule()
    {
        return $this->hasOne('App\Models\StudentSchedule');
    }
}
