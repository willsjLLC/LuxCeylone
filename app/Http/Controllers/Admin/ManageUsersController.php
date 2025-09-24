<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BonusTransactionHistory;
use App\Models\CompanyExpensesSavingHistory;
use App\Models\CompanySaving;
use App\Models\CustomerBonus;
use App\Models\Deposit;
use App\Models\JobPost;
use App\Models\LeaderBonus;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\SecondOwner;
use App\Models\TopLeader;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ManageUsersController extends Controller
{

    public function allUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'All Users';
        $users = $this->userData();
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycApprovedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.kyc_pending')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'KYC Approved Users';
        $users = $this->userData('kycApproved');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function topLeaders()
    {
        // $admin = auth()->guard('admin')->user();

        // if (!$admin || !$admin->can('users.top_leaders')) {
        //     return response()->view('admin.errors.403', [], 403);
        // }

        $pageTitle = 'Top Leaders';
        $emptyMessage = 'No top leaders found';
        $users = $this->userData('topLeaders');

        return view('admin.users.list', compact('pageTitle', 'users', 'emptyMessage'));
    }

    public function activeLeaders()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.leaders')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Leaders';
        $users = $this->userData('leaders');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function activeUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.active')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Active Users';
        $users = $this->userData('active');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function bannedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.banned')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Banned Users';
        $users = $this->userData('banned');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.email_unverified')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Email Unverified Users';
        $users = $this->userData('emailUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycUnverifiedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.kyc_unverified')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'KYC Unverified Users';
        $users = $this->userData('kycUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycPendingUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.kyc_pending')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'KYC Pending Users';
        $users = $this->userData('kycPending');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailVerifiedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.email_verified')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Email Verified Users';
        $users = $this->userData('emailVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function mobileUnverifiedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.mobile_unverified')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Mobile Unverified Users';
        $users = $this->userData('mobileUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function mobileVerifiedUsers()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.mobile_verified')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Mobile Verified Users';
        $users = $this->userData('mobileVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function usersWithBalance()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.with_balance')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Users with Balance';
        $users = $this->userData('withBalance');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    protected function userData($scope = null)
    {
        // $admin = auth()->guard('admin')->user();

        // if (!$admin || !$admin->can('users.view')) {
        //     return response()->view('admin.errors.403', [], 403);
        // }

        if ($scope) {
            if ($scope == 'topLeaders') {
                // Special case for top leaders
                $users = User::where('role', 2)
                    ->where('is_top_leader', 1);
            } else {
                // Use the existing scope pattern
                $users = User::$scope();
            }
        } else {
            $users = User::query();
        }

        return $users->searchable(['username', 'email'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function detail($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.detail')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $user = User::with('topLeaderBonuses', 'leaderBonuses', 'customerBonuses')->find($id);

        $pageTitle = 'User Detail - ' . $user->username;

        $totalDeposit = Deposit::where('user_id', $user->id)->successful()->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id', $user->id)->approved()->sum('amount');
        $totalTransaction = Transaction::where('user_id', $user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $companySavingAmount = CompanySaving::first();

        if ($companySavingAmount && $user->username == 'luxceylone') {
            $companySaving = $companySavingAmount->balance;
        } else {
            $companySaving = null;
        }

        // $job['total_job']    = JobPost::where('user_id', $id)->count();
        // $job['pending_job']  = JobPost::pending()->where('user_id', $id)->count();
        // $job['complete_job'] = JobPost::completed()->where('user_id', $id)->count();
        // $job['cancel_job']   = JobPost::rejected()->where('user_id', $id)->count();

        // $job['total_job_amount']    = JobPost::where('user_id', $id)->sum('total');
        // $job['pending_job_amount']  = JobPost::where('user_id', $id)->pending()->sum('total');
        // $job['complete_job_amount'] = JobPost::where('user_id', $id)->completed()->sum('total');
        // $job['cancel_job_amount']   = JobPost::where('user_id', $id)->rejected()->sum('total');




        return view('admin.users.detail', compact('pageTitle', 'user', 'totalDeposit', 'totalWithdrawals', 'totalTransaction', 'countries', 'companySaving'));
    }

        public function referralHierarchy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Get all referral data recursively
            $hierarchyData = $this->getReferralHierarchy($id);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'username' => $user->username,
                    'email' => $user->email
                ],
                'hierarchy' => $hierarchyData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load hierarchy: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getReferralHierarchy($userId, $level = 1, $maxLevel = 100)
    {
        // Prevent infinite recursion and deep nesting
        if ($level > $maxLevel) {
            return [];
        }

        // Get direct referrals with proper error handling
        $referrals = User::select('id', 'firstname', 'lastname', 'username', 'email', 'created_at', 'status', 'balance')
            ->where('referred_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $result = [];
        
        foreach ($referrals as $referral) {
            // Get children recursively
            $children = $this->getReferralHierarchy($referral->id, $level + 1, $maxLevel);
            
            // Count total referrals for this user
            $totalReferrals = $this->countDirectReferrals($referral->id);
            
            $result[] = [
                'id' => $referral->id,
                'name' => trim($referral->firstname . ' ' . $referral->lastname),
                'username' => $referral->username,
                'email' => $referral->email,
                'balance' => showAmount($referral->balance ?? 0),
                'joined_date' => date('M d, Y', strtotime($referral->created_at)),
                'status' => $referral->status,
                'level' => $level,
                'children' => $children,
                'direct_referrals' => $totalReferrals,
                'has_children' => count($children) > 0
            ];
        }

        return $result;
    }

     private function countDirectReferrals($userId)
    {
        return User::where('referred_user_id', $userId)->count();
    }

    private function countTotalReferrals($userId, $visited = [])
    {
        // Prevent infinite loops
        if (in_array($userId, $visited)) {
            return 0;
        }
        
        $visited[] = $userId;
        
        $directCount = User::where('referred_user_id', $userId)->count();
        $totalCount = $directCount;
        
        // Get all direct referrals
        $directReferrals = User::where('referred_user_id', $userId)->pluck('id');
        
        // Recursively count referrals of referrals
        foreach ($directReferrals as $referralId) {
            $totalCount += $this->countTotalReferrals($referralId, $visited);
        }
        
        return $totalCount;
    }


    public function kycDetails($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.kyc_detail')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'KYC Details';
        $user = User::findOrFail($id);
        return view('admin.users.kyc_detail', compact('pageTitle', 'user'));
    }

    // public function kycApprove($id)
    // {
    //     $admin = auth()->guard('admin')->user();

    //     if (!$admin || !$admin->can('users.update')) {
    //         return response()->view('admin.errors.403', [], 403);
    //     }

    //     $user = User::findOrFail($id);

    //     $kyc_data = $user->kyc_data;

    //     $files = [];

    //     foreach ($kyc_data as $val) {
    //         if ($val->type === 'file') {

    //             $fileRelativePath = $val->value;
    //             $fullPath = getFilePath('verify') . '/' . $fileRelativePath;

    //             if (file_exists($fullPath)) {
    //                 $filename = basename($fileRelativePath);
    //                 $files[] = [
    //                     'name' => 'file_upload[]',
    //                     'contents' => file_get_contents($fullPath),
    //                     'filename' => $filename,
    //                 ];
    //             }
    //         }
    //     }

    //     $proApiResponse = Http::withHeaders([
    //         'X-API-KEY' => config('services.lite_api.key'),
    //     ])->attach($files)
    //         ->post(config('services.lite_api.url') . '/api/admin/users/kyc-approve', [
    //             'username' => $user->username,
    //             'kyc_data' => json_encode($kyc_data),
    //         ]);

    //     $responseBody = $proApiResponse->json();

    //     if ($proApiResponse->successful()) {

    //         if (isset($responseBody['status']) && $responseBody['status'] === 'success') {
    //             DB::commit();

    //             $user->kv = Status::KYC_VERIFIED;
    //             $user->save();
    //             notify($user, 'KYC_APPROVE', []);

    //             $notify[] = ['success', 'KYC approved successfully with Lite Account'];
    //             return to_route('admin.users.kyc.pending')->withNotify($notify);
    //         } else {
    //             $user->kv = Status::KYC_VERIFIED;
    //             $user->save();
    //             $notify[] = ['success', 'KYC approved successfully'];
    //             return to_route('admin.users.kyc.pending')->withNotify($notify);
    //         }
    //     } else {
    //         $notify[] = ['error', 'Check Your Network Connection'];
    //         return back()->withNotify($notify);
    //     }
    // }

    // public function kycReject(Request $request, $id)
    // {
    //     $admin = auth()->guard('admin')->user();

    //     if (!$admin || !$admin->can('users.update')) {
    //         return response()->view('admin.errors.403', [], 403);
    //     }

    //     $request->validate([
    //         'reason' => 'required'
    //     ]);
    //     $user = User::findOrFail($id);
    //     $user->kv = Status::KYC_UNVERIFIED;
    //     $user->kyc_rejection_reason = $request->reason;
    //     $user->save();

    //     notify($user, 'KYC_REJECT', [
    //         'reason' => $request->reason
    //     ]);

    //     $notify[] = ['success', 'KYC rejected successfully'];
    //     return to_route('admin.users.kyc.pending')->withNotify($notify);
    // }

    public function kycApprove($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $user = User::findOrFail($id);
        $user->kv = Status::KYC_VERIFIED;
        $user->save();

        notify($user, 'KYC_APPROVE', []);

        $notify[] = ['success', 'KYC approved successfully'];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }

    public function kycReject(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'reason' => 'required'
        ]);
        $user = User::findOrFail($id);
        $user->kv = Status::KYC_UNVERIFIED;
        $user->kyc_rejection_reason = $request->reason;
        $user->save();

        notify($user, 'KYC_REJECT', [
            'reason' => $request->reason
        ]);

        $notify[] = ['success', 'KYC rejected successfully'];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $user = User::findOrFail($id);
        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array) $countryData;
        $countries = implode(',', array_keys($countryArray));

        $countryCode = $request->country;
        $country = $countryData->$countryCode->country;
        $dialCode = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:40',
            'country' => 'required|in:' . $countries,
        ]);

        $exists = User::where('mobile', $request->mobile)->where('dial_code', $dialCode)->where('id', '!=', $user->id)->exists();
        if ($exists) {
            $notify[] = ['error', 'The mobile number already exists.'];
            return back()->withNotify($notify);
        }

        $role = ($request?->role == 'on') ? Status::LEADER : Status::CUSTOMER;
        $is_top_leader = ($request?->is_top_leader == 'on') ? Status::TOP_LEADER : Status::NORMAL_LEADER;

        $user->mobile = $request->mobile;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        $user->country_name = @$country;
        $user->dial_code = $dialCode;
        $user->country_code = $countryCode;
        $user->role = $role;
        $user->is_top_leader = $is_top_leader;

        $user->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $user->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $user->ts = $request->ts ? Status::ENABLE : Status::DISABLE;

        if (!$request->kv) {
            $user->kv = Status::KYC_UNVERIFIED;
            if ($user->kyc_data) {
                foreach ($user->kyc_data as $kycData) {
                    if ($kycData->type == 'file') {
                        fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
                    }
                }
            }
            $user->kyc_data = null;
        } else {
            $user->kv = Status::KYC_VERIFIED;

            // Update KYC data if it exists and we have address/state/zip updates
            if ($user->kyc_data && ($request->filled('address') || $request->filled('state') || $request->filled('zip'))) {
                $kycCollection = collect($user->kyc_data);
                $kycByName = $kycCollection->keyBy('name');

                // Update Address field
                if ($request->filled('address') && $kycByName->has('Address')) {
                    $addressField = $kycByName->get('Address');
                    $addressField->value = $request->address;
                }

                // Update State field
                if ($request->filled('state') && $kycByName->has('State')) {
                    $stateField = $kycByName->get('State');
                    $stateField->value = $request->state;
                }

                // Update Zip Code field
                if ($request->filled('zip') && $kycByName->has('Zip Code')) {
                    $zipField = $kycByName->get('Zip Code');
                    $zipField->value = $request->zip;
                }


                // Convert back to array for saving
                $user->kyc_data = $kycCollection->values()->toArray();
            }
        }

        $user->save();

        $notify[] = ['success', 'User details updated successfully'];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $trx = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $user->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', 'Balance added successfully'];
        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $user->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $user->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[] = ['success', 'Balance subtracted successfully'];
        }

        $user->save();

        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx = $trx;
        $transaction->details = $request->remark;
        $transaction->save();

        notify($user, $notifyTemplate, [
            'trx' => $trx,
            'amount' => showAmount($amount, currencyFormat: false),
            'remark' => $request->remark,
            'post_balance' => showAmount($user->balance, currencyFormat: false)
        ]);

        return back()->withNotify($notify);
    }

    public function login($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.login')) {
            return response()->view('admin.errors.403', [], 403);
        }

        // Auth::loginUsingId($id);
        Auth::guard('web')->loginUsingId($id);
        return to_route('user.product.index');
    }

    public function status(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $user = User::findOrFail($id);
        if ($user->status == Status::USER_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255'
            ]);
            $user->status = Status::USER_BAN;
            $user->ban_reason = $request->reason;
            $notify[] = ['success', 'User banned successfully'];
        } else {
            $user->status = Status::USER_ACTIVE;
            $user->ban_reason = null;
            $notify[] = ['success', 'User unbanned successfully'];
        }
        $user->save();
        return back()->withNotify($notify);
    }


    public function showNotificationSingleForm($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.notification')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $user = User::findOrFail($id);
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.users.detail', $user->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $user->username;
        return view('admin.users.notification_single', compact('pageTitle', 'user'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.notification')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'message' => 'required',
            'via' => 'required|in:email,sms,push',
            'subject' => 'required_if:via,email,push',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $imageUrl = null;
        if ($request->via == 'push' && $request->hasFile('image')) {
            $imageUrl = fileUploader($request->image, getFilePath('push'));
        }

        $template = NotificationTemplate::where('act', 'DEFAULT')->where($request->via . '_status', Status::ENABLE)->exists();
        if (!$template) {
            $notify[] = ['warning', 'Default notification template is not enabled'];
            return back()->withNotify($notify);
        }

        $user = User::findOrFail($id);
        notify($user, 'DEFAULT', [
            'subject' => $request->subject,
            'message' => $request->message,
        ], [$request->via], pushImage: $imageUrl);
        $notify[] = ['success', 'Notification sent successfully'];
        return back()->withNotify($notify);
    }

    public function showNotificationAllForm()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.notification')) {
            return response()->view('admin.errors.403', [], 403);
        }


        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToUser = User::notifyToUser();
        $users = User::active()->count();
        $pageTitle = 'Notification to Verified Users';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('admin.users.notification_all', compact('pageTitle', 'users', 'notifyToUser'));
    }

    public function sendNotificationAll(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.notification')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'via' => 'required|in:email,sms,push',
            'message' => 'required',
            'subject' => 'required_if:via,email,push',
            'start' => 'required|integer|gte:1',
            'batch' => 'required|integer|gte:1',
            'being_sent_to' => 'required',
            'cooling_time' => 'required|integer|gte:1',
            'number_of_top_deposited_user' => 'required_if:being_sent_to,topDepositedUsers|integer|gte:0',
            'number_of_days' => 'required_if:being_sent_to,notLoginUsers|integer|gte:0',
            'image' => ["nullable", 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'number_of_days.required_if' => "Number of days field is required",
            'number_of_top_deposited_user.required_if' => "Number of top deposited user field is required",
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }


        $template = NotificationTemplate::where('act', 'DEFAULT')->where($request->via . '_status', Status::ENABLE)->exists();
        if (!$template) {
            $notify[] = ['warning', 'Default notification template is not enabled'];
            return back()->withNotify($notify);
        }

        if ($request->being_sent_to == 'selectedUsers') {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['user' => session()->get('SEND_NOTIFICATION')['user']]);
            } else {
                if (!$request->user || !is_array($request->user) || empty($request->user)) {
                    $notify[] = ['error', "Ensure that the user field is populated when sending an email to the designated user group"];
                    return back()->withNotify($notify);
                }
            }
        }

        $scope = $request->being_sent_to;
        $userQuery = User::oldest()->active()->$scope();

        if (session()->has("SEND_NOTIFICATION")) {
            $totalUserCount = session('SEND_NOTIFICATION')['total_user'];
        } else {
            $totalUserCount = (clone $userQuery)->count() - ($request->start - 1);
        }


        if ($totalUserCount <= 0) {
            $notify[] = ['error', "Notification recipients were not found among the selected user base."];
            return back()->withNotify($notify);
        }


        $imageUrl = null;

        if ($request->via == 'push' && $request->hasFile('image')) {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['image' => session()->get('SEND_NOTIFICATION')['image']]);
            }
            if ($request->hasFile("image")) {
                $imageUrl = fileUploader($request->image, getFilePath('push'));
            }
        }

        $users = (clone $userQuery)->skip($request->start - 1)->limit($request->batch)->get();

        foreach ($users as $user) {
            notify($user, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], [$request->via], pushImage: $imageUrl);
        }

        return $this->sessionForNotification($totalUserCount, $request);
    }

    private function sessionForNotification($totalUserCount, $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.notification')) {
            return response()->view('admin.errors.403', [], 403);
        }

        if (session()->has('SEND_NOTIFICATION')) {
            $sessionData = session("SEND_NOTIFICATION");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData = $request->except('_token');
            $sessionData['total_sent'] = $request->batch;
            $sessionData['total_user'] = $totalUserCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalUserCount) {
            session()->forget("SEND_NOTIFICATION");
            $message = ucfirst($request->via) . " notifications were sent successfully";
            $url = route("admin.users.notification.all");
        } else {
            session()->put('SEND_NOTIFICATION', $sessionData);
            $message = $sessionData['total_sent'] . " " . $sessionData['via'] . "  notifications were sent successfully";
            $url = route("admin.users.notification.all") . "?email_sent=yes";
        }
        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }

    public function countBySegment($methodName)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.active')) {
            return response()->view('admin.errors.403', [], 403);
        }

        return User::active()->$methodName()->count();
    }

    public function list()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $query = User::active();

        if (request()->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . request()->search . '%')->orWhere('username', 'like', '%' . request()->search . '%');
            });
        }
        $users = $query->orderBy('id', 'desc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'users' => $users,
            'more' => $users->hasMorePages()
        ]);
    }

    public function notificationLog($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('users.notification')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $user = User::findOrFail($id);
        $pageTitle = 'Notifications Sent to ' . $user->username;
        $logs = NotificationLog::where('user_id', $id)->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'user'));
    }

    // customer cash transfer
    public function customerVoucherTransfer($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $customerBonus = CustomerBonus::where('user_id', $id)->first();

            if (!$user || !$customerBonus || $customerBonus->voucher_balance <= 0) {
                return back()->withNotify([['error', 'Invalid user or no voucher balance available.']]);
            }

            $voucherBalance = $customerBonus->voucher_balance;
            $customerBonus->decrement('voucher_balance', $voucherBalance);
            $customerBonus->decrement('total_balance', $voucherBalance);

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            $user->increment('balance', $voucherBalance);

            BonusTransactionHistory::create([
                'user_id' => $customerBonus->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $voucherBalance,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'customers_voucher' => $voucherBalance,
                'post_bonus_balance' => $customerBonus->total_balance,
                'details' => 'Customer Voucher Bonus Balance transferred to customer main account',
                'remark' => 'customers_voucher_transfer_to_customer_main_account',
            ]);

            Transaction::create([
                'user_id' => $customerBonus->user_id,
                'amount' => $voucherBalance,
                'trx_type' => '+',
                'remark' => 'customers_voucher_from_bonus_to_main_account',
                'details' => 'Customer Voucher Bonus Balance From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Bonus Transaction Successfully']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    public function customerFestivalTransfer($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $customerBonus = CustomerBonus::where('user_id', $id)->first();

            if (!$customerBonus || $customerBonus->festival_bonus_balance <= 0) {
                return back()->withNotify([['error', 'Invalid user or no festival bonus available.']]);
            }

            $festivalAmount = $customerBonus->festival_bonus_balance;

            $customerBonus->decrement('festival_bonus_balance', $festivalAmount);
            $customerBonus->decrement('total_balance', $festivalAmount);

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            $user->increment('balance', $festivalAmount);

            BonusTransactionHistory::create([
                'user_id' => $customerBonus->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $festivalAmount,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'customers_festival' => $festivalAmount,
                'post_bonus_balance' => $customerBonus->total_balance,
                'details' => 'Festival Bonus Balance transferred to customer main account',
                'remark' => 'festival_bonus_balance_transfer_to_customer_main_account',
            ]);

            Transaction::create([
                'user_id' => $customerBonus->user_id,
                'amount' => $festivalAmount,
                'trx_type' => '+',
                'remark' => 'festival_bonus_balance_from_bonus_to_main_account',
                'details' => 'Festival Bonus Balance From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Festival Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    public function customerSavingTransfer($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $customerBonus = CustomerBonus::where('user_id', $id)->first();

            if (!$customerBonus || $customerBonus->saving <= 0) {
                return back()->withNotify([['error', 'Invalid user or no saving bonus available.']]);
            }

            $savingAmount = $customerBonus->saving;

            $customerBonus->decrement('saving', $savingAmount);
            $customerBonus->decrement('total_balance', $savingAmount);

            $user->increment('balance', $savingAmount);

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            BonusTransactionHistory::create([
                'user_id' => $customerBonus->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $savingAmount,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'customers_saving' => $savingAmount,
                'post_bonus_balance' => $customerBonus->total_balance,
                'details' => 'Saving Bonus Balance transferred to customer main account',
                'remark' => 'saving_bonus_balance_transfer_to_customer_main_account',
            ]);

            Transaction::create([
                'user_id' => $customerBonus->user_id,
                'amount' => $savingAmount,
                'trx_type' => '+',
                'remark' => 'customer_saving_balance_from_bonus_to_main_account',
                'details' => 'Customer Saving Balance From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Saving Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    // leader  cash transfer
    public function leaderLeasingTransfer($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
            $leaderBonus = LeaderBonus::where('user_id', $id)->first();

            if (!$leaderBonus || $leaderBonus->leasing_amount <= 0) {
                return back()->withNotify([['error', 'Invalid user or no leasing amount available.']]);
            }

            $leasingAmount = $leaderBonus->leasing_amount;

            $leaderBonus->decrement('leasing_amount', $leasingAmount);
            $leaderBonus->decrement('total_balance', $leasingAmount);

            $user->increment('balance', $leasingAmount);

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            BonusTransactionHistory::create([
                'user_id' => $leaderBonus->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $leasingAmount,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'leader_vehicle_lease' => $leasingAmount,
                'post_bonus_balance' => $leaderBonus->total_balance,
                'details' => 'Vehicle bonus transfer to leader main account',
                'remark' => 'vehicle_bonus_transfer_to_main_account',
            ]);

            Transaction::create([
                'user_id' => $leaderBonus->user_id,
                'amount' => $leasingAmount,
                'trx_type' => '+',
                'remark' => 'leader_vehicle_bonus_from_bonus_to_main_account',
                'details' => 'Leader Vehicle Bonus From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Vehicle Leasing Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    public function leaderPetrolTransfer($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
            $leaderBonus = LeaderBonus::where('user_id', $id)->first();

            if (!$leaderBonus || $leaderBonus->petrol_allowance <= 0) {
                return back()->withNotify([['error', 'Invalid user or no petrol allowance available.']]);
            }

            $petrolAllowance = $leaderBonus->petrol_allowance;

            $leaderBonus->decrement('petrol_allowance', $petrolAllowance);
            $leaderBonus->decrement('total_balance', $petrolAllowance);

            $user->increment('balance', $petrolAllowance);

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            BonusTransactionHistory::create([
                'user_id' => $leaderBonus->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $petrolAllowance,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'leader_petrol' => $petrolAllowance,
                'post_bonus_balance' => $leaderBonus->total_balance,
                'details' => 'Petrol Allowance transfer to leader main account',
                'remark' => 'petrol_allowance_transfer_to_leader_main_account',
            ]);

            Transaction::create([
                'user_id' => $leaderBonus->user_id,
                'amount' => $petrolAllowance,
                'trx_type' => '+',
                'remark' => 'leader_petrol_allowance_from_bonus_to_main_account',
                'details' => 'Leader Petrol Allowance From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Petrol Allowance Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    public function leaderReferralCalculate($id)
    {
        DB::beginTransaction();

        try {
            $headUser = User::find($id);
            if (!$headUser) {
                return back()->withNotify([['error', 'User not found']]);
            }

            $referralStats = $this->getReferralTreeStats($headUser);
            $totalUsers = $referralStats['total_users'] ?? 0;
            $totalLevels = $referralStats['total_levels'] ?? 0;

            $leaderBonus = LeaderBonus::where('user_id', $id)->first();
            if (!$leaderBonus) {
                return back()->withNotify([['error', 'Leader bonus record not found']]);
            }

            $leaderBonus->total_users = $totalUsers;
            $leaderBonus->total_levels = $totalLevels;
            $leaderBonus->save();

            DB::commit();
            return back()->withNotify([['success', 'Leader Referral Tree Recalculated Successfully']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    // top leader  cash transfer
    public function topLeaderCarTransfer($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return back()->withNotify([['error', 'User not found']]);
            }

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            $topLeader = TopLeader::where('user_id', $id)->first();
            if (!$topLeader) {
                return back()->withNotify([['error', 'Top Leader bonus record not found']]);
            }

            $carBonus = $topLeader->for_car;

            $topLeader->decrement('for_car', $carBonus);
            $topLeader->decrement('total_balance', $carBonus);

            BonusTransactionHistory::create([
                'user_id' => $topLeader->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $carBonus,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'top_leader_car' => $carBonus,
                'post_bonus_balance' => $topLeader->total_balance,
                'details' => 'Car Bonus Transfer To Top Leader Main Account',
                'remark' => 'car_bonus_transfer_top_leader_to_main_account',
            ]);

            $user->increment('balance', $carBonus);

            Transaction::create([
                'user_id' => $topLeader->user_id,
                'amount' => $carBonus,
                'trx_type' => '+',
                'remark' => 'top_leader_car_bonus_from_bonus_to_main_account',
                'details' => 'Top Leader Car Bonus From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Car Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    public function topLeaderHouseTransfer($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
            if (!$user) {
                return back()->withNotify([['error', 'User not found']]);
            }

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            $topLeader = TopLeader::where('user_id', $id)->first();
            if (!$topLeader) {
                return back()->withNotify([['error', 'Top Leader record not found']]);
            }

            $houseBonus = $topLeader->for_house;

            $topLeader->decrement('for_house', $houseBonus);
            $topLeader->decrement('total_balance', $houseBonus);

            BonusTransactionHistory::create([
                'user_id' => $topLeader->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $houseBonus,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'top_leader_house' => $houseBonus,
                'post_bonus_balance' => $topLeader->total_balance,
                'details' => 'House Bonus Transfer To Top Leader Main Account',
                'remark' => 'house_bonus_transfer_from_bonus_to_top_leader_main_account',
            ]);

            $user->increment('balance', $houseBonus);

            Transaction::create([
                'user_id' => $topLeader->user_id,
                'amount' => $houseBonus,
                'trx_type' => '+',
                'remark' => 'top_leader_house_from_bonus_to_main_account',
                'details' => 'Top Leader House Bonus From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'House Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    public function topLeaderExpensesTransfer($id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
            if (!$user) {
                return back()->withNotify([['error', 'User not found']]);
            }

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            $topLeader = TopLeader::where('user_id', $id)->first();
            if (!$topLeader) {
                return back()->withNotify([['error', 'Top Leader not found']]);
            }

            $expenseBonus = $topLeader->for_expenses;

            $topLeader->decrement('for_expenses', $expenseBonus);
            $topLeader->decrement('total_balance', $expenseBonus);

            BonusTransactionHistory::create([
                'user_id' => $topLeader->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $expenseBonus,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'top_leader_expenses' => $expenseBonus,
                'post_bonus_balance' => $topLeader->total_balance,
                'details' => 'Expense bonus transfer to top leader main account',
                'remark' => 'expense_bonus_transfer_top_leader_to_main_balance',
            ]);

            $user->increment('balance', $expenseBonus);

            Transaction::create([
                'user_id' => $topLeader->user_id,
                'amount' => $expenseBonus,
                'trx_type' => '+',
                'remark' => 'top_leader_expense_from_bonus_to_main_account',
                'details' => 'Top Leader Expense From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Expense Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    // company expenses saving transfer
    public function companySavingTransfer($id)
    {

        DB::beginTransaction();
        try {
            $user = User::find($id);
            $companySaving = CompanySaving::first();

            if (!$user || !$companySaving || $companySaving->balance <= 0) {
                $notify[] = ['error', 'Invalid user or no voucher balance available'];
                return back()->withNotify($notify);
            }
            $savingBalance = $companySaving->balance;
            $companySaving->decrement('balance', $savingBalance);

            $user->increment('balance', $savingBalance);

            CompanyExpensesSavingHistory::create([
                'company_id' => $user->id,
                // 'pkg_id' => $package->id,
                // 'pkg_activation_comm_id' => $packageCommissions->id,
                'user_id' => $user->id,
                'remark' => 'transfer_company_saving_to_wallet',
                'charge' => 0,
                'trx_type' => '-',
                'details' => 'Company Saving Transfer To Wallet',
                'amount' => $savingBalance,
                'post_saving_balance' => $companySaving->balance,
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $savingBalance,
                'trx_type' => '+',
                'remark' => 'company_saving_from_saving_account',
                'details' => 'Company Saving From Saving ACC To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Saving Expenses Transaction Successfully']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
        }
    }

    // sub functions
    private function getReferralTreeStats(User $user)
    {
        $totalUsers = 0;
        $maxLevel = 0;

        $this->countReferralsRecursively($user->id, 1, $totalUsers, $maxLevel);

        return [
            'total_users' => $totalUsers,
            'total_levels' => $maxLevel,
        ];
    }

    private function countReferralsRecursively($userId, $level, &$totalUsers, &$maxLevel)
    {
        $referredUsers = User::where('referred_user_id', $userId)->get();

        if ($referredUsers->isNotEmpty()) {
            $maxLevel = max($maxLevel, $level);
            foreach ($referredUsers as $referredUser) {
                $totalUsers++;
                $this->countReferralsRecursively($referredUser->id, $level + 1, $totalUsers, $maxLevel);
            }
        }
    }

    public function isLeader($id)
    {
        $user = User::find($id);
        return $user && $user->role == Status::LEADER;
    }

    public function isTopLeader($id)
    {
        $user = User::find($id);
        return $user && $user->role == Status::LEADER;
    }

    public function secondOwner($id)
    {
        $user = User::find($id);

        if (!$user) {
            return back()->withNotify([['error', 'User not found']]);
        }

        $secondOwner = SecondOwner::where('original_owner_id', $user->id)->first();
        if (!$secondOwner) {
            return back()->withNotify([['error', 'Second owner not found']]);
        }

        $pageTitle = 'Second Owner Details of ' . $user->username;

        return view('admin.users.second_owner', compact('pageTitle', 'user', 'secondOwner'));
    }
}
