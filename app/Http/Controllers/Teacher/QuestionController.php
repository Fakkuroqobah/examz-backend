<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerOption;
use App\Models\AnswerEssay;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Type;

class QuestionController extends Controller
{
    public function index($id) {
        $exam = Exam::find($id);
        if(!$exam) return abort(404);

        $question = Question::with(['answerOption', 'answerEssay'])->where('exam_id', $id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $question
        ], 200);
    }

    private function is_answer_option_empty($arr)
    {
        if(is_array($arr)) {
            foreach($arr as $value) if(empty(trim($value['answer_option']))) return true;
        }

        return false;
    }

    private function is_answer_correct_empty($arr)
    {
        if(is_array($arr)) {
            foreach($arr as $value) {
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
            'answer' => 'required',
            'type' => 'required'
        ]);

        if($request->type == 'choice') {
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
        }else{
            if(empty(trim($request->answer))) {
                return response()->json([
                    'errors' => ['error' => ['Ekspektasi jawaban wajib diisi']]
                ], 422);
            }
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

            if($request->type == 'choice') {
                foreach ($request->answer as $answer) {
                    AnswerOption::create([
                        'question_id' => $question->id,
                        'subject' => $answer['answer_option'],
                        'correct' => $answer['answer_correct']
                    ]);
                }
            }else{
                AnswerEssay::create([
                    'question_id' => $question->id,
                    'default_answer' => $request->answer,
                ]);
            }

            DB::commit();

            if($request->type == 'choice') {
                $question = Question::with(['answerOption'])->findOrFail($question->id);
            }else{
                $question = Question::with(['answerEssay'])->findOrFail($question->id);
            }

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
            'answer' => 'required',
            'type' => 'required'
        ]);

        if($request->type == 'choice') {
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
        }else{
            if(empty(trim($request->answer))) {
                return response()->json([
                    'errors' => ['error' => ['Ekspektasi jawaban wajib diisi']]
                ], 422);
            }
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

            if($request->type == 'choice') {
                AnswerOption::where('question_id', $id)->delete();
                foreach ($answers as $answer) {
                    AnswerOption::create([
                        'question_id' => $question->id,
                        'subject' => $answer['answer_option'],
                        'correct' => $answer['answer_correct']
                    ]);
                }
            }else{
                AnswerEssay::where('question_id', $id)->delete();
                AnswerEssay::create([
                    'question_id' => $question->id,
                    'default_answer' => $request->answer,
                ]);
            }

            DB::commit();
            
            if($request->type == 'choice') {
                $question = Question::with(['answerOption'])->findOrFail($question->id);
            }else{
                $question = Question::with(['answerEssay'])->findOrFail($question->id);
            }

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
