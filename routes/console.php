<?php

use App\Console\Commands\CalculateVoucherRemainingDates;
use App\Console\Commands\ProcessUnpaidPackageEarnings;
use App\Console\Commands\UpdateAdvertisementExpiry;
use App\Console\Commands\UpdateBoostedHistoryStatus;
use App\Console\Commands\UpdatePackageActivationHistories;
use App\Console\Commands\UpdateRemainingBoostedDuration;
use App\Console\Commands\UpdateUserTotalAds;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// FOR TESTING
// everyMinute();
// everyTwoMinutes()
// everyThreeMinutes()
// everyFiveMinutes()
// everyTenMinutes()
// everyFifteenMinutes()
// everyThirtyMinutes()
// hourly()
// dailyAt('17:24');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// CRON 01
Artisan::command('ads:update-expired-status', function () {
    (new UpdateAdvertisementExpiry())->handle();
})->describe('Update expired status for advertisements and related tables')->dailyAt('00:00');

// CRON 02
Artisan::command('app:update-package-activation-histories', function () {
    (new UpdatePackageActivationHistories())->handle();
})->describe('Check & Update expiry Users package activation status')->dailyAt('00:10');

// CRON 06
Artisan::command('app:calculate-voucher-remaining-dates', function () {
    (new CalculateVoucherRemainingDates())->handle();
})->describe('Calculate the voucher remaining date after package activation')->dailyAt('00:20');

// CRON 02.1
// Artisan::command('app:process-unpaid-package-earnings', function () {
//     (new ProcessUnpaidPackageEarnings())->handle();
// })->describe('Transfer unpaid package earnings to company')->dailyAt('00:30');

// CRON 03
Artisan::command('app:update-boosted-history-status', function () {
    (new UpdateBoostedHistoryStatus())->handle();
})->describe('Check & Update expiry Boosted ad status')->dailyAt('00:40');

// CRON 04
Artisan::command('app:update-remaining-boosted-duration', function () {
    (new UpdateRemainingBoostedDuration())->handle();
})->describe('Check & Update Boosted ad remaining days to end boost')->dailyAt('00:50');

// CRON 05
Artisan::command('app:update-user-total-ads', function () {
    (new UpdateUserTotalAds())->handle();
})->describe('Update users total posted ads individually')->dailyAt('01:00');


// OPTION 02
// Schedule::command('ads:update-expired-status')->everyMinute()->withoutOverlapping();
// Schedule::command('app:update-package-activation-histories')->everyMinute()->withoutOverlapping();
// Schedule::command('app:process-unpaid-package-earnings')->everyMinute()->withoutOverlapping();
// Schedule::command('app:update-boosted-history-status')->everyMinute()->withoutOverlapping();
// Schedule::command('app:update-remaining-boosted-duration')->everyMinute()->withoutOverlapping();
// Schedule::command('app:update-user-total-ads')->everyMinute()->withoutOverlapping();
// Schedule::command('app:calculate-voucher-remaining-dates')->everyMinute()->withoutOverlapping();