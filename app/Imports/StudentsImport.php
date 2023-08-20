<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Room;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $room = Room::where('name', $row['nama_ruangan'])->firstOrFail();
        
        return new Student([
            'nis' => $row['nis'],
            'name' => $row['nama'],
            'username' => $row['username'],
            'class' => $row['kelas'],
            'room_id' => $room['id'],
            'password' => bcrypt($row['password']),
        ]);
    }
}
