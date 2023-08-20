<?php

namespace App\Imports;

use App\Models\Supervisor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupervisorsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Supervisor([
            'code' => $row['kode'],
            'name' => $row['nama'],
            'username' => $row['username'],
            'password' => bcrypt($row['password']),
        ]);
    }
}
