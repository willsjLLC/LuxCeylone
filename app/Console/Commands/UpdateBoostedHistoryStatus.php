<?php

namespace App\Console\Commands;

use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBoostedHistoryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-boosted-history-status';

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

        // CRON 03
        // Get the currently expired and still active boosted histories
        $justExpiredAds = DB::table('advertisement_boosted_histories')
            ->where('expiry_date', '<', $now)
            ->where('status', '!=', $boostCompletedStatus)
            ->get();

        // Extract advertisement IDs BEFORE updating
        $expiredBoostedAdIds = $justExpiredAds->pluck('advertisement_id')->unique();

        // update the boosted history statuses
        DB::table('advertisement_boosted_histories')
            ->whereIn('id', $justExpiredAds->pluck('id'))
            ->update([
                'status' => $boostCompletedStatus,
                'remaining' => 0,
                'updated_at' => now(),
            ]);

        // update only those related ads
        if ($expiredBoostedAdIds->isNotEmpty()) {
            DB::table('advertisements')
                ->whereIn('id', $expiredBoostedAdIds)
                ->update(['is_boosted' => Status::ADVERTISEMENT_NOT_BOOSTED]);

            foreach ($expiredBoostedAdIds as $adId) {
                Log::info("Advertisement marked as NOT BOOSTED: ID = $adId");
            }
        }
        // end boost advertisements 

        Log::info('Boosted Advertisement status updated - ' . $now->toDateTimeString());
    }
}
