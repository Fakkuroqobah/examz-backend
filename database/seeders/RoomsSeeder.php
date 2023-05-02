<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room = [
            ['name' => 'R01'],
            ['name' => 'R02'],
            ['name' => 'R03'],
            ['name' => 'R04'],
            ['name' => 'R05']
        ];

        foreach ($room as $value) {
            Room::create($value);
        }
    }
}
