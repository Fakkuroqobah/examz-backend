<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use Exception;

class ExamLaunchController extends Controller
{
    public function index()
    {
        $data = Exam::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function trigger(Request $request, $type)
    {
        $exam = Exam::findOrFail($request->id);

        try {
            $exam->update([
                'status' => $type,
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $exam
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'errors' => ['server' => ['Something went error']]
            ], 500);
        }
    }

    public function triggerRated(Request $request, $type)
    {
        $exam = Exam::findOrFail($request->id);

        try {
            $exam->update([
                'is_rated' => intval($type),
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $exam
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'errors' => ['server' => ['Something went error']]
            ], 500);
        }
    }
}
