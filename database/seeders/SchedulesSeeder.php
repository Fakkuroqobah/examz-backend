<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schedule = [
            [
                'room_id' => '1',
                'supervisor_id' => '1',
                'exam_id' => '1',
            ],
            [
                'room_id' => '2',
                'supervisor_id' => '2',
                'exam_id' => '2',
            ],
        ];

        foreach ($schedule as $value) {
            Schedule::create($value);
        }
    }
}
