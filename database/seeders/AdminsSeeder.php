<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ];
        
        Admin::create($admins);
    }
}
