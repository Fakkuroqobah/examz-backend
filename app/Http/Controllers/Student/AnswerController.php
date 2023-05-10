<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerStudent;
use App\Models\StudentSchedule;
use App\Models\Question;
use App\Models\Schedule;
use App\Models\Exam;
use Exception;
use DateTime;
use Auth;

class AnswerController extends Controller
{
    public function examLaunched()
    {
        $data = StudentSchedule::with(['schedule.exam'])
        ->where('student_id', Auth::user()->id)
        ->whereNull('end_time')
        ->whereHas('schedule.exam', function($q) {
            $q->where('status', 'launched');
        })->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function examFinished()
    {
        $data = StudentSchedule::with(['schedule.exam'])
        ->where('student_id', Auth::user()->id)
        ->whereNotNull('end_time')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function token(Request $request, $id)
    {
        $request->validate([
            'token' => 'required',
        ]);

        $data = Schedule::where('token', $request->token)->find($id);
        if(!is_null($data)) {
            $exam = Exam::with(['question.answerOption'])->findOrFail($data->exam_id);

            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($data) {
                $q->where('exam_id', $data->exam_id);
            })->firstOrFail();

            $studentSchedule->update([
                'start_time' => now()
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $exam
            ], 200);
        }else{
            return response()->json([
                'message' => 'Error',
            ], 404);
        }
    }

    public function answer(Request $request)
    {
        $check = AnswerStudent::where('student_id', Auth::user()->id)->where('question_id', $request->question_id)->first();

        try {
            if(is_null($check)) {
                $answerStudent = AnswerStudent::create([
                    'question_id' => $request->question_id,
                    'answer' => $request->answer,
                    'student_id' => Auth::user()->id,
                ]);
            }else{
                $check->update([
                    'answer' => $request->answer
                ]);
            }
    
            return response()->json([
                'message' => 'success'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function endExam(Request $request)
    {
        try {
            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($request) {
                $q->where('exam_id', $request->exam_id);
            })->firstOrFail();

            $studentSchedule->update([
                'end_time' => now()
            ]);

            return response()->json([
                'message' => 'success'
            ], 200);
        }catch(Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function block(Request $request)
    {
        try {
            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($request) {
                $q->where('exam_id', $request->exam_id);
            })->firstOrFail();

            $studentSchedule->update([
                'block' => $this->fisherYatesShuffle()
            ]);

            return response()->json([
                'message' => 'success'
            ], 200);
        }catch(Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function openBlock(Request $request)
    {
        try {
            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($request) {
                $q->where('exam_id', $request->exam_id);
            })->firstOrFail();

            if($studentSchedule->block == $request->block) {
                $studentSchedule->update([
                    'block' => null
                ]);

                return response()->json([
                    'message' => 'Success'
                ], 200);
            }else{
                return response()->json([
                    'message' => "Error"
                ], 404);
            }
        }catch(Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }
}
