<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    protected $table = 'student_exam';
    protected $guarded = [];

    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }
}
