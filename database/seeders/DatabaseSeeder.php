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
            AdminsSeeder::class,
            StudentsSeeder::class,
            SupervisorsSeeder::class,
            TeachersSeeder::class,
            ExamsSeeder::class,
            RoomsSeeder::class,
            SchedulesSeeder::class,
        ]);
    }
}
