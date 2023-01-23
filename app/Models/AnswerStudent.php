<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerStudent extends Model
{
    protected $table = 'answer_student';
    protected $guarded = ['id'];

    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }
}
