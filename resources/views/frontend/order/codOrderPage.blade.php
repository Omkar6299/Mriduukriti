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
        <h1 class="fw-bold mt-3">Order Confirmed!</h1>
        <p class="lead mb-0">Thank you, your order has been placed successfully 🎉</p>
        <p class="opacity-75">Order #{{ $order->order_number }}</p>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <!-- Left Column -->
            <div class="col-lg-7">
                <!-- Items -->
                <h4 class="fw-bold mb-3">Your Items</h4>
                <ul class="list-group shadow-sm mb-4">
                    @foreach ($order->orderItems as $item)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($item->product->productSkus->image) ?? asset('admin_panel/assets/img/default/default_pro.png') }}"
                                     class="rounded me-3" style="width:70px; height:70px; object-fit:cover;">
                                <div>
                                    <h6 class="mb-1">{{ $item->product->name ?? 'Product' }}</h6>
                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                </div>
                            </div>
                            <span class="fw-bold text-dark">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                <!-- Delivery Info -->
                <div class="p-4 border rounded-3 mb-4">
                    <h5 class="fw-bold"><i class="bi bi-truck me-2 text-primary"></i> Delivery Address</h5>
                    <p class="mb-0">
                        {{ $order->user->userAddress->address_line_1 ?? '' }},<br>
                        {{ $order->user->userAddress->city ?? '' }},
                        {{ $order->user->userAddress->state ?? '' }} -
                        {{ $order->user->userAddress->postal_code ?? '' }}
                    </p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-5">
                @php
                    $method = strtolower(trim($order->payment_method ?? ''));
                    $status = strtolower(trim($order->payment_status ?? ''));
                    $isCod = $method === 'cod';
                    $isOnline = $method === 'online';
                    $isPaid = $status === 'paid';

                    if ($isCod) {
                        $paymentBadgeClass = 'bg-success text-white';
                        $paymentLabel = 'Cash on Delivery';
                        $paymentIcon = 'fa-solid fa-truck-fast';
                    } elseif ($isOnline && $isPaid) {
                        $paymentBadgeClass = 'bg-success text-white';
                        $paymentLabel = 'Online Payment (Paid)';
                        $paymentIcon = 'fa-solid fa-circle-check';
                    } elseif ($isOnline && ! $isPaid) {
                        $paymentBadgeClass = 'bg-warning text-dark';
                        $paymentLabel = 'Online Payment (Pending)';
                        $paymentIcon = 'fa-solid fa-hourglass-half';
                    } else {
                        $paymentBadgeClass = 'bg-secondary text-white';
                        $paymentLabel = ucfirst($order->payment_method ?? 'Payment Method');
                        $paymentIcon = 'fa-solid fa-wallet';
                    }
                @endphp
                <div class="p-4 border rounded-3 shadow-sm">
                    <h4 class="fw-bold mb-3">Order Summary</h4>
                    <p class="mb-1"><strong>Subtotal:</strong> ₹{{ number_format($order->subtotal, 2) }}</p>
                    <p class="mb-1"><strong>Delivery Fee:</strong> ₹{{ number_format($order->delivery_fee, 2) }}</p>
                    <p class="mb-1"><strong>Taxes:</strong> ₹{{ number_format($order->sgst + $order->cgst + $order->igst, 2) }}</p>
                    <hr>
                    <h3 class="fw-bold text-success mb-0">
                        ₹{{ number_format($order->grand_total, 2) }}
                    </h3>
                    <div class="mt-3">
                        <span class="badge {{ $paymentBadgeClass }} px-3 py-2 rounded-pill">
                            <i class="{{ $paymentIcon }} me-1"></i> {{ $paymentLabel }}
                        </span>
                        <div class="mt-2 small text-muted">
                            Payment mode: <strong>{{ strtoupper($order->payment_method ?? 'N/A') }}</strong> |
                            Payment status: <strong>{{ ucfirst($order->payment_status ?? 'N/A') }}</strong>
                        </div>
                    </div>
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

