<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('orders.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All Orders";
        $orders = Order::searchable(['code'])->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.orders.index', compact('pageTitle', 'orders'));
    }

    public function view($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('orders.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Order Information";
        $order       = Order::where('id', $id)->with('customer')->first();
        $order_items       = OrderItem::where('order_id', $order->id)->with('product')->get();
        return view('admin.orders.view', compact('pageTitle', 'order', 'order_items'));
    }

    public function processingOrders()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('orders.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Processing Orders";
        $orders = Order::where('status', Status::ORDER_PROCESSING)->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.orders.processing', compact('pageTitle', 'orders'));
    }
    public function completedOrders()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('orders.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Completed Orders";
        $orders = Order::where('status', Status::ORDER_COMPLETED)->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.orders.completed', compact('pageTitle', 'orders'));
    }
    public function canceledOrders()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('orders.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Canceled Orders";
        $orders = Order::where('status', Status::ORDER_CANCELED)->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.orders.canceled', compact('pageTitle', 'orders'));
    }

    public function updateDeliveryStatus(Request $request, $order_id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('orders.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $order = Order::find($order_id);

        if (!$order) {
            return back()->withNotify([["error", "Order not found"]]);
        }

        $newDeliveryStatus = $request->delivery_status;
        $currentDeliveryStatus = $order->delivery_status;

        if ($newDeliveryStatus == $currentDeliveryStatus) {
            return back()->withNotify([["info", "Nothing Changed"]]);
        }

        $statusMap = [
            Status::DELIVERY_PENDING => Status::ORDER_PROCESSING,
            Status::DELIVERY_COMPLETE => Status::ORDER_COMPLETED,
            Status::DELIVERY_CANCELED => Status::ORDER_CANCELED,
        ];

        $messageMap = [
            Status::DELIVERY_PENDING => "Order Status Changed Successfully",
            Status::DELIVERY_COMPLETE => "Order is successfully completed",
            Status::DELIVERY_CANCELED => "Order Canceled Successfully",
        ];

        if (isset($statusMap[$newDeliveryStatus])) {
            $order->delivery_status = $newDeliveryStatus;
            $order->status = $statusMap[$newDeliveryStatus];
            $order->save();

            return back()->withNotify([["success", $messageMap[$newDeliveryStatus]]]);
        }

        return back()->withNotify([["error", "Invalid delivery status"]]);
    }
}
