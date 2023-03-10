<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;

class SupervisorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supervisors = [
            'name' => 'supervisors',
            'username' => 'supervisors',
            'password' => bcrypt('password'),
            'role' => 'supervisors'
        ];
        
        Supervisor::create($supervisors);
    }
}
