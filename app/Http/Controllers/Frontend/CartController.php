<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            $carts = Cart::where('user_id', Auth::guard('customer')->id())
                ->where('status', 'active')
                // ->with('cartItems.product', 'cartItems.sku')
                ->with(['cartItems.product', 'cartItems.sku'])
                ->first();
        } else {
            // Guest cart
            $sessionCart = $request->session()->get('cart', []);

            // Convert session cart into a "fake object" for frontend
            $carts = $request->session()->get('cart', []);
        }
        $user = User::with(['userAddress'])->find(Auth::guard('customer')->id());

        return view('frontend.cart.index', compact('carts', 'user'));
    }


    // public function addToCart(Request $request, $productId)
    // {
    //     if (!Auth::guard('customer')->check()) {
    //         return response()->json([
    //             'success' => false,
    //             'auth'    => false,
    //             'message' => 'Please login to add product to cart.'
    //         ]);
    //     }

    //     $product = Product::with(['productSkus'])->findOrFail($productId);
    //     $sku = $product->productSkus->first(); // ✅ pick one SKU

    //     if (Auth::guard('customer')->check()) {
    //         // Logged-in user: DB cart
    //         $cart = Cart::firstOrCreate(
    //             ['user_id' =>  Auth::guard('customer')->id(), 'status' => 'active'],
    //             ['status' => 'active']
    //         );
    //         $cartPreviousValue = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->where('sku_id', $product->productSkus->id)->first();
    //         // dd($cartPreviousValue);
    //         $cartItem = CartItem::updateOrCreate(
    //             [
    //                 'cart_id'    => $cart->id,
    //                 'product_id' => $product->id,
    //                 'sku_id'     => $product->productSkus->id,
    //             ],
    //             [
    //                 'price'    => $sku->sale_price ?? $sku->price,
    //                 'quantity' => $cartPreviousValue->quantity ?? 0,
    //                 'total'    => 0,
    //             ]
    //         );

    //         $cartItem->quantity += 1; // increment
    //         $cartItem->total = $cartItem->price * $cartItem->quantity;
    //         $cartItem->save();

    //         $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');
    //     } else {
    //         // Guest user: session cart
    //         $cart = $request->session()->get('cart', []);

    //         if (isset($cart[$productId])) {
    //             $cart[$productId]['quantity']++;
    //         } else {
    //             $cart[$productId] = [
    //                 "name"       => $product->name,
    //                 "product_id" => $product->id,
    //                 "sku_id"     => $sku->id,
    //                 "price"      => $sku->sale_price ?? $sku->price,
    //                 "quantity"   => 1,
    //                 "image"      => $sku->image,
    //             ];
    //         }

    //         // dd($cart);
    //         // ✅ save the entire cart back
    //         $request->session()->put('cart', $cart);


    //         $cartCount = collect($cart)->sum('quantity');
    //     }

    //     return response()->json([
    //         'success'    => true,
    //         'message'    => 'Product added to cart!',
    //         'cart_count' => $cartCount
    //     ]);
    // }
    public function addToCart(Request $request, $productId)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json([
                'success' => false,
                'auth'    => false,
                'message' => 'Please login to add product to cart.'
            ]);
        }

        $product = Product::with(['productSkus'])->findOrFail($productId);
        $sku = $product->productSkus->first();

        $cart = Cart::firstOrCreate(
            ['user_id' =>  Auth::guard('customer')->id(), 'status' => 'active'],
            ['status' => 'active']
        );

        $cartPreviousValue = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('sku_id', $product->productSkus->id)
            ->first();

        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'sku_id'     => $product->productSkus->id,
            ],
            [
                'price'    => $sku->sale_price ?? $sku->price,
                'quantity' => $cartPreviousValue->quantity ?? 0,
                'total'    => 0,
            ]
        );

        $cartItem->quantity += 1;
        $cartItem->total = $cartItem->price * $cartItem->quantity;
        $cartItem->save();

        $cartCount = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'success'    => true,
            'message'    => 'Product added to cart!',
            'cart_count' => $cartCount
        ]);
    }



    public function removeFromCart(Request $request, $productId)
    {
        if (Auth::guard('customer')->check()) {
            $cartItem = CartItem::where('id', $productId)->first();
            if ($cartItem) {
                $cartItem->delete();
            }
        } else {
            $cart = $request->session()->get('cart', []);
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                $request->session()->put('cart', $cart);
            }
        }
        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    /**
     * Update cart item quantity via AJAX
     */
    public function updateQuantity(Request $request, $cartItemId)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update cart.'
            ], 401);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        try {
            $cartItem = CartItem::where('id', $cartItemId)
                ->whereHas('cart', function($query) {
                    $query->where('user_id', Auth::guard('customer')->id())
                          ->where('status', 'active');
                })
                ->firstOrFail();

            $cartItem->quantity = $validated['quantity'];
            $cartItem->total = $cartItem->price * $cartItem->quantity;
            $cartItem->save();

            // Get updated cart totals
            $cart = Cart::where('user_id', Auth::guard('customer')->id())
                ->where('status', 'active')
                ->with('cartItems.sku')
                ->first();

            $itemTotal = 0;
            $originalTotal = 0;
            $deliveryFee = 100;
            $gstRate = 18;
            $storeState = 'Uttar Pradesh';

            foreach ($cart->cartItems as $ci) {
                $price = $ci->sku->sale_price ?? $ci->price;
                $mrp = $ci->sku->price ?? $price;
                $itemTotal += $price * $ci->quantity;
                $originalTotal += $mrp * $ci->quantity;
            }

            $user = Auth::guard('customer')->user()->load('userAddress');
            $userAddress = $user->userAddress ?? null;
            $customerState = $userAddress->state ?? $storeState;

            if (trim(strtolower($customerState)) === strtolower($storeState)) {
                $sgstRate = $cgstRate = $gstRate / 2;
                $sgstAmount = round(($itemTotal * $sgstRate) / 100, 2);
                $cgstAmount = round(($itemTotal * $cgstRate) / 100, 2);
                $igstAmount = 0;
            } else {
                $sgstAmount = $cgstAmount = 0;
                $igstAmount = round(($itemTotal * $gstRate) / 100, 2);
            }

            $grandTotal = $itemTotal + $sgstAmount + $cgstAmount + $igstAmount + $deliveryFee;
            $totalSavings = $originalTotal - $itemTotal;

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated successfully!',
                'data' => [
                    'item_total' => number_format($itemTotal, 2),
                    'sgst_amount' => number_format($sgstAmount, 2),
                    'cgst_amount' => number_format($cgstAmount, 2),
                    'igst_amount' => number_format($igstAmount, 2),
                    'delivery_fee' => number_format($deliveryFee, 2),
                    'grand_total' => number_format($grandTotal, 2),
                    'total_savings' => number_format($totalSavings, 0),
                    'item_subtotal' => number_format($cartItem->total, 2),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quantity: ' . $e->getMessage()
            ], 500);
        }
    }
}
