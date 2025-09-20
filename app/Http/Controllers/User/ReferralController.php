<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ReferralLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Constants\Status;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    private function getAllLevels($userId, $maxLevel = 4)
    {
        $levels = [];
            $currentLevelUserIds = [$userId];
            $level = 1;

            // If maxLevel is null (for leaders), set a high number to effectively make it unlimited
            $actualMaxLevel = $maxLevel ?? 1000;

            while ($level <= $actualMaxLevel) {
                $nextLevelUserIds = [];
                $levelUsers = [];

                foreach ($currentLevelUserIds as $id) {
                    // Get users who were referred by users in the current level
                    $referredUsers = User::where('referred_user_id', $id)
                        ->select('id', 'firstname', 'lastname', 'mobile', 'employee_package_activated')
                        ->get();

                    // Add package activation history check for each user
                    $referredUsersWithHistory = $referredUsers->map(function($user) {
                        $user->has_package_history = $this->hasPackageActivationHistory($user->id);
                        return $user;
                    });

                    $levelUsers = array_merge($levelUsers, $referredUsersWithHistory->toArray());
                    $nextLevelUserIds = array_merge($nextLevelUserIds, $referredUsers->pluck('id')->toArray());
                }

                $levels[$level] = $levelUsers;

                // If no users at this level, we can stop as there won't be any more referrals
                if (empty($nextLevelUserIds)) {
                    break;
                }

                $currentLevelUserIds = $nextLevelUserIds;
                $level++;
            }

            return $levels;
    }
    // Check if user has package activation history
    private function hasPackageActivationHistory($userId)
    {
        return DB::table('employee_package_activation_histories')
            ->where('user_id', $userId)
            ->exists();
    }

    public function canReferMore(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $directReferralsCount = User::where('referred_user_id', $user->id)->count();

        return response()->json([
            'canRefer' => $directReferralsCount < 2
        ]);
    }
    // referral function with proper level limits
    public function referral()
    {
        $pageTitle = 'Affiliates';
        $user = auth()->user();

        // Determine max levels based on user role - Customer gets 4 levels, Leader gets unlimited
        $maxLevels = ($user->role == Status::LEADER) ? null : 4;

        // Get all levels of referrals with commission data
        $allLevels = $this->getAllLevelsWithCommissions($user->id, $maxLevels);
        Log::info('Retrieved all levels with commissions', ['user_id' => $user->id, 'levels' => $allLevels]);

        // Count total affiliates (all levels combined)
        $totalAffiliatesCount = 0;
        foreach ($allLevels as $level) {
            $totalAffiliatesCount += count($level);
        }
        Log::info('Total affiliates count', ['user_id' => $user->id, 'count' => $totalAffiliatesCount]);

        // Count active levels (levels that have at least one user)
        $activeLevelsCount = count(array_filter($allLevels, function($level) {
            return count($level) > 0;
        }));
        Log::info('Active levels count', ['user_id' => $user->id, 'count' => $activeLevelsCount]);

        // Calculate current active users count (employee_package_activated = 1)
        $currentActiveUsersIds = $this->getAllActiveUsersIds($user->id, 0, [], $maxLevels);
        $currentActiveUsersCount = count($currentActiveUsersIds);

        // Calculate total active users count (users with package activation history)
        $totalActiveUsersIds = $this->getAllTotalActiveUsersIds($user->id, 0, [], $maxLevels);
        $totalActiveUsersCount = count($totalActiveUsersIds);

        // Get paginated direct affiliates with commission data
        $paginatedUsers = $this->getDirectAffiliatesWithCommissions($user->id);

        return view('Template::user.referrals', compact(
            'pageTitle',
            'totalAffiliatesCount',
            'allLevels',
            'activeLevelsCount',
            'currentActiveUsersCount',
            'totalActiveUsersCount',
            'maxLevels',
            'paginatedUsers'
        ));
    }

    // getAllLevelsWithCommissions function
    private function getAllLevelsWithCommissions($userId, $maxLevel = 4)
    {
        $levels = [];
        $currentLevelUserIds = [$userId];
        $level = 1;

        // If maxLevel is null (for leaders), set a high number to effectively make it unlimited
        $actualMaxLevel = $maxLevel ?? 1000;

        while ($level <= $actualMaxLevel) {
            $nextLevelUserIds = [];
            $levelUsers = [];

            foreach ($currentLevelUserIds as $id) {
                $referredUsers = User::where('referred_user_id', $id)
                    ->select('id', 'firstname', 'lastname', 'mobile', 'employee_package_activated')
                    ->get();

                $referredUsersWithCommissions = $referredUsers->map(function($user) use ($userId) {
                    $user->has_package_history = $this->hasPackageActivationHistory($user->id);
                    
                    // Get individual commission data for this specific affiliate
                    $commissionData = $this->getIndividualUserCommissionData($userId, $user->id);
                    $user->package_activation_commission = $commissionData['package_activation'];
                    $user->product_purchase_commission = $commissionData['product_purchase'];
                    $user->bonus_commission = $commissionData['bonus_commission']; 
                    $user->total_commission = $commissionData['total'];
                    
                    return $user;
                });

                $levelUsers = array_merge($levelUsers, $referredUsersWithCommissions->toArray());
                $nextLevelUserIds = array_merge($nextLevelUserIds, $referredUsers->pluck('id')->toArray());
            }

            $levels[$level] = $levelUsers;

            if (empty($nextLevelUserIds)) {
                break;
            }

            $currentLevelUserIds = $nextLevelUserIds;
            $level++;
        }
        
        Log::info('Retrieved all levels with commissions', [
            'user_id' => $userId,
            'levels' => $levels
        ]);
        
        return $levels;
    }

    // getDirectAffiliatesWithCommissions function
    private function getDirectAffiliatesWithCommissions($userId, $perPage = 10)
    {
        $directAffiliates = User::where('referred_user_id', $userId)
            ->select('id', 'firstname', 'lastname', 'mobile', 'employee_package_activated')
            ->get();

        $affiliatesWithCommissions = $directAffiliates->map(function($user) use ($userId) {
            // Get individual commission data for this specific affiliate
            $commissionData = $this->getIndividualUserCommissionData($userId, $user->id);
            $user->package_activation_commission = $commissionData['package_activation'];
            $user->product_purchase_commission = $commissionData['product_purchase'];
            $user->bonus_commission = $commissionData['bonus_commission']; 
            $user->total_commission = $commissionData['total'];
            
            return $user;
        });

        Log::info('Retrieved direct affiliates with commissions', [
            'user_id' => $userId,
            'affiliates' => $affiliatesWithCommissions
        ]);

        return $affiliatesWithCommissions;
    }

    // get individual user commission data
    private function getIndividualUserCommissionData($rootUserId, $affiliateUserId)
    {
        // Get package activation commissions from transactions table for this specific affiliate
        // Using composite key: root_user_id (user_id) and affiliate_user_id (debit_user_id)
        $packageActivationCommission = DB::table('transactions')
            ->where('user_id', $rootUserId)
            ->where('debit_user_id', $affiliateUserId)
            ->where(function($query) {
                $query->where('remark', 'LIKE', '%package%activation%')
                    ->orWhere('remark', 'LIKE', '%employee%package%')
                    ->orWhere('remark', 'LIKE', '%package%commission%');
            })
            ->sum('amount');

        Log::info('Individual package activation commission', [
            'root_user_id' => $rootUserId,
            'affiliate_user_id' => $affiliateUserId,
            'amount' => $packageActivationCommission
        ]);

        // Get product purchase commissions from transactions table for this specific affiliate
        $productPurchaseCommission = DB::table('transactions')
            ->where('user_id', $rootUserId)
            ->where('debit_user_id', $affiliateUserId)
            ->where(function($query) {
                $query->where('remark', 'LIKE', '%product%purchase%')
                    ->orWhere('remark', 'LIKE', '%product%commission%')
                    ->orWhere(function($subquery) {
                        $subquery->where('remark', 'LIKE', '%commission%')
                                ->where('remark', 'NOT LIKE', '%package%')
                                ->where('remark', 'NOT LIKE', '%employee%');
                    });
            })
            ->sum('amount');

        Log::info('Individual product purchase commission', [
            'root_user_id' => $rootUserId,
            'affiliate_user_id' => $affiliateUserId,
            'amount' => $productPurchaseCommission
        ]);

        // Get bonus commissions from bonus_transaction_histories for this specific affiliate
        // Using composite key: root_user_id (user_id) and affiliate_user_id (debit_user_id)
        $bonusCommissions = DB::table('bonus_transaction_histories')
            ->where('user_id', $rootUserId)
            ->where('debit_user_id', $affiliateUserId)
            ->select('customers_voucher', 'customers_festival', 'customers_saving', 
                    'leader_bonus', 'leader_vehicle_lease', 'leader_petrol')
            ->get();

        $totalBonusCommissions = 0;
        foreach ($bonusCommissions as $bonus) {
            $totalBonusCommissions += ($bonus->customers_voucher ?? 0) + 
                                    ($bonus->customers_festival ?? 0) + 
                                    ($bonus->customers_saving ?? 0) + 
                                    ($bonus->leader_bonus ?? 0) + 
                                    ($bonus->leader_vehicle_lease ?? 0) + 
                                    ($bonus->leader_petrol ?? 0);
        }

        Log::info('Individual bonus commissions', [
            'root_user_id' => $rootUserId,
            'affiliate_user_id' => $affiliateUserId,
            'amount' => $totalBonusCommissions
        ]);

        // Updated return to include bonus commission separately
        return [
            'package_activation' => number_format($packageActivationCommission, 2),
            'product_purchase' => number_format($productPurchaseCommission, 2),
            'bonus_commission' => number_format($totalBonusCommissions, 2), // New field
            'total' => number_format($packageActivationCommission + $productPurchaseCommission + $totalBonusCommissions, 2)
        ];
    }

    // getAllActiveUsersIds function with proper level limits
    private function getAllActiveUsersIds($userId, $currentLevel = 0, $processedIds = [], $maxLevel = 4)
    {
        // If maxLevel is null (for leaders), set a high number to effectively make it unlimited
        $actualMaxLevel = $maxLevel ?? 1000;
        
        // Safety check to prevent infinite recursion and stay within max level
        if ($currentLevel >= $actualMaxLevel || in_array($userId, $processedIds)) {
            return [];
        }

        // Add this ID to processed list to avoid revisiting
        $processedIds[] = $userId;

        // Find all direct referrals of this user
        $referredUsers = User::where('referred_user_id', $userId)->get();

        $activeUserIds = [];

        foreach ($referredUsers as $user) {
            // If this user has activated package (current active), add to result
            if ($user->employee_package_activated == 1) {
                $activeUserIds[] = $user->id;
            }

            // Continue recursively for this user's referrals (next level)
            if ($currentLevel < ($actualMaxLevel - 1)) {
                $childActiveIds = $this->getAllActiveUsersIds(
                    $user->id,
                    $currentLevel + 1,
                    $processedIds,
                    $maxLevel
                );

                // Add all active child IDs to our results
                $activeUserIds = array_merge($activeUserIds, $childActiveIds);

                // Update processed IDs to avoid revisiting
                $processedIds = array_merge($processedIds, $childActiveIds);
            }
        }

        return array_unique($activeUserIds);
    }

    // getAllTotalActiveUsersIds function with proper level limits
    private function getAllTotalActiveUsersIds($userId, $currentLevel = 0, $processedIds = [], $maxLevel = 4)
    {
        // If maxLevel is null (for leaders), set a high number to effectively make it unlimited
        $actualMaxLevel = $maxLevel ?? 1000;
        
        // Safety check to prevent infinite recursion and stay within max level
        if ($currentLevel >= $actualMaxLevel || in_array($userId, $processedIds)) {
            return [];
        }

        // Add this ID to processed list to avoid revisiting
        $processedIds[] = $userId;

        // Find all direct referrals of this user
        $referredUsers = User::where('referred_user_id', $userId)->get();

        $totalActiveUserIds = [];

        foreach ($referredUsers as $user) {
            // If this user has any package activation history, add to result
            if ($this->hasPackageActivationHistory($user->id)) {
                $totalActiveUserIds[] = $user->id;
            }

            // Continue recursively for this user's referrals (next level)
            if ($currentLevel < ($actualMaxLevel - 1)) {
                $childTotalActiveIds = $this->getAllTotalActiveUsersIds(
                    $user->id,
                    $currentLevel + 1,
                    $processedIds,
                    $maxLevel
                );

                // Add all total active child IDs to our results
                $totalActiveUserIds = array_merge($totalActiveUserIds, $childTotalActiveIds);

                // Update processed IDs to avoid revisiting
                $processedIds = array_merge($processedIds, $childTotalActiveIds);
            }
        }

        return array_unique($totalActiveUserIds);
    }

    // getHierarchyData function with proper level limits
    public function getHierarchyData()
    {
        $user = auth()->user();
        // Customer gets 4 levels, Leader gets unlimited
        $maxLevels = ($user->role == Status::LEADER) ? null : 4;
        $allLevels = $this->getAllLevelsWithCommissions($user->id, $maxLevels);
        
        return response()->json([
            'user' => [
                'name' => $user->firstname . ' ' . $user->lastname,
                'mobile' => $user->mobile,
                'isActive' => $user->employee_package_activated == 1
            ],
            'levels' => $allLevels
        ]);
    }

    public function getTotalCommissionSummary($userId, $maxLevels = null)
    {
        $allLevels = $this->getAllLevelsWithCommissions($userId, $maxLevels);
        
        $totalPackageActivation = 0;
        $totalProductPurchase = 0;
        $totalBonusCommission = 0;
        
        foreach ($allLevels as $levelUsers) {
            foreach ($levelUsers as $user) {
                $totalPackageActivation += floatval(str_replace(',', '', $user['package_activation_commission']));
                $totalProductPurchase += floatval(str_replace(',', '', $user['product_purchase_commission']));
                $totalBonusCommission += floatval(str_replace(',', '', $user['bonus_commission']));
            }
        }
        
        return [
            'total_package_activation' => number_format($totalPackageActivation, 2),
            'total_product_purchase' => number_format($totalProductPurchase, 2),
            'total_bonus_commission' => number_format($totalBonusCommission, 2),
            'total_commission' => number_format($totalPackageActivation + $totalProductPurchase + $totalBonusCommission, 2)
        ];
    }
    
}


