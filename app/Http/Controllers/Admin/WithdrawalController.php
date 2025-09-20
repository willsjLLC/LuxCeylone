<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function pending($userId = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.pending')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Pending Withdrawals';
        $withdrawals = $this->withdrawalData('pending', userId: $userId);
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function approved($userId = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.approved')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Approved Withdrawals';
        $withdrawals = $this->withdrawalData('approved', userId: $userId);
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function rejected($userId = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.rejected')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Rejected Withdrawals';
        $withdrawals = $this->withdrawalData('rejected', userId: $userId);
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function all($userId = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.all')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'All Withdrawals';
        $withdrawalData = $this->withdrawalData($scope = null, $summary = true, userId: $userId);
        $withdrawals = $withdrawalData['data'];
        $summary = $withdrawalData['summary'];
        $successful = $summary['successful'];
        $pending = $summary['pending'];
        $rejected = $summary['rejected'];


        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'successful', 'pending', 'rejected'));
    }

    protected function withdrawalData($scope = null, $summary = false, $userId = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.view')) {
            return response()->view('admin.errors.403', [], 403);
        }


        if ($scope) {
            $withdrawals = Withdrawal::$scope();
        } else {
            $withdrawals = Withdrawal::where('status', '!=', Status::PAYMENT_INITIATE);
        }

        if ($userId) {
            $withdrawals = $withdrawals->where('user_id', $userId);
        }

        $withdrawals = $withdrawals->searchable(['trx', 'user:username'])->dateFilter();

        $request = request();
        if ($request->method) {
            $withdrawals = $withdrawals->where('method_id', $request->method);
        }
        if (!$summary) {
            return $withdrawals->with(['user', 'method'])->orderBy('id', 'desc')->paginate(getPaginate());
        } else {

            $successful = clone $withdrawals;
            $pending = clone $withdrawals;
            $rejected = clone $withdrawals;

            $successfulSummary = $successful->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
            $pendingSummary = $pending->where('status', Status::PAYMENT_PENDING)->sum('amount');
            $rejectedSummary = $rejected->where('status', Status::PAYMENT_REJECT)->sum('amount');


            return [
                'data' => $withdrawals->with(['user', 'method'])->orderBy('id', 'desc')->paginate(getPaginate()),
                'summary' => [
                    'successful' => $successfulSummary,
                    'pending' => $pendingSummary,
                    'rejected' => $rejectedSummary,
                ]
            ];
        }
    }

    public function details($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.detail')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $withdrawal = Withdrawal::where('id', $id)->where('status', '!=', Status::PAYMENT_INITIATE)->with(['user', 'method'])->firstOrFail();
        $pageTitle = $withdrawal->user->username . ' Withdraw Requested ' . showAmount($withdrawal->amount);
        $details = $withdrawal->withdraw_information ? json_encode($withdrawal->withdraw_information) : null;

        return view('admin.withdraw.detail', compact('pageTitle', 'withdrawal', 'details'));
    }

    public function approve(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->with('user')->firstOrFail();
        $withdraw->status = Status::PAYMENT_SUCCESS;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        notify($withdraw->user, 'WITHDRAW_APPROVE', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount' => showAmount($withdraw->amount, currencyFormat: false),
            'charge' => showAmount($withdraw->charge, currencyFormat: false),
            'rate' => showAmount($withdraw->rate, currencyFormat: false),
            'trx' => $withdraw->trx,
            'admin_details' => $request->details
        ]);

        $notify[] = ['success', 'Withdrawal approved successfully'];
        return to_route('admin.withdraw.data.pending')->withNotify($notify);
    }


    public function reject(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('withdrawals.update')) {
            return response()->view('admin.errors.403', [], 403);
        }
        
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->with('user')->firstOrFail();

        $withdraw->status = Status::PAYMENT_REJECT;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        $user = $withdraw->user;
        $user->balance += $withdraw->amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->remark = 'withdraw_reject';
        $transaction->details = 'Refunded for withdrawal rejection';
        $transaction->trx = $withdraw->trx;
        $transaction->save();

        notify($user, 'WITHDRAW_REJECT', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount' => showAmount($withdraw->amount, currencyFormat: false),
            'charge' => showAmount($withdraw->charge, currencyFormat: false),
            'rate' => showAmount($withdraw->rate, currencyFormat: false),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance, currencyFormat: false),
            'admin_details' => $request->details
        ]);

        $notify[] = ['success', 'Withdrawal rejected successfully'];
        return to_route('admin.withdraw.data.pending')->withNotify($notify);
    }
}
