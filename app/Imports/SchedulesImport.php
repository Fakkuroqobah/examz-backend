<?php

namespace App\Imports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Room;
use App\Models\Supervisor;
use App\Models\Exam;

class SchedulesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $room = Room::where('name', $row['ruangan'])->firstOrFail();
        $supervisor = Supervisor::where('code', $row['kode_pengawas'])->firstOrFail();
        $exam = Exam::where('id', $row['id_mata_ujian'])->firstOrFail();

        return new Schedule([
            'room_id' => $room->id,
            'supervisor_id' => $supervisor->id,
            'exam_id' => $row['id_mata_ujian'],
        ]);
    }
}
