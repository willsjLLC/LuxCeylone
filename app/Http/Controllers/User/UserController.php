<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Deposit;
use App\Models\EmployeePackageActivationHistory;
use App\Models\JobPost;
use App\Models\JobProve;
use App\Models\Training;
use App\Models\UserTraining;
use App\Models\Withdrawal;
use App\Lib\GoogleAuthenticator;
use App\Models\Category;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\Transaction;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Service\PackageActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\User\ProductCategoryController;
use App\Models\FavoriteProducts;
use App\Models\Advertisement;
use App\Models\AdvertisementBoostPackage;
use App\Models\BonusTransactionHistory;
use App\Models\LeaderBonus;
use App\Models\User;
use App\Models\KeyValuePair;
use App\Models\Month; // Ensure the Month model is imported
use App\Models\CustomerBonus;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $packageActivationService;

    public function __construct(PackageActivationService $packageActivationService)
    {
        $this->packageActivationService = $packageActivationService;
    }

    public function home()
    {
        $user = auth()->user();
        $pageTitle = 'Dashboard';
        $userId = auth()->user()->id;
        $data['balance'] = auth()->user()->balance;
        $data['deposit_balance'] = Deposit::successful()->where('user_id', $userId)->sum('amount');
        $data['withdraw_balance'] = Withdrawal::approved()->where('user_id', $userId)->sum('amount');
        $data['complete_job'] = JobProve::where('user_id', $userId)->approve()->count();
        $data['created_job'] = JobPost::where('user_id', $userId)->count();
        $data['transaction'] = Transaction::where("user_id", $userId)->count();

        $job = JobProve::where('user_id', $userId)->approve();
        $jobs = $job->with('job', 'user')->orderBy('id', 'desc')->take(5)->get();
        $prove = $job->select('id', 'created_at')->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m');
        });

        $jobCount = [];
        $jobArr = [];

        foreach ($prove as $key => $value) {
            $jobCount[(int) $key] = count($value);
        }

        $month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 1; $i <= 12; $i++) {

            if (!empty($jobCount[$i])) {
                $jobArr[$i]['count'] = $jobCount[$i];
            } else {
                $jobArr[$i]['count'] = 0;
            }

            $jobArr[$i]['month'] = $month[$i - 1] . -Date('Y');
        }

        return view('Template::user.dashboard', compact('pageTitle', 'data', 'jobArr', 'jobs', 'needsActivation'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->save();

            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function customerBonusTransactions()
    {
        // Check if user is a leader (role = 2)


        $pageTitle = 'Customer Bonus Transactions';
        $remarks = BonusTransactionHistory::distinct('remark')->orderBy('remark')->get('remark');


        // Get distinct transaction types for filtering
        $trxTypes = BonusTransactionHistory::distinct('trx_type')->orderBy('trx_type')->get('trx_type');

        // Query bonus transactions for customer-related bonuses where user is the leader
        $transactions = BonusTransactionHistory::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('customers_voucher', '>', 0)
                    ->orWhere('customers_festival', '>', 0)
                    ->orWhere('customers_saving', '>', 0);
            })
            ->searchable(['trx'])
            ->filter(['trx_type'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('Template::user.customerBonusTransactions', compact('pageTitle', 'transactions', 'trxTypes', 'remarks'));
    }

    public function leaderBonusTransactions()
    {
        // Check if user is a leader (role = 2)
        if (auth()->user()->role != 2) {
            abort(403, 'Access denied. Only leaders can access this page.');
        }

        $pageTitle = 'Leader Bonus Transactions';
        $remarks = BonusTransactionHistory::distinct('remark')->orderBy('remark')->get('remark');
        // Get distinct transaction types for filtering
        $trxTypes = BonusTransactionHistory::distinct('trx_type')->orderBy('trx_type')->get('trx_type');

        // Query bonus transactions for leader-related bonuses where user is the leader
        $transactions = BonusTransactionHistory::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('leader_bonus', '>', 0)
                    ->orWhere('leader_vehicle_lease', '>', 0)
                    ->orWhere('leader_petrol', '>', 0);
            })
            ->searchable(['trx'])
            ->filter(['trx_type'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('Template::user.leaderBonusTransactions', compact('pageTitle', 'transactions', 'trxTypes', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.product.index')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.product.index')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        abort_if($user->kv == Status::VERIFIED, 403);
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {

        // $duplicateUser = DB::table('users')
        //     ->whereIn('kv', [Status::KYC_PENDING, Status::KYC_VERIFIED])
        //     ->whereJsonContains('kyc_data', [['name' => 'NIC Number', 'value' => $request->id_no]])
        //     ->exists();
        // if ($duplicateUser) {
        //     return back()->withNotify([["error", 'ID No Duplicate Found']]);
        // }

        $form = Form::where('act', 'kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);
        $user->kyc_data = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.product.index')->withNotify($notify);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.product.index');
        }

        $pageTitle = 'User Data';
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        // Get districts from the database
        $districts = DB::table('districts')->orderBy('name')->get();
        $defaultDistrictId = DB::table('districts')->first()->id;
        $cities = DB::table('cities')->where('district_id', $defaultDistrictId)->orderBy('name')->get();

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode', 'districts', 'cities'));
    }

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.product.index');
        }

        $countryData = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'required|in:' . $countries,
            'mobile_code' => 'required|in:' . $mobileCodes,
            'username' => 'required|unique:users|min:6',
            'mobile' => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);
        $username = strtolower(trim($request->username));

        if (preg_match("/[^a-z0-9_]/", trim($username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code = $request->country_code;
        $user->mobile = $request->mobile;
        $user->username = $username;


        $user->address = $request->address;
        $user->city = $request->city_name;
        $user->district = $request->district_name;
        $user->district_id = $request->district_id;
        $user->city_id = $request->city_id;
        $user->state = $request->state;
        $user->zip = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.product.index');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token = $request->token;
        $deviceToken->is_app = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userHome()
    {
        $user = auth()->user();
        $pageTitle = 'User Home';

        $categories = Category::with('subcategories')->orderBy('name')->get();

        $advertisements = Advertisement::where('status', [Status::AD_APPROVED, Status::AD_COMPLETED])

            ->with(['city', 'district'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(10);

        $needsTopUp = $this->packageActivationService->needsPackageActivation($user);

        $isPackageActive = $this->packageActivationService->isPackageActive($user);

        $recursive_top_up_range = getValue('USER_RECURSIVE_TOP_UP_RANGE');
        $lastActivatedTier = $user->employeePackageActivationHistories()->max('activated_for_earning_tier');

        $currentEarningTier = floor($user->total_earning / $recursive_top_up_range);

        $skippedPackages = 0;
        $outstandingTopUpAmount = 0;

        if ($currentEarningTier > 0 && $needsTopUp) {
            $expectedTier = $lastActivatedTier ? $lastActivatedTier + 1 : 1;
            $skippedPackages = $currentEarningTier - $expectedTier;
            $outstandingTopUpAmount = $expectedTier * $recursive_top_up_range;
        }

        return view('Template::user.home', compact(
            'pageTitle',
            'user',
            'categories',
            'advertisements',
            'needsTopUp',
            'isPackageActive',
            'skippedPackages',
            'outstandingTopUpAmount'
        ));
    }

    public function wallet()
    {
        $pageTitle = 'Wallet';
        $totalDeposit = Deposit::where('user_id', auth()->user()->id)->successful()->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id', auth()->user()->id)->approved()->sum('amount');

        $user = auth()->user()->load([
            'customerBonuses',
            'leaderBonuses'
        ]);

        $festivalBonusMessage = '';
        $withdrawalEnabled = true;
        $withdrawalMessage = '';
        $currentMonth = now()->month;
        $fromMonth = 0;
        $toMonth = 0;

        if ($user->kv == 0) {
            $festivalBonusMessage = 'KYC Verification Required';
        } else {
            $kycData = $user->kyc_data;
            $religion = collect($kycData)->firstWhere('name', 'religion')->value ?? null;

            $bonusPeriods = [
                'Sinhala' => [
                    'from' => getValue('SINHALISE_BONUS_FROM'),
                    'to' => getValue('SINHALISE_BONUS_TO'),
                    'monthNames' => [
                        Month::find(getValue('SINHALISE_BONUS_FROM'))->name ?? 'Unknown',
                        Month::find(getValue('SINHALISE_BONUS_TO'))->name ?? 'Unknown'
                    ]
                ],
                'Tamil' => [
                    'from' => getValue('TAMIL_BONUS_FROM'),
                    'to' => getValue('TAMIL_BONUS_TO'),
                    'monthNames' => [
                        Month::find(getValue('TAMIL_BONUS_FROM'))->name ?? 'Unknown',
                        Month::find(getValue('TAMIL_BONUS_TO'))->name ?? 'Unknown'
                    ]
                ],
                'Muslims' => [
                    'from' => getValue('MUSLIMS_BONUS_FROM'),
                    'to' => getValue('MUSLIMS_BONUS_TO'),
                    'monthNames' => [
                        Month::find(getValue('MUSLIMS_BONUS_FROM'))->name ?? 'Unknown',
                        Month::find(getValue('MUSLIMS_BONUS_TO'))->name ?? 'Unknown'
                    ]
                ],
                'Christian/Catholic' => [
                    'from' => getValue('CHRISTIAN_BONUS_FROM'),
                    'to' => getValue('CHRISTIAN_BONUS_TO'),
                    'monthNames' => [
                        Month::find(getValue('CHRISTIAN_BONUS_FROM'))->name ?? 'Unknown',
                        Month::find(getValue('CHRISTIAN_BONUS_TO'))->name ?? 'Unknown'
                    ]
                ],
            ];

            if ($religion && isset($bonusPeriods[$religion])) {
                $fromMonth = (int) $bonusPeriods[$religion]['from'];
                $toMonth = (int) $bonusPeriods[$religion]['to'];
                $monthNames = $bonusPeriods[$religion]['monthNames'];

                if ($currentMonth >= $fromMonth && $currentMonth < $toMonth) {
                    $festivalBonusMessage = 'Transfer Now!';
                } else {
                    $festivalBonusMessage = "Festival bonus available from {$monthNames[0]} to {$monthNames[1]}";
                }
            } else {
                $festivalBonusMessage = 'Religion not specified or invalid';
            }
        }

        $trainings = Training::all();
        $userTotalEarning = $user->total_earning;
        $reason = 0;

        foreach ($trainings as $training) {
            if ($userTotalEarning >= $training->min_income_threshold) {
                $userTraining = null;
                if ($user->userTrainings) {
                    $userTraining = $user->userTrainings->firstWhere('training_id', $training->id);
                }

                if (!$userTraining) {
                    $withdrawalEnabled = false;
                    $reason = 1;
                    $withdrawalMessage = "You must buy a ticket for the 'R.s. {$training->min_income_threshold} commission earn' training to enable withdrawals & cash transfers.";
                    break;
                } elseif ($userTraining->status !== Status::TRAINING_COMPLETED) {
                    $reason = 1;
                    $withdrawalEnabled = false;
                    $withdrawalMessage = "Wait until your 'R.s. {$training->min_income_threshold} commission earn' training is completed.";
                    break;
                }
            }
        }

        if ($withdrawalEnabled) {
            $hasActivePackage = EmployeePackageActivationHistory::where('user_id', $user->id)
                ->exists();

            if (!$hasActivePackage) {
                $withdrawalEnabled = false;
                $reason = 2;
                $withdrawalMessage = "You must have an active package to withdraw funds.";
            }
        }

        if ($withdrawalEnabled) {
            $needsTopUp = $this->packageActivationService->needsPackageActivation($user);

            if ($needsTopUp) {
                $lastActivatedTier = $user->employeePackageActivationHistories()->max('activated_for_earning_tier');
                $recursive_top_up_range = getValue('USER_RECURSIVE_TOP_UP_RANGE');
                $currentEarningTier = floor($user->total_earning / $recursive_top_up_range);
                $expectedTier = $lastActivatedTier ? $lastActivatedTier + 1 : 1;
                $skippedPackages = $currentEarningTier - $expectedTier;
                $outstandingTopUpAmount = $expectedTier * $recursive_top_up_range;

                $withdrawalEnabled = false;
                $reason = 3;

                $withdrawalMessage = "You have outstanding package activations.";
                if ($skippedPackages > 0) {
                    $withdrawalMessage .= " You have skipped " . ($skippedPackages + 1) . " package tiers.";
                }
                $withdrawalMessage .= " You must activate the package for tier {$expectedTier} to enable withdrawals.";
            }
        }

        $userTrainingData = UserTraining::where('user_id', $user->id)->get();
        $trainings = Training::all();

        return view('Template::user.wallet', compact(
            'pageTitle',
            'totalDeposit',
            'totalWithdrawals',
            'user',
            'festivalBonusMessage',
            'currentMonth',
            'fromMonth',
            'toMonth',
            'trainings',
            'withdrawalEnabled',
            'withdrawalMessage',
            'reason'
        ));
    }

    public function getFavorites()
    {
        $user = auth()->user();

        $pageTitle = 'Favourites';

        $favorites_jobs = Favorite::where('user_id', $user->id)->with('user')->with('job')->get();

        $favorite_products = FavoriteProducts::where('user_id', $user->id)->with('product')->get();

        return view('Template::user.favorite', compact('pageTitle', 'favorites_jobs', 'favorite_products'));
    }

    public function customerfestivalTransfer()
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $id = $user->id;
            $user = User::find($id);
            $customerBonus = CustomerBonus::where('user_id', $id)->first();

            // Check if user has any festival bonus available
            if (!$customerBonus || $customerBonus->festival_bonus_balance <= 0) {
                return back()->withNotify([['error', 'Invalid user or no festival bonus available.']]);
            }

            // Get the festival bonus amount
            $festivalBonus = $customerBonus->festival_bonus_balance;

            // Decrement the festival bonus balance
            $customerBonus->decrement('festival_bonus_balance', $festivalBonus);
            $customerBonus->decrement('total_balance', $festivalBonus);

            // Increment user's main balance
            $user->increment('balance', $festivalBonus);

            // Create transaction record
            Transaction::create([
                'user_id' => $customerBonus->user_id,
                'amount' => $festivalBonus,
                'trx_type' => '+',
                'remark' => 'customer_festival_bonus_from_bonus_to_main_account',
                'details' => 'Customer Festival Bonus From Bonus To Main Account',
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

    // leader bonus transfers
    public function leaderLeasingTransfer()
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $id = $user->id;
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

    public function leaderPetrolTransfer()
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = $user->id;
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

    public function leaderBonusTransfer()
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = $user->id;
            $user = User::find($id);
            $leaderBonus = LeaderBonus::where('user_id', $id)->first();

            if (!$leaderBonus || $leaderBonus->bonus <= 0) {
                return back()->withNotify([['error', 'Invalid user or no petrol allowance available.']]);
            }

            $bonusAllowance = $leaderBonus->bonus;

            $leaderBonus->decrement('bonus', $bonusAllowance);
            $leaderBonus->decrement('total_balance', $bonusAllowance);

            $user->increment('balance', $bonusAllowance);

            $is_leader = $this->isLeader($id);
            $is_top_leader = $this->isTopLeader($id);

            BonusTransactionHistory::create([
                'user_id' => $leaderBonus->user_id,
                'is_leader' => $is_leader,
                'is_top_leader' => $is_top_leader,
                'amount' => $bonusAllowance,
                'charge' => 0,
                'trx_type' => '-',
                'trx' => getTrx(),
                'leader_bonus' => $bonusAllowance,
                'post_bonus_balance' => $leaderBonus->total_balance,
                'details' => 'Bonus transfer to leader main account',
                'remark' => 'bonus_transfer_to_leader_main_account',
            ]);

            Transaction::create([
                'user_id' => $leaderBonus->user_id,
                'amount' => $bonusAllowance,
                'trx_type' => '+',
                'remark' => 'leader_bonus_from_bonus_to_main_account',
                'details' => 'Leader Bonus From Bonus To Main Account',
                'trx' => getTrx(),
                'post_balance' => $user->balance,
            ]);

            DB::commit();
            return back()->withNotify([['success', 'Bonus Successfully Transferred']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withNotify([['error', 'Something went wrong: ' . $e->getMessage()]]);
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

    // public function transferToLite(Request $request)
    // {

    //     try {
    //         $request->validate([
    //             'amount' => 'required|numeric|min:0.01',
    //         ]);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => 'Invalid amount provided.'], 422);
    //     }


    //     $liteUser = $request->user();
    //     $amount = $request->input('amount');

    //     if ($liteUser->balance < $amount) {
    //         return response()->json(['message' => 'Insufficient balance in your Lite account.'], 403);
    //     }

    //     DB::beginTransaction();

    //     try {

    //         $liteUser->balance -= $amount;
    //         $liteUser->save();

    //         $proApiResponse = Http::withHeaders([
    //             'X-API-KEY' => config('services.lite_api.key'),
    //         ])->post(config('services.lite_api.url') . '/api/user/wallet/add', [
    //                     'username' => $liteUser->username,
    //                     'amount' => $amount,
    //                 ]);

    //         if ($proApiResponse->successful()) {

    //             Transaction::create([
    //                 'user_id' => $liteUser->id,
    //                 'amount' => $amount,
    //                 'post_balance' => $liteUser->balance,
    //                 'charge' => 0,
    //                 'trx_type' => '-',
    //                 'details' => 'Transfer amount to LITE Account',
    //                 'trx' => getTrx(),
    //                 'remark' => 'transfer_amount_to_lite_account',
    //             ]);

    //             DB::commit();

    //             return response()->json([
    //                 'message' => 'Transfer successful!',
    //                 'lite_balance' => $liteUser->balance,
    //                 'pro_balance' => $proApiResponse->json('new_balance'),
    //                 'pro_message' => $proApiResponse->json('message'),
    //             ], 200);

    //         }

    //         DB::rollBack();
    //         return response()->json(['message' => 'PRO account transfer failed: ' . $proApiResponse->json('message')], $proApiResponse->status());

    //     } catch (\Exception $e) {

    //         DB::rollBack();
    //         return response()->json(['message' => 'An internal server error occurred.'], 500);
    //     }
    // }

}
