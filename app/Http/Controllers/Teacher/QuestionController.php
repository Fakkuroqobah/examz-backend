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

        $questions = Question::with('answerOptions')->where('exam_id', $id)->orderBy('order_number', 'ASC')->get();
        $sumQuestionByType = DB::table('questions')->where('exam_id', $id)
        ->select(
            DB::raw('sum(type_id = 1) sumQuestionOption'),
            DB::raw('sum(type_id = 2) sumQuestionMultipleChoice'),
            DB::raw('sum(type_id = 3) sumQuestionEssai')
        )->get();

        $sumQuestionOption = $sumQuestionByType[0]->sumQuestionOption;
        $sumQuestionMultipleChoice = $sumQuestionByType[0]->sumQuestionMultipleChoice;
        $sumQuestionEssai = $sumQuestionByType[0]->sumQuestionEssai;

        if($sumQuestionOption == null) $sumQuestionOption = 0;
        if($sumQuestionMultipleChoice == null) $sumQuestionMultipleChoice = 0;
        if($sumQuestionEssai == null) $sumQuestionEssai = 0;

        return response()->json([
            'message' => 'Success',
            'data' => [
                'exam' => $exam,
                'questions' => $questions,
                'sumQuestionOption' => intval($sumQuestionOption),
                'sumQuestionMultipleChoice' => intval($sumQuestionMultipleChoice),
                'sumQuestionEssai' => intval($sumQuestionEssai)
            ]
        ], 200);
    }

    public function viewAdd($id) {
        $types = Type::all();
        
        $exam = Exam::select('id', 'name', 'starts', 'due')->find($id);
        if($exam->isActive() || $exam->isLaunched() || $exam->isFinished()) return abort(404);
        
        $last_question = Question::where('exam_id', $exam->id)->orderBy('order_number', 'DESC')->first();
        
        $number = 1;
        if($last_question != null) $number = ++$last_question->order_number;

        return response()->json([
            'message' => 'Success',
            'data' => [
                'types' => $types,
                'exam' => $exam,
                'number' => $number
            ]
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
            'type' => 'required',
            'subject' => 'required',
            'narrative' => 'nullable'
        ]);
        
        $answers = null;
        if($request->type == 1 || $request->type == 2) {
            $answers = json_decode($request->answer, true);

            if($this->is_answer_option_empty($answers)) {
                return response()->json([
                    'errors' => ['options' => ['Choice questions require an answer option']]
                ], 422);
            }else if($this->is_answer_correct_empty($answers)) {
                return response()->json([
                    'errors' => ['options' => ['Choose the correct answer to this question']]
                ], 422);
            }
        }else{
            if(empty($request->answer)) {
                return response()->json([
                    'errors' => ['options' => ['The correct answer of the essay is required']]
                ], 422);
            }
        }

        $exam = Exam::select('id', 'starts', 'due')->find($request->exam_id);
        if($exam->isAssignOrLaunchOrOver()) return $exam->isAssignOrLaunchOrOver();

        DB::transaction(function() use ($request, $answers) {
            $question = Question::create([
                'exam_id' => $request->exam_id,
                'type_id' => $request->type,
                'subject' => $request->subject,
                'narrative' => $request->narrative,
                'order_number' => $request->order_number
            ]);

            if(!$question) {
                return response()->json([
                    'message' => 'Error'
                ], 500);
            }

            if($request->type == 1 || $request->type == 2) {
                foreach ($answers as $answer) {
                    if(!empty($answer['answer_option'])) {
                        AnswerOption::create([
                            'question_id' => $question->id,
                            'subject' => $answer['answer_option'],
                            'correct' => ($answer['answer_correct']) ? $answer['answer_option'] : null
                        ]);
                    }
                }
            }else{
                AnswerOption::create([
                    'question_id' => $question->id,
                    'subject' => 'essai',
                    'correct' => $request->answer,
                    'default_answer' => $request->default_answer
                ]);
            }
        });

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function delete($id)
    {
        $question = Question::with('exam')->find($id);
        if($question->exam->isAssignOrLaunchOrOver()) return $question->exam->isAssignOrLaunchOrOver();

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
