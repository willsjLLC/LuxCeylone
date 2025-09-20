<?php

namespace Database\Seeders;

use App\Models\WithdrawMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WithdrawMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $WithdrawMethods = [
            [
                'id' => 1,
                'form_id' => 4,
                'name' => 'Bank Transfer',
                'image' => '679fbaa17968e1738521249.png',
                'min_limit' => 100.00000000,
                'max_limit' => 100000.00000000,
                'fixed_charge' => 30.00000000,
                'rate' => 1.00000000,
                'percent_charge' => 0.00,
                'currency' => 'LKR',
                'description' => '<p style="text-align: center;"><strong>Withdrawal Instructions – SLJob.net</strong></p><p style="text-align: center;"><br></p><ol><li style="text-align: center;">Select <strong>Bank Transfer</strong> as your withdrawal method.</li><li style="text-align: center;">Enter your bank details correctly (Name, Account Number, Bank Name, and Branch)</li></ol><div style="text-align: center;"><br></div><ol><li style="text-align: center;"><b><font color="#ff0066">Important: The bank account holder\'s name must match the SLJob.net account holder\'s name to process withdrawals successfully</font></b></li></ol>',
                'status' => 1,
            ],
            [
                'id' => 2,
                'form_id' => 5,
                'name' => 'Binance',
                'image' => '679fbb98ddfd51738521496.jpg',
                'min_limit' => 300.00000000,
                'max_limit' => 100000.00000000,
                'fixed_charge' => 0.00000000,
                'rate' => 0.00340000,
                'percent_charge' => 0.00,
                'currency' => 'USDT',
                'description' => '<div style="text-align: center;"><strong><font color="#ff0033">Please Note:</font></strong> The Binance withdrawal method is currently under development. We will notify you once it is fully operational. We appreciate your patience and understanding.</div>',
                'status' => 1,
            ]
        ];

        foreach ($WithdrawMethods as $WithdrawMethod) {
            WithdrawMethod::create($WithdrawMethod);
        }
    }
}
