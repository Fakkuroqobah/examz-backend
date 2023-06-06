<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Room;
use App\Models\Schedule;
use Exception;

class DeleteController extends Controller {
    public function deleteTeacher($id)
    {
        try {
            $data = Teacher::findOrFail($id);
            $data->delete();
        } catch (Exception $e) {
            if($e->getCode() == 23000) {
                return response()->json([
                    'errors' => ['error' => ['Guru tidak bisa dihapus karena terdapat pada jadwal ujian']]
                ], 422);
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function deleteStudent($id)
    {
        try {
            $data = Student::findOrFail($id);
            $data->delete();
        } catch (Exception $e) {
            if($e->getCode() == 23000) {
                return response()->json([
                    'errors' => ['error' => ['Siswa tidak bisa dihapus karena telah melakukan ujian']]
                ], 422);
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function deleteSupervisor($id)
    {
        try {
            $data = Supervisor::findOrFail($id);
            $data->delete();
        } catch (Exception $e) {
            if($e->getCode() == 23000) {
                return response()->json([
                    'errors' => ['error' => ['Pengawas tidak bisa dihapus karena terdapat pada jadwal ujian']]
                ], 422);
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function deleteRoom($id)
    {
        try {
            $data = Room::findOrFail($id);
            $data->delete();
        } catch (Exception $e) {
            if($e->getCode() == 23000) {
                return response()->json([
                    'errors' => ['error' => ['Ruangan tidak bisa dihapus karena terdapat pada jadwal ujian']]
                ], 422);
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function deleteSchedule($id)
    {
        try {
            $data = Schedule::findOrFail($id);
            $data->delete();
        } catch (Exception $e) {
            if($e->getCode() == 23000) {
                return response()->json([
                    'errors' => ['error' => ['Jadwal tidak bisa dihapus karena ujian sudah aktif']]
                ], 422);
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }
}