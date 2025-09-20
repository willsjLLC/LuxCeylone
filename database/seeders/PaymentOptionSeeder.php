<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentOption;

class PaymentOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentOptions = [
            ['name' => 'Online Payment or Bank Transfer', 'status' => true],
            ['name' => 'Cash Payment', 'status' => true],
            ['name' => 'Deduct an Amount from the Account Balance', 'status' => true],

        ];

        foreach ($paymentOptions as $option) {
            PaymentOption::create($option);
        }
    }
}
