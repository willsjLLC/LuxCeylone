<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\EmployeePackageActivationHistory;
use App\Models\FreeAd;
use App\Models\FreeUserAd;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All Advertisements";
        $advertisements       = $this->advertisementData();
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }
    public function pending()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Pending Advertisements";
        $advertisements       = $this->advertisementData('pending');
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }
    public function approved()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Approve Advertisements";
        $advertisements       = $this->advertisementData('approved');
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }

    public function complete()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Completed Advertisements";
        $advertisements       = $this->advertisementData('completed');
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }

    public function rejected()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Rejected Advertisements";
        $advertisements       = $this->advertisementData('rejected');
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }

    public function expired()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Expired Advertisements";
        $advertisements       = $this->advertisementData('expired');
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }

    public function canceled()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Canceled Advertisements";
        $advertisements       = $this->advertisementData('canceled');
        return view('admin.advertisements.index', compact('pageTitle', 'advertisements'));
    }

    protected function advertisementData($scope = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        if ($scope) {
            $advertisements  = Advertisement::$scope();
        } else {
            $advertisements  = Advertisement::query();
        }
        return $advertisements->searchable(['category:name', 'user:username', 'title', 'advertisement_code'])->filter(['user_id'])->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function view($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Advertisement Information";

        $advertisement = Advertisement::with([
            'user',
            'category',
            'subcategory',
            'package',
            'paymentOption',
            'district',
            'city',
            'images',
            'boostHistories'
        ])->findOrFail($id);

        return view('admin.advertisements.view', compact('pageTitle', 'advertisement'));
    }

    public function approve($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $ad         = Advertisement::findOrFail($id);
        $ad->status = Status::AD_APPROVED;
        $ad->save();

        $activatedPackage = EmployeePackageActivationHistory::with('package')
            ->where('user_id', $ad->user_id)
            ->where('activation_expired', 0)
            ->where('payment_status', Status::PAYMENT_SUCCESS)
            ->first();

        if ($ad->is_free == Status::FREE_AD) {
            $freeAds = FreeUserAd::where('user_id', $ad->user_id)->first();
            $freeAdDetail = FreeAd::first();
            if (!$freeAds) {
                FreeUserAd::create([
                    'user_id'   => $ad->user_id,
                    'total_ads' => $freeAdDetail->no_of_advertisements,
                    'used_ads'  => 1,
                ]);
            } else {
                $freeAds->used_ads += 1;
                $freeAds->save();
            }
        } else {
            $activatedPackage->used_ads += 1;
            $activatedPackage->save();
        }

        notify($ad->user, 'ADMIN_JOB_APPROVE', [
            'posted_by' => $ad->user->username,
            'job_code'  => $ad->job_code,
            'quantity'  => $ad->quantity,
            'amount'    => showAmount($ad->rate, currencyFormat: false),
            'total'     => showAmount($ad->total, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Advertisement approved successfully'];
        return back()->withNotify($notify);
    }

    // public function reject($id)
    // {
    //     $admin = auth()->guard('admin')->user();

    //     if (!$admin || !$admin->can('advertisements.update')) {
    //         return response()->view('admin.errors.403', [], 403);
    //     }

    //     $ad         = Advertisement::pending()->findOrFail($id);
    //     $ad->status = Status::AD_REJECTED;
    //     $ad->save();

    //     $notify[] = ['success', 'Advertisement rejected successfully'];
    //     return back()->withNotify($notify);
    // }

    public function reject(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('advertisements.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'reason' => 'required'
        ]);

        $ad = Advertisement::pending()->findOrFail($id);
        $ad->status = Status::AD_REJECTED;
        $ad->rejection_reason = $request->reason; // Store the rejection reason
        $ad->save();

        // If there's a notification system, you could add this
        notify($ad->user, 'AD_REJECT', [
            'reason' => $request->reason,
            'ad_title' => $ad->title
        ]);

        $notify[] = ['success', 'Advertisement rejected successfully'];
        return back()->withNotify($notify);
    }




}
