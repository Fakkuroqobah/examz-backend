<?php

namespace Database\Seeders;

use App\Models\StudentSchedule;
use Illuminate\Database\Seeder;

class StudentScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studentSchedule = [
            [
                'room_id' => '1',
                'student_id' => '1',
            ],
        ];

        foreach ($studentSchedule as $value) {
            StudentSchedule::create($value);
        }
    }
}
