<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = ['id'];

    public function answerOption()
    {
        return $this->hasMany('App\Models\AnswerOption');
    }

    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }
}
