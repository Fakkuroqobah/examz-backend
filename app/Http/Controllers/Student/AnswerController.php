<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerStudent;
use App\Models\StudentSchedule;
use App\Models\Question;
use App\Models\Exam;
use Exception;
use Auth;
use DateTime;

class AnswerController extends Controller
{
    public function exam($id)
    {
        $exam = Exam::with(['question'])->where('status', 'launched')
            ->where('class', Auth::user()->class)
            ->findOrFail($id);
        
        return response()->json([
            'message' => 'success',
            'data' => [
                'exam' => $exam
            ]
        ], 200);
    }

    public function answer(Request $request)
    {
        $check = AnswerStudent::where('student_id', Auth::user()->id)->where('question', $request->question_id)->first();
        if(is_null($check)) {
            $answerStudent = AnswerStudent::create([
                'question_id' => $request->question_id,
                'answer' => $request->answer,
                'student_id' => Auth::user()->id,
            ]);
        }else{
            $answerStudent->update([
                'answer' => $request->answer
            ]);
        }

        return response()->json([
            'message' => 'success'
        ], 200);
    }

    public function endExam(Request $request)
    {
        try {
            $data = StudentSchedule::select(['id', 'status'])->findOrFail($request->student);

            if($data->status == 1) {
                return response()->json([
                    'message' => 'the test is done'
                ], 422);
            }

            $data->update([
                'status' => 1
            ]);

            return response()->json([
                'message' => 'success'
            ], 200);
        }catch(Exception $err) {
            return response()->json([
                'message' => "Something went error"
            ], 500);
        }
    }
}
