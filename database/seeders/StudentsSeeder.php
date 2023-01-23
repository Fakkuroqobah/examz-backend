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
        $student = [
            'name' => 'student',
            'class' => '10',
            'username' => 'student',
            'password' => bcrypt('password'),
            'role' => 'student'
        ];
        
        Student::create($student);
    }
}
