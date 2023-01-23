<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'single choice'],
            ['name' => 'multiple choice'],
            ['name' => 'essai']
        ];

        foreach ($types as $data) {
            Type::create($data);
        }
    }
}
