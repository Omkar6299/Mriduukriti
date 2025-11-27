<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function orderStore(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'payment_method' => 'required|in:cod,online',
        ], [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
        ]);

        // get cart (only to check empty or not)
        if (Auth::guard('customer')->check()) {
            $cart = Cart::where('user_id', Auth::guard('customer')->id())
                ->where('status', 'active')
                ->with('cartItems')
                ->firstOrFail();
            $cartItems = $cart->cartItems;
        } else {
            $cartItems = collect(session('cart', []));
        }

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Cart is empty.');
        }

        // calculate totals from request items
        $itemTotal = 0;
        foreach ($request->items as $item) {
            $itemTotal += $item['price'] * $item['quantity'];
        }

        $deliveryFee = 100;
        $gstRate = 18;
        $storeState = 'Uttar Pradesh';
        $customerState = $validated['state'];

        // Normalize state names
        if (trim(strtolower($customerState)) === strtolower($storeState)) {
            $sgstAmount = round($itemTotal * ($gstRate / 2) / 100, 2);
            $cgstAmount = round($itemTotal * ($gstRate / 2) / 100, 2);
            $igstAmount = 0;
        } else {
            $sgstAmount = $cgstAmount = 0;
            $igstAmount = round($itemTotal * $gstRate / 100, 2);
        }

        $grandTotal = $itemTotal + $deliveryFee + $sgstAmount + $cgstAmount + $igstAmount;

        DB::beginTransaction();
        try {
            // Save address if logged in
            if (Auth::guard('customer')->check()) {
                UserAddress::updateOrCreate(
                    ['user_id' => Auth::guard('customer')->id()],
                    [
                        'type' => $validated['type'],
                        'name' => $validated['name'],
                        'phone' => $validated['phone'],
                        // 'email' => $validated['email'],
                        'address_line_1' => $validated['address_line_1'],
                        'address_line_2' => $request->address_line_2,
                        'city' => $validated['city'],
                        'state' => $validated['state'],
                        'postal_code' => $validated['postal_code'],
                        'country' => $validated['country'],
                        'is_default' => $request->is_default ?? 0,
                    ],
                );
            }

            // Unique order number
            $lastOrder = Order::latest('id')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $orderNumber = 'ORD' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // Create order for both COD and online payment
            $order = Order::create([
                'user_id' => Auth::guard('customer')->id(),
                'order_number' => $orderNumber,
                'subtotal' => $itemTotal,
                'delivery_fee' => $deliveryFee,
                'sgst' => $sgstAmount,
                'cgst' => $cgstAmount,
                'igst' => $igstAmount,
                'grand_total' => $grandTotal,
                'status' => $validated['payment_method'] === 'cod' ? 'confirmed' : 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'sku_id' => $item['sku_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            // Handle payment method
            if ($validated['payment_method'] === 'online') {
                // Don't clear cart yet - will be cleared after successful payment
                DB::commit();

                // Log for debugging
                \Log::info('Redirecting to payment initiation', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'amount' => $order->grand_total,
                ]);

                // Redirect to payment initiation
                return redirect()->route('nttdata.payment.initiate', ['orderId' => $order->id]);
            }


            // clear cart
            if (Auth::guard('customer')->check()) {
                $cart->delete();
            } else {
                session()->forget('cart');
            }
            DB::commit();
            return redirect()->route('customer.orderPageCod', $order->id)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function orderPageCod($id)
    {
        $order = Order::with('orderItems.product', 'orderItems.sku')
            ->where('id', $id)
            ->where('user_id', Auth::guard('customer')->id())
            ->firstOrFail();

        // Example data you will pass to payment gateway
        $paymentData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $order->grand_total, // total to be paid
            'currency' => 'INR',
            'customer_name' => $order->user->name ?? '',
            'customer_email' => $order->user->email ?? '',
            'customer_phone' => $order->user->phone ?? '',
        ];

        return view('frontend.order.codOrderPage', compact('order', 'paymentData'));
    }
}
