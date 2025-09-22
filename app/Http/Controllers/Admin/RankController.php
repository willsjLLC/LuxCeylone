<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ClaimedRankReward;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\RankReward;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function index()
    {
        $pageTitle = "Manage Ranks";
        $ranks = Rank::searchable(['name'])->orderBy('no_of_stars')->paginate(getPaginate());
        return view('admin.rank.index', compact('pageTitle', 'ranks'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            "rank" => 'required|unique:ranks,rank,' . $id,
            "name" => 'required|unique:ranks,name,' . $id,
            "no_of_stars" => 'required|unique:ranks,no_of_stars,' . $id,
        ]);

        if ($id) {
            $rank = Rank::findOrFail($id);
            $message = "Rank updated successfully";
        } else {
            $rank = new Rank();
            $message = "Rank created successfully";
        }

        if ($request->hasFile('image')) {
            try {
                $old = $rank->image;
                $rank->image = fileUploader($request->file('image'), getFilePath('rank'), getFileSize('rank'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $alias = strtolower(str_replace(' ', '_', $request->name));

        $originalAlias = $alias;
        $counter = 1;
        while (Rank::where('alias', $alias)->where('id', '!=', $id)->exists()) {
            $alias = $originalAlias . '_' . $counter++;
        }

        $rank->alias = $alias;
        $rank->name = $request->name;
        $rank->rank = $request->rank;
        $rank->no_of_stars = $request->no_of_stars;
        $rank->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $rank = Rank::findOrFail($id);
        $rank->delete();
        $message = "Rank deleted successfully";
        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function requirementIndex()
    {
        $pageTitle = "Manage Rank Requirements";
        $rankRequirements = RankRequirement::paginate(getPaginate());
        $ranks = Rank::orderBy('no_of_stars')->get();
        return view('admin.rank.requirements.index', compact('pageTitle', 'rankRequirements', 'ranks'));
    }

    public function requirementCreate($id = null)
    {
        $pageTitle = "Create Rank Requirement";
        $ranks = Rank::orderBy('no_of_stars')->get();
        $rankRequirement = RankRequirement::find($id);
        return view('admin.rank.requirements.create', compact('pageTitle', 'ranks', 'rankRequirement'));
    }

    public function requirementStore(Request $request, $id = null)
    {
        $request->validate([
            'rank_id' => 'required|exists:ranks,id',
            'min_rank_id' => 'nullable',
            'level_one_user_count' => 'nullable|integer',
            'level_two_user_count' => 'nullable|integer',
            'level_three_user_count' => 'nullable|integer',
            'level_four_user_count' => 'nullable|integer',
        ]);

        $existingRequirement = RankRequirement::where('rank_id', $request->rank_id)->first();

        if (!$id && $existingRequirement) {
            $notify[] = ["error", "A requirement for this rank already exists. Please edit the existing one."];
            return back()->withNotify($notify);
        }

        $requirement = $id
            ? RankRequirement::findOrFail($id)
            : ($existingRequirement ?: new RankRequirement);

        $message = $id
            ? "Rank requirement updated successfully"
            : "Rank requirement stored successfully";

        $requirement->rank_id = $request->rank_id;
        $requirement->min_rank_id = $request->min_rank_id ? $request->min_rank_id : null;
        $requirement->level_one_user_count = $request->level_one_user_count ? $request->level_one_user_count : 0;
        $requirement->level_two_user_count = $request->level_two_user_count ? $request->level_two_user_count : 0;
        $requirement->level_three_user_count = $request->level_three_user_count ? $request->level_three_user_count : 0;
        $requirement->level_four_user_count = $request->level_four_user_count ? $request->level_four_user_count : 0;
        $requirement->required_at_least_one_product_purchase = $request->required_at_least_one_product_purchase	 == 'on' ? 1 : 0;

        $requirement->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function requirementDelete($id)
    {
        $requirement = RankRequirement::findOrFail($id);
        $requirement->delete();
        $message = "Rank Requirement deleted successfully";
        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function rewardIndex()
    {
        $pageTitle = "Manage Rank Rewards";
        $rankRewards = RankReward::searchable(['reward'])->paginate(getPaginate());
        $ranks = Rank::orderBy('no_of_stars')->get();
        return view('admin.rank.rewards.index', compact('pageTitle', 'rankRewards', 'ranks'));
    }

    public function rewardStore(Request $request, $id = 0)
    {
        $request->validate([
            "reward" => 'required|string',
            "rank_id" => 'required|integer|exists:ranks,id',
        ]);

        if ($id) {
            $rankReward = RankReward::findOrFail($id);
            $message = "Rank Reward updated successfully";
        } else {
            $rankReward = new RankReward();
            $message = "Rank Reward created successfully";
        }

        if ($request->hasFile('image')) {
            try {
                $old = $rankReward->image;
                $rankReward->image = fileUploader($request->file('image'), getFilePath('rankReward'), getFileSize('rankReward'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $rankReward->reward = $request->reward;
        $rankReward->rank_id = $request->rank_id;
        $rankReward->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function rewardDelete($id)
    {
        $rankReward = RankReward::findOrFail($id);
        $rankReward->delete();
        $message = "Rank deleted successfully";
        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function allUserRewardIndex()
    {
        $pageTitle = "All User Rank Rewards";
        $rankRewards = ClaimedRankReward::paginate(getPaginate());
        return view('admin.rank.userRewards.index', compact('pageTitle', 'rankRewards'));
    }

    public function viewUserRewardIndex($id)
    {
        $pageTitle = "All User Rank Rewards";
        $rankReward = ClaimedRankReward::find($id);
        return view('admin.rank.userRewards.view', compact('pageTitle', 'rankReward'));
    }

    public function updateUserRewardIndex(Request $request)
    {

        $rankReward = ClaimedRankReward::find($request->id);
        $rankReward->rank_one_claimed_status = $request->rank_one_claimed_status;
        $rankReward->rank_two_claimed_status = $request->rank_two_claimed_status;
        $rankReward->rank_three_claimed_status = $request->rank_three_claimed_status;
        $rankReward->rank_four_claimed_status = $request->rank_four_claimed_status;

        $rankReward->save();


        $message = "User Rank Reward updated successfully";
        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function pendingUserRewardIndex()
    {
        // const RANK_CLAIM_PROCESSING = 2
        $pageTitle = "Processing User Rank Rewards";

        $rankRewards = ClaimedRankReward::where(function ($query) {
            $query->where('rank_one_claimed_status', Status::RANK_CLAIM_PROCESSING)
                ->orWhere('rank_two_claimed_status', Status::RANK_CLAIM_PROCESSING)
                ->orWhere('rank_three_claimed_status', Status::RANK_CLAIM_PROCESSING)
                ->orWhere('rank_four_claimed_status', Status::RANK_CLAIM_PROCESSING);
        })->paginate(getPaginate());

        return view('admin.rank.userRewards.pending', compact('pageTitle', 'rankRewards'));
    }

    public function completedUserRewardIndex()
    {
        // const RANK_CLAIM_PROCESSING = 2
        $pageTitle = "Completed User Rank Rewards";

        $rankRewards = ClaimedRankReward::where(function ($query) {
            $query->where('rank_one_claimed_status', Status::RANK_CLAIM_COMPLETED)
                ->orWhere('rank_two_claimed_status', Status::RANK_CLAIM_COMPLETED)
                ->orWhere('rank_three_claimed_status', Status::RANK_CLAIM_COMPLETED)
                ->orWhere('rank_four_claimed_status', Status::RANK_CLAIM_COMPLETED);
        })->paginate(getPaginate());

        return view('admin.rank.userRewards.completed', compact('pageTitle', 'rankRewards'));
    }

    public function canceledUserRewardIndex()
    {
        // const RANK_CLAIM_PROCESSING = 2
        $pageTitle = "Canceled User Rank Rewards";

        $rankRewards = ClaimedRankReward::where(function ($query) {
            $query->where('rank_one_claimed_status', Status::RANK_CLAIM_CANCELED)
                ->orWhere('rank_two_claimed_status', Status::RANK_CLAIM_CANCELED)
                ->orWhere('rank_three_claimed_status', Status::RANK_CLAIM_CANCELED)
                ->orWhere('rank_four_claimed_status', Status::RANK_CLAIM_CANCELED);
        })->paginate(getPaginate());

        return view('admin.rank.userRewards.canceled', compact('pageTitle', 'rankRewards'));
    }
}
