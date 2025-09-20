<?php

namespace Database\Seeders;

use App\Models\Month;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $months = [
            ['id' => 1,  'name' => 'January'],
            ['id' => 2,  'name' => 'February'],
            ['id' => 3,  'name' => 'March'],
            ['id' => 4,  'name' => 'April'],
            ['id' => 5,  'name' => 'May'],
            ['id' => 6,  'name' => 'June'],
            ['id' => 7,  'name' => 'July'],
            ['id' => 8,  'name' => 'August'],
            ['id' => 9,  'name' => 'September'],
            ['id' => 10, 'name' => 'October'],
            ['id' => 11, 'name' => 'November'],
            ['id' => 12, 'name' => 'December'],
        ];

        foreach ($months as $month) {
            Month::create($month);
        }
    }
}
