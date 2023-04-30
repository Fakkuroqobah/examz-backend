<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerStudent;
use App\Models\StudentExam;
use App\Models\Question;
use App\Models\Exam;
use Exception;
use Auth;
use DateTime;

class AnswerController extends Controller
{
    public function generateExam($id, $exam, $student)
    {
        if($exam->is_random == 1) {
            $questions = Question::select(['id', 'exam_id'])->where('exam_id', $id)->inRandomOrder()->get();
        }else{
            $questions = Question::select(['id', 'exam_id'])->where('exam_id', $id)->get();
        }

        DB::transaction(function() use ($id, $questions, $student) {
            foreach ($questions as $question) {
                AnswerStudent::create([
                    'exam_id' => $id,
                    'student_id' => Auth::user()->id,
                    'question_id' => $question->id
                ]);
            }

            $student->update(['is_generate' => 1]);
        });

        return response()->json([
            'message' => 'Generate test has completed'
        ], 201);
    }

    public function exam($id)
    {
        $student = StudentExam::select(['id', 'exam_id', 'student_id', 'status', 'is_generate'])
            ->where('student_id', Auth::user()->id)
            ->where('exam_id', $id)
            ->first();
        
        $exam = Exam::where('status', 'inactive')
            ->where('class', Auth::user()->class)
            ->findOrFail($id);

        if($student->is_generate == 0 && $student->status == 0) {
            $this->generateExam($id, $exam, $student);

            $student = StudentExam::select(['id', 'exam_id', 'student_id', 'status'])
                ->where('student_id', Auth::user()->id)
                ->where('exam_id', $id)
                ->first();

        }else if($student->status == 1) return abort(404);
        
        $questions = AnswerStudent::select(['answer_student.id', 'answer_student.exam_id', 'answer_student.student_id', 'answer_student.question_id'])
            ->with(['question.answerOption' => function($q) {
                $q->select(['id', 'question_id', 'subject', 'default_answer']);
            }])->where('student_id', Auth::user()->id)->where('exam_id', $id)->get();
        
        return response()->json([
            'message' => 'success',
            'data' => [
                'student' => $student,
                'exam' => $exam,
                'questions' => $questions
            ]
        ], 200);
    }

    public function answer(Request $request)
    {
        $answerStudent = AnswerStudent::select(['id', 'question_id'])->with(['question' => function($q) {
            $q->select('id', 'type_id');
        }])->findOrFail($request->questionId);
        
        if(!empty($request->answer)) {
            $answerStudent->update([
                'answer' => $request->answer
            ]);
        }else{
            $answerStudent->update([
                'answer' => null
            ]);
        }

        return response()->json([
            'message' => 'success'
        ], 200);
    }

    public function endExam(Request $request)
    {
        try {
            $studentExam = StudentExam::select(['id', 'status'])->findOrFail($request->student);

            if($studentExam->status == 1) {
                return response()->json([
                    'message' => 'the test is done'
                ], 422);
            }

            DB::transaction(function() use ($request, $studentExam) {
                foreach ($request->answers as $answer) {
                    $answerStudent = AnswerStudent::select(['id', 'question_id', 'answer'])->with(['question' => function($q) {
                        $q->select('id', 'type_id');
                    }])->findOrFail($answer['questionId']);

                    if($answerStudent->answer == null) {
                        if(!empty($answer['answer'])) {
                            $answerStudent->update([
                                'answer' => $answer['answer']
                            ]);
                        }
                    }
                }
                
                $studentExam->update([
                    'status' => 1
                ]);
            });

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
