<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartItemController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $cart_items = CartItem::where('customer_id', $user->id)->with('product')->orderBy('id', 'desc')->get();

        $order_history = Order::with('orderItems.product')->where('customer_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $pageTitle = 'Cart';
        return view('Template::cart.index', compact('pageTitle', 'cart_items', 'order_history'));
    }


    public function addToCart(Request $request, $product_id)
    {
        $user = auth()->user();
        $product = Product::findOrFail($product_id);
        $cart_item = CartItem::where('product_id', $product_id)->where('customer_id', $user->id)->first();

        if ($cart_item) {
            $new_quantity = $cart_item->quantity + 1;
            $total_discount = $product->discount * $new_quantity;
            $sub_total = $product->original_price * $new_quantity;
            $net_total = $product->selling_price * $new_quantity;
            $cart_item->update([
                'quantity' => $new_quantity,
                'discount' => $total_discount,
                'sub_total' => $sub_total,
                'net_total' => $net_total,
            ]);
            $message = 'Product quantity updated in cart.';
        } else {
            CartItem::create([
                'customer_id'    => $user->id,
                'customer_name'  => $user->firstname,
                'product_id'     => $product->id,
                'product_name'   => $product->name,
                'original_price'  => $product->original_price,
                'selling_price'  => $product->selling_price,
                'discount'       => $product->discount,
                'sub_total'      => $product->original_price,
                'net_total'      => $product->selling_price,
                'quantity'       => 1,
            ]);
            $message = 'Product added to cart.';
        }

        // Get the updated cart count
        $cartCount = CartItem::where('customer_id', $user->id)->sum('quantity');

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $cartCount
            ]);
        }

        return back()->withNotify([["success", $message]]);
    }


    // Also update the updateCart and removeCartItem methods to include similar AJAX handling

    public function getCartCount()
    {
        $user = auth()->user();
        $cartCount = CartItem::where('customer_id', $user->id)->sum('quantity');

        return response()->json([
            'cartCount' => $cartCount
        ]);
    }

    public function updateCart(Request $request)
    {
        $user = auth()->user();
        $product = Product::findOrFail($request->product_id);

        $cart_item = CartItem::where('product_id', $request->product_id)->first();

        $total_discount = $product->discount * $request->quantity;
        $sub_total = $product->original_price * $request->quantity;
        $net_total = $product->selling_price * $request->quantity;

        if ($cart_item) {
            $cart_item->update([
                'customer_id'    => $user->id,
                'product_id'     => $product->id,
                'selling_price'  => $product->selling_price,
                'discount'       => $total_discount,
                'sub_total'      => $sub_total,
                'net_total'      => $net_total,
                'quantity'       => $request->quantity,
            ]);

            $message = 'Cart Updated Successfully';

            return back()->withNotify([["success", $message]]);
        }
    }

    public function checkout()
    {
        $user = auth()->user();

        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        // Fetch cart items for the user
        $cart_items = CartItem::where('customer_id', $user->id)->get();

        if ($cart_items->isEmpty()) {
            return back()->withNotify([["success", 'Your cart is empty']]);
        }

        // Fetch previous pending orders
        $previous_pending_orders = Order::where('status', 'pending')->get();

        foreach ($previous_pending_orders as $previous_order) {
            $order_items = OrderItem::where('order_id', $previous_order->id)->get();

            // Check if cart item count matches the order item count
            if ($cart_items->count() == $order_items->count()) {
                // Check if products in both match
                $matched = $order_items->every(function ($order_item) use ($cart_items) {
                    return $cart_items->contains('product_id', $order_item->product_id, 'quantity', $order_item->quantity, 'created_at', $order_item->created_at);
                });

                if ($matched) {
                    return view('Template::cart.checkout', [
                        'pageTitle' => 'Checkout',
                        'order' => $previous_order,
                        'order_items' => $order_items,
                        'user' => $user,
                        'countries' => $countries,
                        'mobileCode' => $mobileCode,
                    ]);
                }
            }
        }

        // No matching order found, create a new order
        return $this->createNewOrder($user, $cart_items);
    }

    private function createNewOrder($user, $cart_items)
    {
        $user = auth()->user();
        $cart_items = CartItem::where('customer_id', $user->id)->get();

        foreach ($cart_items as $cart_item) {
            $product = Product::find($cart_item->product_id);
            if (!$product || $product->quantity < $cart_item->quantity) {
                return back()->withNotify([["error", "Insufficient stock for {$cart_item->product_name}"]]);
            }
        }

        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        try {
            // Calculate totals
            $discount = $cart_items->sum('discount');
            $sub_total = $cart_items->sum('sub_total');
            $net_total = $cart_items->sum('net_total');
            $quantity = $cart_items->sum('quantity');

            // Generate Order Code
            $last_order = Order::orderBy('id', 'desc')->first();
            $last_code = $last_order ? intval(substr($last_order->code, 3)) : 0;
            $new_code = str_pad($last_code + 1, 8, '0', STR_PAD_LEFT);
            $order_code = 'ODR' . $new_code;

            // DB::beginTransaction(); // Start transaction

            $order = Order::create([
                'code' => $order_code,
                'customer_id' => $user->id,
                'customer_name' => $user->firstname . ' ' . $user->lastname,
                'discount' => $discount,
                'sub_total' => $sub_total,
                'net_total' => $net_total,
                'quantity' => $quantity,
                'payment_method' => null,
                'status' => 'pending'
            ]);

            $order_items = [];

            foreach ($cart_items as $cart_item) {
                $product = Product::find($cart_item->product_id);
                if ($product && $product->quantity >= $cart_item->quantity) {
                    // $product->decrement('quantity', $cart_item->quantity); // Reduce stock

                    $order_items[] = [
                        'order_id' => $order->id,
                        'order_code' => $order->code,
                        'product_id' => $cart_item->product_id,
                        'customer_id' => $order->customer_id,
                        'customer_name' => $order->customer_name,
                        'product_name' => $cart_item->product_name,
                        'original_price' => $cart_item->original_price,
                        'selling_price' => $cart_item->selling_price,
                        'discount' => $cart_item->discount,
                        'sub_total' => $cart_item->sub_total,
                        'net_total' => $cart_item->net_total,
                        'quantity' => $cart_item->quantity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    return back()->withNotify([["error", "Insufficient stock for {$cart_item->product_name}"]]);
                }
            }

            OrderItem::insert($order_items);

            // DB::commit(); // Commit transaction
            $bonus = $user?->customerBonuses;
            // $festivalBonusBalance = $bonus?->festival_bonus_balance ?? 0;
            $currentMonth = now()->month;
            $nextMonth = now()->addMonth()->month;
            $voucherBalance = $bonus?->voucher_balance ?? 0;
            $voucherOpen = $bonus?->is_voucher_open == Status::VOUCHER_OPEN;
            $orderTotal = $order->net_total;
            $walletBalance = $user->balance ?? 0;

            // Decode KYC data
            // $kycData = $user->kyc_data;
            // $religion = collect($kycData)->firstWhere('name', 'religion')->value ?? null;
            

            // $bonusFrom = [
            //     'Sinhala' => getValue('SINHALISE_BONUS_FROM'),
            //     'Tamil' => getValue('TAMIL_BONUS_FROM'),
            //     'Muslims' => getValue('MUSLIMS_BONUS_FROM'),
            //     'Christian/Catholic' => getValue('CHRISTIAN_BONUS_FROM'),
            // ];

            // $bonusTo = [
            //     'Sinhala' => getValue('SINHALISE_BONUS_TO'),
            //     'Tamil' => getValue('TAMIL_BONUS_TO'),
            //     'Muslims' => getValue('MUSLIMS_BONUS_TO'),
            //     'Christian/Catholic' => getValue('CHRISTIAN_BONUS_TO'),
            // ];

            $fullyVoucher = $voucherMain = $mainOnly = false;

            // Festival (need kyc verifications)
            // if ($religion && isset($bonusFrom[$religion]) && $currentMonth == $bonusFrom[$religion] && $nextMonth == $bonusTo[$religion] && $user->kv == Status::KYC_VERIFIED) {
            //     // pure festival
            //     if ($festivalBonusBalance >= $orderTotal) {
            //         $fullyFestival = true;
            //     } elseif ($festivalBonusBalance > 0 && $voucherOpen && $voucherBalance > 0) {
            //         // try with voucher
            //         if ($festivalBonusBalance + $voucherBalance >= $orderTotal) {
            //             $withVoucher = true;
            //             // try with voucher + wallet
            //         } elseif ($festivalBonusBalance + $voucherBalance + $walletBalance >= $orderTotal) {
            //             $withVoucherMain = true;
            //         }
            //     } elseif ($festivalBonusBalance > 0 && ($festivalBonusBalance + $walletBalance) >= $orderTotal) {
            //         $withMain = true;
            //     }
            // }

            // Voucher only ( need complete remaining days )
            // if (!$fullyFestival && !$withVoucher && !$withVoucherMain) {
                // pure voucher
                if ($voucherOpen && $voucherBalance >= $orderTotal) {
                    $fullyVoucher = true;
                    // voucher + wallet
                } elseif ($voucherOpen && $voucherBalance > 0 && $voucherBalance + $walletBalance >= $orderTotal) {
                    $voucherMain = true;
                }
            // }

            // Wallet only
            if ($walletBalance >= $orderTotal) {
                $mainOnly = true;
            }

            $order_items = OrderItem::where('order_id', $order->id)->get();
            return view('Template::cart.checkout', [
                'pageTitle' => 'Checkout',
                'order' => $order,
                'user' => $user,
                'order_items' => $order_items,
                'countries' => $countries,
                'mobileCode' => $mobileCode,
                // 'fullyFestival' =>  $fullyFestival,
                // 'withVoucher' =>  $withVoucher,
                // 'withMain' => $withMain,
                'fullyVoucher' =>  $fullyVoucher,
                // 'withVoucherMain' =>  $withVoucherMain,
                'voucherMain' =>  $voucherMain,
                'mainOnly' =>  $mainOnly,
            ]);
        } catch (\Exception $e) {
            return $e;
            // DB::rollBack();
            return back()->withNotify([["error", 'Failed to process checkout']]);
        }
    }


    public function removeCartItem($product_id)
    {
        CartItem::where('product_id', $product_id)->delete();
        $message = 'Cart item successfully removed';
        return back()->withNotify([["success", $message]]);
    }
}
