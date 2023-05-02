<?php

namespace App\Imports;

use App\Models\StudentSchedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentScheduleImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new StudentSchedule([
            'schedule_id' => $row['jadwal'],
            'student_id' => $row['siswa'],
        ]);
    }
}
