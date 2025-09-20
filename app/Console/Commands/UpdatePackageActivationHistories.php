<?php

namespace App\Console\Commands;

use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePackageActivationHistories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-package-activation-histories';

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
        $activationExpiredStatus = Status::ACTIVATION_EXPIRED;
        $packageInactiveStatus = Status::PACKAGE_INACTIVE;

        // CRON 02
        // package activation history expiry status updates 
        $expiredPackages = DB::table('employee_package_activation_histories')
            ->where('expiry_date', '<', $now)
            ->where('activation_expired', '!=', $activationExpiredStatus)
            ->get();

        foreach ($expiredPackages as $package) {

            DB::table('employee_package_activation_histories')
                ->where('id', $package->id)
                ->update(['activation_expired' => $activationExpiredStatus]);

            DB::table('users')
                ->where('id', $package->user_id)
                ->update(['employee_package_activated' => $packageInactiveStatus]);
        }

        Log::info('Package Activation Expiry Status Updated at ' . $now->toDateTimeString());
    }
}
