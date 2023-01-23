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

    public function isInActive() // 0
    {
        if($this->starts == null) return true;
        return false;
    }

    public function isActive() // 1
    {
        if($this->isInActive()) return false;

        if($this->starts >= date('Y-m-d H:i:s') && $this->due >= date('Y-m-d H:i:s')) return true;
        return false;
    }

    public function isLaunched() // 2
    {
        if($this->isInActive()) return false;

        if($this->starts <= date('Y-m-d H:i:s') && $this->due >= date('Y-m-d H:i:s')) return true;
        return false;
    }

    public function isFinished() // 3
    {
        if($this->isInActive()) return false;
        
        if($this->starts <= date('Y-m-d H:i:s') && $this->due <= date('Y-m-d H:i:s')) return true;
        return false;
    }

    public function isAssignOrLaunch()
    {
        if($this->isActive()) {
            return response()->json([
                'message' => 'The exam is currently being assigned'
            ], 500);
        }else if($this->isLaunched()) {
            return response()->json([
                'message' => 'Exam is being launched'
            ], 500);
        }
    }

    public function isAssignOrLaunchOrOver()
    {
        if($this->isActive()) {
            return response()->json([
                'message' => 'The exam is currently being assigned'
            ], 500);
        }else if($this->isLaunched()) {
            return response()->json([
                'message' => 'Exam is being launched'
            ], 500);
        }else if($this->isFinished()) {
            return response()->json([
                'message' => 'The exam is over'
            ], 500);
        }
    }

    public function studentExams()
    {
        return $this->hasMany('App\Models\StudentExam');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }

    public static function getCurrentDurationToMinutes($model_hour, $model_minute)
    {
        $minutes_hour = $model_hour * 60;
        $minutes = $model_minute + $minutes_hour;
        return $minutes;
    }

    public static function getDurations($model_due, $model_hour, $model_minute)
    {
        $time = new DateTime($model_due);
        $diff = $time->diff(new DateTime());
        $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        if(self::getCurrentDurationToMinutes($model_hour, $model_minute) >= $minutes){
            $hour = floor($minutes /60);
            $minutes = $minutes % 60;
        }else{
            $hour = $model_hour;
            $minutes = $model_minute;
        }

        return [
            'hour' => $hour,
            'minutes' => $minutes
        ];
    }
}
