<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Advertisement;
use App\Models\AdvertisementBoostedHistory;
use App\Models\AdvertisementBoostPackage;
use App\Models\AdvertisementPackage;
use App\Models\BonusTransactionHistory;
use App\Models\CartItem;
use App\Models\CompanyExpensesSavingHistory;
use App\Models\CompanySaving;
use App\Models\CustomerBonus;
use App\Models\Deposit;
use App\Models\EmployeePackageActivationHistory;
use App\Models\GatewayCurrency;
use App\Models\LeaderBonus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackageActivationCommission;
use App\Models\PurchaseHistory;
use App\Models\TopLeader;
use App\Models\Training;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\UserTraining;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Deposit Methods';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = urlPath('user.deposit.history');
        $data->failed_url = urlPath('user.deposit.history');
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $user->balance += $deposit->amount;
            $user->save();

            $methodName = $deposit->methodName();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $methodName;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'deposit';
            $transaction->save();

            pendingCommisions($deposit->user_id);
            processEmployeePackageActivations($deposit->user_id);

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                'amount' => showAmount($deposit->amount, currencyFormat: false),
                'charge' => showAmount($deposit->charge, currencyFormat: false),
                'rate' => showAmount($deposit->rate, currencyFormat: false),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $inputSlipNumber = $request->input('transaction_id');

        if (!$inputSlipNumber) {
            return back()->withNotify([["error", "Transaction ID is required"]]);
        }

        $deposits = Deposit::whereIn('status', [Status::PAYMENT_SUCCESS, Status::PAYMENT_PENDING])->whereNotNull('detail')->get();

        foreach ($deposits as $deposit) {
            $detail = is_string($deposit->detail) ? json_decode($deposit->detail, true) : $deposit->detail;

            if (!is_array($detail))
                continue;

            $existingSlipNumber = collect($detail)->firstWhere('name', 'Transaction ID')->value ?? null;

            if ($existingSlipNumber && $existingSlipNumber == $inputSlipNumber) {
                return back()->withNotify([["error", "Transaction ID duplicate found"]]);
            }
        }

        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->slip_no = $inputSlipNumber;
        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

    // public function manualDepositUpdate(Request $request)
    // {
    //     $inputSlipNumber = $request->input('transaction_id');

    //     if (!$inputSlipNumber) {
    //         return back()->withNotify([["error", "Transaction ID is required"]]);
    //     }

    //     // Check local deposits (existing code)
    //     $deposits = Deposit::whereIn('status', [Status::PAYMENT_SUCCESS, Status::PAYMENT_PENDING])->whereNotNull('detail')->get();

    //     foreach ($deposits as $deposit) {
    //         $detail = is_string($deposit->detail) ? json_decode($deposit->detail, true) : $deposit->detail;

    //         if (!is_array($detail))
    //             continue;

    //         $existingSlipNumber = collect($detail)->firstWhere('name', 'Transaction ID')->value ?? null;

    //         if ($existingSlipNumber && $existingSlipNumber == $inputSlipNumber) {
    //             return back()->withNotify([["error", "Transaction ID already exists in Pro system"]]);
    //         }
    //     }

    //     // NEW: Check Lite system for duplicate transaction ID
    //     try {
    //         $liteApiUrl = env('ADD_CITI_LITE_API');
    //         if ($liteApiUrl) {
    //             $response = Http::timeout(10)->post($liteApiUrl . '/api/check-transaction-id', [
    //                 'transaction_id' => $inputSlipNumber
    //             ]);

    //             if ($response->successful()) {
    //                 $responseData = $response->json();
    //                 if (isset($responseData['exists']) && $responseData['exists']) {
    //                     return back()->withNotify([["error", "Transaction ID already exists in Lite system"]]);
    //                 }
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         // Log the error but continue with local validation only
    //         Log::warning('Lite system transaction ID check failed: ' . $e->getMessage());
    //     }

    //     // Rest of your existing code remains the same...
    //     $track = session()->get('Track');
    //     $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
    //     abort_if(!$data, 404);
    //     $gatewayCurrency = $data->gatewayCurrency();
    //     $gateway = $gatewayCurrency->method;
    //     $formData = $gateway->form->form_data;

    //     $formProcessor = new FormProcessor();
    //     $validationRule = $formProcessor->valueValidation($formData);
    //     $request->validate($validationRule);
    //     $userData = $formProcessor->processFormData($request, $formData);

    //     $data->slip_no = $inputSlipNumber;
    //     $data->detail = $userData;
    //     $data->status = Status::PAYMENT_PENDING;
    //     $data->save();

    //     $adminNotification = new AdminNotification();
    //     $adminNotification->user_id = $data->user->id;
    //     $adminNotification->title = 'Deposit request from ' . $data->user->username;
    //     $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
    //     $adminNotification->save();

    //     notify($data->user, 'DEPOSIT_REQUEST', [
    //         'method_name' => $data->gatewayCurrency()->name,
    //         'method_currency' => $data->method_currency,
    //         'method_amount' => showAmount($data->final_amount, currencyFormat: false),
    //         'amount' => showAmount($data->amount, currencyFormat: false),
    //         'charge' => showAmount($data->charge, currencyFormat: false),
    //         'rate' => showAmount($data->rate, currencyFormat: false),
    //         'trx' => $data->trx
    //     ]);

    //     $notify[] = ['success', 'You have deposit request has been taken'];
    //     return to_route('user.deposit.history')->withNotify($notify);
    // }

    public function employeePackageActiveForm()
    {
        $pageTitle = 'Adciti Packages';
        $user = auth()->user();
        $packages = AdvertisementPackage::all();
        $activatedPackage = EmployeePackageActivationHistory::where('user_id', $user = auth()->user()->id)->where('activation_expired', Status::DISABLE)->first();
        return view('Template::user.payment.package_activate', compact('pageTitle', 'user', 'packages', 'activatedPackage'));
    }

    protected const MAX_REFERRAL_LEVELS = 4;

    // start of package activation & cash distribution
    public function employeePackageActive(Request $request)
    {

        $package = AdvertisementPackage::findOrFail($request->id);
        $user = auth()->user();
        $currentDate = now();

        $recursive_top_up_range = getValue('USER_RECURSIVE_TOP_UP_RANGE');

        $currentEarningTier = floor($user->total_earning / $recursive_top_up_range);

        $lastActivatedTier = EmployeePackageActivationHistory::where('user_id', $user->id)
            ->max('activated_for_earning_tier');

        if ($currentEarningTier == 0) {
            $tierToActivate = $currentEarningTier;
        } else {
            $tierToActivate = ($lastActivatedTier === null) ? 1 : $lastActivatedTier + 1;
        }

        if ($user->balance < $package->price) {
            $notify[] = ['error', 'Insufficient balance. Please top-up your account!'];
            return to_route('user.deposit.index')->withNotify($notify);
        }

        DB::beginTransaction();
        try {
            // Create transaction for package activation
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $package->price,
                'post_balance' => $user->balance - $package->price,
                'charge' => 0,
                'trx_type' => '-',
                'details' => 'User Package Activation',
                'trx' => getTrx(),
                'remark' => 'user_package_activation',
            ]);

            // oldest package
            $isPreviousPackage = EmployeePackageActivationHistory::where('user_id', $user->id)->oldest()->first();

            if ($isPreviousPackage) {
                $initialExpiryDate = Carbon::parse($isPreviousPackage->expiry_date);
                $daysSinceInitialExpiry = $initialExpiryDate->diffInDays($currentDate, false);

                if ($daysSinceInitialExpiry <= 0) {
                    // Current date is before or same as initial expiry date: give full duration
                    $expiryDate = $currentDate->copy()->addDays($package->package_duration);
                } else {
                    $remain = $daysSinceInitialExpiry % $package->package_duration;
                    $actuallyRemainDates = $package->package_duration - $remain;
                    $expiryDate = $currentDate->copy()->addDays($actuallyRemainDates);
                }
            } else {
                $expiryDate = $currentDate->copy()->addDays($package->package_duration);
            }

            // Boost option handling
            $canBoost = $package->includes_boost && $package->boost_package_id && $package->no_of_boost ? Status::BOOST_PACKAGE_AVAILABLE : Status::BOOST_PACKAGE_NOT_AVAILABLE;
            $boostPackageId = $canBoost == Status::BOOST_PACKAGE_AVAILABLE ? $package->boost_package_id : null;
            $noOfBoost = $canBoost == Status::BOOST_PACKAGE_AVAILABLE ? $package->no_of_boost : 0;

            // Commission and bonus amounts
            $packageCommissions = PackageActivationCommission::where('pkg_id', $package->id)->first();
            if (!$packageCommissions) {
                throw new \Exception('Package commission details not found.');
            }

            $companyCommission = $packageCommissions->company_commission;
            $companyExpenses = $packageCommissions->company_expenses;
            // $customersCommission = $packageCommissions->customers_commission;

            $levelCommissions = [
                0 => $packageCommissions->level_one_commission,
                1 => $packageCommissions->level_two_commission,
                2 => $packageCommissions->level_three_commission,
                3 => $packageCommissions->level_four_commission,
            ];

            // $singleCustomerCommission = $customersCommission / self::MAX_REFERRAL_FOR_CUSTOMER_COMMISSION;
            $customersVoucher = $packageCommissions->customers_voucher / self::MAX_REFERRAL_LEVELS;
            $customersFestival = $packageCommissions->customers_festival / self::MAX_REFERRAL_LEVELS;
            $customersSaving = $packageCommissions->customers_saving / self::MAX_REFERRAL_LEVELS;
            $leaderBonus = $packageCommissions->leader_bonus;
            $leaderVehicleLease = $packageCommissions->leader_vehicle_lease;
            $leaderPetrol = $packageCommissions->leader_petrol;
            $maxRefCompleteToCar = $packageCommissions->max_ref_complete_to_car;
            $topLeaderCar = $packageCommissions->top_leader_car;
            $topLeaderHouse = $packageCommissions->top_leader_house;
            $topLeaderExpenses = $packageCommissions->top_leader_expenses;

            $companyAccount = User::where('username', 'luxceylone')->firstOrFail();

            // Company profit and saving
            $companyAccount->increment('balance', $companyCommission);
            Transaction::create([
                'user_id' => $companyAccount->id,
                'debit_user_id' => $user->id,
                'amount' => $companyCommission,
                'post_balance' => $companyAccount->balance,
                'charge' => 0,
                'trx_type' => '+',
                'details' => 'Package Activation Commission To Company',
                'trx' => getTrx(),
                'remark' => 'package_activation_commission_to_company',
            ]);

            $companySaving = CompanySaving::first();
            if (!$companySaving) {
                $companySaving = CompanySaving::create(['id' => 1, 'balance' => 0]);
            }

            // Increment balance
            $companySaving->balance += $companyExpenses;
            $companySaving->save();

            CompanyExpensesSavingHistory::create([
                'company_id' => $companyAccount->id,
                'pkg_id' => $package->id,
                'pkg_activation_comm_id' => $packageCommissions->id,
                'user_id' => $user->id,
                'remark' => 'package_activation_company_saving',
                'charge' => 0,
                'trx_type' => '+',
                'details' => 'Package Activation Company Saving',
                'amount' => $companyExpenses,
                'post_saving_balance' => $companySaving->balance,
            ]);

            // Referral commission distribution
            $referrer = $user;
            $referralLevel = 1;

            // $referralCount = 0;
            // $eligibleReferrers = [];

            // for ($i = 0; $i < self::MAX_REFERRAL_LEVELS && $referrer->referred_user_id && $referrer->referred_user_id != $companyAccount->id; $i++) {
            //     $referrer = User::find($referrer->referred_user_id);
            //     if ($referrer) {
            //         $eligibleReferrers[] = $referrer;
            //         $referralCount++;
            //     } else {
            //         break;
            //     }
            // }

            // $distributedCommission = 0;
            // foreach ($eligibleReferrers as $referredUser) {

            //     // if ($referredUser->employee_package_activated == Status::PACKAGE_ACTIVE) { //else continue with other section
            //     $referredUser->increment('balance', $singleCustomerCommission);
            //     $referredUser->increment('total_earning', $singleCustomerCommission);
            //     Transaction::create([
            //         'user_id' => $referredUser->id,
            //         'debit_user_id' => $user->id,
            //         'amount' => $singleCustomerCommission,
            //         'post_balance' => $referredUser->balance,
            //         'charge' => 0,
            //         'trx_type' => '+',
            //         'details' => 'Package Activation Referral Commission',
            //         'trx' => getTrx(),
            //         'remark' => 'package_activation_referral_commission',
            //     ]);
            //     // }
            //     $distributedCommission += $singleCustomerCommission;
            //     $this->updateCustomerBonus($referredUser, $package, $packageCommissions, $customersVoucher, $customersFestival, $customersSaving, $user);
            // }

            // Referral commission distribution
            $referrer = $user;
            $referralLevel = 0;
            $distributedCommission = 0; // Initialize a counter for the total distributed commission

            $totalCustomerCommission = $packageCommissions->level_one_commission + $packageCommissions->level_two_commission + $packageCommissions->level_three_commission + $packageCommissions->level_four_commission;

            for ($i = 0; $i < self::MAX_REFERRAL_LEVELS && $referrer->referred_user_id && $referrer->referred_user_id != $companyAccount->id; $i++) {

                $referrer = User::find($referrer->referred_user_id);

                if ($referrer && isset($levelCommissions[$referralLevel])) {

                    $commissionAmount = $levelCommissions[$referralLevel];

                    $referrer->increment('balance', $commissionAmount);
                    $referrer->increment('total_earning', $commissionAmount);

                    Transaction::create([
                        'user_id' => $referrer->id,
                        'debit_user_id' => $user->id,
                        'amount' => $commissionAmount,
                        'post_balance' => $referrer->balance,
                        'charge' => 0,
                        'trx_type' => '+',
                        'details' => 'Level ' . $referralLevel . ' Referral Commission',
                        'trx' => getTrx(),
                        'remark' => 'package_activation_referral_commission',
                    ]);

                    $distributedCommission += $commissionAmount;

                    $this->updateCustomerBonus($referrer, $package, $packageCommissions, $customersVoucher, $customersFestival, $customersSaving, $user, $commissionAmount);

                    $referralLevel++;

                }
            }

            Log::info('ref level count' . $referralLevel);

            // Handle incomplete referral tree
            if ($referralLevel < self::MAX_REFERRAL_LEVELS) {
                Log::info('working=' . $referralLevel);
                $remainingCommission = $totalCustomerCommission - $distributedCommission;
                if ($remainingCommission > 0) {
                    $companyAccount->increment('balance', $remainingCommission);
                    Transaction::create([
                        'user_id' => $companyAccount->id,
                        'debit_user_id' => $user->id,
                        'amount' => $remainingCommission,
                        'post_balance' => $companyAccount->balance,
                        'charge' => 0,
                        'trx_type' => '+',
                        'details' => 'Undistributed Package Activation Referral Bonus Commission To Company',
                        'trx' => getTrx(),
                        'remark' => 'undistributed_package_activation_referral_bonus_commission_to_company',
                    ]);
                }
                $remainingBonusVoucher = $customersVoucher * (self::MAX_REFERRAL_LEVELS - $referralLevel);
                $remainingBonusFestival = $customersFestival * (self::MAX_REFERRAL_LEVELS - $referralLevel);
                $remainingBonusSaving = $customersSaving * (self::MAX_REFERRAL_LEVELS - $referralLevel);
                $totalRemainingBonus = $remainingBonusVoucher + $remainingBonusFestival + $remainingBonusSaving;

                if ($totalRemainingBonus > 0) {

                    // log to transaction - company reservation
                    $companyAccount->increment('balance', $totalRemainingBonus);
                    Transaction::create([
                        'user_id' => $companyAccount->id,
                        'debit_user_id' => $user->id,
                        'amount' => $totalRemainingBonus,
                        'post_balance' => $companyAccount->balance,
                        'charge' => 0,
                        'trx_type' => '+',
                        'details' => 'Undistributed Package Activation Customer Bonus Commission To Company',
                        'trx' => getTrx(),
                        'remark' => 'undistributed_package_activation_customer_bonus_commission_to_company',
                    ]);

                    // log to bonus history - company reservation
                    BonusTransactionHistory::create([
                        'user_id' => $companyAccount->id,
                        'debit_user_id' => $user->id,
                        'is_leader' => 0,
                        'is_top_leader' => 0,
                        'amount' => $totalRemainingBonus,
                        'charge' => 0,
                        'trx_type' => '+',
                        'trx' => getTrx(),
                        'customers_voucher' => $remainingBonusVoucher,
                        'customers_festival' => $remainingBonusFestival,
                        'customers_saving' => $remainingBonusSaving,
                        'post_bonus_balance' => $companyAccount->balance,
                        'details' => 'Unclaimed Referral Customer Bonus Package Activation to Company',
                        'remark' => 'unclaimed_referral_customer_bonus_package_activation_to_company',
                    ]);
                }
            }

            $headUser = $user;
            $totalLevelsToHead = 0;

            // check leaders
            while ($headUser->referred_user_id) {
                $headUser = User::find($headUser->referred_user_id);

                if (!$headUser)
                    break;

                if ($headUser->role == Status::LEADER && $headUser->is_top_leader != Status::TOP_LEADER) {
                    $totalLevelsToHead++;
                    break;
                }
                $totalLevelsToHead++;
            }


            if ($headUser && $headUser->role == Status::LEADER && $headUser->is_top_leader != Status::TOP_LEADER) {

                $referralStats = $this->getReferralTreeStats($headUser);

                $totalUsers = $referralStats['total_users'];
                $totalLevels = $referralStats['total_levels'];

                // post other bonus to leader & history table
                $this->updateLeaderBonus($headUser, $package, $packageCommissions, $leaderBonus, $leaderVehicleLease, $leaderPetrol, $totalLevelsToHead, $totalUsers >= $maxRefCompleteToCar, $totalUsers, $totalLevels, $user);
            } else {
                // If the activating user is directly under the company or no referrer found after the company
                $companyAccount->increment('balance', $leaderBonus + $leaderVehicleLease + $leaderPetrol);
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $leaderBonus + $leaderVehicleLease + $leaderPetrol,
                    'post_balance' => $companyAccount->balance,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => 'Unclaimed Package Activation Bonuses (Directly Under Company)',
                    'trx' => getTrx(),
                    'remark' => 'unclaimed_package_activation_bonuses_direct_under_company',
                ]);

                // copy the bonuses to the company
                BonusTransactionHistory::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'is_leader' => 0,
                    'is_top_leader' => 0,
                    'amount' => $leaderBonus + $leaderVehicleLease + $leaderPetrol,
                    'charge' => 0,
                    'trx_type' => '+',
                    'trx' => getTrx(),
                    'leader_bonus' => $leaderBonus,
                    'leader_vehicle_lease' => $leaderVehicleLease,
                    'leader_petrol' => $leaderPetrol,
                    'post_bonus_balance' => $companyAccount->balance,
                    'details' => 'Unclaimed Package Activation Bonuses (Leader)',
                    'remark' => 'unclaimed_package_activation_bonuses_direct_under_company',
                ]);
            }

            $headUser = $user;
            $totalLevelsToHead = 0;

            // check top leaders
            while ($headUser->referred_user_id) {
                $headUser = User::find($headUser->referred_user_id);

                if (!$headUser)
                    break;

                if ($headUser->role == Status::LEADER && $headUser->is_top_leader == Status::TOP_LEADER) {
                    $totalLevelsToHead++;
                    break;
                }
                $totalLevelsToHead++;
            }


            if ($headUser && $headUser->role == Status::LEADER && $headUser->is_top_leader == Status::TOP_LEADER) {

                $referralStats = $this->getReferralTreeStats($headUser);

                $totalUsers = $referralStats['total_users'];
                $totalLevels = $referralStats['total_levels'];


                $this->updateTopLeaderBonus($headUser, $package, $packageCommissions, $topLeaderCar, $topLeaderHouse, $topLeaderExpenses, $user);
            } else {
                // If the activating user is directly under the company or no referrer found after the company
                $companyAccount->increment('balance', $topLeaderCar + $topLeaderHouse + $topLeaderExpenses);
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $topLeaderCar + $topLeaderHouse + $topLeaderExpenses,
                    'post_balance' => $companyAccount->balance,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => 'Unclaimed Package Activation Bonuses (Directly Under Company)',
                    'trx' => getTrx(),
                    'remark' => 'unclaimed_package_activation_bonuses_direct_under_company',
                ]);

                // copy the bonuses to the company
                BonusTransactionHistory::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'is_leader' => 0,
                    'is_top_leader' => 0,
                    'amount' => $topLeaderCar + $topLeaderHouse + $topLeaderExpenses,
                    'charge' => 0,
                    'trx_type' => '+',
                    'trx' => getTrx(),
                    'top_leader_car' => $topLeaderCar,
                    'top_leader_house' => $topLeaderHouse,
                    'top_leader_expenses' => $topLeaderExpenses,
                    'post_bonus_balance' => $companyAccount->balance,
                    'details' => 'Unclaimed Package Activation Bonuses (Top Leader)',
                    'remark' => 'unclaimed_package_activation_bonuses_direct_under_company',
                ]);
            }

            // make expired all previous packages
            $previousPackages = EmployeePackageActivationHistory::where('user_id', $user->id)->get();
            if ($previousPackages->isNotEmpty()) {
                foreach ($previousPackages as $previousPackage) {
                    $previousPackage->activation_expired = Status::ACTIVATION_EXPIRED;
                    $previousPackage->save();
                }
            }

            // Create package activation history
            EmployeePackageActivationHistory::create([
                'user_id' => $user->id,
                'activated_for_earning_tier' => $tierToActivate,
                'package_id' => $package->id,
                'transaction_id' => $transaction->id,
                'payment_method' => 'PAY_BY_WALLET',
                'total_ads' => $package->no_of_advertisements,
                'used_ads' => 0,
                'total_boosted_ads' => $noOfBoost,
                'used_boosted_ads' => 0,
                'can_boost' => $canBoost,
                'boost_package_id' => $boostPackageId,
                'payment_status' => Status::PAYMENT_SUCCESS,
                'expiry_date' => $expiryDate,
            ]);

            $user->employee_package_activated = Status::PACKAGE_ACTIVE;
            $user->balance = $user->balance - $package->price;
            $user->save();

            DB::commit();

            notify($user, 'EMPLOYEE_PACKAGE_ACTIVE', [
                'username' => $user->username,
                'expiry_date' => $expiryDate->format('d M, Y'),
            ]);

            session([
                'package_activation_success' => [
                    'name' => $package->name,
                    'price' => $package->price,
                    'expiry_date' => $expiryDate->format('d M, Y'),
                    'total_ads' => $package->no_of_advertisements,
                    'has_boost' => $canBoost == Status::BOOST_PACKAGE_AVAILABLE,
                    'boost_ads' => $noOfBoost,
                ]
            ]);

            $notify[] = ['success', 'Employee package activated successfully valid until ' . $expiryDate->format('d M, Y')];
            return redirect()->route('user.product.index')->withNotify($notify);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            $notify[] = ['error', 'Package activation failed: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    protected function updateCustomerBonus(User $user, AdvertisementPackage $package, PackageActivationCommission $packageCommissions, $voucher, $festival, $saving, $debitUser, $commissionAmount)
    {
        $existBonus = CustomerBonus::firstOrCreate(['user_id' => $user->id], [
            'pkg_id' => $package->id,
            'pkg_activation_comm_id' => $packageCommissions->id,
            'status' => Status::ENABLE,
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'commission_balance' => 0,
            'voucher_balance' => 0,
            'festival_bonus_balance' => 0,
            'saving' => 0,
            'joined_at' => $user->created_at,
            'total_balance' => 0,
        ]);

        // calculate voucher dates
        if ($existBonus->is_voucher_open != Status::VOUCHER_OPEN) {

            $now = Carbon::now();
            $remainingDate = getValue('VOUCHER_REMAINING_DATE');
            $oldestRecord = EmployeePackageActivationHistory::where('user_id', $user->id)
                ->orderBy('created_at')
                ->first();

            if ($oldestRecord) {
                $diffInDays = Carbon::parse($oldestRecord->created_at)->diffInDays($now);

                if ($diffInDays > $remainingDate) {
                    $existBonus->is_voucher_open = Status::VOUCHER_OPEN;
                    $existBonus->voucher_remaining_to_open = 0;
                } else {
                    $existBonus->voucher_remaining_to_open = $remainingDate - $diffInDays;
                }
            }
        }

        // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
        $existBonus->increment('commission_balance', $commissionAmount);
        $existBonus->increment('voucher_balance', $voucher);
        $existBonus->increment('festival_bonus_balance', $festival);
        $existBonus->increment('saving', $saving);

        $total_bonus_this_time = $voucher + $festival + $saving;
        $user->increment('total_earning', $total_bonus_this_time);

        $existBonus->total_balance = $existBonus->commission_balance + $existBonus->voucher_balance + $existBonus->festival_bonus_balance + $existBonus->saving;
        $existBonus->save();

        BonusTransactionHistory::create([
            'user_id' => $user->id,
            'debit_user_id' => $debitUser->id,
            'is_leader' => $user->role == Status::LEADER ? 1 : 0,
            'is_top_leader' => $user->is_top_leader,
            'amount' => ($voucher + $festival + $saving),
            'charge' => 0,
            'trx_type' => '+',
            'trx' => getTrx(),
            'customers_voucher' => $voucher,
            'customers_festival' => $festival,
            'customers_saving' => $saving,
            'post_bonus_balance' => CustomerBonus::where('user_id', $user->id)->value('voucher_balance') + CustomerBonus::where('user_id', $user->id)->value('festival_bonus_balance') + CustomerBonus::where('user_id', $user->id)->value('saving'),
            'details' => 'Commission from package activation vouchers, festival & saving',
            'remark' => 'commission_from_package_activation_voucher_festival_saving',
        ]);
    }

    protected function updateLeaderBonus(User $user, AdvertisementPackage $package, PackageActivationCommission $packageCommissions, $bonus, $vehicleLease, $petrol, $totalLevelsToHead, $isProgressCompleted, $totalUsers, $totalLevels, $debitUser)
    {
        // return 1;
        $existLeaderBonus = LeaderBonus::firstOrCreate(['user_id' => $user->id], [
            'pkg_id' => $package->id,
            'pkg_activation_comm_id' => $packageCommissions->id,
            'status' => Status::ENABLE,
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'bonus' => 0,
            'leasing_amount' => 0,
            'petrol_allowance' => 0,
            'current_referral_count' => 0,
            'total_levels' => 0,
            'total_users' => 0,
            'is_progress_completed' => $isProgressCompleted,
            'joined_at' => $user->created_at,
            'total_balance' => 0,
        ]);


        // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
        $existLeaderBonus->increment('bonus', $bonus);
        $existLeaderBonus->increment('leasing_amount', $vehicleLease);
        $existLeaderBonus->increment('petrol_allowance', $petrol);

        $total_bonus_this_time = $bonus + $vehicleLease + $petrol;
        $user->increment('total_earning', $total_bonus_this_time);

        $existLeaderBonus->current_referral_count = $totalLevelsToHead;
        $existLeaderBonus->total_users = $totalUsers;
        $existLeaderBonus->total_levels = $totalLevels;
        $existLeaderBonus->is_progress_completed = $isProgressCompleted;
        $existLeaderBonus->total_balance = $existLeaderBonus->bonus + $existLeaderBonus->leasing_amount + $existLeaderBonus->petrol_allowance;
        $existLeaderBonus->save();

        BonusTransactionHistory::create([
            'user_id' => $user->id,
            'debit_user_id' => $debitUser->id,
            'is_leader' => 1,
            'is_top_leader' => $user->is_top_leader,
            'amount' => ($bonus + $vehicleLease + $petrol),
            'charge' => 0,
            'trx_type' => '+',
            'trx' => getTrx(),
            'leader_bonus' => $bonus,
            'leader_vehicle_lease' => $vehicleLease,
            'leader_petrol' => $petrol,
            'post_bonus_balance' => LeaderBonus::where('user_id', $user->id)->value('bonus') + LeaderBonus::where('user_id', $user->id)->value('leasing_amount') + LeaderBonus::where('user_id', $user->id)->value('petrol_allowance'),
            'details' => 'Bonus from Package Activation to Leader',
            'remark' => 'bonus_from_package_activation_to_leader',
        ]);
    }

    protected function updateTopLeaderBonus(User $user, AdvertisementPackage $package, PackageActivationCommission $packageCommissions, $car, $house, $expenses, $debitUser)
    {

        $existTopLeader = TopLeader::firstOrCreate(['user_id' => $user->id], [
            'pkg_id' => $package->id,
            'pkg_activation_comm_id' => $packageCommissions->id,
            'leader_id' => $user->id,
            'for_car' => 0,
            'for_house' => 0,
            'for_expenses' => 0,
            'total_balance' => 0,
        ]);

        // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
        $existTopLeader->increment('for_car', $car);
        $existTopLeader->increment('for_house', $house);
        $existTopLeader->increment('for_expenses', $expenses);

        $total_bonus_this_time = $car + $house + $expenses;
        $user->increment('total_earning', $total_bonus_this_time);

        $existTopLeader->total_balance = $existTopLeader->for_car + $existTopLeader->for_house + $existTopLeader->for_expenses;
        $existTopLeader->save();

        BonusTransactionHistory::create([
            'user_id' => $user->id,
            'debit_user_id' => $debitUser->id,
            'is_leader' => 1,
            'is_top_leader' => 1,
            'amount' => ($car + $house + $expenses),
            'charge' => 0,
            'trx_type' => '+',
            'trx' => getTrx(),
            'top_leader_car' => $car,
            'top_leader_house' => $house,
            'top_leader_expenses' => $expenses,
            'post_bonus_balance' => TopLeader::where('user_id', $user->id)->value('for_car') + TopLeader::where('user_id', $user->id)->value('for_house') + TopLeader::where('user_id', $user->id)->value('for_expenses'),
            'details' => 'Bonus from Package Activation to Top Leader',
            'remark' => 'bonus_from_package_activation_to_top_leader',
        ]);
    }

    // protected function updateReferralIncompleteTopLeaderBonus(User $user, AdvertisementPackage $package, PackageActivationCommission $packageCommissions, $car, $house, $expenses, $debitUser)
    // {
    //     // if top leader is not complete with 12 referral, received only expense bonus
    //     $leaderBonus = LeaderBonus::where('user_id', $user->id)->firstOrFail();

    //     $existTopLeader = TopLeader::firstOrCreate(['user_id' => $user->id], [
    //         'pkg_id' => $package->id,
    //         'pkg_activation_comm_id' => $packageCommissions->id,
    //         'leader_id' => $leaderBonus->id,
    //         'for_car' => 0,
    //         'for_house' => 0,
    //         'for_expenses' => 0,
    //         'total_balance' => 0,
    //     ]);

    //     // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
    //     $existTopLeader->increment('for_expenses', $expenses);
    //     $user->increment('total_earning', $expenses);

    //     $existTopLeader->total_balance = $existTopLeader->for_expenses;
    //     $existTopLeader->save();

    //     BonusTransactionHistory::create([
    //         'user_id' => $user->id,
    //         'debit_user_id' => $debitUser->id,
    //         'is_leader' => 1,
    //         'is_top_leader' => 1,
    //         'amount' => $expenses,
    //         'charge' => 0,
    //         'trx_type' => '+',
    //         'trx' => getTrx(),
    //         'top_leader_car' => 0,
    //         'top_leader_house' => 0,
    //         'top_leader_expenses' => $expenses,
    //         'post_bonus_balance' => TopLeader::where('user_id', $user->id)->value('for_expenses'),
    //         'details' => 'Bonus from Package Activation to Incomplete Referral Top Leader (expense only)',
    //         'remark' => 'bonus_from_package_activation_to_incomplete_referral_top_leader',
    //     ]);

    //     // car and house balance goes to the company
    //     $companyAccount = User::where('username', 'luxceylone')->firstOrFail();
    //     $companyAccount->increment('balance', $car + $house);
    //     Transaction::create([
    //         'user_id' => $companyAccount->id,
    //         'debit_user_id' => $debitUser->id,
    //         'amount' => $car + $house,
    //         'post_balance' => $companyAccount->balance,
    //         'charge' => 0,
    //         'trx_type' => '+',
    //         'details' => 'Unclaimed Top Leader Bonus (Incomplete Referrals car + house) From Package Activation To Company',
    //         'trx' => getTrx(),
    //         'remark' => 'unclaimed_top_leader_bonus_incomplete_referrals_from_package_activation_to_company',
    //     ]);

    //     BonusTransactionHistory::create([
    //         'user_id' => $companyAccount->id,
    //         'debit_user_id' => $debitUser->id,
    //         'is_leader' => 0,
    //         'is_top_leader' => 0,
    //         'amount' => $car + $house,
    //         'charge' => 0,
    //         'trx_type' => '+',
    //         'trx' => getTrx(),
    //         'top_leader_expenses' => $car + $house,
    //         'post_bonus_balance' => $companyAccount->balance,
    //         'details' => 'Bonus from Package Activation to Incomplete Referral Top Leader (car + house) To Company',
    //         'remark' => 'bonus_from_package_activation_to_incomplete_referral_top_leader_to_company',
    //     ]);
    // }

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
    // end of package activation & cash distribution

    public function deactivateExpiredEmployeePackages()
    {
        $currentDate = now();

        // Find all expired packages
        $expiredPackages = EmployeePackageActivationHistory::where('expiry_date', '<', $currentDate)
            ->where('activation_expired', Status::DISABLE)
            ->get();

        foreach ($expiredPackages as $package) {
            // Mark the package as expired
            $package->activation_expired = Status::ENABLE;
            $package->save();

            // Deactivate the user's package if they don't have another active one
            $user = $package->user;
            $hasActivePackage = EmployeePackageActivationHistory::where('user_id', $user->id)
                ->where('expiry_date', '>', $currentDate)
                ->where('activation_expired', Status::DISABLE)
                ->exists();

            if (!$hasActivePackage) {
                $user->employee_package_activated = Status::DISABLE;
                $user->save();

                // Notify user about expiration
                notify($user, 'EMPLOYEE_PACKAGE_EXPIRED', [
                    'username' => $user->username,
                    'expiry_date' => $package->expiry_date->format('d M, Y')
                ]);
            }
        }
    }

    // start of PO cash distribution
    public function purchaseOrder(Request $request, $order_id)
    {

        $useBonus = false;
        if (isset($request->use_bonus) && $request->use_bonus == 'on') {
            $useBonus = true;
        }

        $order = Order::with('orderItems.product.productPurchaseCommission')->findOrFail($order_id);
        $user = auth()->user();

        $productDeliveryChargers = getValue('PRODUCT_DELIVERY_CHARGERS');

        if ($request->delivery_method == 'door_step') {
            $order->net_total = $order->net_total + $productDeliveryChargers;
        } else {
            $productDeliveryChargers = 0;
        }

        $walletBalance = $user->balance ?? 0;
        $orderTotal = $order->net_total;

        if ($useBonus) {


            $currentMonth = now()->month;
            $nextMonth = now()->addMonth()->month;

            // Bonus balances
            $bonus = $user?->customerBonuses;
            // $festivalBonusBalance = $bonus?->festival_bonus_balance ?? 0;
            $voucherBalance = $bonus?->voucher_balance ?? 0;
            $voucherOpen = $bonus?->is_voucher_open == Status::VOUCHER_OPEN;

            // Eligibility flags
            $fullyVoucher = $voucherMain = $mainOnly = false;
            $amountToProcess = $orderTotal;

            // pure voucher
            if ($voucherOpen && $voucherBalance >= $orderTotal) {
                $fullyVoucher = true;
                $amountToProcess = $orderTotal;
                // voucher + wallet
            } elseif ($voucherOpen && $voucherBalance > 0 && $voucherBalance + $walletBalance >= $orderTotal) {
                $voucherMain = true;
                $amountToProcess = $orderTotal;
            }
            // }
        }

        // Wallet only
        if ($walletBalance >= $orderTotal) {
            $mainOnly = true;
            $amountToProcess = $orderTotal;
        } else {
            $mainOnly = false;
        }


        DB::beginTransaction();
        try {
            if ($useBonus) {

                if ($fullyVoucher) {
                    $user->customerBonuses()->decrement('voucher_balance', $amountToProcess);
                    $paymentMethod = 'PAY_BY_VOUCHER_BONUS';
                } elseif ($voucherMain) {
                    $usedVoucher = min($voucherBalance, $amountToProcess);
                    $remaining = $amountToProcess - $usedVoucher;
                    $user->customerBonuses()->decrement('voucher_balance', $usedVoucher);
                    $user->decrement('balance', $remaining);
                    $paymentMethod = 'PAY_BY_VOUCHER_BONUS_&_WALLET';
                }
            } elseif ($mainOnly) {
                $user->decrement('balance', $amountToProcess);
                $paymentMethod = 'PAY_BY_WALLET';
            } else {
                DB::rollBack();
                $notify[] = ['error', 'Insufficient balance. Please top-up your account!'];
                return to_route('user.deposit.index')->withNotify($notify);
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $order->net_total,
                'post_balance' => $user->balance ?? 0, // newly added
                'charge' => 0,
                'trx_type' => '-',
                'details' => "Order #{$order->code} Purchase Transaction",
                'trx' => getTrx(),
                'remark' => 'order_purchase'
            ]);

            $previous_purchase_count = PurchaseHistory::where('customer_id', $user->id)->count();
            $previous_purchase_count = ($previous_purchase_count) ? 1 : $previous_purchase_count + 1;

            PurchaseHistory::create([
                'user_id' => $user->id,
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'payment_method' => $paymentMethod,
                'total_purchase_did' => $previous_purchase_count,
                'payment_status' => Status::PAYMENT_SUCCESS,
                'total_amount' => $order->net_total,
                'amount_paid' => $order->net_total,
                'discount' => $order->discount,
                'currency' => 'LKR',
                'delivery_method' => $request->delivery_method,
                'delivery_charge' => $productDeliveryChargers
            ]);

            // $user->decrement('balance', $order->net_total);

            $order->update([
                'payment_method' => $paymentMethod,
                'status' => Status::ORDER_PROCESSING,
                'payment_status' => Status::PAYMENT_SUCCESS,
                'delivery_status' => Status::DELIVERY_PENDING,
                'customer_name' => $request->firstname . ' ' . $request->lastname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'alternative_mobile' => $request->alternative_mobile,
                'shipping_address' => $request->address,
                'zip' => $request->zip,
                'city' => $request->city,
                'country' => $request->country,
                'delivery_method' => $request->delivery_method,
                'delivery_charge' => $productDeliveryChargers
            ]);

            $total_profit = 0;
            $companyCommissionTotal = 0;
            $companyExpensesTotal = 0;
            $customersCommissionTotal = 0;
            $customersVoucherTotal = 0;
            $customersFestivalTotal = 0;
            $customersSavingTotal = 0;
            $leaderBonusTotal = 0;
            $leaderVehicleLeaseTotal = 0;
            $leaderPetrolTotal = 0;
            $topLeaderCarTotal = 0;
            $topLeaderHouseTotal = 0;
            $topLeaderExpensesTotal = 0;

            foreach ($order->orderItems as $order_item) {
                $product = $order_item->product;
                $commission = $product->productPurchaseCommission;

                if (!$product || !$commission) {
                    continue;
                }

                if ($product->quantity < $order_item->quantity) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                $quantity = $order_item->quantity;
                $total_profit += $product->profit * $quantity;
                $companyCommissionTotal += $commission->company_commission * $quantity;
                $companyExpensesTotal += $commission->company_expenses * $quantity;
                $customersCommissionTotal += $commission->customers_commission * $quantity;
                $customersVoucherTotal += ($commission->customers_voucher / self::MAX_REFERRAL_LEVELS) * $quantity;
                $customersFestivalTotal += ($commission->customers_festival / self::MAX_REFERRAL_LEVELS) * $quantity;
                $customersSavingTotal += ($commission->customers_saving / self::MAX_REFERRAL_LEVELS) * $quantity;
                $leaderBonusTotal += $commission->leader_bonus * $quantity;
                $leaderVehicleLeaseTotal += $commission->leader_vehicle_lease * $quantity;
                $leaderPetrolTotal += $commission->leader_petrol * $quantity;
                $topLeaderCarTotal += $commission->top_leader_car * $quantity;
                $topLeaderHouseTotal += $commission->top_leader_house * $quantity;
                $topLeaderExpensesTotal += $commission->top_leader_expenses * $quantity;

                $product->decrement('quantity', $quantity);
            }

            // Commission Distribution
            $companyAccount = User::where('username', 'luxceylone')->firstOrFail();
            if ($companyCommissionTotal > 0) {
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $companyCommissionTotal,
                    'trx_type' => '+',
                    'remark' => 'product_purchase_commission_to_company',
                    'details' => 'Product Purchase Commission To Company',
                    'trx' => getTrx(),
                    'post_balance' => $companyAccount->balance + $companyCommissionTotal,
                ]);
                $companyAccount->increment('balance', $companyCommissionTotal);
            }

            // $companySaving = CompanySaving::firstOrCreate(['id' => 1], ['balance' => 0]);
            // $companySaving = CompanySaving::where('id', 1)->first();
            $companySaving = CompanySaving::first();
            if (!$companySaving) {
                $companySaving = CompanySaving::create(['id' => 1, 'balance' => 0]);
            }

            if ($companyExpensesTotal > 0) {
                $companySaving->increment('balance', $companyExpensesTotal);
                CompanyExpensesSavingHistory::create([
                    'company_id' => $companyAccount->id,
                    'user_id' => $user->id,
                    'remark' => 'product_purchase_company_saving',
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => 'Product Purchase Company Saving',
                    'amount' => $companyExpensesTotal,
                    'post_saving_balance' => $companySaving->balance,
                ]);
            }

            $referrer = User::find($user->referred_user_id);
            $referralLevel = 1;
            $distributedCustomerCommission = 0;
            $distributedVoucher = 0;
            $distributedFestival = 0;
            $distributedSaving = 0;
            $visitedReferrers = [];

            while ($referrer && $referralLevel <= self::MAX_REFERRAL_LEVELS && !in_array($referrer->id, $visitedReferrers) && $referrer->id !== $companyAccount->id) {
                $visitedReferrers[] = $referrer->id;

                if ($customersCommissionTotal > 0) {
                    // if ($referrer->employee_package_activated == Status::PACKAGE_ACTIVE) { //check if pkg activation directly go to account
                    $singleCustomerCommissionAmount = $customersCommissionTotal / self::MAX_REFERRAL_LEVELS;
                    Transaction::create([
                        'user_id' => $referrer->id,
                        'debit_user_id' => $user->id,
                        'amount' => $singleCustomerCommissionAmount,
                        'trx_type' => '+',
                        'remark' => "product_purchase_referral_commission",
                        'details' => "Product Purchase Referral Commission",
                        'trx' => getTrx(),
                        'post_balance' => $referrer->balance + $singleCustomerCommissionAmount,
                    ]);
                    $referrer->increment('balance', $singleCustomerCommissionAmount);
                    $referrer->increment('total_earning', $singleCustomerCommissionAmount);
                    $distributedCustomerCommission += $singleCustomerCommissionAmount;
                    // }
                }

                $voucherAmount = $customersVoucherTotal;
                $festivalAmount = $customersFestivalTotal;
                $savingAmount = $customersSavingTotal;

                // need to check if $commissionAmount 0
                $this->updateCustomerBonusForPurchase($referrer, $customersCommissionTotal, $voucherAmount, $festivalAmount, $savingAmount, $user);
                $distributedVoucher += $voucherAmount;
                $distributedFestival += $festivalAmount;
                $distributedSaving += $savingAmount;

                $referrer = User::find($referrer->referred_user_id);
                $referralLevel++;
            }

            // Handle undistributed customer commissions/bonuses
            $remainingCustomerCommission = $customersCommissionTotal - $distributedCustomerCommission;
            $remainingVoucher = ($customersVoucherTotal * self::MAX_REFERRAL_LEVELS) - $distributedVoucher;
            $remainingFestival = ($customersFestivalTotal * self::MAX_REFERRAL_LEVELS) - $distributedFestival;
            $remainingSaving = ($customersSavingTotal * self::MAX_REFERRAL_LEVELS) - $distributedSaving;
            $totalRemainingCustomerBonus = $remainingVoucher + $remainingFestival + $remainingSaving;

            if ($remainingCustomerCommission > 0) {
                $companyAccount->increment('balance', $remainingCustomerCommission);
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $remainingCustomerCommission,
                    'trx_type' => '+',
                    'remark' => 'undistributed_product_purchase_commission_to_company',
                    'details' => 'Undistributed Product Purchase Commission To Company',
                    'trx' => getTrx(),
                    'post_balance' => $companyAccount->balance,
                ]);
            }

            if ($totalRemainingCustomerBonus > 0) {

                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $totalRemainingCustomerBonus,
                    'trx_type' => '+',
                    'remark' => 'undistributed_customer_bonus_product_purchase_to_company',
                    'details' => 'Undistributed Customer Bonus from Product Purchase To Company',
                    'trx' => getTrx(),
                    'post_balance' => $companyAccount->balance + $totalRemainingCustomerBonus,
                ]);

                BonusTransactionHistory::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'is_leader' => 0,
                    'is_top_leader' => 0,
                    'amount' => $totalRemainingCustomerBonus,
                    'charge' => 0,
                    'trx_type' => '+',
                    'trx' => getTrx(),
                    'customers_voucher' => $remainingVoucher,
                    'customers_festival' => $remainingFestival,
                    'customers_saving' => $remainingSaving,
                    'post_bonus_balance' => $companyAccount->balance + $totalRemainingCustomerBonus,
                    'details' => 'Undistributed Customer Bonus from Product Purchase To Company',
                    'remark' => 'undistributed_customer_bonus_product_purchase_to_company',
                ]);
                $companyAccount->increment('balance', $totalRemainingCustomerBonus);
            }

            // Leader Bonus
            $headUser = $user;
            while ($headUser = User::find($headUser->referred_user_id)) {
                if ($headUser->role == Status::LEADER && $headUser->is_top_leader != Status::TOP_LEADER && $leaderBonusTotal > 0) {
                    $this->updateLeaderBonusForPurchase($headUser, $leaderBonusTotal, $leaderVehicleLeaseTotal, $leaderPetrolTotal, $user);
                    break;
                }
            }
            if (!$headUser && $leaderBonusTotal > 0) {
                $companyAccount->increment('balance', $leaderBonusTotal + $leaderVehicleLeaseTotal + $leaderPetrolTotal);

                // transaction history
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $leaderBonusTotal + $leaderVehicleLeaseTotal + $leaderPetrolTotal,
                    'trx_type' => '+',
                    'remark' => 'unclaimed_leader_bonus_product_purchase',
                    'details' => 'Unclaimed Leader Bonus from Product Purchase',
                    'trx' => getTrx(),
                    'post_balance' => $companyAccount->balance,
                ]);

                // bonus transaction history from leaders to company
                BonusTransactionHistory::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'is_leader' => 0,
                    'is_top_leader' => 0,
                    'amount' => $leaderBonusTotal + $leaderVehicleLeaseTotal + $leaderPetrolTotal,
                    'charge' => 0,
                    'trx_type' => '+',
                    'trx' => getTrx(),
                    'leader_bonus' => $leaderBonusTotal,
                    'leader_vehicle_lease' => $leaderVehicleLeaseTotal,
                    'leader_petrol' => $leaderPetrolTotal,
                    'post_bonus_balance' => $companyAccount->balance,
                    'details' => 'Unclaimed Leader Bonus from Product Purchase To Company',
                    'remark' => 'unclaimed_leader_bonus_product_purchase_to_company',
                ]);
            }

            // Top Leader Bonus
            $topHeadUser = $user;
            $treeLevelCount = 0;

            while ($topHeadUser->referred_user_id) {
                $topHeadUser = User::find($topHeadUser->referred_user_id);
                if (!$topHeadUser)
                    break;
                if ($topHeadUser->role == Status::LEADER && $topHeadUser->is_top_leader == Status::TOP_LEADER) {
                    $treeLevelCount++;
                    break;
                }
                $treeLevelCount++;
            }

            $referralStats = $this->getReferralTreeStats($topHeadUser);

            $totalUsers = $referralStats['total_users'];
            // while ($topHeadUser = User::find($topHeadUser->referred_user_id)) {
            if ($topHeadUser->is_top_leader == Status::TOP_LEADER && $topHeadUser->role == Status::LEADER) {
                $this->updateTopLeaderBonusForPurchase($topHeadUser, $topLeaderCarTotal, $topLeaderHouseTotal, $topLeaderExpensesTotal, $user);
            } else {
                $companyAccount->increment('balance', $topLeaderCarTotal + $topLeaderHouseTotal + $topLeaderExpensesTotal);

                // bonus transaction history from top leaders to company
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $topLeaderCarTotal + $topLeaderHouseTotal + $topLeaderExpensesTotal,
                    'trx_type' => '+',
                    'remark' => 'unclaimed_top_leader_bonus_product_purchase',
                    'details' => 'Unclaimed Top Leader Bonus from Product Purchase',
                    'trx' => getTrx(),
                    'post_balance' => $companyAccount->balance,
                ]);

                // bonus transaction history from top leaders to company
                BonusTransactionHistory::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'is_leader' => 0,
                    'is_top_leader' => 0,
                    'amount' => $topLeaderCarTotal + $topLeaderHouseTotal + $topLeaderExpensesTotal,
                    'charge' => 0,
                    'trx_type' => '+',
                    'trx' => getTrx(),
                    'top_leader_car' => $topLeaderCarTotal,
                    'top_leader_house' => $topLeaderHouseTotal,
                    'top_leader_expenses' => $topLeaderExpensesTotal,
                    'post_bonus_balance' => $companyAccount->balance,
                    'details' => 'Unclaimed Top Leader Bonus from Product Purchase To Company',
                    'remark' => 'unclaimed_top_leader_bonus_product_purchase_to_company',
                ]);
            }

            CartItem::where('customer_id', $user->id)->delete();

            DB::commit();

            updateUserRankRequirements();

            updateUserRanks();

            $notify[] = ['success', 'Order purchased successfully'];
            return redirect()->route('user.product.index')->withNotify($notify);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            // Log::error('Order Purchase Transaction failed: ' . $e->getMessage());
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return redirect()->route('user.product.index')->withNotify($notify);
        }
    }

    protected function updateCustomerBonusForPurchase(User $user, $commission, $voucher, $festival, $saving, $debitUser)
    {
        $singleCustomerCommissionAmount = $commission / self::MAX_REFERRAL_LEVELS;

        $existBonus = CustomerBonus::firstOrCreate(['user_id' => $user->id], [
            'pkg_id' => null,
            'pkg_activation_comm_id' => null,
            'status' => Status::ENABLE,
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'commission_balance' => 0,
            'voucher_balance' => 0,
            'festival_bonus_balance' => 0,
            'saving' => 0,
            'joined_at' => $user->created_at,
            'total_balance' => 0,
        ]);

        // calculate voucher dates
        if ($existBonus->is_voucher_open != Status::VOUCHER_OPEN) {

            $now = Carbon::now();
            $remainingDate = getValue('VOUCHER_REMAINING_DATE');
            $oldestRecord = EmployeePackageActivationHistory::where('user_id', $user->id)
                ->orderBy('created_at')
                ->first();

            if ($oldestRecord) {
                $diffInDays = Carbon::parse($oldestRecord->created_at)->diffInDays($now);

                if ($diffInDays > $remainingDate) {
                    $existBonus->is_voucher_open = Status::VOUCHER_OPEN;
                    $existBonus->voucher_remaining_to_open = 0;
                } else {
                    $existBonus->voucher_remaining_to_open = $remainingDate - $diffInDays;
                }
            }
        }

        //check if pkg activation directly go to main bonus else goes to the temporary section
        // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
        $existBonus->increment('commission_balance', $singleCustomerCommissionAmount); //newly added need to check
        $existBonus->increment('voucher_balance', $voucher);
        $existBonus->increment('festival_bonus_balance', $festival);
        $existBonus->increment('saving', $saving);

        $total_earn_this_time = $voucher + $festival + $saving;
        $user->increment('total_earning', $total_earn_this_time);


        $existBonus->total_balance = $existBonus->commission_balance + $existBonus->voucher_balance + $existBonus->festival_bonus_balance + $existBonus->saving;
        $existBonus->save();

        // how ever it print on bonus transaction history
        BonusTransactionHistory::create([
            'user_id' => $user->id,
            'debit_user_id' => $debitUser->id,
            'is_leader' => $user->role == Status::LEADER ? 1 : 0,
            'is_top_leader' => $user->is_top_leader,
            'amount' => ($voucher + $festival + $saving),
            'charge' => 0,
            'trx_type' => '+',
            'trx' => getTrx(),
            'customers_voucher' => $voucher,
            'customers_festival' => $festival,
            'customers_saving' => $saving,
            'post_bonus_balance' => $existBonus->voucher_balance + $existBonus->festival_bonus_balance + $existBonus->saving,
            'details' => 'Bonus from product purchase',
            'remark' => 'bonus_from_product_purchase',
        ]);
    }

    protected function updateLeaderBonusForPurchase(User $user, $bonus, $vehicleLease, $petrol, $debitUser)
    {

        // update balance with leader bonus table
        $existLeaderBonus = LeaderBonus::firstOrCreate(['user_id' => $user->id], [
            'pkg_id' => null,
            'pkg_activation_comm_id' => null,
            'status' => Status::ENABLE,
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'bonus' => 0,
            'leasing_amount' => 0,
            'petrol_allowance' => 0,
            'current_referral_count' => 0,
            'total_levels' => 0,
            'total_users' => 0,
            'is_progress_completed' => false,
            'joined_at' => $user->created_at,
            'total_balance' => 0,
        ]);

        // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
        $existLeaderBonus->increment('bonus', $bonus);
        $existLeaderBonus->increment('leasing_amount', $vehicleLease);
        $existLeaderBonus->increment('petrol_allowance', $petrol);

        $total_earn_this_time = $bonus + $vehicleLease + $petrol;
        $user->increment('total_earning', $total_earn_this_time);


        $existLeaderBonus->total_balance = $existLeaderBonus->bonus + $existLeaderBonus->leasing_amount + $existLeaderBonus->petrol_allowance;
        $existLeaderBonus->save();

        // update bonus history log
        BonusTransactionHistory::create([
            'user_id' => $user->id,
            'is_leader' => 1,
            'debit_user_id' => $debitUser->id,
            'is_top_leader' => $user->is_top_leader,
            'amount' => ($bonus + $vehicleLease + $petrol),
            'charge' => 0,
            'trx_type' => '+',
            'trx' => getTrx(),
            'leader_bonus' => $bonus,
            'leader_vehicle_lease' => $vehicleLease,
            'leader_petrol' => $petrol,
            'post_bonus_balance' => $existLeaderBonus->bonus + $existLeaderBonus->leasing_amount + $existLeaderBonus->petrol_allowance,
            'details' => 'Bonus from product purchase to leader',
            'remark' => 'bonus_from_product_purchase_to_leader',
        ]);
    }

    protected function updateTopLeaderBonusForPurchase(User $user, $car, $house, $expenses, $debitUser)
    {

        $existTopLeader = TopLeader::firstOrCreate(['user_id' => $user->id], [
            'pkg_id' => null,
            'pkg_activation_comm_id' => null,
            'leader_id' => $user->id,
            'for_car' => 0,
            'for_house' => 0,
            'for_expenses' => 0,
            'total_balance' => 0,
        ]);

        // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
        $existTopLeader->increment('for_car', $car);
        $existTopLeader->increment('for_house', $house);
        $existTopLeader->increment('for_expenses', $expenses);

        $total_earn_this_time = $car + $house + $expenses;
        $user->increment('total_earning', $total_earn_this_time);

        $existTopLeader->total_balance = $existTopLeader->for_car + $existTopLeader->for_house + $existTopLeader->for_expenses;
        $existTopLeader->save();

        BonusTransactionHistory::create([
            'user_id' => $user->id,
            'debit_user_id' => $debitUser->id,
            'is_leader' => 1,
            'is_top_leader' => 1,
            'amount' => ($car + $house + $expenses),
            'charge' => 0,
            'trx_type' => '+',
            'trx' => getTrx(),
            'top_leader_car' => $car,
            'top_leader_house' => $house,
            'top_leader_expenses' => $expenses,
            'post_bonus_balance' => $existTopLeader->for_car + $existTopLeader->for_house + $existTopLeader->for_expenses,
            'details' => 'Bonus from product purchase to top leader',
            'remark' => 'bonus_from_product_purchase_to_top_leader',
        ]);
    }

    // protected function updateIncompleteReferralTopLeaderBonusForPurchase(User $user, $car, $house, $expenses, $debitUser)
    // {
    //     // top leader bonuses
    //     // $leaderBonus = LeaderBonus::where('user_id', $user->id)->firstOrFail();

    //     $existTopLeader = TopLeader::firstOrCreate(['user_id' => $user->id], [
    //         'pkg_id' => null,
    //         'pkg_activation_comm_id' => null,
    //         'leader_id' => $leaderBonus->id,
    //         'for_car' => 0,
    //         'for_house' => 0,
    //         'for_expenses' => 0,
    //         'total_balance' => 0,
    //     ]);

    //     // if ($user->employee_package_activated == Status::PACKAGE_ACTIVE) {
    //     $existTopLeader->increment('for_expenses', $expenses);
    //     $existTopLeader->total_balance = $existTopLeader->for_expenses;

    //     $user->increment('total_earning', $expenses);

    //     $existTopLeader->save();

    //     BonusTransactionHistory::create([
    //         'user_id' => $user->id,
    //         'debit_user_id' => $debitUser->id,
    //         'is_leader' => 1,
    //         'is_top_leader' => 1,
    //         'amount' => $expenses,
    //         'charge' => 0,
    //         'trx_type' => '+',
    //         'trx' => getTrx(),
    //         'top_leader_car' => 0,
    //         'top_leader_house' => 0,
    //         'top_leader_expenses' => $expenses,
    //         'post_bonus_balance' => $existTopLeader->for_expenses,
    //         'details' => 'Bonus from product purchase to top leader (expense only)',
    //         'remark' => 'bonus_from_product_purchase_to_top_leader',
    //     ]);

    //     // balance send to company
    //     $companyAccount = User::where('username', 'luxceylone')->firstOrFail();
    //     $companyAccount->increment('balance', ($car + $house));
    //     Transaction::create([
    //         'user_id' => $companyAccount->id,
    //         'debit_user_id' => $debitUser->id,
    //         'amount' => $car + $house,
    //         'trx_type' => '+',
    //         'remark' => 'unclaimed_top_leader_bonus_product_purchase',
    //         'details' => 'Unclaimed Top Leader Bonus from Product Purchase (car + house)',
    //         'trx' => getTrx(),
    //         'post_balance' => $companyAccount->balance,
    //     ]);

    //     // bonus transaction history from top leaders to company
    //     BonusTransactionHistory::create([
    //         'user_id' => $companyAccount->id,
    //         'debit_user_id' => $debitUser->id,
    //         'is_leader' => 0,
    //         'is_top_leader' => 0,
    //         'amount' => $car + $house,
    //         'charge' => 0,
    //         'trx_type' => '+',
    //         'trx' => getTrx(),
    //         'top_leader_car' => $car,
    //         'top_leader_house' => $house,
    //         'post_bonus_balance' => $companyAccount->balance,
    //         'details' => 'Unclaimed Top Leader Bonus from Product Purchase (car + house) To Company',
    //         'remark' => 'unclaimed_top_leader_bonus_product_purchase_to_company',
    //     ]);
    // }

    private function getReferralLevelCount(User $user, $count = 0, $visited = [])
    {
        if ($user->referred_user_id && !in_array($user->id, $visited)) {
            $referrer = User::find($user->referred_user_id);
            if ($referrer) {
                $visited[] = $user->id;
                return $this->getReferralLevelCount($referrer, $count + 1, $visited);
            }
        }
        return $count;
    }

    // starting boost advertisement
    public function boostAd(int $ad_id, int $boost_option_id)
    {
        $user = auth()->user();

        $boost_package = AdvertisementBoostPackage::find($boost_option_id);
        $advertisement = Advertisement::where('user_id', $user->id)->where('id', $ad_id)->first();

        if (!$advertisement) {
            return back()->withNotify([['error', 'Advertisement not found.']]);
        }

        // if ($advertisement->is_boosted == Status::BOOST_STARTED) {
        //     return back()->withNotify([['error', 'This advertisement is still boosting.']]);
        // }

        $companyCommissionPercentage = getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_COMPANY');
        $refUserCommissionPercentage = getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_REFERRED_USER');
        $nonDirectUserCommissionPercentage = getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_NON_DIRECT_USERS');
        $noOfUsersEligibleForBoostingReferral = getValue('NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_AD_BOOST_COMMISSION');
        $afterDirectReferral = $noOfUsersEligibleForBoostingReferral - 1;
        $nonDirectOneUserCommission = ($boost_package->price * ($nonDirectUserCommissionPercentage / 100) / $afterDirectReferral);

        try {
            DB::beginTransaction();

            $package_activations = EmployeePackageActivationHistory::where('user_id', $user->id)->latest()->first();

            $canBoostWithPackage = $package_activations &&
                $package_activations->can_boost == Status::BOOST_PACKAGE_AVAILABLE &&
                $package_activations->remaining_boosted_ads > 0 &&
                $package_activations->boost_package_id == $boost_option_id;

            $canBoostWithBalance = $user->balance >= $boost_package->price;

            if (!$canBoostWithPackage && !$canBoostWithBalance) {
                $notify = [];

                if ($package_activations) {
                    if ($package_activations->boost_package_id == $boost_option_id && $package_activations->remaining_boosted_ads <= 0) {
                        $notify[] = ['error', 'Your package\'s remaining boost balance is over. Please recharge your wallet.'];
                    } elseif ($package_activations->boost_package_id != $boost_option_id || $package_activations->can_boost != Status::BOOST_PACKAGE_AVAILABLE) {
                        $notify[] = ['error', 'Your current package cannot use this boost option. Please recharge your wallet.'];
                    }
                } else {
                    $notify[] = ['error', 'Insufficient balance. Please activate a package or recharge your wallet.'];
                    return to_route('user.deposit.index')->withNotify($notify);
                }

                DB::rollBack();
                return back()->withNotify($notify);
            }

            if ($canBoostWithPackage) {
                AdvertisementBoostedHistory::create([
                    'status' => Status::BOOST_STARTED,
                    'user_id' => $user->id,
                    'advertisement_id' => $advertisement->id,
                    'user_package_id' => $package_activations->package_id,
                    'is_package_boost' => Status::PACKAGE_BOOST,
                    'boost_package_id' => $package_activations->boost_package_id,
                    'payment_option_id' => Status::PAY_BY_WALLET,
                    'is_free_advertisement' => $advertisement->is_free,
                    'price' => null,
                    'impression' => 0,
                    'clicks' => 0,
                    'boosted_date' => now(),
                    'expiry_date' => now()->addDays($boost_package->duration),
                    'remaining' => $boost_package->duration,
                ]);

                $package_activations->used_boosted_ads += 1;
                $package_activations->save();
            } else {
                // Deduct balance
                $user->decrement('balance', $boost_package->price);

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $boost_package->price,
                    'post_balance' => $user->balance,
                    'charge' => 0,
                    'trx_type' => '-',
                    'details' => "Boost Advertisement Transaction",
                    'trx' => getTrx(),
                    'remark' => 'ad_boost_purchase'
                ]);

                AdvertisementBoostedHistory::create([
                    'status' => Status::BOOST_STARTED,
                    'user_id' => $user->id,
                    'advertisement_id' => $advertisement->id,
                    'user_package_id' => null,
                    'is_package_boost' => Status::CASH_BOOST,
                    'boost_package_id' => $boost_package->id,
                    'payment_option_id' => Status::PAY_BY_WALLET,
                    'transaction_id' => $transaction->id,
                    'is_free_advertisement' => $advertisement->is_free,
                    'price' => $boost_package->price,
                    'impression' => 0,
                    'clicks' => 0,
                    'boosted_date' => now(),
                    'expiry_date' => now()->addDays($boost_package->duration),
                    'remaining' => $boost_package->duration,
                ]);

                // Company Commission
                $companyCommission = $boost_package->price * ($companyCommissionPercentage / 100);
                $companyAccount = User::where('username', 'luxceylone')->first();
                if ($companyAccount) {
                    $companyAccount->increment('balance', $companyCommission);
                    Transaction::create([
                        'user_id' => $companyAccount->id,
                        'debit_user_id' => $user->id,
                        'amount' => $companyCommission,
                        'trx_type' => '+',
                        'remark' => 'ad_boost_commission_for_company',
                        'details' => 'Ad Boost Commission For Company',
                        'trx' => getTrx(),
                        'post_balance' => $companyAccount->balance,
                    ]);
                }

                // Direct Referral
                $refUser = User::find($user->referred_user_id);
                if ($refUser && $refUser->id != $companyAccount?->id) {
                    $refUserCommission = $boost_package->price * ($refUserCommissionPercentage / 100);

                    $refUser->increment('balance', $refUserCommission);
                    $refUser->increment('total_earning', $refUserCommission);

                    Transaction::create([
                        'user_id' => $refUser->id,
                        'debit_user_id' => $user->id,
                        'amount' => $refUserCommission,
                        'trx_type' => '+',
                        'remark' => 'ad_boost_commission_for_referred_user',
                        'details' => 'Ad Boost Commission For Referred User',
                        'trx' => getTrx(),
                        'post_balance' => $refUser->balance,
                    ]);

                    // Non-direct referral (up to 11 levels, as the direct referral is level 0)
                    $visitedUsers = [$user->id, $refUser->id];
                    $counter = 0;
                    $ancestorUser = User::find($refUser->referred_user_id);

                    while ($ancestorUser && !in_array($ancestorUser->id, $visitedUsers) && $counter < $afterDirectReferral) {
                        $visitedUsers[] = $ancestorUser->id;

                        if ($ancestorUser->id != $companyAccount?->id) {
                            $ancestorUser->increment('balance', $nonDirectOneUserCommission);
                            $ancestorUser->increment('total_earning', $nonDirectOneUserCommission);
                            Transaction::create([
                                'user_id' => $ancestorUser->id,
                                'debit_user_id' => $user->id,
                                'amount' => $nonDirectOneUserCommission,
                                'trx_type' => '+',
                                'remark' => 'ad_boost_commission_for_non_direct_users',
                                'details' => 'Ad Boost Commission For Non Direct Users',
                                'trx' => getTrx(),
                                'post_balance' => $ancestorUser->balance,
                            ]);
                            $counter++;
                        }

                        $ancestorUser = User::find($ancestorUser->referred_user_id);
                    }

                    // Handle any remaining non-direct commission if levels are less than 11
                    if ($counter < $afterDirectReferral) {
                        $remainingLevels = $afterDirectReferral - $counter;
                        $incompleteReferralBalance = $nonDirectOneUserCommission * $remainingLevels;
                        if ($companyAccount) {
                            $companyAccount->increment('balance', $incompleteReferralBalance);
                            Transaction::create([
                                'user_id' => $companyAccount->id,
                                'debit_user_id' => $user->id,
                                'amount' => $incompleteReferralBalance,
                                'trx_type' => '+',
                                'remark' => 'ad_boost_commission_from_incomplete_referral_to_company_from_' . $remainingLevels . '_levels',
                                'details' => 'Ad Boost Commission From Incomplete Referral To Company (Remaining ' . $remainingLevels . ' Levels)',
                                'trx' => getTrx(),
                                'post_balance' => $companyAccount->balance,
                            ]);
                        }
                    }
                } else {
                    // If no direct referrer, the direct referral commission and all non-direct commissions go to the company
                    $totalReferralIncompleteBalance = ($boost_package->price * ($refUserCommissionPercentage / 100)) + ($nonDirectOneUserCommission * $afterDirectReferral);
                    if ($companyAccount) {
                        $companyAccount->increment('balance', $totalReferralIncompleteBalance);
                        Transaction::create([
                            'user_id' => $companyAccount->id,
                            'debit_user_id' => $user->id,
                            'amount' => $totalReferralIncompleteBalance,
                            'trx_type' => '+',
                            'remark' => 'ad_boost_commission_for_company_no_referral',
                            'details' => 'Ad Boost Commission For Company (No Referral)',
                            'trx' => getTrx(),
                            'post_balance' => $companyAccount->balance,
                        ]);
                    }
                }
            }

            $advertisement->is_boosted = Status::BOOST_STARTED;
            $advertisement->save();

            DB::commit();
            return back()->withNotify([['success', 'Advertisement boost initiated successfully.']]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Ad Boost Transaction failed: ' . $e->getMessage());
            return back()->withNotify([['error', 'Something went wrong. Please try again.']]);
        }
    }

    public function buyTicket(Request $request, $training_id)
    {
        $user = auth()->user();
        $training = Training::find($training_id);

        if (!$training) {
            $notify[] = ['error', 'Training not found.'];
            return response()->json([
                'status' => 'error',
                'message' => 'Training not found.'
            ], 500);
        }

        // Check if the user has already bought a ticket for this training
        $hasRecord = UserTraining::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->exists();

        if ($hasRecord) {
            $notify[] = ['error', 'You have already bought a ticket for this training.'];
            return response()->json([
                'status' => 'error',
                'message' => 'You have already bought a ticket for this training'
            ], 500);
        }

        // Check if user balance is sufficient
        if ($user->balance < $training->ticket_price) {
            $notify[] = ['error', 'Insufficient balance. Please recharge your wallet.'];
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient balance. Please recharge your wallet.'
            ], 500);
        }

        // Check if user total earning meets the threshold
        if ($user->total_earning < $training->min_income_threshold) {
            $notify[] = ['error', 'You do not meet the minimum income threshold to purchase this ticket.'];
            return response()->json([
                'status' => 'error',
                'message' => 'You do not meet the minimum income threshold to purchase this ticket'
            ], 500);
        }

        // Commission percentages and user levels
        $companyCommissionPercentage = getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_COMPANY');
        $refUserCommissionPercentage = getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_REFERRED_USER');
        $nonDirectUserCommissionPercentage = getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_NON_DIRECT_USERS');
        $noOfUsersEligibleForReferral = 4;
        $afterDirectReferral = $noOfUsersEligibleForReferral - 1;

        // Calculate commission for each level
        $nonDirectOneUserCommission = ($training->ticket_price * ($nonDirectUserCommissionPercentage / 100) / $afterDirectReferral);
        $directUserCommission = $training->ticket_price * ($refUserCommissionPercentage / 100);
        $companyCommission = $training->ticket_price * ($companyCommissionPercentage / 100);

        try {
            DB::beginTransaction();

            // 1. Deduct ticket price from user's balance
            $user->decrement('balance', $training->ticket_price);

            // 2. Create a transaction record for the purchase
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => $training->ticket_price,
                'post_balance' => $user->balance,
                'charge' => 0,
                'trx_type' => '-',
                'details' => "Purchase ticket for " . $training->name,
                'trx' => getTrx(),
                'remark' => 'training_ticket_purchase'
            ]);

            // 3. Create a UserTraining record
            UserTraining::create([
                'user_id' => $user->id,
                'training_id' => $training->id,
                'status' => Status::TRAINING_PENDING, // Or any initial status
            ]);

            // 4. Distribute commissions

            // Get the company account (assuming it's a user with a specific username like 'luxceylone')
            $companyAccount = User::where('username', 'luxceylone')->first();

            // 4.1 Company Commission
            if ($companyAccount) {
                $companyAccount->increment('balance', $companyCommission);
                Transaction::create([
                    'user_id' => $companyAccount->id,
                    'debit_user_id' => $user->id,
                    'amount' => $companyCommission,
                    'trx_type' => '+',
                    'remark' => 'training_commission_for_company',
                    'details' => 'Training Commission for Company from user ' . $user->username,
                    'trx' => getTrx(),
                    'post_balance' => $companyAccount->balance,
                ]);
            }

            // 4.2 Direct Referral Commission
            $refUser = User::find($user->referred_user_id);
            if ($refUser && $refUser->id != $companyAccount?->id) {
                $refUser->increment('balance', $directUserCommission);
                $refUser->increment('total_earning', $directUserCommission);
                Transaction::create([
                    'user_id' => $refUser->id,
                    'debit_user_id' => $user->id,
                    'amount' => $directUserCommission,
                    'trx_type' => '+',
                    'remark' => 'training_commission_for_referred_user',
                    'details' => 'Training Commission for Referred User from user ' . $user->username,
                    'trx' => getTrx(),
                    'post_balance' => $refUser->balance,
                ]);

                // 4.3 Non-direct referral commission (up the tree)
                $visitedUsers = [$user->id, $refUser->id];
                $counter = 0;
                $ancestorUser = User::find($refUser->referred_user_id);

                while ($ancestorUser && !in_array($ancestorUser->id, $visitedUsers) && $counter < $afterDirectReferral) {
                    $visitedUsers[] = $ancestorUser->id;

                    if ($ancestorUser->id != $companyAccount?->id) {
                        $ancestorUser->increment('balance', $nonDirectOneUserCommission);
                        $ancestorUser->increment('total_earning', $nonDirectOneUserCommission);
                        Transaction::create([
                            'user_id' => $ancestorUser->id,
                            'debit_user_id' => $user->id,
                            'amount' => $nonDirectOneUserCommission,
                            'trx_type' => '+',
                            'remark' => 'training_commission_for_non_direct_users',
                            'details' => 'Training Commission for Non-Direct Users from user ' . $user->username,
                            'trx' => getTrx(),
                            'post_balance' => $ancestorUser->balance,
                        ]);
                        $counter++;
                    }

                    $ancestorUser = User::find($ancestorUser->referred_user_id);
                }

                // 4.4 Handle any remaining non-direct commission if the tree is incomplete
                if ($counter < $afterDirectReferral) {
                    $remainingLevels = $afterDirectReferral - $counter;
                    $incompleteReferralBalance = $nonDirectOneUserCommission * $remainingLevels;
                    if ($companyAccount) {
                        $companyAccount->increment('balance', $incompleteReferralBalance);
                        Transaction::create([
                            'user_id' => $companyAccount->id,
                            'debit_user_id' => $user->id,
                            'amount' => $incompleteReferralBalance,
                            'trx_type' => '+',
                            'remark' => 'training_commission_from_incomplete_referral_to_company',
                            'details' => 'Training Commission From Incomplete Referral to Company (Remaining ' . $remainingLevels . ' Levels)',
                            'trx' => getTrx(),
                            'post_balance' => $companyAccount->balance,
                        ]);
                    }
                }
            } else {
                // If no direct referrer, distribute all referral commissions to the company
                $totalReferralIncompleteBalance = $directUserCommission + ($nonDirectOneUserCommission * $afterDirectReferral);
                if ($companyAccount) {
                    $companyAccount->increment('balance', $totalReferralIncompleteBalance);
                    Transaction::create([
                        'user_id' => $companyAccount->id,
                        'debit_user_id' => $user->id,
                        'amount' => $totalReferralIncompleteBalance,
                        'trx_type' => '+',
                        'remark' => 'training_commission_for_company_no_referral',
                        'details' => 'Training Commission For Company (No Referral)',
                        'trx' => getTrx(),
                        'post_balance' => $companyAccount->balance,
                    ]);
                }
            }

            DB::commit();
            $notify[] = ['success', 'Training ticket purchased successfully!'];
            // return back()->withNotify($notify);

            return response()->json([
                'status' => 'success',
                'message' => 'Training ticket purchased successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $notify[] = ['error', 'An error occurred during the transaction. Please try again.'];
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction. Please try again.'
            ], 500);
        }
    }
}
