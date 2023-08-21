<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AnswerStudent;
use App\Models\StudentSchedule;
use App\Models\Question;
use App\Models\AnswerOption;
use App\Models\Exam;
use App\Exports\RatedExport;
use Excel;
use Auth;

class StudentRatedController extends Controller
{
    public function index() {
        $data = Exam::where('teacher_id', Auth::guard('teacher')->user()->id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    // id exam
    public function detailStudent($id, $class)
    {
        $data = StudentSchedule::with(['student'])
        ->whereHas('schedule.exam', function($q) use($id) {
            $q->where('id', $id);
        })->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    // id student
    public function detailRated($studentId, $examId)
    {
        $totalCorrectChoice = 0;
        $totalCorrectEssay = 0;
        $answerStudent = AnswerStudent::with(['question'])->where('student_id', $studentId)
        ->whereHas('question', function($q) use($examId) {
            $q->where('exam_id', $examId);
        })->orderBy('question_id', 'ASC')->get();

        foreach ($answerStudent as $value) {
             if($value->question->type == 'choice') {
                $answerOption = AnswerOption::find($value->answer_option_id);
                if($answerOption->correct == 1) {
                    $totalCorrectChoice += $value->score;
                }
            }else{
                if($value->score == -1) {
                    $totalCorrectEssay += 0;
                }else{
                    $totalCorrectEssay += $value->score;
                }
            }
        }

        $questions = Question::with(['answerOption'])->where('exam_id', $examId)->get();
        foreach ($questions as $value) {
            $value['answer'] = 0;
            foreach ($answerStudent as $row) {
                if($row->question_id == $value->id) {
                    $value['answer'] = $row->answer_option_id;
                    break;
                }
            }
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'score_choice' => $totalCorrectChoice,
                'score_essai' => $totalCorrectEssay,
                'answer_student' => $answerStudent,
                'questions' => $questions
            ]
        ], 200);
    }

    public function updateRated(Request $request, $id)
    {
        $data = AnswerStudent::findOrFail($id);
        $data->update([
            'score' => $request->score
        ]);

        return $this->detailRated($request->studentId, $request->examId);
    }

    public function export($id) // exam id
    {
        $student = StudentSchedule::with(['student'])
        ->whereHas('schedule.exam', function($q) use($id) {
            $q->where('id', $id);
        })->get();

        $data = [];
        foreach ($student as $row) {
            $totalCorrectChoice = 0;
            $totalCorrectEssay = 0;
            $answerStudent = AnswerStudent::with(['question'])->where('student_id', $row->student_id)
            ->whereHas('question', function($q) use($id) {
                $q->where('exam_id', $id);
            })->orderBy('question_id', 'ASC')->get();
    
            foreach ($answerStudent as $value) {
                if($value->question->type == 'choice') {
                    $answerOption = AnswerOption::find($value->answer_option_id);
                    if($answerOption->correct == 1) {
                        $totalCorrectChoice += $value->score;
                    }
                }else{
                    if($value->score == -1) {
                        $totalCorrectEssay += 0;
                    }else{
                        $totalCorrectEssay += $value->score;
                    }
                }
            }

            $data[] = [
                'nis' => $row->student->nis,
                'class' => $row->student->class,
                'name' => $row->student->name,
                'score_choice' => $totalCorrectChoice,
                'score_essai' => $totalCorrectEssay,
            ];
        }

        return Excel::download(new RatedExport($data), 'nilai.xlsx');
    }
}
