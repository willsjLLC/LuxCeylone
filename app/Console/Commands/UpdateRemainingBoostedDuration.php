<?php

namespace App\Console\Commands;

use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRemainingBoostedDuration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-remaining-boosted-duration';

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
        $boostCompletedStatus = Status::BOOST_COMPLETED;

        // CRON 04
        // get remaining boost ads
        $remainingAds = DB::table('advertisement_boosted_histories')
            ->where('status', '!=', $boostCompletedStatus)
            ->get();

        // update remaining dates 
        foreach ($remainingAds as $remainingAd) {
            $remainingDate = Carbon::parse($now)->diffInDays(Carbon::parse($remainingAd->expiry_date));

            DB::table('advertisement_boosted_histories')
                ->where('id', $remainingAd->id)
                ->update(['remaining' => $remainingDate]);
        }

        Log::info('Boosted Advertisement Remaining Dates Updated - ' . $now->toDateTimeString());
    }
}
