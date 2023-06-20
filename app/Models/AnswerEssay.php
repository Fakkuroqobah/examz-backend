<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerEssay extends Model
{
    protected $table = 'answer_essay';
    protected $guarded = ['id'];
    
    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }
}
