<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'firstname' => 'LUXCEYLONE',
                'lastname' => 'Company',
                'username' => 'luxceylone',
                'email' => 'main.luxceylone@luxceylone.net',
                'dial_code' => '94',
                'country_code' => 'LK',
                'mobile' => '123456789',
                'balance' => '0',
                'password' => Hash::make('user@luxceylone'),
                'referred_user_id' => null,
                'country_name' => 'Sri Lanka',
                'city' => 'Unknown',
                'district' => 'Unknown',
                'district_id' => '0',
                'city_id' => '0',
                'state' => 'Unknown',
                'zip' => '00000',
                'image' => null,
                'address' => null,
                'status' => 1,
                'ev' => 1,
                'sv' => 1,
                'sv' => 1,
                'profile_complete' => 1,
                'ver_code' => null,
                'ver_code_send_at' => null,
                'ts' => 0,
                'tv' => 1,
                'tsc' => null,
                'kv' => 1,
                'kyc_data' => null,
                'kyc_rejection_reason' => null,
                'ban_reason' => null,
                'remember_token' => null,
                'provider' => null,
                'provider_id' => null,
                'employee_package_activated' => 1,
                'pending_job_commision_total' => 0,
                'role' => 1,
            ],
        ];

        foreach ($users  as $user) {
            $user = User::create($user);
        }
    }
}
