<?php

namespace App\Http\Controllers\Supervisior;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerStudent;
use App\Models\Student;
use App\Models\Question;
use App\Models\Exam;
use Exception;

class ExamStartController extends Controller
{
    public function start($id, Request $request)
    {
        $request->validate([
            'class' => 'required'
        ]);

        $sumStudent = Student::select(['id'])->count();
        if($sumStudent == 0) {
            return response()->json([
                'errors' => ['server' => ['Blank student data']],
            ], 422);
        }

        $sumQuestion = Question::select(['id'])->where('exam_id', $id)->first();
        if(is_null($sumQuestion)) {
            return response()->json([
                'errors' => ['server' => ['The question is still empty']],
            ], 422);
        }

        try {
            $exam = Exam::select('id')->where('status', 'inactive')->where('id', $id)->first();

            if(is_null($exam)) {
                return response()->json([
                    'errors' => ['server' => ['The exam is running or finished']],
                ], 422);
            }

            $exam->update([
                'status' => 'launched',
            ]);
    
            return response()->json([
                'message' => 'Success'
            ], 200);

        }catch(Exception $err) {
            return response()->json([
                'errors' => ['server' => [$err->getMessage()]]
            ], 404);
        }
    }

    public function stop(Request $request)
    {
        try {
            $exam = Exam::findOrFail($request->id);
            $exam->update([
                'status' => 'finished',
            ]);

            return response()->json([
                'message' => 'Success'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'errors' => ['server' => ['Something went error']]
            ], 500);
        }
    }
}
