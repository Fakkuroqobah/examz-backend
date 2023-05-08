<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Question;
use App\Models\Student;
use App\Models\Exam;
use Exception;
use Auth;

class ExamStartController extends Controller
{
    private function fisherYatesShuffle($limit = 5) {
        $alphabet = range('a', 'z');
        $len = count($alphabet);
        
        for ($i = $len - 1; $i > 0; $i--) {
          $j = mt_rand(0, $i);
          [$alphabet[$i], $alphabet[$j]] = [$alphabet[$j], $alphabet[$i]];
        }
        
        $shuffled = array_slice($alphabet, 0, $limit);
        return implode('', $shuffled);
    }

    public function index()
    {
        $data = Schedule::with(['exam'])->where('supervisor_id', Auth::guard('supervisor')->user()->id)->whereHas('exam', function($q) {
            $q->where('status', 'launched');
        })->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function start(Request $request, $id)
    {
        // $sumStudent = Student::select(['id'])->count();
        // if($sumStudent == 0) {
        //     return response()->json([
        //         'errors' => ['error' => ['Blank student data']],
        //     ], 422);
        // }

        // $sumQuestion = Question::select(['id'])->where('exam_id', $id)->first();
        // if(is_null($sumQuestion)) {
        //     return response()->json([
        //         'errors' => ['error' => ['The question is still empty']],
        //     ], 422);
        // }

        
        $data = Schedule::with(['exam'])->find($id);
        // return response()->json([
        //     'message' => 'Success',
        //     'data' => $data
        // ], 404);

        try {
            // if(is_null($exam)) {
            //     return response()->json([
            //         'errors' => ['error' => ['The exam is running or finished']],
            //     ], 422);
            // }

            $data->update([
                'token' => $this->fisherYatesShuffle(),
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
