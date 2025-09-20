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
}
