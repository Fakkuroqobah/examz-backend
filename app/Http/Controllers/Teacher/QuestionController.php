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

        $question = Question::with('answerOption')->where('exam_id', $id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $question
        ], 200);
    }

    private function is_answer_option_empty($arr)
    {
        if(is_array($arr)) {
            foreach($arr as $value) if(!empty($value['answer_option'])) return false;
        }

        return true;
    }

    private function is_answer_correct_empty($arr)
    {
        if(is_array($arr)) {
            foreach($arr as $value) {
                // $correct;
                // if($value['answer_correct'] == "true") {
                //     $correct = true;
                // }else{
                //     $correct = false;
                // }

                // if($correct && !empty($value['answer_option'])) return false;
                if($value['answer_correct']) return false;
            }
        }

        return true;
    }

    public function add(Request $request)
    {
        $request->validate([
            'exam_id' => 'required',
            'subject' => 'required',
            'answer' => 'required'
        ]);

        $answers = $request->answer;
        if($this->is_answer_option_empty($answers)) {
            return response()->json([
                'errors' => ['error' => ['Terdapat opsi jawaban yang kosong']]
            ], 422);
        }
        
        if($this->is_answer_correct_empty($answers)) {
            return response()->json([
                'errors' => ['error' => ['Pilih salah satu opsi jawaban yang benar']]
            ], 422);
        }

        $exam = Exam::findOrFail($request->exam_id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Ujian harus belum aktif'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $question = Question::create([
                'exam_id' => $request->exam_id,
                'subject' => $request->subject,
            ]);

            if(!$question) {
                return response()->json([
                    'message' => 'Error'
                ], 500);
            }

            foreach ($answers as $answer) {
                if(!empty($answer['answer_option'])) {
                    // $correct;
                    // if($answer['answer_correct'] == "true") {
                    //     $correct = true;
                    // }else{
                    //     $correct = false;
                    // }

                    AnswerOption::create([
                        'question_id' => $question->id,
                        'subject' => $answer['answer_option'],
                        'correct' => $answer['answer_correct']
                        // 'correct' => ($correct) ? $answer['answer_option'] : null
                    ]);
                }
            }

            DB::commit();
            $question = Question::with(['answerOption'])->findOrFail($question->id);
            return response()->json([
                'message' => 'Success',
                'data' => $question
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'exam_id' => 'required',
            'subject' => 'required',
            'answer' => 'required'
        ]);

        $answers = $request->answer;
        if($this->is_answer_option_empty($answers)) {
            return response()->json([
                'errors' => ['error' => ['Terdapat opsi jawaban yang kosong']]
            ], 422);
        }
        
        if($this->is_answer_correct_empty($answers)) {
            return response()->json([
                'errors' => ['error' => ['Pilih salah satu opsi jawaban yang benar']]
            ], 422);
        }

        $exam = Exam::findOrFail($request->exam_id);
        if($exam->status != 'inactive') {
            return response()->json([
                'message' => 'Ujian harus belum aktif'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $question = Question::findOrFail($id);
            $question->update([
                'subject' => $request->subject,
            ]);

            if(!$question) {
                return response()->json([
                    'message' => 'Error'
                ], 500);
            }

            AnswerOption::where('question_id', $id)->delete();
            foreach ($answers as $answer) {
                if(!empty($answer['answer_option'])) {
                    $correct;
                    if($answer['answer_correct'] == "true") {
                        $correct = true;
                    }else{
                        $correct = false;
                    }

                    AnswerOption::create([
                        'question_id' => $question->id,
                        'subject' => $answer['answer_option'],
                        'correct' => ($correct) ? $answer['answer_option'] : null
                    ]);
                }
            }

            DB::commit();
            $question = Question::with(['answerOption'])->findOrFail($question->id);
            return response()->json([
                'message' => 'Success',
                'data' => $question
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        $question = Question::with('exam')->find($id);
        if($question->exam->status != 'inactive') {
            return response()->json([
                'message' => 'Ujian harus belum aktif'
            ], 422);
        }

        if($question == null) {
            return response()->json([
                'message' => 'Pertanyaan tidak ditemukan'
            ], 404);
        }

        $question->delete();

        return response()->json([
            'message' => 'Success'
        ], 200);
    }
}
