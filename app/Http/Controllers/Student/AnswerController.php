<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\AnswerStudent;
use App\Models\AnswerOption;
use App\Models\StudentSchedule;
use App\Models\Schedule;
use App\Models\Question;
use App\Models\Exam;
use Exception;
use DateTime;
use DateInterval;
use Auth;

class AnswerController extends Controller
{
    public function examLaunched()
    {
        $data = Schedule::with(['exam', 'studentSchedule'])
        ->where('room_id', Auth::user()->room_id)
        ->whereHas('exam', function($q) {
            $q->where('class', Auth::user()->class)->where('status', 'launched');
        })
        ->get();

        foreach($data as $key => $row) {
            if(!is_null($row->studentSchedule)) {
                if(!is_null($row->studentSchedule->end_time)) {
                    $data->forget($key);
                }
            }
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data->values()
        ], 200);
    }

    public function examFinished()
    {
        $data = StudentSchedule::with(['schedule.exam'])
        ->where('student_id', Auth::user()->id)
        ->whereNotNull('end_time')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function rated($id)
    {
        $totalCorrectChoice = 0;
        $totalCorrectEssay = 0;
        $answerStudent = AnswerStudent::with(['question'])->where('student_id', Auth::user()->id)
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

        $questions = Question::with(['answerOption'])->where('exam_id', $id)->get();
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

    public function token(Request $request, $id)
    {
        $request->validate([
            'token' => 'required',
        ]);

        $data = Schedule::where('token', $request->token)->find($id);
        if(!is_null($data)) {
            $check = StudentSchedule::where('schedule_id', $data->id)->where('student_id', Auth::user()->id)->first();
            if(is_null($check)) {
                StudentSchedule::create([
                    'schedule_id' => $data->id,
                    'student_id' => Auth::user()->id,
                    'start_time' => now()
                ]);
            }else{
                if(!is_null($check->end_time)) {
                    return response()->json([
                        'errors' => ['error' => ['Maaf, kamu sudah menyelesaikan ujian ini']]
                    ], 422);
                }
            }

            $exam = Exam::with('question.answerOption')->findOrFail($data->exam_id);
            if($exam->is_random == 1) {
                // $exam = Exam::with(['question', 'question.answerOption'])->findOrFail($id);
                // $arr = [];
                // foreach($exam->question as $row) {
                //     $arr[] = $row->id;
                // }

                // $parent = Exam::with(['question', 'question.answerOption'])->findOrFail($data->exam_id);
                // $exam = $parent->children()
                //     ->orderByRaw('FIELD(id, ' . implode(',', $arr) . ')')
                //     ->get();

                $exam = Exam::with(['question' => function($q) {
                    $q->inRandomOrder();
                }, 'question.answerOption'])->findOrFail($data->exam_id);
            }

            $databaseDateTime = $data->updated_at;
            $minutesToAdd = $exam->time;
            $databaseTime = DateTime::createFromFormat('Y-m-d H:i:s', $databaseDateTime);
            $updatedTime = clone $databaseTime;
            $updatedTime->add(new DateInterval("PT" . $minutesToAdd . "M"));

            $currentDateTime = new DateTime();
            $diff = $currentDateTime->diff($updatedTime);
            $remainingMinutes = $diff;

            if($currentDateTime < $updatedTime) {
                $exam['remaining_time'] = $remainingMinutes->i;
            }else{
                $exam['remaining_time'] = 0;
            }

            $exam['remaining_time'] = $exam->time;

            return response()->json([
                'message' => 'Success',
                'data' => $exam
            ], 200);
        }else{
            return response()->json([
                'message' => 'Error',
            ], 404);
        }
    }

    public function answer(Request $request)
    {
        $check = AnswerStudent::where('student_id', Auth::user()->id)->where('question_id', $request->question_id)->first();

        try {
            if($request->type == 'choice') {
                if(is_null($check)) {
                    $answerStudent = AnswerStudent::create([
                        'question_id' => $request->question_id,
                        'answer_option_id' => $request->answer_option_id,
                        'student_id' => Auth::user()->id,
                    ]);
                }else{
                    $check->update([
                        'answer_option_id' => $request->answer_option_id 
                    ]);
                }
            }else{
                if(is_null($check)) {
                    $answerStudent = AnswerStudent::create([
                        'question_id' => $request->question_id,
                        'answer_essay' => $request->answer_essay,
                        'student_id' => Auth::user()->id,
                        'score' => -1
                    ]);
                }else{
                    $check->update([
                        'answer_essay' => $request->answer_essay,
                        'score' => -1
                    ]);
                }
            }

            return response()->json([
                'message' => 'success'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function endExam(Request $request)
    {
        try {
            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($request) {
                $q->where('exam_id', $request->exam_id);
            })->firstOrFail();

            $studentSchedule->update([
                'end_time' => now()
            ]);

            return response()->json([
                'message' => 'success'
            ], 200);
        }catch(Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function block(Request $request)
    {
        try {
            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($request) {
                $q->where('exam_id', $request->exam_id);
            })->firstOrFail();

            $studentSchedule->update([
                'block' => $this->fisherYatesShuffle()
            ]);

            return response()->json([
                'message' => 'success'
            ], 200);
        }catch(Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function openBlock(Request $request)
    {
        try {
            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($request) {
                $q->where('exam_id', $request->exam_id);
            })->firstOrFail();

            if($studentSchedule->block == $request->block) {
                $studentSchedule->update([
                    'block' => null
                ]);

                return response()->json([
                    'message' => 'Success'
                ], 200);
            }else{
                return response()->json([
                    'message' => "Error"
                ], 404);
            }
        }catch(Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }
}