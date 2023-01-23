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
        $examInActive = Exam::whereNull('starts')->get();
        $examActive = Exam::where('starts', '>=', date('Y-m-d H:i:s'))->where('due', '>=', date('Y-m-d H:i:s'))->get(); // Ujian belum mulai tapi sudah di set
        $examLaunched = Exam::where('starts', '<=', date('Y-m-d H:i:s'))->where('due', '>=', date('Y-m-d H:i:s'))->get(); // Ujian yang sedang berlangsung
        $examFinished = Exam::where('starts', '<=', date('Y-m-d H:i:s'))->where('due', '<=', date('Y-m-d H:i:s'))->get(); // Ujian yang sudah selesai

        $sumExamInActive = 0;
        $sumExamActive = 0;
        $sumExamLaunched = 0;
        $sumExamFinished = 0;

        if(isset($examInActive)) $sumExamInActive = count($examInActive);
        if(isset($examActive)) $sumExamActive = count($examActive);
        if(isset($examLaunched)) $sumExamLaunched = count($examLaunched);
        if(isset($examFinished)) $sumExamFinished = count($examFinished);

        return response()->json([
            'message' => 'Success',
            'data' => [
                "examInActive" => $examInActive,
                "examActive" => $examActive,
                "examLaunched" => $examLaunched,
                "examFinished" => $examFinished,
                "sumExamInActive" => $sumExamInActive,
                "sumExamActive" => $sumExamActive,
                "sumExamLaunched" => $sumExamLaunched,
                "sumExamFinished" => $sumExamFinished
            ]
        ], 200);
    }

    // public function index() {
    //     $examInActive = Exam::where('status', 0)->get();
    //     $examLaunched = Exam::where('status', 1)->get();
    //     $examFinished = Exam::where('status', 2)->get();

    //     $sumExamInActive = 0;
    //     $sumExamLaunched = 0;
    //     $sumExamFinished = 0;

    //     if(isset($examInActive)) $sumExamInActive = count($examInActive);
    //     if(isset($examLaunched)) $sumExamLaunched = count($examLaunched);
    //     if(isset($examFinished)) $sumExamFinished = count($examLaunched);

    //     return response()->json([
    //         'message' => 'Success',
    //         'data' => [
    //             "examInActive" => $examInActive,
    //             "examLaunched" => $examLaunched,
    //             "examFinished" => $examLaunched,
    //             "sumExamInActive" => $sumExamInActive,
    //             "sumExamLaunched" => $sumExamLaunched,
    //             "sumExamFinished" => $sumExamLaunched
    //         ]
    //     ], 200);
    // }

    public function showByClass($class)
    {   
        $examInActive = Exam::where('class', $class)->whereNull('starts')->get();
        $examActive = Exam::where('class', $class)->where('starts', '>=', date('Y-m-d H:i:s'))->where('due', '>=', date('Y-m-d H:i:s'))->get();
        $examLaunched = Exam::where('class', $class)->where('starts', '<=', date('Y-m-d H:i:s'))->where('due', '>=', date('Y-m-d H:i:s'))->get();
        $examFinished = Exam::where('class', $class)->where('starts', '<=', date('Y-m-d H:i:s'))->where('due', '<=', date('Y-m-d H:i:s'))->get();

        $sumExamInActive = 0;
        $sumExamActive = 0;
        $sumExamLaunched = 0;
        $sumExamFinished = 0;

        if(isset($examInActive)) $sumExamInActive = count($examInActive);
        if(isset($examActive)) $sumExamActive = count($examActive);
        if(isset($examLaunched)) $sumExamLaunched = count($examLaunched);
        if(isset($examFinished)) $sumExamFinished = count($examFinished);

        return response()->json([
            'message' => 'Success',
            'data' => [
                "examInActive" => $examInActive,
                "examActive" => $examActive,
                "examLaunched" => $examLaunched,
                "examFinished" => $examFinished,
                "sumExamInActive" => $sumExamInActive,
                "sumExamActive" => $sumExamActive,
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
        $exam = Exam::findOrFail($id);
        
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

        $exam = Exam::find($id);
        if($exam->isAssignOrLaunchOrOver()) return $exam->isAssignOrLaunchOrOver();

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
        $exam = Exam::find($id);
        
        if($exam->isAssignOrLaunch()) return $exam->isAssignOrLaunch();
        
        $exam->delete();
        
        $old = explode('/', $exam->thumbnail);
        if($exam->is_default_image($old)) File::delete('storage/exam/' . end($old));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function clone($id)
    {
        $exam = Exam::find($id);
        
        $newExam = $exam->replicate();
        $newExam->name = $exam->name."(copy-".rand(111,999).")";
        $newExam->starts = null;
        $newExam->due = null;
        $newExam->hours = null;
        $newExam->minutes = null;
        $newExam->class = $exam->class;
        $newExam->save();

        foreach ($exam->questions as $question) {
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

    public function answer($participantId, $quizId)
    {
        $categoryById = Category::findOrFail($categoryId);
        $quizzes = Quiz::with('participantQuizzes.participant')->get();
        $participant = Participant::with('participantQuizzes')->where('id', $participantId)->first();
        $answerParticipants = AnswerParticipant::with('quiz', 'question.answerOptions')->where('quiz_id', $quizId)->where('participant_id', $participantId)->get();
        
        return view('admin.participant.participant_answer', compact('quizzes', 'quizId', 'participant', 'categoryById', 'answerParticipants'));
    }
}
