<?php

namespace App\Service;

use App\Constants\Status;
use App\Models\User;
use App\Models\EmployeePackageActivationHistory;
use Illuminate\Support\Facades\Log;

class PackageActivationService
{
    /**
     * Checks if a user needs to activate a package for their current earning tier.
     */
    public function needsPackageActivation(User $user): bool
    {
        $recursive_top_up_range = getValue('USER_RECURSIVE_TOP_UP_RANGE');

        $currentEarningTier = floor($user->total_earning / $recursive_top_up_range);

        if ($currentEarningTier == 0) {
            return false;
        }

        $lastActivatedTier = EmployeePackageActivationHistory::where('user_id', $user->id)
            ->max('activated_for_earning_tier');

        if ($lastActivatedTier === null) {
            return true;
        }
        
        if ($currentEarningTier > $lastActivatedTier) {
            return true;
        }

        return false;
    }

    public function isPackageActive($user)
    {
        return $user->employee_package_activated == Status::ENABLE;
    }

}