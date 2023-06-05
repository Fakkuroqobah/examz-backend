<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Room;

class EditController extends Controller {
    public function editTeacher(Request $request, $id) {
        $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:30',
            'password' => 'nullable',
        ]);

        $data = Teacher::findOrFail($id);
        if(isset($request->password) && !empty(trim($request->password))) {
            $data->update([
                'name' => $request->name,
                'username' => $request->username,
                'password' => bcrypt($request->password),
            ]);
        }else{
            $data->update([
                'name' => $request->name,
                'username' => $request->username
            ]);
        }
        
        if(!$data) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function editStudent(Request $request, $id) {
        $request->validate([
            'name' => 'required|max:255',
            'class' => 'required',
            'username' => 'required|max:30',
            'password' => 'nullable',
        ]);

        $data = Student::findOrFail($id);
        if(isset($request->password) && !empty(trim($request->password))) {
            $data->update([
                'name' => $request->name,
                'class' => $request->class,
                'username' => $request->username,
                'password' => bcrypt($request->password),
            ]);
        }else{
            $data->update([
                'name' => $request->name,
                'class' => $request->class,
                'username' => $request->username
            ]);
        }
        
        if(!$data) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function editSupervisor(Request $request, $id) {
        $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:30',
            'password' => 'nullable',
        ]);

        $data = Supervisor::findOrFail($id);
        if(isset($request->password) && !empty(trim($request->password))) {
            $data->update([
                'name' => $request->name,
                'username' => $request->username,
                'password' => bcrypt($request->password),
            ]);
        }else{
            $data->update([
                'name' => $request->name,
                'username' => $request->username
            ]);
        }
        
        if(!$data) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function editRoom(Request $request, $id) {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $data = Room::findOrFail($id);
        $data->update([
            'name' => $request->name
        ]);
        
        if(!$data) {
            return response()->json([
                'message' => 'Error'
            ], 500);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }
}