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
        $data = StudentSchedule::with(['schedule.exam'])
        ->where('student_id', Auth::user()->id)
        ->whereNull('end_time')
        ->whereHas('schedule.exam', function($q) {
            $q->where('status', 'launched');
        })->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
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
        $totalCorrect = 0;
        $answerStudent = AnswerStudent::where('student_id', Auth::user()->id)
        ->whereHas('question', function($q) use($id) {
            $q->where('exam_id', $id);
        })->orderBy('question_id', 'ASC')->get();

        foreach ($answerStudent as $value) {
            $answerOption = AnswerOption::find($value->answer_option_id);
            if($answerOption->correct == 1) {
                $totalCorrect++;
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
                'total' => $totalCorrect,
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
            $exam = Exam::with('question.answerOption')->findOrFail($data->exam_id);
            if($exam->is_random == 1) {
                $exam = Exam::with(['question' => function($q) {
                    $q->inRandomOrder();
                }, 'question.answerOption'])->findOrFail($data->exam_id);
            }

            $studentSchedule = StudentSchedule::where('student_id', Auth::user()->id)
            ->whereNull('end_time')
            ->whereHas('schedule.exam', function($q) use($data) {
                $q->where('exam_id', $data->exam_id);
            })->firstOrFail();

            $studentSchedule->update([
                'start_time' => now()
            ]);

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