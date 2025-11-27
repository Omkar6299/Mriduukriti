@extends('frontend.layouts.app')
@section('content')
    <style>
        /* Success Animation */
        #succes_bg{
        background: var(--primary-color);
        }
        .success-icon {
            font-size: 90px;
            color: #28a745;
            animation: pop 0.6s ease forwards;
        }
        @keyframes pop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }

        /* Confetti */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: red;
            top: -10px;
            opacity: 0.7;
            animation: fall linear forwards;
        }
        @keyframes fall {
            to {
                transform: translateY(110vh) rotate(360deg);
                opacity: 0.9;
            }
        }
    </style>

    <div class="text-white text-center py-5 position-relative" id="succes_bg">
        <i class="bi bi-check-circle-fill success-icon"></i>
        <h1 class="fw-bold mt-3">Payment Successful!</h1>
        <p class="lead mb-0">Thank you, your payment has been processed successfully 🎉</p>
        <p class="opacity-75">Order #{{ $order->order_number }}</p>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <!-- Left Column -->
            <div class="col-lg-7">
                <!-- Items -->
                <h4 class="fw-bold mb-3">Your Items</h4>
                <ul class="list-group shadow-sm mb-4">
                    @forelse ($order->orderItems ?? [] as $item)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                @php
                                    // Try to get image from sku first, then product->productSkus, then default
                                    $productImage = null;
                                    if ($item->sku && $item->sku->image) {
                                        $productImage = $item->sku->image;
                                    } elseif ($item->product && $item->product->productSkus && $item->product->productSkus->image) {
                                        $productImage = $item->product->productSkus->image;
                                    }
                                    $imageUrl = $productImage ? asset($productImage) : asset('admin_panel/assets/img/default/default_pro.png');
                                @endphp
                                <img src="{{ $imageUrl }}"
                                     class="rounded me-3" style="width:70px; height:70px; object-fit:cover;"
                                     alt="{{ $item->product->name ?? 'Product' }}">
                                <div>
                                    <h6 class="mb-1">{{ $item->product->name ?? 'Product' }}</h6>
                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                </div>
                            </div>
                            <span class="fw-bold text-dark">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">No items found</li>
                    @endforelse
                </ul>

                <!-- Delivery Info -->
                <div class="p-4 border rounded-3 mb-4">
                    <h5 class="fw-bold"><i class="bi bi-truck me-2 text-primary"></i> Delivery Address</h5>
                    @if($order->user && $order->user->userAddress)
                        <p class="mb-0">
                            {{ $order->user->userAddress->address_line_1 ?? '' }},<br>
                            {{ $order->user->userAddress->city ?? '' }},
                            {{ $order->user->userAddress->state ?? '' }} -
                            {{ $order->user->userAddress->postal_code ?? '' }}
                        </p>
                    @else
                        <p class="mb-0 text-muted">Address not available</p>
                    @endif
                </div>

                <!-- Payment Details -->
                @if($payment)
                <div class="p-4 border rounded-3 mb-4">
                    <h5 class="fw-bold"><i class="bi bi-credit-card me-2 text-primary"></i> Payment Details</h5>
                    <p class="mb-1"><strong>Transaction ID:</strong> {{ $payment->merchant_transaction_id }}</p>
                    @if($payment->ntt_data_transaction_id)
                        <p class="mb-1"><strong>NTT Data Txn ID:</strong> {{ $payment->ntt_data_transaction_id }}</p>
                    @endif
                    @if($payment->bank_transaction_id)
                        <p class="mb-1"><strong>Bank Txn ID:</strong> {{ $payment->bank_transaction_id }}</p>
                    @endif
                    @if($payment->payment_mode)
                        <p class="mb-1"><strong>Payment Mode:</strong> {{ $payment->payment_mode }}</p>
                    @endif
                    <p class="mb-0"><strong>Amount Paid:</strong> ₹{{ number_format($payment->amount, 2) }}</p>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-5">
                <div class="p-4 border rounded-3 shadow-sm">
                    <h4 class="fw-bold mb-3">Order Summary</h4>
                    <p class="mb-1"><strong>Subtotal:</strong> ₹{{ number_format($order->subtotal, 2) }}</p>
                    <p class="mb-1"><strong>Delivery Fee:</strong> ₹{{ number_format($order->delivery_fee, 2) }}</p>
                    <p class="mb-1"><strong>Taxes:</strong> ₹{{ number_format($order->sgst + $order->cgst + $order->igst, 2) }}</p>
                    <hr>
                    <h3 class="fw-bold text-success mb-0">
                        ₹{{ number_format($order->grand_total, 2) }}
                    </h3>
                    <span class="badge bg-success mt-2 px-3 py-2 rounded-pill">
                        <i class="bi bi-credit-card me-1"></i> Online Payment
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center mt-5">
            <a href="{{ route('frontend.shop.list')}}" class="btn btn-outline-dark btn-lg rounded-pill px-5 me-3">
                <i class="bi bi-shop me-2"></i> Continue Shopping
            </a>
            <a href="{{route('user_dashboard')}}#v-pills-orders" class="btn btn-dark btn-lg rounded-pill px-5">
                <i class="bi bi-box-seam me-2"></i> View My Orders
            </a>
        </div>
    </div>

    <!-- Confetti Script -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            for (let i = 0; i < 80; i++) {
                let confetti = document.createElement("div");
                confetti.classList.add("confetti");
                confetti.style.left = Math.random() * 100 + "vw";
                confetti.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
                confetti.style.animationDuration = (Math.random() * 3 + 2) + "s";
                document.body.appendChild(confetti);

                setTimeout(() => confetti.remove(), 5000);
            }
        });
    </script>
@endsection

