<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = [
            ['id' => 1, 'name' => 'Colombo'],
            ['id' => 2, 'name' => 'Gampaha'],
            ['id' => 3, 'name' => 'Kalutara'],
            ['id' => 4, 'name' => 'Kandy'],
            ['id' => 5, 'name' => 'Matale'],
            ['id' => 6, 'name' => 'Nuwaraeliya'],
            ['id' => 7, 'name' => 'Batticaloa'],
            ['id' => 8, 'name' => 'Trincomalee'],
            ['id' => 9, 'name' => 'Ampara'],
            ['id' => 10, 'name' => 'Jaffna'],
            ['id' => 11, 'name' => 'Mannar'],
            ['id' => 12, 'name' => 'Mullaitivu'],
            ['id' => 13, 'name' => 'Vavuniya'],
            ['id' => 14, 'name' => 'Anuradhapura'],
            ['id' => 15, 'name' => 'Polonnaruwa'],
            ['id' => 16, 'name' => 'Kurunegala'],
            ['id' => 17, 'name' => 'Puttalam'],
            ['id' => 18, 'name' => 'Ratnapura'],
            ['id' => 19, 'name' => 'Kegalle'],
            ['id' => 20, 'name' => 'Galle'],
            ['id' => 21, 'name' => 'Matara'],
            ['id' => 22, 'name' => 'Hambantota'],
            ['id' => 23, 'name' => 'Badulla'],
            ['id' => 24, 'name' => 'Monaragala'],
            ['id' => 25, 'name' => 'Kilinochchi'],
        ];

        foreach ($districts as &$district) {
            $district['slug'] = $district['slug'] ?? Str::slug($district['name']);
        }

        DB::table('districts')->insert($districts);
    }
}
