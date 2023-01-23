<?php

namespace Database\Seeders;

use App\Models\Supervisors;
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
        
        Supervisors::create($supervisors);
    }
}
