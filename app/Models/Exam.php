<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guarded = ['id'];

    public function is_default_image($old)
    {
        if('exam/' . end($old) == 'exam/exam_image.png') {
            return false;
        }

        return true;
    }

    public function studentExam()
    {
        return $this->hasMany('App\Models\StudentExam');
    }

    public function question()
    {
        return $this->hasMany('App\Models\Question');
    }
}
