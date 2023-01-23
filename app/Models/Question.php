<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = ['id'];

    public function answerOptions()
    {
        return $this->hasMany('App\Models\AnswerOption');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }
}
