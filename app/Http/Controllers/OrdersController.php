<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {

        $order = Order::orderBy('created_at', 'desc')->get();
        return view('admin_panel.order.index', compact('order'));
    }

    public function orderDetail($order_no)
    {

        $order = Order::with(['orderItems'])->where('order_number', $order_no)->first();
        $user = User::with(['userAddress'])->where('id', $order->user_id)->first();
        return view('admin_panel.order.order_page', compact('order', 'user'));
    }

    public function orderUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled',
            // 'payment_status' => 'required|string|in:unpaid,paid,refunded',
        ]);

        $order = Order::findOrFail($id);
        // Update order
        $order->status = $request->status;

        // $order->payment_status = $request->payment_status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }


    public function ordercancel($id)
    {
        $order = Order::findOrFail($id);

        if (in_array($order->status, ['pending', 'confirmed'])) {
            $order->status = 'cancelled';
            $order->save();
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        }

        return redirect()->back()->with('error', 'This order cannot be cancelled.');
    }
}
