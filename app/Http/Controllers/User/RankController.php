<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserRankDetail;
use App\Models\RankRequirement;
use App\Constants\Status;

class RankController extends Controller
{
    public function index()
    {
        $pageTitle = "My Rank Progress";
        $user = Auth::user();
        $ranks = Rank::orderBy('rank')->get();
        
        // Get user rank details
        $userRankDetail = UserRankDetail::where('user_id', $user->id)->first();
        
        // Get current rank details
        $currentRank = $userRankDetail && $userRankDetail->current_rank_id 
            ? Rank::find($userRankDetail->current_rank_id) 
            : null;
        $currentRankLevel = $currentRank ? $currentRank->rank : 0;

        // Calculate progress for next rank
        $nextRank = Rank::where('rank', $currentRankLevel + 1)->first();
        $progressData = $this->calculateRankProgress($user, $userRankDetail, $currentRankLevel + 1);

        return view('Template::user.rank.index', compact(
            'pageTitle',
            'ranks',
            'currentRank',
            'nextRank',
            'currentRankLevel',
            'progressData',
            'user',
            'userRankDetail'
        ));
    }

    private function calculateRankProgress($user, $userRankDetail, $targetRank)
    {
        $progress = 0;
        $requirements = [];

        // Get target rank requirement
        $targetRankObj = Rank::where('rank', $targetRank)->first();
        if (!$targetRankObj) {
            return [
                'progress' => 0,
                'requirements' => [],
                'target_rank' => $targetRank
            ];
        }

        $rankRequirement = RankRequirement::where('rank_id', $targetRankObj->id)->first();
        if (!$rankRequirement) {
            return [
                'progress' => 0,
                'requirements' => [],
                'target_rank' => $targetRank
            ];
        }

        // Get current user's rank
        $currentRank = $userRankDetail && $userRankDetail->current_rank_id 
            ? Rank::find($userRankDetail->current_rank_id) 
            : null;
        $currentRankNumber = $currentRank ? $currentRank->rank : 0;

        // Check if user meets minimum rank requirement
        if ($rankRequirement->min_rank_id) {
            $minRank = Rank::find($rankRequirement->min_rank_id);
            $minRankNumber = $minRank ? $minRank->rank : 0;
            
            if ($currentRankNumber < $minRankNumber) {
                $requirements['min_rank'] = [
                    'label' => 'Minimum Rank Required: ' . ($minRank ? $minRank->name : 'Unknown'),
                    'current' => $currentRankNumber,
                    'required' => $minRankNumber,
                    'completed' => false
                ];
                return [
                    'progress' => 0,
                    'requirements' => $requirements,
                    'target_rank' => $targetRank
                ];
            }

            // Add minimum rank requirement (completed)
            $requirements['min_rank'] = [
                'label' => 'Minimum Rank: ' . $minRank->name,
                'current' => 1,
                'required' => 1,
                'completed' => true
            ];
        }

        $totalRequirements = 0;
        $completedRequirements = 0;
        $totalProgress = 0;

        // Check level requirements
        if ($rankRequirement->level_one_user_count > 0) {
            $current = $userRankDetail ? $userRankDetail->level_one_user_count : 0;
            $required = $rankRequirement->level_one_user_count;
            
            $requirements['level_one'] = [
                'label' => 'Level 1 Users Required',
                'current' => $current,
                'required' => $required,
                'completed' => $current >= $required
            ];
            
            $totalRequirements++;
            if ($current >= $required) {
                $completedRequirements++;
            }
            $totalProgress += min(100, ($current / $required) * 100);
        }

        if ($rankRequirement->level_two_user_count > 0) {
            $current = $userRankDetail ? $userRankDetail->level_two_user_count : 0;
            $required = $rankRequirement->level_two_user_count;
            
            $requirements['level_two'] = [
                'label' => 'Level 2 Users Required',
                'current' => $current,
                'required' => $required,
                'completed' => $current >= $required
            ];
            
            $totalRequirements++;
            if ($current >= $required) {
                $completedRequirements++;
            }
            $totalProgress += min(100, ($current / $required) * 100);
        }

        if ($rankRequirement->level_three_user_count > 0) {
            $current = $userRankDetail ? $userRankDetail->level_three_user_count : 0;
            $required = $rankRequirement->level_three_user_count;
            
            $requirements['level_three'] = [
                'label' => 'Level 3 Users Required',
                'current' => $current,
                'required' => $required,
                'completed' => $current >= $required
            ];
            
            $totalRequirements++;
            if ($current >= $required) {
                $completedRequirements++;
            }
            $totalProgress += min(100, ($current / $required) * 100);
        }

        if ($rankRequirement->level_four_user_count > 0) {
            $current = $userRankDetail ? $userRankDetail->level_four_user_count : 0;
            $required = $rankRequirement->level_four_user_count;
            
            $requirements['level_four'] = [
                'label' => 'Level 4 Users Required',
                'current' => $current,
                'required' => $required,
                'completed' => $current >= $required
            ];
            
            $totalRequirements++;
            if ($current >= $required) {
                $completedRequirements++;
            }
            $totalProgress += min(100, ($current / $required) * 100);
        }

        // Calculate overall progress
        if ($totalRequirements > 0) {
            $progress = $totalProgress / $totalRequirements;
        } else {
            $progress = 100; // No specific requirements beyond minimum rank
        }

        return [
            'progress' => min(100, $progress),
            'requirements' => $requirements,
            'target_rank' => $targetRank,
            'completed_requirements' => $completedRequirements,
            'total_requirements' => $totalRequirements
        ];
    }

    public function detail($id)
    {
        $rank = Rank::findOrFail($id);
        $pageTitle = "Rank Details - " . $rank->name;
        $user = Auth::user();
        
        // Get user rank details
        $userRankDetail = UserRankDetail::where('user_id', $user->id)->first();

        // Get current rank details
        $currentRank = $userRankDetail && $userRankDetail->current_rank_id 
            ? Rank::find($userRankDetail->current_rank_id) 
            : null;
        $currentRankLevel = $currentRank ? $currentRank->rank : 0;

        // Get rank requirements from database
        $rankRequirements = RankRequirement::where('rank_id', $rank->id)->first();

        // Calculate progress for this specific rank
        $progressData = $this->calculateRankProgress($user, $userRankDetail, $rank->rank);

        // Check if user can achieve this rank (meets minimum rank requirement)
        $canAchieve = true;
        if ($rankRequirements && $rankRequirements->min_rank_id) {
            $minRank = Rank::find($rankRequirements->min_rank_id);
            $minRankNumber = $minRank ? $minRank->rank : 0;
            $canAchieve = $currentRankLevel >= $minRankNumber;
        }

        // Get all ranks for navigation
        $allRanks = Rank::orderBy('rank')->get();

        return view('Template::user.rank.detail', compact(
            'pageTitle',
            'rank',
            'currentRank',
            'currentRankLevel',
            'progressData',
            'user',
            'userRankDetail',
            'rankRequirements',
            'canAchieve',
            'allRanks'
        ));
    }
}