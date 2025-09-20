<?php

namespace App\Console\Commands;

use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateVoucherRemainingDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-voucher-remaining-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $remainingDate = getValue('VOUCHER_REMAINING_DATE');
        // CRON 06
        // get the voucher closed users
        $remainingVoucherUpdates = DB::table('customer_bonuses')
            ->where('is_voucher_open', Status::VOUCHER_CLOSED)
            ->get();

        foreach ($remainingVoucherUpdates as $bonus) {
            try {

                // Get the oldest activation history record for this user
                $oldestRecord = DB::table('employee_package_activation_histories')
                    ->where('user_id', $bonus->user_id)
                    ->orderBy('created_at')
                    ->first();

                if (!$oldestRecord) {
                    continue; // No activation record found for user
                }

                $diffInDays = Carbon::parse($oldestRecord->created_at)->diffInDays($now);

                if ($diffInDays > $remainingDate) {
                    DB::table('customer_bonuses')
                        ->where('user_id', $bonus->user_id)
                        ->update([
                            'is_voucher_open' => Status::VOUCHER_OPEN,
                            'voucher_remaining_to_open' => 0
                        ]);
                } else {
                    DB::table('customer_bonuses')
                        ->where('user_id', $bonus->user_id)
                        ->update(['voucher_remaining_to_open' => $remainingDate - $diffInDays]);
                }
            } catch (\Exception $e) {
                Log::error('Cron job: Error updating user ID ' . $bonus->user_id . ' - ' . $e->getMessage());
            }
        }

        Log::info('Voucher update process completed.' . $now->toDateTimeString());
    }
}
