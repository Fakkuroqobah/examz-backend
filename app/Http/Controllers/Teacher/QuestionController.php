<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Type;

class QuestionController extends Controller
{
    public function index($id) {
        $exam = Exam::find($id);
        if(!$exam) return abort(404);

        $question = Question::with('answerOption')->where('exam_id', $id)->orderBy('order_number', 'ASC')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $question
        ], 200);
    }

    private function is_answer_option_empty($arr)
    {
        if(is_array($arr)){
            foreach($arr as $value) if(!empty($value['answer_option'])) return false;
        }

        return true;
    }

    private function is_answer_correct_empty($arr)
    {
        if(is_array($arr)){
            foreach($arr as $value) if($value['answer_correct'] && !empty($value['answer_option'])) return false;
        }

        return true;
    }

    public function add(Request $request) {
        
        $request->validate([
            'exam_id' => 'required',
            'subject' => 'required',
            'answer' => 'required'
        ]);
        
        $answers = $request->answer;
        if($this->is_answer_option_empty($answers)) {
            return response()->json([
                'errors' => 'Choice questions require an answer option'
            ], 422);
        }else if($this->is_answer_correct_empty($answers)) {
            return response()->json([
                'errors' => 'Choose the correct answer to this question'
            ], 422);
        }

        $exam = Exam::findOrFail($request->exam_id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Exam must be inactive'
            ], 422);
        }

        DB::transaction(function() use ($request, $answers) {
            $question = Question::create([
                'exam_id' => $request->exam_id,
                'subject' => $request->subject,
                // 'order_number' => $request->order_number
            ]);

            if(!$question) {
                return response()->json([
                    'message' => 'Error'
                ], 500);
            }

            foreach ($answers as $answer) {
                if(!empty($answer['answer_option'])) {
                    AnswerOption::create([
                        'question_id' => $question->id,
                        'subject' => $answer['answer_option'],
                        'correct' => ($answer['answer_correct']) ? $answer['answer_option'] : null
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function delete($id)
    {
        $question = Question::with('exam')->find($id);
        if($question->exam->status != 'inactive') {
            return response()->json([
                'message' => 'Exam must be inactive'
            ], 422);
        }

        if($question == null) {
            return response()->json([
                'message' => 'Question not found'
            ], 404);    
        }

        $question->delete();

        return response()->json([
            'message' => 'Success'
        ], 200);
    }
}
