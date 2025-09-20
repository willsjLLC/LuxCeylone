<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUserTotalAds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-total-ads';

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

        // CRON 05
        // update users total ads
        $adsCounts = DB::table('advertisements')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $count = $adsCounts[$user->id] ?? 0;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['total_created_ads' => $count]);
        }

        Log::info('Users Total Ad Update Completed - ' . $now->toDateTimeString());
    }
}
