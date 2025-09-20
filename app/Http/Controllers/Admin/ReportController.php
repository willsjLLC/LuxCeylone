<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeePackageActivationHistory;
use App\Models\NotificationLog;
use App\Models\ReferralLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use App\Models\AdvertisementView;
use App\Models\Advertisement;
use App\Models\AdvertisementBoostPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdvertisementBoostedHistory;
use App\Constants\Status;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function transaction(Request $request, $userId = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.transaction_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::searchable(['trx', 'user:username'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->with('user');
        if ($userId) {
            $transactions = $transactions->where('user_id', $userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.login_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.login_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.notification_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.email_details')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function employeePackageActivationHistories()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.employee_package_activation_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Employee Package Activation Histories';
        $logs = EmployeePackageActivationHistory::searchable(['user:username'])->filter(['payment_method', 'activation_expired'])->dateFilter()->orderBy('id', 'desc')->with('user', 'transaction')->paginate(getPaginate());
        $paymentMethods = EmployeePackageActivationHistory::distinct('payment_method')->orderBy('payment_method')->get('payment_method');
        return view('admin.reports.employee_package_activation_histories', compact('pageTitle', 'logs', 'paymentMethods'));
    }

    public function referralLogs()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.referral_logs')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Referral Logs';
        $logs = ReferralLog::searchable(['referred:username','referrer:username', 'referred:email', 'referrer:email'])
                        ->dateFilter()
                        ->orderBy('id', 'desc')
                        ->with('referrer', 'referred')
                        ->paginate(getPaginate());
        return view('admin.reports.referral_logs', compact('pageTitle', 'logs'));
    }

    public function advertisementBoostHistory(Request $request)
    {
        // $admin = auth()->guard('admin')->user();

        // if (!$admin || !$admin->can('reports.advertisement_boost_history')) {
        //     return response()->view('admin.errors.403', [], 403);
        // }

        $pageTitle = 'Advertisement Boost History';

        $statuses = \App\Models\AdvertisementBoostedHistory::distinct('status')->orderBy('status')->get('status');
        $paymentOptions = \App\Models\AdvertisementBoostedHistory::distinct('payment_option_id')->orderBy('payment_option_id')->get('payment_option_id');

        $boostHistories = \App\Models\AdvertisementBoostedHistory::searchable(['transaction_id', 'user:username'])
            ->filter(['status', 'payment_option_id', 'is_free_advertisement'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->with('user', 'advertisement')
            ->paginate(getPaginate());

        return view('admin.reports.advertisement_boost_history', compact('pageTitle', 'boostHistories', 'statuses', 'paymentOptions'));
    }

    public function leaderBonusHistory(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.leader_bonus_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Leader Bonus History';

        $bonuses = \App\Models\LeaderBonus::searchable(['user:username'])
            ->filter(['status'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate(getPaginate());

        return view('admin.reports.leader_bonus_history', compact('pageTitle', 'bonuses'));
    }

    public function customerBonusHistory(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.customer_bonus_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Customer Bonus History';

        $bonuses = \App\Models\CustomerBonus::searchable(['user:username'])
            ->filter(['status'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate(getPaginate());

        return view('admin.reports.customer_bonus_history', compact('pageTitle', 'bonuses'));
    }

    public function topLeaderBonusHistory(Request $request)
    {

        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin->can('reports.top_leader_bonus')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Top Leader Bonus History';
        $emptyMessage = 'No data found';

        $topLeaders = \App\Models\TopLeader::searchable(['user:username'])
            ->filter(['for_car', 'for_house', 'for_expenses'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate(getPaginate());

        return view('admin.reports.top_leader_bonus_history', compact('pageTitle', 'topLeaders', 'emptyMessage'));
    }

    public function companyExpensesSavingHistory(Request $request)
    {

        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin->can('reports.company_expenses_saving_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Company Expenses Saving History';

        $histories = \App\Models\CompanyExpensesSavingHistory::searchable(['user:username'])
            ->filter(['trx_type'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate(getPaginate());

        return view('admin.reports.company_expenses_saving_history', compact('pageTitle', 'histories'));
    }

    public function bonusTransactionHistory(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.bonus_transaction_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Bonus Transaction History';

        $bonuses = \App\Models\BonusTransactionHistory::searchable(['user:username'])
            ->filter(['trx_type'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate(getPaginate());

        return view('admin.reports.bonus_transaction_history', compact('pageTitle', 'bonuses'));
    }




    public function productPurchaseCommissionHistory(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('reports.product_purchase_commission_history')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Product Purchase Commission History';
        $emptyMessage = 'No commission records found';

        $commissions = \App\Models\ProductPurchaseCommission::query();

        if ($request->search) {
            $commissions = $commissions->whereHas('Product', function ($query) use ($request) {
                $query->where('name', 'like', "%$request->search%");
            });
        }

        if ($request->date) {
            $dates = explode(' - ', $request->date);
            $startDate = Carbon::parse($dates[0])->startOfDay();
            $endDate = Carbon::parse($dates[1])->endOfDay();
            $commissions = $commissions->whereBetween('created_at', [$startDate, $endDate]);
        }

        $commissions = $commissions->orderBy('id', 'desc')
            ->with('Product')
            ->paginate(getPaginate());

        return view('admin.reports.product_purchase_commission_history', compact('pageTitle', 'commissions', 'emptyMessage'));
    }


}
