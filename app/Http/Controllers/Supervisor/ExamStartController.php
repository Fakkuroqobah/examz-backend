<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Question;
use App\Models\StudentSchedule;
use App\Models\Student;
use App\Models\Exam;
use Exception;
use Auth;

class ExamStartController extends Controller
{
    public function index()
    {
        $data = Schedule::with(['exam'])
        ->where('supervisor_id', Auth::guard('supervisor')->user()->id)
        ->whereHas('exam', function($q) {
            $q->where('status', 'launched');
        })->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    // id schedule
    public function student($id)
    {
        $data = StudentSchedule::with('student')->where('schedule_id', $id)->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function start(Request $request, $id)
    {
        $data = Schedule::with(['exam'])->find($id);

        try {
            $data->update([
                'token' => $this->fisherYatesShuffle([]),
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $data
            ], 200);

        }catch(Exception $err) {
            return response()->json([
                'errors' => ['server' => [$err->getMessage()]]
            ], 404);
        }
    }

    public function stop(Request $request)
    {
        try {
            $exam = Exam::findOrFail($request->id);
            $exam->update([
                'status' => 'finished',
            ]);

            return response()->json([
                'message' => 'Success'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'errors' => ['server' => ['Something went error']]
            ], 500);
        }
    }
}
