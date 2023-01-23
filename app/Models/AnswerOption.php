<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    protected $table = 'answer_option';
    protected $guarded = ['id'];
    
    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }
}
