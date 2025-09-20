<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KeyValuePair;

class KeyValuePairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keyValuePairs = [
            ['id' => 1, 'key' => 'AD_BOOST_COMMISSION_PERCENTAGE_FOR_COMPANY', 'value' => '50'],
            ['id' => 2, 'key' => 'AD_BOOST_COMMISSION_PERCENTAGE_FOR_REFERRED_USER',  'value' => '25'],
            ['id' => 3, 'key' => 'AD_BOOST_COMMISSION_PERCENTAGE_FOR_NON_DIRECT_USERS', 'value' => '25'],
            ['id' => 4, 'key' => 'NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_AD_BOOST_COMMISSION', 'value' => '12'],
            ['id' => 5, 'key' => 'DEFAULT_WATERMARK_TEXT', 'value' => 'luxceylone.com'],
            ['id' => 6, 'key' => 'SINHALISE_BONUS_FROM', 'value' => '4'],
            ['id' => 7, 'key' => 'SINHALISE_BONUS_TO', 'value' => '5'],
            ['id' => 8, 'key' => 'TAMIL_BONUS_FROM', 'value' => '1'],
            ['id' => 9, 'key' => 'TAMIL_BONUS_TO', 'value' => '2'],
            ['id' => 10, 'key' => 'MUSLIMS_BONUS_FROM', 'value' => '3'],
            ['id' => 11, 'key' => 'MUSLIMS_BONUS_TO', 'value' => '4'],
            ['id' => 12, 'key' => 'CHRISTIAN_BONUS_FROM', 'value' => '12'],
            ['id' => 13, 'key' => 'CHRISTIAN_BONUS_TO', 'value' => '1'],
            ['id' => 14, 'key' => 'VOUCHER_REMAINING_DATE', 'value' => '30'],
            ['id' => 15, 'key' => 'USER_RECURSIVE_TOP_UP_RANGE', 'value' => '50000'],
            
        ];

        foreach ($keyValuePairs as $keyValuePair) {
            KeyValuePair::updateOrCreate(
                ['key' => $keyValuePair['key']],
                $keyValuePair
            );
        }
    }
}
