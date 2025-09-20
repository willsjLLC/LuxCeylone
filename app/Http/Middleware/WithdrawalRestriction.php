<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use App\Models\EmployeePackageActivationHistory;
use App\Models\Training;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $user = auth()->user();

        // $withdrawalEnabled = true;
        // $trainings = Training::all();
        // $userTotalEarning = $user->total_earning;

        // foreach ($trainings as $training) {
        //     if ($userTotalEarning >= $training->min_income_threshold) {
        //         $userTraining = null;
        //         if ($user->userTrainings) {
        //             $userTraining = $user->userTrainings->firstWhere('training_id', $training->id);
        //         }

        //         if (!$userTraining) {
        //             $withdrawalEnabled = false;
        //             $notify[] = "You must buy a ticket for the 'R.s. {$training->min_income_threshold} commission earn' training to enable withdrawals.";
        //             return to_route('user.training.index')->withNotify($notify);

        //         } elseif ($userTraining->status !== Status::TRAINING_COMPLETED) {
        //             $withdrawalEnabled = false;
        //             $notify[] = "Wait until your 'R.s. {$training->min_income_threshold} commission earn' training is completed.";
        //             return to_route('user.training.index')->withNotify($notify);
        //         }
        //     }
        // }

        // if ($withdrawalEnabled) {
        //     $hasActivePackage = EmployeePackageActivationHistory::where('user_id', $user->id)
        //         ->exists();

        //     if (!$hasActivePackage) {
        //         $notify[] = 'You must have an active package to withdraw funds.';
        //         return to_route('user.deposit.employee.package.active')->withNotify($notify);

        //     }
        // }

        // return $next($request);

        $user = auth()->user();
        $withdrawalEnabled = true;

        $trainings = Training::all();
        $userTotalEarning = $user->total_earning;

        foreach ($trainings as $training) {
            if ($userTotalEarning >= $training->min_income_threshold) {

                $userTraining = $user->userTrainings()->firstWhere('training_id', $training->id);

                if (!$userTraining) {
                    $notify[] = "You must buy a ticket for the 'R.s. {$training->min_income_threshold} commission earn' training to enable withdrawals.";
                    return to_route('user.training.index')->withNotify($notify);
                } elseif ($userTraining->status !== Status::TRAINING_COMPLETED) {
                    $notify[] = "Wait until your 'R.s. {$training->min_income_threshold} commission earn' training is completed.";
                    return to_route('user.training.index')->withNotify($notify);
                }
            }
        }

        $packageActivationService = app('App\Service\PackageActivationService'::class);
        $needsTopUp = $packageActivationService->needsPackageActivation($user);

        if ($needsTopUp) {
            $lastActivatedTier = $user->employeePackageActivationHistories()->max('activated_for_earning_tier');
            $recursive_top_up_range = getValue('USER_RECURSIVE_TOP_UP_RANGE');
            $currentEarningTier = floor($user->total_earning / $recursive_top_up_range);

            $nextRequiredTier = $lastActivatedTier ? $lastActivatedTier + 1 : 1;

            if ($currentEarningTier >= $nextRequiredTier) {
                $notify[] = "You must activate the package for tier {$nextRequiredTier} before you can withdraw funds.";
                return to_route('user.deposit.employee.package.active')->withNotify($notify);
            }
        }

        $hasActivePackage = EmployeePackageActivationHistory::where('user_id', $user->id)->exists();
        if (!$hasActivePackage) {
            $notify[] = 'You must have an active package to withdraw funds.';
            return to_route('user.deposit.employee.package.active')->withNotify($notify);
        }

        return $next($request);
    }
}
