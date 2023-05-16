<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exams = [
            [
                'class' => '10',
                'name' => 'Bahasa Indonesia',
                'status' => 'inactive',
                'is_random' => 1,
                'thumbnail' => 'exam/exam_image.png',
                'description' => '<p>Laravel is a web application framework with expressive, elegant syntax.</p>',
                'time' => 1,
                'teacher_id' => 1
            ],
            [
                'class' => '10',
                'name' => 'Matematika',
                'status' => 'launched',
                'is_random' => 1,
                'thumbnail' => 'exam/exam_image.png',
                'description' => '<p>Laravel is a web application framework with expressive, elegant syntax.</p>',
                'time' => 1,
                'teacher_id' => 1
            ],
            [
                'class' => '11',
                'name' => 'Ipa',
                'status' => 'finished',
                'is_random' => 1,
                'thumbnail' => 'exam/exam_image.png',
                'description' => '<p>Laravel is a web application framework with expressive, elegant syntax.</p>',
                'time' => 1,
                'teacher_id' => 1
            ],
        ];

        foreach ($exams as $value) {
            Exam::create($value);
        }
    }
}
