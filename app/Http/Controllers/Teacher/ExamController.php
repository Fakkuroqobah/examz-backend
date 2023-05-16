<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AnswerStudent;
use App\Models\Student;
use App\Models\Question;
use App\Models\Exam;
use Exception;
use Storage;
use File;
use Auth;

class ExamController extends Controller
{
    public function index() {
        $examInActive = Exam::where('status', 'inactive')->where('teacher_id', Auth::guard('teacher')->user()->id)->get();
        $examLaunched = Exam::where('status', 'launched')->where('teacher_id', Auth::guard('teacher')->user()->id)->get();
        $examFinished = Exam::where('status', 'finished')->where('teacher_id', Auth::guard('teacher')->user()->id)->get();

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
            'class' => 'required',
            'description' => 'nullable',
            'thumbnail' => 'nullable',
            'is_random' => 'required',
            'time' => 'required',
        ]);

        if(!empty($request->thumbnail)) {
            $thumbnailName = time() . Str::random(5) . "." .$request->thumbnail['extension'];
            $thumbnailPath = 'exam/' . $thumbnailName;

            $exam = Exam::create([
                'name' => $request->name,
                'thumbnail' => $thumbnailPath,
                'description' => $request->description,
                'class' => $request->class,
                'is_random' => ($request->is_random) ? 1 : 0,
                'time' => $request->time,
                'teacher_id' => Auth::guard('teacher')->user()->id
            ]);

            Storage::disk('public')->put($thumbnailPath, base64_decode($request->thumbnail['byte']));
        }else{
            $exam = Exam::create([
                'name' => $request->name,
                'description' => $request->description,
                'class' => $request->class,
                'is_random' => ($request->is_random) ? 1 : 0,
                'time' => $request->time,
                'teacher_id' => Auth::guard('teacher')->user()->id
            ]);
        }

        if(!$exam) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        $exam = Exam::findOrFail($exam->id);
        return response()->json([
            'message' => 'Success',
            'data' => $exam
        ], 201);
    }

    public function edit(Request $request, $id) {
        $request->validate([
            'name' => 'required|max:30',
            'class' => 'required',
            'description' => 'nullable',
            'thumbnail' => 'nullable',
            'is_random' => 'required',
            'time' => 'required',
        ]);

        $exam = Exam::findOrFail($id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Ujian harus belum aktif'
            ], 422);
        }

        if(!isset($request->thumbnail)) {
            $exam->update([
                'name' => $request->name,
                'description' => $request->description,
                'class' => $request->class,
                'is_random' => ($request->is_random) ? 1 : 0,
                'time' => $request->time,
            ]);
        }else {
            $thumbnailName = time() . Str::random(5) . "." . $request->thumbnail['extension'];
            $thumbnailPath = 'exam/' . $thumbnailName;

            $thumbnailOld = $exam->thumbnail;

            $exam->update([
                'name' => $request->name,
                'thumbnail' => $thumbnailPath,
                'description' => $request->description,
                'class' => $request->class,
                'is_random' => ($request->is_random) ? 1 : 0,
                'time' => $request->time,
            ]);

            Storage::disk('public')->put($thumbnailPath, base64_decode($request->thumbnail['byte']));

            $old = explode('/', $thumbnailOld);
            if($exam->is_default_image($old)) File::delete('storage/exam/' . end($old));
        }

        if(!$exam) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        $exam = Exam::findOrFail($exam->id);
        return response()->json([
            'message' => 'Success',
            'data' => $exam
        ], 200);
    }

    public function delete($id)
    {
        $exam = Exam::where('teacher_id', Auth::guard('teacher')->user()->id)->findOrFail($id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Ujian harus belum aktif'
            ], 422);
        }
        
        $exam->delete();
        
        $old = explode('/', $exam->thumbnail);
        if($exam->is_default_image($old)) File::delete('storage/exam/' . end($old));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function answer($examId, $studentId)
    {
        $exam = Exam::with('studentSchedule.student')->get();
        $student = Student::with('studentSchedule')->where('id', $studentId)->firstOrFail();
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
