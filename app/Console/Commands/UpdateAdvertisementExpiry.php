<?php

namespace App\Console\Commands;

use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateAdvertisementExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:update-expired-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired status for advertisements and related tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $adExpiredStatus = Status::AD_EXPIRED;

        // CRON 01
        // update advertisement expiry status 
        DB::table('advertisements')
            ->where('expiry_date', '<', $now)
            ->where('status', '!=', $adExpiredStatus)
            ->update(['status' => $adExpiredStatus]);

        Log::info('Expiry Advertisement Status Updated: ' . $now->toDateTimeString());

    }
}
