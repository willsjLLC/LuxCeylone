<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function existOrderCheckout($order_id)
    {

        $user = auth()->user();

        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $order = Order::find($order_id);

        $order_items = OrderItem::where('order_id', $order_id)->orderBy('id', 'desc')->get();

        // DB::commit(); // Commit transaction
        $bonus = $user?->customerBonuses;
        $festivalBonusBalance = $bonus?->festival_bonus_balance ?? 0;
        $currentMonth = now()->month;
        $nextMonth = now()->addMonth()->month;
        $voucherBalance = $bonus?->voucher_balance ?? 0;
        $voucherOpen = $bonus?->is_voucher_open == Status::VOUCHER_OPEN;
        $orderTotal = $order->net_total;
        $walletBalance = $user->balance ?? 0;

        // Decode KYC data
        $kycData = $user->kyc_data;
        $religion = collect($kycData)->firstWhere('name', 'religion')->value ?? null;

        $bonusFrom = [
            'Sinhala' => getValue('SINHALISE_BONUS_FROM'),
            'Tamil' => getValue('TAMIL_BONUS_FROM'),
            'Muslims' => getValue('MUSLIMS_BONUS_FROM'),
            'Christian/Catholic' => getValue('CHRISTIAN_BONUS_FROM'),
        ];

        $bonusTo = [
            'Sinhala' => getValue('SINHALISE_BONUS_TO'),
            'Tamil' => getValue('TAMIL_BONUS_TO'),
            'Muslims' => getValue('MUSLIMS_BONUS_TO'),
            'Christian/Catholic' => getValue('CHRISTIAN_BONUS_TO'),
        ];

        $fullyFestival = $withVoucher = $withMain = $withVoucherMain = $fullyVoucher = $voucherMain = $mainOnly = false;

        // Festival (need kyc verifications)
        if ($religion && isset($bonusFrom[$religion]) && $currentMonth == $bonusFrom[$religion] && $nextMonth == $bonusTo[$religion] && $user->kv == Status::KYC_VERIFIED) {
            // pure festival
            if ($festivalBonusBalance >= $orderTotal) {
                $fullyFestival = true;
            } elseif ($festivalBonusBalance > 0 && $voucherOpen && $voucherBalance > 0) {
                // try with voucher
                if ($festivalBonusBalance + $voucherBalance >= $orderTotal) {
                    $withVoucher = true;
                    // try with voucher + wallet
                } elseif ($festivalBonusBalance + $voucherBalance + $walletBalance >= $orderTotal) {
                    $withVoucherMain = true;
                }
            } elseif ($festivalBonusBalance > 0 && ($festivalBonusBalance + $walletBalance) >= $orderTotal) {
                $withMain = true;
            }
        }

        // Voucher only ( need complete remaining days )
        if (!$fullyFestival && !$withVoucher && !$withVoucherMain) {
            // pure voucher
            if ($voucherOpen && $voucherBalance >= $orderTotal) {
                $fullyVoucher = true;
                // voucher + wallet
            } elseif ($voucherOpen && $voucherBalance > 0 && $voucherBalance + $walletBalance >= $orderTotal) {
                $voucherMain = true;
            }
        }

        // Wallet only
        if ($walletBalance >= $orderTotal) {
            $mainOnly = true;
        }

        return view('Template::cart.checkout', [
            'pageTitle' => 'Checkout',
            'order' => $order,
            'user' => $user,
            'order_items' => $order_items,
            'countries' => $countries,
            'mobileCode' => $mobileCode,
            'fullyFestival' =>  $fullyFestival,
            'withVoucher' =>  $withVoucher,
            'withMain' => $withMain,
            'fullyVoucher' =>  $fullyVoucher,
            'withVoucherMain' =>  $withVoucherMain,
            'voucherMain' =>  $voucherMain,
            'mainOnly' =>  $mainOnly,
        ]);
    }

    // public function updateDeliveryStatus(Request $request, $order_id)
    // {
    //     $admin = auth()->guard('admin')->user();

    //     if (!$admin || !$admin->can('orders.update')) {
    //         return response()->view('admin.errors.403', [], 403);
    //     }

    //     $order = Order::find($order_id);

    //     if (!$order) {
    //         return back()->withNotify([["error", "Order not found"]]);
    //     }

    //     $newDeliveryStatus = $request->delivery_status;
    //     $currentDeliveryStatus = $order->delivery_status;

    //     if ($newDeliveryStatus == $currentDeliveryStatus) {
    //         return back()->withNotify([["info", "Nothing Changed"]]);
    //     }

    //     $statusMap = [
    //         Status::DELIVERY_PENDING => Status::ORDER_PROCESSING,
    //         Status::DELIVERY_COMPLETE => Status::ORDER_COMPLETED,
    //         Status::DELIVERY_CANCELED => Status::ORDER_CANCELED,
    //     ];

    //     $messageMap = [
    //         Status::DELIVERY_PENDING => "Order Status Changed Successfully",
    //         Status::DELIVERY_COMPLETE => "Order is successfully completed",
    //         Status::DELIVERY_CANCELED => "Order Canceled Successfully",
    //     ];

    //     if (isset($statusMap[$newDeliveryStatus])) {
    //         $order->delivery_status = $newDeliveryStatus;
    //         $order->status = $statusMap[$newDeliveryStatus];
    //         $order->save();

    //         return back()->withNotify([["success", $messageMap[$newDeliveryStatus]]]);
    //     }

    //     return back()->withNotify([["error", "Invalid delivery status"]]);
    // }

    public function cancelExistOrder($order_id)
    {
        $user = auth()->user();

        Order::find($order_id)->delete();

        OrderItem::where('order_id', $order_id)->where('customer_id', $user->id)->delete();;

        return back()->withNotify([["success", "Order Deleted Successfully"]]);
    }

   
    public function deleteAllHistory()
    {
        $userId = auth()->id();
        
        // Delete only pending orders by user
        $orderIds = Order::where('customer_id', $userId)
                         ->where('status', Status::ORDER_PENDING)
                         ->pluck('id'); // get pending order IDs for the user
        
        if ($orderIds->isEmpty()) {
            return redirect()->back()->withNotify([["info", "No pending orders found to delete."]]);
        }
        
        // Delete all related order items
        OrderItem::whereIn('order_id', $orderIds)->delete();
        
        // Delete the orders themselves
        Order::whereIn('id', $orderIds)->delete();
        
        return redirect()->back()->withNotify([["success", "All Pending Order History Deleted Successfully"]]);
    }

   

//     public function showCart()
// {
//     $cart_items = CartItem::where('user_id', auth()->id())->get();
//     $order_history = Order::where('customer_id', auth()->id())->get();
    
//     // Check if user has pending orders
//     $hasPendingOrders = $this->hasPendingOrders();
//     $pendingOrdersCount = $this->getPendingOrdersCount();
    
//     return view('your.view.name', compact(
//         'cart_items', 
//         'order_history', 
//         'hasPendingOrders',
//         'pendingOrdersCount'
//     ));
// }
}
