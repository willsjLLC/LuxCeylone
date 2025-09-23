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
use App\Models\ClaimedRankReward;
use App\Models\RankReward;
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

        // Get claimed rank rewards
        $claimedRankRewards = ClaimedRankReward::where('user_id', $user->id)->first();
        
        // Get current rank details
        $currentRank = $userRankDetail && $userRankDetail->current_rank_id 
            ? Rank::find($userRankDetail->current_rank_id) 
            : null;
        $currentRankLevel = $currentRank ? $currentRank->rank : 0;

        // Calculate progress for next rank
        $nextRank = Rank::where('rank', $currentRankLevel + 1)->first();
        $progressData = $this->calculateRankProgress($user, $userRankDetail, $currentRankLevel + 1);

        // Add claim status to each rank
        foreach ($ranks as $rank) {
            // Check if user has achieved this rank
            $hasAchievedRank = $currentRankLevel >= $rank->rank;
            $rank->claim_status = $this->getRankClaimStatus($rank, $user, $claimedRankRewards, $hasAchievedRank);
        }

        return view('Template::user.rank.index', compact(
            'pageTitle',
            'ranks',
            'currentRank',
            'nextRank',
            'currentRankLevel',
            'progressData',
            'user',
            'userRankDetail',
            'claimedRankRewards'
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

        // Get rank rewards
        $rankRewards = RankReward::where('rank_id', $rank->id)->get();

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
        
        // Get claimed rank rewards and claim status
        $claimedRankRewards = ClaimedRankReward::where('user_id', $user->id)->first();
        
        // Check if user has achieved this rank
        $hasAchievedRank = $currentRankLevel >= $rank->rank;
        $claimStatus = $this->getRankClaimStatus($rank, $user, $claimedRankRewards, $hasAchievedRank);

        return view('Template::user.rank.detail', compact(
            'pageTitle',
            'rank',
            'currentRank',
            'currentRankLevel',
            'progressData',
            'user',
            'userRankDetail',
            'rankRequirements',
            'rankRewards',
            'claimStatus',
            'canAchieve',
            'allRanks'
        ));
    }
    
    public function claimRankReward(Request $request)
    {
        $user = Auth::user();
        $rankNumber = $request->input('rank_number'); // Changed from rank_id to rank_number

        // Log request details
        Log::info('claimRankReward called', [
            'user_id' => $user->id,
            'rank_number' => $rankNumber,
            'current_rank_id' => $user->current_rank_id
        ]);

        // Validate rank exists using rank number
        $rank = Rank::where('rank', $rankNumber)->first();
        if (!$rank) {
            Log::warning('Rank not found', ['rank_number' => $rankNumber]);
            return response()->json(['success' => false, 'message' => 'Rank not found'], 404);
        }

        // Get current user's rank number
        // $currentRank = $user->current_rank_id ? Rank::find($user->current_rank_id) : null;
        // $currentRankNumber = $currentRank ? $currentRank->rank : 0;

         // Get user's current rank from UserRankDetail
    $userRankDetail = UserRankDetail::where('user_id', $user->id)->first();
    $currentRank = $userRankDetail && $userRankDetail->current_rank_id 
        ? Rank::find($userRankDetail->current_rank_id) 
        : null;
    $currentRankNumber = $currentRank ? $currentRank->rank : 0;


        // Check if user has achieved this rank
        if ($currentRankNumber < $rankNumber) {
            Log::warning('Rank not achieved', [
                'user_id' => $user->id,
                'rank_number' => $rankNumber,
                'current_rank_number' => $currentRankNumber
            ]);
            return response()->json(['success' => false, 'message' => 'Rank not achieved yet'], 403);
        }

        // Get or create claimed rank rewards record
        $claimedRankReward = ClaimedRankReward::firstOrCreate(
            ['user_id' => $user->id],
            [
                'current_rank_id' => $user->current_rank_id,
                'rank_one_status' => Status::RANK_PENDING,
                'rank_one_claimed_status' => Status::RANK_NOT_SATISFIED,
                'rank_two_status' => Status::RANK_PENDING,
                'rank_two_claimed_status' => Status::RANK_NOT_SATISFIED,
                'rank_three_status' => Status::RANK_PENDING,
                'rank_three_claimed_status' => Status::RANK_NOT_SATISFIED,
                'rank_four_status' => Status::RANK_PENDING,
                'rank_four_claimed_status' => Status::RANK_NOT_SATISFIED,
            ]
        );

        // Use the model method to get current claim status
        $currentClaimStatus = $claimedRankReward->getRankClaimedStatus($rankNumber);

        Log::info('Current claim status', [
            'user_id' => $user->id,
            'rank_number' => $rankNumber,
            'current_status' => $currentClaimStatus
        ]);

        // Check if reward is already claimed or processing
        if (in_array($currentClaimStatus, [Status::RANK_CLAIM_PROCESSING, Status::RANK_CLAIM_COMPLETED])) {
            Log::warning('Reward already claimed or processing', [
                'status' => $currentClaimStatus
            ]);
            return response()->json(['success' => false, 'message' => 'Reward already claimed or in processing'], 403);
        }

        // Update claim status using a transaction
        try {
            DB::beginTransaction();

            $claimedRankReward->setRankClaimedStatus($rankNumber, Status::RANK_CLAIM_PROCESSING);
            $claimedRankReward->save();

            // Refresh the model to get updated values
            $claimedRankReward->refresh();

            // Verify the update
            $verifyStatus = $claimedRankReward->getRankClaimedStatus($rankNumber);

            if ($verifyStatus !== Status::RANK_CLAIM_PROCESSING) {
                Log::error('Database update verification failed', [
                    'user_id' => $user->id,
                    'rank_number' => $rankNumber,
                    'expected_status' => Status::RANK_CLAIM_PROCESSING,
                    'actual_status' => $verifyStatus
                ]);
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update claim status'
                ], 500);
            }

            DB::commit();

            Log::info('Claim status updated successfully', [
                'user_id' => $user->id,
                'rank_number' => $rankNumber,
                'new_status' => $verifyStatus
            ]);

            // Get the updated claim status using the same method as in the view
            $updatedClaimStatus = $this->getRankClaimStatus($rank, $user, $claimedRankReward, true);

            return response()->json([
                'success' => true,
                'message' => 'Rank reward claim is now processing',
                'claim_status' => $updatedClaimStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception during claim process', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'rank_number' => $rankNumber,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to process claim: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRankClaimStatus($rank, $user, $claimedRankRewards, $hasAchievedRank)
    {
        if (!$hasAchievedRank) {
            return [
                'can_claim' => false,
                'is_achieved' => false,
                'status' => 'not_achieved',
                'button_text' => 'Not Achieved',
                'button_class' => 'btn btn-secondary disabled'
            ];
        }

        if (!$claimedRankRewards) {
            return [
                'can_claim' => true,
                'is_achieved' => true,
                'status' => 'pending',
                'button_text' => 'Claim Reward',
                'button_class' => 'btn btn-success claim-btn'
            ];
        }

        $claimStatus = $claimedRankRewards->getRankClaimedStatus($rank->rank);

        $statusMap = [
            Status::RANK_NOT_SATISFIED => [
                'can_claim' => true,
                'is_achieved' => true,
                'status' => 'pending',
                'button_text' => 'Claim Reward',
                'button_class' => 'btn btn-success claim-btn'
            ],
            Status::RANK_CLAIM_PENDING => [
                'can_claim' => true,
                'is_achieved' => true,
                'status' => 'pending',
                'button_text' => 'Claim Reward',
                'button_class' => 'btn btn-success claim-btn'
            ],
            Status::RANK_CLAIM_PROCESSING => [
                'can_claim' => false,
                'is_achieved' => true,
                'status' => 'processing',
                'button_text' => 'Processing',
                'button_class' => 'btn btn-warning disabled'
            ],
            Status::RANK_CLAIM_COMPLETED => [
                'can_claim' => false,
                'is_achieved' => true,
                'status' => 'completed',
                'button_text' => 'Claimed',
                'button_class' => 'btn btn-primary disabled'
            ],
            Status::RANK_CLAIM_CANCELED => [
                'can_claim' => false,
                'is_achieved' => true,
                'status' => 'canceled',
                'button_text' => 'Canceled',
                'button_class' => 'btn btn-danger disabled'
            ]
        ];

        return $statusMap[$claimStatus] ?? [
            'can_claim' => true,
            'is_achieved' => true,
            'status' => 'pending',
            'button_text' => 'Claim Reward',
            'button_class' => 'btn btn-success claim-btn'
        ];
    }

}