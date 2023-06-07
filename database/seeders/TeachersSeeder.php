<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeachersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teachers = [
            'code' => '1',
            'name' => 'teacher',
            'username' => 'teacher',
            'password' => bcrypt('password'),
            'role' => 'teacher'
        ];
        
        Teacher::create($teachers);
    }
}
