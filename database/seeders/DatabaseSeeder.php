<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoomsSeeder::class,
            AdminsSeeder::class,
            StudentsSeeder::class,
            SupervisorsSeeder::class,
            TeachersSeeder::class,
            ExamsSeeder::class,
            SchedulesSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
