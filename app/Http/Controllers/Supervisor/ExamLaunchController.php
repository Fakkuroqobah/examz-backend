<?php

namespace App\Http\Controllers\Supervisior;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\StudentExam;
use App\Models\AnswerStudent;
use App\Models\Student;
use App\Models\Question;
use App\Models\Exam;
use Exception;

class ExamLaunchController extends Controller
{
    public function launch($id, Request $request)
    {
        $request->validate([
            'due' => 'required',
            'hours' => 'required',
            'minutes' => 'required',
            'class' => 'required'
        ]);

        $start = now();

        if($start >= $request->due) {
            return response()->json([
                'errors' => ['server' => ['The start date cannot be greater than the due date']]
            ], 422);
        }

        $sumStudent = Student::select(['id'])->count();
        if($sumStudent == 0) {
            return response()->json([
                'errors' => ['server' => ['Blank student data']],
                'type' => 1
            ], 422);
        }

        $sumQuestion = Question::select(['id'])->where('exam_id', $id)->first();
        if($sumQuestion == null) {
            return response()->json([
                'errors' => ['server' => ['The question is still empty']],
            ], 422);
        }

        try {
            $exam = Exam::select('id')->whereNull('starts')->where('id', $id)->first();

            if($exam == null) throw new Exception("The exam is running");

            DB::transaction(function() use ($request, $start, $exam) {
                $students = Student::select(['id'])->where('class', $request->class)->get()->toArray();
                $arr = [];

                foreach ($students as $value) {
                    $arr[] = [
                        'exam_id' => $exam->id, 
                        'student_id' => $value['id']
                    ];
                }

                StudentExam::insert($arr);

                $exam->update([
                    'starts' => $start,
                    'due' => $request->due,
                    'hours' => $request->hours,
                    'minutes' => $request->minutes,
                    'is_random' => ($request->random == 'true') ? true : false
                ]);
            });
    
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
            $exam = Exam::find($request->id);

            DB::transaction(function() use ($request, $exam) {
                $exam->update([
                    'starts' => null,
                    'due' => null,
                    'hours' => null,
                    'minutes' => null
                ]);

                StudentExam::select('id')->where('exam_id', $request->id)->delete();
                AnswerStudent::select('id')->where('exam_id', $request->id)->delete();
            });

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
