@extends('frontend.layouts.app')
@section('content')
    @if (Auth::guard('customer')->check())

        @if (!empty($carts['cartItems']) && count($carts['cartItems']) > 0)
            <div class="container py-5">
                <div class="row">
                    <!-- Left Section -->
                    <div class="col-md-8">

                        <h4 class="mb-4">Shopping cart</h4>
                        @foreach ($carts['cartItems'] as $item)
                            <div class="card p-3 mb-3">
                                <div class="d-flex align-items-center">
                                    @php
                                        $imagePath = public_path($item['sku']['image'] ?? '');
                                    @endphp
                                    <img src="{{ file_exists($imagePath) && !empty($item['sku']['image'])
                                        ? asset($item['sku']['image'])
                                        : asset('admin_panel/assets/img/default/default_pro.png') }}"
                                        alt="product" class="rounded me-3" style="width:100px;height:100px;">

                                    <div class="flex-grow-1">
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
                                            <p class="text-success small mb-1">
                                                You saved ₹{{ $item['sku']['price'] - $item['sku']['sale_price'] }}
                                            </p>
                                        @endif

                                        <select class="form-select form-select-sm w-auto change-qty"
                                            data-item-id="{{ $item['id'] }}">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $item['quantity'] == $i ? 'selected' : '' }}>
                                                    Qty: {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger text-decoration-none ms-3">
                                            REMOVE
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        <form action="{{ route('customer.orderStore') }}" method="POST">
                            @csrf
                            <!-- Delivery Address -->
                            @foreach ($carts['cartItems'] as $item)
                                <input type="hidden" name="items[{{ $item['id'] }}][product_id]"
                                    value="{{ $item['product']['id'] }}">
                                <input type="hidden" name="items[{{ $item['id'] }}][sku_id]"
                                    value="{{ $item['sku']['id'] }}">
                                <input type="hidden" name="items[{{ $item['id'] }}][quantity]"
                                    value="{{ $item['quantity'] }}">
                                <input type="hidden" name="items[{{ $item['id'] }}][price]"
                                    value="{{ $item['sku']['sale_price'] ?? $item['price'] }}">
                            @endforeach
                            <div class="card p-3 mb-3">
                                <h5 class="mb-3">Delivery Address</h5>

                                <!-- Default address option -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" name="is_default" type="checkbox" value="1"
                                        id="useDefaultAddress" checked>
                                    <label class="form-check-label" for="useDefaultAddress">
                                        Use my default address
                                    </label>
                                </div>

                                <!-- Address Form -->
                                <div id="addressForm">
                                    <div class="row">
                                        <!-- Address Type -->
                                        <div class="col-md-6 mb-3">
                                            <label for="type" class="form-label">Address Type</label>
                                            <select class="form-select" id="type" name="type">
                                                <option value="Home">Home</option>
                                                <option value="Office">Office</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>

                                        <!-- Name -->
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ Auth::guard('customer')->user()->name ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Phone -->
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ $user->userAddress->phone ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ Auth::guard('customer')->user()->email ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Address Lines -->
                                    <div class="mb-3">
                                        <label for="address_line_1" class="form-label">Address Line 1</label>
                                        <input type="text" class="form-control" id="address_line_1" name="address_line_1"
                                            value="{{ $user->userAddress->address_line_1 ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address_line_2" class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control" id="address_line_2" name="address_line_2"
                                            value="{{ $user->userAddress->address_line_2 ?? '' }}">
                                    </div>

                                    <div class="row">
                                        <!-- City -->
                                        <div class="col-md-4 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="city"
                                                value="{{ $user->userAddress->city ?? '' }}" name="city">
                                        </div>

                                        <!-- State -->
                                        <div class="col-md-4 mb-3">
                                            <label for="state" class="form-label">State</label>
                                            <input type="text" class="form-control" id="state" name="state"
                                                value="{{ $user->userAddress->state ?? '' }}">
                                        </div>

                                        <!-- Postal Code -->
                                        <div class="col-md-4 mb-3">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control" id="postal_code"
                                                value="{{ $user->userAddress->postal_code ?? '' }}" name="postal_code">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country"
                                            value="{{ $user->userAddress->country ?? '' }}" value="India">
                                    </div>
                                </div>
                            </div>

                            <div class="card p-4 mb-3">
                                <h5 class="mb-3">Payment Method</h5>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                        value="cod" checked required>
                                    <label class="form-check-label" for="cod">
                                        <i class="bi bi-cash-coin me-1"></i> Cash on Delivery (COD)
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="online" value="online" required>
                                    <label class="form-check-label" for="online">
                                        <i class="bi bi-credit-card me-1"></i> Online Payment
                                    </label>
                                </div>

                                @error('payment_method')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                    </div>

                    <!-- Right Section -->
                    <div class="col-md-4">
                        <div class="card p-3">
                            @php
                                $itemTotal = 0;
                                $originalTotal = 0;
                                $deliveryFee = 100;
                                $gstRate = 18;
                                $storeState = 'Uttar Pradesh';

                                foreach ($carts['cartItems'] as $ci) {
                                    $price = $ci['sku']['sale_price'] ?? $ci['price'];
                                    $mrp = $ci['sku']['price'] ?? $price;

                                    $itemTotal += $price * $ci['quantity'];
                                    $originalTotal += $mrp * $ci['quantity'];
                                }

                                $customerState = Auth::guard('customer')->user()->state ?? $storeState;

                                if ($customerState === $storeState) {
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
                                $savingsPercent =
                                    $originalTotal > 0 ? round(($totalSavings / $originalTotal) * 100) : 0;
                            @endphp

                            <!-- Order Summary -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Item total</span>
                                <span>
                                    @if ($totalSavings > 0)
                                        <del class="original-total">₹{{ number_format($originalTotal, 0) }}</del>
                                    @endif
                                    <span class="fw-bold item-total"> ₹{{ number_format($itemTotal, 2) }}</span>
                                </span>
                            </div>

                            @if ($sgstAmount > 0 || $cgstAmount > 0)
                                <div class="d-flex justify-content-between sgst-row">
                                    <span>SGST ({{ $sgstRate }}%)</span>
                                    <span class="sgst-amount">₹{{ number_format($sgstAmount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between cgst-row">
                                    <span>CGST ({{ $cgstRate }}%)</span>
                                    <span class="cgst-amount">₹{{ number_format($cgstAmount, 2) }}</span>
                                </div>
                            @endif

                            @if ($igstAmount > 0)
                                <div class="d-flex justify-content-between igst-row">
                                    <span>IGST ({{ $gstRate }}%)</span>
                                    <span class="igst-amount">₹{{ number_format($igstAmount, 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between">
                                <span>Delivery fee</span>
                                <span class="delivery-fee">₹{{ number_format($deliveryFee, 2) }}</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between fw-bold">
                                <span>Grand total</span>
                                <span class="grand-total">₹{{ number_format($grandTotal, 2) }}</span>
                            </div>

                            <p class="small text-muted">Inclusive of all taxes</p>

                            @if ($totalSavings > 0)
                                <div class="alert alert-success p-2 small">
                                    🎉 You have saved {{ $savingsPercent }}% (₹{{ number_format($totalSavings, 0) }}) on
                                    your order!
                                </div>
                            @endif
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-dark w-100">Place Order</button>
                        </div>
                    </div>
                    </form>

                </div>

                <div class="d-flex justify-content-between mt-4">
                    <img src="{{ asset('frontend/assets/images/payment/paymentmodes.webp') }}" alt="Payments"
                        style="height: 25px;">
                    <p class="small mt-2"><i class="bi bi-lock"></i> 100% secured payments</p>
                </div>
            </div>
        @else
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <div class="card border-0 p-5 rounded  shadow-sm ">
                            <div class="mb-4">
                                <i class="bi bi-cart-x text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h3 class="mb-3">Your Cart is Empty</h3>
                            <p class="text-muted mb-4">
                                Looks like you haven’t added anything to your cart yet.
                                Start shopping now and explore our amazing products!
                            </p>
                            <a href="{{ route('frontend.shop.list') }}"
                                class="btn btn-primary text-primary btn-lg px-4 rounded-pill">
                                <i class="bi bi-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="card border-0 p-5 rounded  shadow-sm ">
                        <div class="mb-4">
                            <i class="bi bi-cart-x text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="mb-3">Your Cart is Empty</h3>
                        <p class="text-muted mb-4">
                            Looks like you haven’t added anything to your cart yet.
                            Start shopping now and explore our amazing products!
                        </p>
                        <a href="{{ route('frontend.shop.list') }}"
                            class="btn btn-primary text-primary btn-lg px-4 rounded-pill">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @endif


@endsection

@push('scripts')
    <script>
        document.getElementById('useDefaultAddress').addEventListener('change', function() {
            const form = document.getElementById('addressForm');
            form.style.display = this.checked ? 'none' : 'block';
        });

        // Real-time quantity update and cart total calculation
        document.querySelectorAll('.change-qty').forEach(function(select) {
            select.addEventListener('change', function() {
                const cartItemId = this.getAttribute('data-item-id');
                const quantity = this.value;
                const originalSelect = this;

                // Disable select during update
                this.disabled = true;

                // Show loading state
                const loadingText = this.options[this.selectedIndex].text;
                this.options[this.selectedIndex].text = 'Updating...';

                fetch(`/cart/update-quantity/${cartItemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                       document.querySelector('input[name="_token"]')?.value || ''
                    },
                    body: JSON.stringify({
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update totals in real-time
                        if (document.querySelector('.item-total')) {
                            document.querySelector('.item-total').textContent = ' ₹' + data.data.item_total;
                        }

                        if (document.querySelector('.sgst-amount')) {
                            document.querySelector('.sgst-amount').textContent = '₹' + data.data.sgst_amount;
                        }

                        if (document.querySelector('.cgst-amount')) {
                            document.querySelector('.cgst-amount').textContent = '₹' + data.data.cgst_amount;
                        }

                        if (document.querySelector('.igst-amount')) {
                            document.querySelector('.igst-amount').textContent = '₹' + data.data.igst_amount;
                        }

                        if (document.querySelector('.grand-total')) {
                            document.querySelector('.grand-total').textContent = '₹' + data.data.grand_total;
                        }

                        // Update hidden input for order form
                        const hiddenInput = document.querySelector(`input[name="items[${cartItemId}][quantity]"]`);
                        if (hiddenInput) {
                            hiddenInput.value = quantity;
                        }

                        // Show success message briefly
                        const successMsg = document.createElement('div');
                        successMsg.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                        successMsg.style.zIndex = '9999';
                        successMsg.innerHTML = `
                            <strong>✓</strong> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.body.appendChild(successMsg);
                        setTimeout(() => successMsg.remove(), 2000);
                    } else {
                        alert('Error: ' + data.message);
                        // Revert select to previous value
                        originalSelect.value = originalSelect.getAttribute('data-previous-value') || 1;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update quantity. Please try again.');
                    // Revert select to previous value
                    originalSelect.value = originalSelect.getAttribute('data-previous-value') || 1;
                })
                .finally(() => {
                    // Re-enable select
                    this.disabled = false;
                    this.options[this.selectedIndex].text = loadingText;
                });
            });

            // Store previous value for revert
            select.setAttribute('data-previous-value', select.value);
        });
    </script>
@endpush
