<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Supervisor;
use App\Models\Student;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\StudentSchedule;
use App\Imports\TeachersImport;
use App\Imports\SupervisorsImport;
use App\Imports\StudentsImport;
use App\Imports\RoomsImport;
use App\Imports\SchedulesImport;
use App\Imports\StudentScheduleImport;
use Excel;

class ImportController extends Controller
{
    public function getTeacher()
    {
        $data = Teacher::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function getSupervisor()
    {
        $data = Supervisor::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function getStudent()
    {
        $data = Student::with(['room'])->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function getRoom()
    {
        $data = Room::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function getSchedule()
    {
        $data = Schedule::with(['room', 'supervisor', 'exam'])->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function getStudentSchedule()
    {
        $data = StudentSchedule::with(['schedule', 'student'])->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }


    public function importTeacher(Request $request)
    {
        $last = Teacher::orderBy('id', 'DESC')->first();
        Excel::import(new TeachersImport, $request->file('excel'));

        if(!is_null($last)) $data = Teacher::where('id', '>', $last->id)->get();
        else $data = Teacher::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function importSupervisor(Request $request)
    {
        $last = Supervisor::orderBy('id', 'DESC')->first();
        Excel::import(new SupervisorsImport, $request->file('excel'));

        if(!is_null($last)) $data = Supervisor::where('id', '>', $last->id)->get();
        else $data = Supervisor::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function importStudent(Request $request)
    {
        $last = Student::orderBy('id', 'DESC')->first();
        Excel::import(new StudentsImport, $request->file('excel'));

        if(!is_null($last)) $data = Student::with(['room'])->where('id', '>', $last->id)->get();
        else $data = Student::with(['room'])->all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function importRoom(Request $request)
    {
        $last = Room::orderBy('id', 'DESC')->first();
        Excel::import(new RoomsImport, $request->file('excel'));

        if(!is_null($last)) $data = Room::where('id', '>', $last->id)->get();
        else $data = Room::all();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function importSchedule(Request $request)
    {
        $last = Schedule::orderBy('id', 'DESC')->first();
        Excel::import(new SchedulesImport, $request->file('excel'));

        if(!is_null($last)) $data = Schedule::with(['room', 'supervisor', 'exam'])->where('id', '>', $last->id)->get();
        else $data = Schedule::with(['room', 'supervisor', 'exam'])->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function importStudentSchedule(Request $request)
    {
        $last = StudentSchedule::orderBy('id', 'DESC')->first();
        Excel::import(new StudentScheduleImport, $request->file('excel'));

        if(!is_null($last)) $data = StudentSchedule::with(['room', 'student'])->where('id', '>', $last->id)->get();
        else $data = StudentSchedule::with(['room', 'student'])->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 201);
    }
}