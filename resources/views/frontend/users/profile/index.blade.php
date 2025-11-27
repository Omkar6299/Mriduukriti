@extends('frontend.layouts.app')
@section('content')
    <style>
        .nav-pills .nav-link.active,
        .nav-pills .show>.nav-link {
            background-color: var(--primary-color) !important;
        }

        .nav-link {
            color: var(--primary-color);
        }

        .nav-link :hover {
            color: var(--primary-color) !important;
        }

        .nav-link :active {

            color: var(--primary-color) !important;

        }
    </style>
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar Nav (4 columns) -->
            <div class="col-md-4">
                <div class="nav flex-column nav-pills border rounded p-2" id="v-pills-tab" role="tablist"
                    aria-orientation="vertical">

                    <button class="nav-link active border mb-1" id="v-pills-profile-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile"
                        aria-selected="true">
                        <i class="bi bi-person-circle me-2"></i> Profile
                    </button>

                    <button class="nav-link border mb-1" id="v-pills-cart-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-cart" type="button" role="tab" aria-controls="v-pills-cart"
                        aria-selected="false">
                        <i class="bi bi-cart3 me-2"></i> My Cart
                    </button>

                    <button class="nav-link border mb-1" id="v-pills-orders-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-orders" type="button" role="tab" aria-controls="v-pills-orders"
                        aria-selected="false">
                        <i class="bi bi-bag-check me-2"></i> Orders
                    </button>

                    <button class="nav-link border mb-1" id="v-pills-wishlist-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-wishlist" type="button" role="tab" aria-controls="v-pills-wishlist"
                        aria-selected="false">
                        <i class="bi bi-heart me-2"></i> Wishlist
                    </button>

                    {{-- <button class="nav-link border mb-1" id="v-pills-history-tab" data-bs-toggle="pill"
                        data-bs-target="#v-pills-history" type="button" role="tab" aria-controls="v-pills-history"
                        aria-selected="false">
                        <i class="bi bi-clock-history me-2"></i> History
                    </button> --}}
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button class="nav-link border w-100" type="submit">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab Content (8 columns) -->
            <div class="col-md-8">
                <div class="tab-content border rounded p-3" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel"
                        aria-labelledby="v-pills-profile-tab">
                        <h4 class="mb-4">My Profile</h4>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3 text-center">
                                        <img src="{{ Auth::guard('customer')->user()->image
                                            ? asset(Auth::guard('customer')->user()->image)
                                            : asset('images/default-avatar.png') }}"
                                            class="rounded-circle img-fluid border" alt="User Avatar">

                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="mb-1">{{ Auth::guard('customer')->user()->name ?? '' }}</h5>

                                        <p class="text-muted mb-2">{{ Auth::guard('customer')->user()->email ?? '' }}</p>
                                        <span class="badge bg-success">Verified</span>
                                    </div>
                                </div>

                                <hr>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted"><i class="bi bi-telephone me-2"></i>Phone</p>
                                        <p>{{ Auth::guard('customer')->user()->userAddress->phone ?? '-' }}</p>


                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted"><i class="bi bi-geo-alt me-2"></i>Default Address</p>
                                        <p>{{ Auth::guard('customer')->user()->userAddress->address_line_1 ?? '' }}
                                            {{ Auth::guard('customer')->user()->userAddress->address_line_2 ?? '' }},
                                            {{ Auth::guard('customer')->user()->userAddress->city ?? '' }},
                                            {{ Auth::guard('customer')->user()->userAddress->state ?? '' }} -
                                            {{ Auth::guard('customer')->user()->userAddress->postal_code ?? '' }}</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted"><i class="bi bi-calendar3 me-2"></i>Member Since</p>
                                        <p>
                                            {{ Auth::guard('customer')->user()->created_at
                                                ? Auth::guard('customer')->user()->created_at->format('d F Y')
                                                : '' }}
                                        </p>

                                    </div>
                                    {{-- <div class="col-md-6">
                                        <p class="mb-1 text-muted"><i class="bi bi-shield-check me-2"></i>Loyalty Status</p>
                                        <p>Gold Member</p>
                                    </div> --}}
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-primary text-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal"><i class="bi bi-pencil me-1"></i>
                                        Edit
                                        Profile</button>
                                    <button class="btn btn-outline-secondary text-secondary btn-sm"><i
                                            class="bi bi-lock me-1"></i> Change
                                        Password</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        use App\Models\Cart;
                        $carts = Cart::where('user_id', Auth::guard('customer')->id())
                            ->where('status', 'active')
                            ->with('cartItems.product', 'cartItems.sku')
                            ->first();
                    @endphp
                    <div class="tab-pane fade" id="v-pills-cart" role="tabpanel" aria-labelledby="v-pills-cart-tab">
                        <h4 class="mb-4">My Cart</h4>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">

                                @if (!empty($carts['cartItems']) && count($carts['cartItems']) > 0)
                                    @foreach ($carts['cartItems'] as $item)
                                        @php
                                            $imagePath = public_path($item['sku']['image'] ?? '');
                                            $image =
                                                file_exists($imagePath) && !empty($item['sku']['image'])
                                                    ? asset($item['sku']['image'])
                                                    : asset('admin_panel/assets/img/default/default_pro.png');
                                        @endphp

                                        <div class="row align-items-center mb-3">
                                            <!-- Product Image -->
                                            <div class="col-md-2 col-4">
                                                <img src="{{ $image }}" class="img-fluid rounded" alt="Product">
                                            </div>

                                            <!-- Product Info -->
                                            <div class="col-md-4 col-8">
                                                <h6 class="mb-1">{{ $item['product']['name'] ?? '' }}</h6>

                                                <p class="mb-1">
                                                    <span class="fw-bold text-success">
                                                        ₹{{ number_format($item['sku']['sale_price'] ?? $item['price'], 0) }}
                                                    </span>
                                                    @if (!empty($item['sku']['price']) && $item['sku']['sale_price'] < $item['sku']['price'])
                                                        <span class="text-muted text-decoration-line-through ms-2">
                                                            ₹{{ number_format($item['sku']['price'], 0) }}
                                                        </span>
                                                    @endif
                                                </p>

                                                @if (!empty($item['sku']['price']) && $item['sku']['sale_price'] < $item['sku']['price'])
                                                    <p class="text-success small mb-0">
                                                        You saved ₹{{ $item['sku']['price'] - $item['sku']['sale_price'] }}
                                                    </p>
                                                @endif
                                            </div>

                                            <!-- Quantity -->
                                            <div class="col-md-2 col-6 mt-2 mt-md-0">
                                                <select class="form-select form-select-sm change-qty"
                                                    data-item-id="{{ $item['id'] }}">
                                                    @for ($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $item['quantity'] == $i ? 'selected' : '' }}>
                                                            Qty: {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-2 col-6 mt-2 mt-md-0">
                                                <p class="mb-0 fw-semibold">
                                                    ₹{{ number_format(($item['sku']['sale_price'] ?? $item['price']) * $item['quantity'], 0) }}
                                                </p>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="col-md-2 text-end">
                                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @if (!$loop->last)
                                            <hr>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted">Your cart is empty.</p>
                                @endif
                            </div>
                        </div>

                        @if (!empty($carts['cartItems']) && count($carts['cartItems']) > 0)
                            <!-- Cart Summary -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    @php
                                        $subtotal = collect($carts['cartItems'])->sum(function ($item) {
                                            return ($item['sku']['sale_price'] ?? $item['price']) * $item['quantity'];
                                        });
                                        $shipping = 50; // You can make this dynamic
                                        $total = $subtotal + $shipping;
                                    @endphp

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span>₹ {{ number_format($subtotal, 0) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping</span>
                                        <span>₹ {{ number_format($shipping, 0) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold mb-3">
                                        <span>Total</span>
                                        <span>₹ {{ number_format($total, 0) }}</span>
                                    </div>
                                    <div class="text-end">
                                        <a href="{{ route('frontend.shop.list') }}"
                                            class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-arrow-left"></i> Continue Shopping
                                        </a>
                                        <a href="#" class="btn btn-success">
                                            <i class="bi bi-bag-check me-1"></i> Checkout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @php
                        use App\Models\Order;

                        $orders = Order::with('orderItems.product.productSkus')
                            ->where('user_id', Auth::guard('customer')->id())
                            ->latest()
                            ->get();

                        // dd($orders->toArray());

                    @endphp

                    <div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
                        <h4 class="mb-4">My Orders</h4>

                        @if ($orders->count() > 0)
                            @foreach ($orders as $order)
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1">Order #{{ $order->order_number }}</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    Placed on:
                                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <span
                                                    class="badge 
                                @if ($order->status == 'delivered') bg-success 
                                @elseif($order->status == 'shipped') bg-warning text-dark 
                                @elseif($order->status == 'pending') bg-secondary 
                                @elseif($order->status == 'cancelled') bg-danger 
                                @else bg-light text-dark @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>

                                                @if (in_array($order->status, ['pending', 'processing', 'confirmed']))
                                                    <form action="{{ route('customer.orderCancel', $order->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="bi bi-x-circle me-1"></i> Cancel
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>

                                        @foreach ($order->orderItems as $item)
                                            <div class="row align-items-center mb-2">
                                                <div class="col-md-2 col-4">
                                                    <img src="{{ $item->product->productSkus->image
                                                        ? asset($item->product->productSkus->image)
                                                        : asset('admin_panel/assets/img/default/default_pro.png') }}"
                                                        class="img-fluid rounded" alt="{{ $item->product->name }}"
                                                        style="height: 70px !important; min-width: 70px !important; object-fit: cover;">
                                                </div>
                                                <div class="col-md-6 col-8">
                                                    <p class="mb-1 fw-semibold">{{ $item->product->name }}</p>
                                                    <p class="text-muted small mb-0">
                                                        Qty: {{ $item->quantity }} • ₹{{ number_format($item->price, 2) }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    {{-- View Order Button --}}
                                                    <a href="{{ route('customer.orderPageCod', $order->id) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i> View Order
                                                    </a>

                                                    {{-- Track Button --}}
                                                    @if (in_array($order->status, ['shipped', 'processing']))
                                                        <a href="#" class="btn btn-outline-secondary btn-sm mt-1">
                                                            <i class="bi bi-truck me-1"></i> Track
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <i class="bi bi-bag-x fs-1 text-muted"></i>
                                <p class="mt-3">You haven’t placed any orders yet.</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="bi bi-cart me-1"></i> Start Shopping
                                </a>
                            </div>
                        @endif
                    </div>


                    @php
                        use App\Models\Whistlist;
                        $userId = Auth::guard('customer')->id();
                        $wishlists = Whistlist::with('product.productSkus')->where('user_id', $userId)->get();
                        // dd($wishlists->toArray());
                    @endphp

                    <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel"
                        aria-labelledby="v-pills-wishlist-tab">
                        <h4 class="mb-4">My Wishlist</h4>

                        <div class="row" id="wishlist-items">
                            @forelse($wishlists as $wishlist)
                                <div class="col-md-6 mb-3" id="wishlist-item-{{ $wishlist->product_id }}">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-4">
                                                <img src="{{ asset($wishlist->product->productSkus->image) ?? asset('admin_panel/assets/img/default/default_pro.png') }}"
                                                    class="img-fluid rounded-start" alt="{{ $wishlist->product->name }}">
                                            </div>
                                            <div class="col-8">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-1">{{ $wishlist->product->name }}</h6>
                                                    <p class="text-muted small mb-2">₹
                                                        {{ number_format($wishlist->product->productSkus->price, 2) }}</p>
                                                    <div class="d-flex">
                                                        {{-- <button
                                                            class="btn btn-sm btn-primary text-primary me-2 add-to-cart-btn"
                                                            data-id="{{ $wishlist->product_id }}">
                                                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                                        </button> --}}
                                                        <button class="btn btn-sm btn-outline-danger wishlist-btn"
                                                            data-id="{{ $wishlist->product_id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <!-- Empty State -->
                                <div class="text-center py-5">
                                    <i class="bi bi-heart fs-1 text-muted"></i>
                                    <p class="mt-3">Your wishlist is empty.</p>
                                    <a href="{{ route('frontend.shop.list') }}" class="btn btn-primary text-primary">
                                        <i class="bi bi-cart me-1"></i> Browse Products
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>


                    {{-- <div class="tab-pane fade" id="v-pills-history" role="tabpanel"
                        aria-labelledby="v-pills-history-tab">
                        <h4 class="mb-4">Browsing History</h4>

                        <div class="row">
                            <!-- History Item -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <img src="{{ asset('admin_panel/assets/img/default/default_pro.png') }}"
                                        class="card-img-top" alt="Product">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Bluetooth Speaker</h6>
                                        <p class="text-muted small mb-2">₹ 1,999</p>
                                        <button class="btn btn-sm btn-outline-primary"><i
                                                class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                                    </div>
                                </div>
                            </div>

                            <!-- History Item -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <img src="{{ asset('admin_panel/assets/img/default/default_pro.png') }}"
                                        class="card-img-top" alt="Product">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Casual T-Shirt</h6>
                                        <p class="text-muted small mb-2">₹ 799</p>
                                        <button class="btn btn-sm btn-outline-primary"><i
                                                class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                                    </div>
                                </div>
                            </div>

                            <!-- History Item -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <img src="{{ asset('admin_panel/assets/img/default/default_pro.png') }}"
                                        class="card-img-top" alt="Product">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">Smartphone Case</h6>
                                        <p class="text-muted small mb-2">₹ 499</p>
                                        <button class="btn btn-sm btn-outline-primary"><i
                                                class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="bi bi-clock-history fs-1 text-muted"></i>
                            <p class="mt-3">You haven’t viewed any products yet.</p>
                            <a href="#" class="btn btn-primary text-primary"><i class="bi bi-shop me-1"></i> Start
                                Shopping</a>
                        </div>
                    </div> --}}


                </div>
            </div>
        </div>
    </div>


    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Personal Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary text-dark">Update changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('#v-pills-tab button[data-bs-toggle="pill"]').forEach(function(tabButton) {
                tabButton.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute("data-bs-target");
                    history.replaceState(null, null, target);
                });
            });

            const hash = window.location.hash;
            if (hash) {
                const targetTab = document.querySelector('#v-pills-tab button[data-bs-target="' + hash + '"]');
                if (targetTab) {
                    const tab = new bootstrap.Tab(targetTab);
                    tab.show();
                }
            }
        });
    </script>
@endpush
