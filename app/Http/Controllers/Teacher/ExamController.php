<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StudentExam;
use App\Models\AnswerStudent;
use App\Models\Student;
use App\Models\Question;
use App\Models\Exam;
use Exception;
use File;

class ExamController extends Controller
{
    public function index() {
        $examInActive = Exam::where('status', 'inactive')->get();
        $examLaunched = Exam::where('status', 'launched')->get();
        $examFinished = Exam::where('status', 'finished')->get();

        $sumExamInActive = 0;
        $sumExamLaunched = 0;
        $sumExamFinished = 0;

        if(isset($examInActive)) $sumExamInActive = count($examInActive);
        if(isset($examLaunched)) $sumExamLaunched = count($examLaunched);
        if(isset($examFinished)) $sumExamFinished = count($examFinished);

        return response()->json([
            'message' => 'Success',
            'data' => [
                "examInActive" => $examInActive,
                "examLaunched" => $examLaunched,
                "examFinished" => $examFinished,
                "sumExamInActive" => $sumExamInActive,
                "sumExamLaunched" => $sumExamLaunched,
                "sumExamFinished" => $sumExamFinished
            ]
        ], 200);
    }

    public function showByClass($class)
    {
        $examInActive = Exam::where('class', $class)->where('status', 'inactive')->get();
        $examLaunched = Exam::where('class', $class)->where('status', 'launched')->get();
        $examFinished = Exam::where('class', $class)->where('status', 'finished')->get();

        $sumExamInActive = 0;
        $sumExamLaunched = 0;
        $sumExamFinished = 0;

        if(isset($examInActive)) $sumExamInActive = count($examInActive);
        if(isset($examLaunched)) $sumExamLaunched = count($examLaunched);
        if(isset($examFinished)) $sumExamFinished = count($examFinished);

        return response()->json([
            'message' => 'Success',
            'data' => [
                "examInActive" => $examInActive,
                "examLaunched" => $examLaunched,
                "examFinished" => $examFinished,
                "sumExamInActive" => $sumExamInActive,
                "sumExamLaunched" => $sumExamLaunched,
                "sumExamFinished" => $sumExamFinished
            ]
        ], 200);
    }

    public function add(Request $request) {
        $request->validate([
            'name' => 'required|max:30',
            'thumbnail.*' => 'image|mimes:jpeg,jpg,png|max:2048',
            'class' => 'required',
            'description' => 'nullable'
        ]);

        if(!empty($request->file('thumbnail'))) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . Str::random(5) . '.' . $thumbnail->clientExtension();

            $exam = Exam::create([
                'name' => $request->name,
                'thumbnail' => 'exam/' . $thumbnailName,
                'description' => $request->description,
                'class' => $request->class
            ]);

            $request->thumbnail->move(storage_path('app/public/exam'), $thumbnailName);
        }else{
            $exam = Exam::create([
                'name' => $request->name,
                'description' => $request->description,
                'class' => $request->class
            ]);
        }

        if(!$exam) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $exam
        ], 201);
    }

    public function detail($id) {
        $exam = Exam::with(['question'])->findOrFail($id);
        
        return response()->json([
            'message' => 'Success',
            'data' => [
                'exam' => $exam
            ]
        ], 200);
    }

    public function edit(Request $request, $id) {
        $request->validate([
            'name' => 'required|max:30',
            'thumbnail.*' => 'image|mimes:jpeg,jpg,png|max:2048',
            'class' => 'required',
            'description' => 'nullable'
        ]);

        $exam = Exam::findOrFail($id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Exam must be inactive'
            ], 422);
        }

        if(empty($request->file('thumbnail'))) {
            $exam->update([
                'name' => $request->name,
                'description' => $request->description,
                'class' => $request->class
            ]);
        }else {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . Str::random(5) . '.' . $thumbnail->clientExtension();
            $thumbnailOld = $exam->thumbnail;

            $exam->update([
                'name' => $request->name,
                'thumbnail' => 'exam/' . $thumbnailName,
                'description' => $request->description,
                'class' => $request->class
            ]);

            $request->thumbnail->move(storage_path('app/public/exam'), $thumbnailName);

            $old = explode('/', $thumbnailOld);
            if($exam->is_default_image($old)) File::delete('storage/exam/' . end($old));
        }

        if(!$exam) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'exam' => $exam
            ]
        ], 200);
    }

    public function delete($id)
    {
        $exam = Exam::findOrFail($id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Exam must be inactive'
            ], 422);
        }
        
        $exam->delete();
        
        $old = explode('/', $exam->thumbnail);
        if($exam->is_default_image($old)) File::delete('storage/exam/' . end($old));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function clone($id)
    {
        $exam = Exam::findOrFail($id);
        
        $newExam = $exam->replicate();
        $newExam->name = $exam->name."(copy-".rand(111,999).")";
        $newExam->class = $exam->class;
        $newExam->save();

        foreach ($exam->question as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->exam_id = $newExam->id;
            $newQuestion->save();

            foreach ($question->answerOptions as $answerOption) {
                $newAnswerOption = $answerOption->replicate();
                $newAnswerOption->question_id = $newQuestion->id;
                $newAnswerOption->save();
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function answer($studentId, $examId)
    {
        $exam = Exam::with('studentExam.student')->get();
        $student = Student::with('studentExam')->where('id', $studentId)->firstOrFail();
        $answerStudent = AnswerStudent::with('exam', 'question.answerOption')->where('exam_id', $examId)->where('student_id', $studentId)->get();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'exam' => $exam,
                'student' => $student,
                'answerStudent' => $answerStudent
            ]
        ], 200);
    }
}
