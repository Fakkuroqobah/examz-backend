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
            [
                'code' => '1',
                'name' => 'supervisor1',
                'username' => 'supervisor1',
                'password' => bcrypt('password'),
                'role' => 'supervisors'
            ],
            [
                'code' => '2',
                'name' => 'supervisor2',
                'username' => 'supervisor2',
                'password' => bcrypt('password'),
                'role' => 'supervisors'
            ]
        ];
        
        foreach ($supervisors as $value) {
            Supervisor::create($value);
        }
    }
}
