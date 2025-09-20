<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function add(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|exists:users,username',
                'amount' => 'required|numeric|min:0.01',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'PRO account not found for this user.'], 422);
        }

        $proUser = User::where('username', $request->input('username'))->first();
        $amount = $request->input('amount');

        if (!$proUser) {
            return response()->json(['message' => 'PRO account not found for this user.'], 404);
        }

        try {
            DB::transaction(function () use ($proUser, $amount) {
                DB::table('users')
                    ->where('id', $proUser->id)
                    ->increment('balance', $amount);
            });

            $newBalance = DB::table('users')->where('id', $proUser->id)->value('balance');

            $updatedUser = User::find($proUser->id);

            Transaction::create(attributes: [
                'user_id' => $proUser->id,
                'amount' => $amount,
                'post_balance' => $updatedUser->balance,
                'charge' => 0,
                'trx_type' => '+',
                'details' => 'Transfer amount from Lite Account',
                'trx' => getTrx(),
                'remark' => 'transfer_amount_from_lite_account',
            ]);
            
            return response()->json([
                'message' => 'Balance added successfully.',
                'new_balance' => $newBalance
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update balance.'], 500);
        }
    }
}
