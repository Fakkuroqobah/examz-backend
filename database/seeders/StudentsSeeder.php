<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = [
            [
                'name' => 'student',
                'class' => '10',
                'username' => 'student',
                'password' => bcrypt('password'),
                'role' => 'student'
            ],
            [
                'name' => 'student2',
                'class' => '10',
                'username' => 'student2',
                'password' => bcrypt('password'),
                'role' => 'student'
            ],
            [
                'name' => 'student3',
                'class' => '10',
                'username' => 'student3',
                'password' => bcrypt('password'),
                'role' => 'student'
            ],
        ];

        foreach ($students as $value) {
            Student::create($value);
        }
    }
}
