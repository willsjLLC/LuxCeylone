<?php

namespace App\Console\Commands;

use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessUnpaidPackageEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-unpaid-package-earnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // ========================================== NOT IN USE ANYMORE =========================================================
    public function handle()
    {
        $now = Carbon::now();
        $packageInactiveStatus = Status::PACKAGE_INACTIVE;
        $packageActiveStatus = Status::PACKAGE_ACTIVE; // Assuming this exists
        $activationExpiredStatus = Status::ACTIVATION_EXPIRED;
        $cycleDuration = 30;

        // Get company account
        $company = DB::table('users')->where('username', 'luxceylone')->first();
        if (!$company) {
            // Log::error("Company account 'luxceylone' not found");
            return;
        }

        // Get all users (both active and inactive package users)
        $inactiveUsers = DB::table('users')
            ->where('employee_package_activated', $packageInactiveStatus)
            ->get();

        $activeUsers = DB::table('users')
            ->where('employee_package_activated', $packageActiveStatus)
            ->get();

        // Process inactive users (original logic)
        // Log::info("Processing " . $inactiveUsers->count() . " inactive package users");
        foreach ($inactiveUsers as $user) {
            $this->processUserCycle($user, $company, $now, $cycleDuration, true); // true = transfer bonuses
        }

        // Process active users (new logic)
        // Log::info("Processing " . $activeUsers->count() . " active package users");
        foreach ($activeUsers as $user) {
            $this->processUserCycle($user, $company, $now, $cycleDuration, false); // false = don't transfer bonuses
        }

        // Log::info("Package bonus cleanup completed at " . $now->toDateTimeString());
    }

    private function processUserCycle($user, $company, $now, $cycleDuration, $shouldTransferBonuses = false)
    {
        $oldestPackage = DB::table('employee_package_activation_histories')
            ->where('user_id', $user->id)
            ->oldest()
            ->first();

        if ($oldestPackage) {
            $initialExpiryDate = Carbon::parse($oldestPackage->expiry_date);
            $initialStartDate = Carbon::parse($oldestPackage->created_at);

            // Calculate days since initial start
            $daysSinceInitialStart = $initialStartDate->diffInDays($now);

            // Calculate total completed cycles and remaining days
            $totalCompletedCycles = floor($daysSinceInitialStart / $cycleDuration);
            $remainingDaysInCurrentCycle = $daysSinceInitialStart % $cycleDuration;
            $daysToCompleteNextCycle = $cycleDuration - $remainingDaysInCurrentCycle;

            // For active users, we might want to check if their current package is still valid
            $currentPackage = DB::table('employee_package_activation_histories')
                ->where('user_id', $user->id)
                ->where('expiry_date', '>=', $now->toDateString())
                ->latest()
                ->first();

            // If we're exactly at the end of a cycle
            if ($remainingDaysInCurrentCycle === 0 && $daysSinceInitialStart > 0) {
                // Cycle just completed
                if ($shouldTransferBonuses) {
                    $this->transferBonusesToCompany($user, $company);
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'total_cycle' => $totalCompletedCycles,
                        'remain_to_complete_next_cycle' => $cycleDuration // Full cycle ahead
                    ]);

                // Log::info("Cycle completed for user {$user->id}. Total cycles: {$totalCompletedCycles}");
            } else {
                // Mid-cycle or just started
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'total_cycle' => $totalCompletedCycles,
                        'remain_to_complete_next_cycle' => $daysToCompleteNextCycle
                    ]);

                // Log::info("Updated cycle for user {$user->id}. Total cycles: {$totalCompletedCycles}, Days remaining: {$daysToCompleteNextCycle}");
            }

            // Handle expired packages
            if ($now > $initialExpiryDate) {
                // Log::info("Package expired for user {$user->id} on {$initialExpiryDate->toDateString()}");

                // For active users with expired packages, you might want to:
                // 1. Change their status to inactive
                // 2. Transfer their bonuses
                if (!$shouldTransferBonuses && !$currentPackage) {
                    // No valid current package found for active user
                    $this->transferBonusesToCompany($user, $company);

                    // Optionally update their status to inactive
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['employee_package_activated' => Status::PACKAGE_INACTIVE]);

                    // Log::info("Converted active user {$user->id} to inactive due to expired package");
                }
            }

        } else {
            // No package history found - calculate based on user creation date
            $daysSinceUserCreation = Carbon::parse($user->created_at)->diffInDays($now);

            if ($daysSinceUserCreation >= $cycleDuration) {
                $totalCompletedCycles = floor($daysSinceUserCreation / $cycleDuration);
                $remainingDaysInCurrentCycle = $daysSinceUserCreation % $cycleDuration;
                $daysToCompleteNextCycle = $cycleDuration - $remainingDaysInCurrentCycle;

                // If exactly at cycle boundary
                if ($remainingDaysInCurrentCycle === 0) {
                    $daysToCompleteNextCycle = $cycleDuration;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'total_cycle' => $totalCompletedCycles,
                        'remain_to_complete_next_cycle' => $daysToCompleteNextCycle
                    ]);

                // Transfer bonuses for users without package history
                if ($shouldTransferBonuses) {
                    $this->transferBonusesToCompany($user, $company);
                }

                // Log::info("Calculated cycle for user {$user->id} with no package history. Total cycles: {$totalCompletedCycles}");
            } else {
                // User hasn't been around long enough for a full cycle
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'total_cycle' => 0,
                        'remain_to_complete_next_cycle' => $cycleDuration - $daysSinceUserCreation
                    ]);

                // Log::info("New user {$user->id}, days remaining for first cycle: " . ($cycleDuration - $daysSinceUserCreation));
            }
        }
    }

    private function transferBonusesToCompany($user, $company)
    {
        $totalTransfer = 0;

        $customerBonus = DB::table('customer_bonuses')->where('user_id', $user->id)->first();
        $leaderBonus = DB::table('leader_bonuses')->where('user_id', $user->id)->first();
        $topLeader = DB::table('top_leaders')->where('user_id', $user->id)->first();

        if ($customerBonus) {
            if ($customerBonus->temp_total > 0) {
                $totalTransfer += $customerBonus->temp_total;

                DB::table('customer_bonuses')->where('user_id', $user->id)->update([
                    'temp_total' => 0,
                    'temp_commission_balance' => 0,
                    'temp_voucher_balance' => 0,
                    'temp_festival_bonus_balance' => 0,
                    'temp_saving' => 0,
                ]);

                DB::table('bonus_transaction_histories')->insert([
                    'user_id' => $user->id,
                    'amount' => $customerBonus->temp_total,
                    'charge' => 0,
                    'trx_type' => '-',
                    'trx' => getTrx(),
                    'post_bonus_balance' => 0,
                    'details' => 'Unpaid Package Activate Customer Bonus To Company',
                    'remark' => 'unpaid_package_active_customer_bonus_to_company',
                ]);
            }
        }

        if ($leaderBonus && $leaderBonus->temp_total > 0) {
            $totalTransfer += $leaderBonus->temp_total;

            DB::table('leader_bonuses')->where('user_id', $user->id)->update([
                'temp_total' => 0,
                'temp_bonus' => 0,
                'temp_leasing_amount' => 0,
                'temp_petrol_allowance' => 0,
            ]);

            DB::table('bonus_transaction_histories')->insert([
                'user_id' => $user->id,
                'amount' => $leaderBonus->temp_total,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'post_bonus_balance' => 0,
                'details' => 'Unpaid Package Activate Leader Bonus To Company',
                'remark' => 'unpaid_package_activation_leader_bonus_to_company',
            ]);
        }

        if ($topLeader && $topLeader->temp_total > 0) {
            $totalTransfer += $topLeader->temp_total;

            DB::table('top_leaders')->where('user_id', $user->id)->update([
                'temp_total' => 0,
                'temp_for_car' => 0,
                'temp_for_house' => 0,
                'temp_for_expenses' => 0,
            ]);

            DB::table('bonus_transaction_histories')->insert([
                'user_id' => $user->id,
                'amount' => $topLeader->temp_total,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'post_bonus_balance' => 0,
                'details' => 'Unpaid Package Activate Top Leader Bonus To Company',
                'remark' => 'unpaid_package_activation_top_leader_bonus_to_company',
            ]);
        }

        if ($totalTransfer > 0) {
            DB::table('users')->where('id', $company->id)->increment('balance', $totalTransfer);

            DB::table('transactions')->insert([
                'user_id' => $company->id,
                'amount' => $totalTransfer,
                'trx_type' => '+',
                'remark' => 'unpaid_package_activation_bonus_to_company',
                'details' => 'Unpaid Package Activate Bonus To Company',
                'trx' => getTrx(),
                'post_balance' => $company->balance + $totalTransfer,
            ]);

            DB::table('bonus_transaction_histories')->insert([
                'user_id' => $company->id,
                'amount' => $totalTransfer,
                'charge' => 0,
                'trx_type' => '+',
                'trx' => getTrx(),
                'post_bonus_balance' => $company->balance + $totalTransfer,
                'details' => 'Unpaid Package Activate Bonus To Company',
                'remark' => 'unpaid_package_activation_bonus_to_company',
            ]);

            // Log::info("Transferred {$totalTransfer} from user #{$user->id} to company (adciti)");
        }
    }
}
