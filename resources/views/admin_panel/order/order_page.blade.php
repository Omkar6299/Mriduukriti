@extends('admin_panel.layouts.app')
@section('title', 'Order Detail')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .track {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }

        .track .step {
            text-align: center;
            position: relative;
            flex: 1;
        }

        .track .step::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 50%;
            height: 5px;
            width: 100%;
            background: #dee2e6;
            z-index: -1;
            transform: translateX(-50%);
        }

        .track .step:last-child::before {
            display: none;
        }

        .track .step .icon {
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            display: inline-block;
            color: #6c757d;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .track .step.active .icon {
            background: var(--bs-primary);
            color: #fff;
        }

        .track .step.active span {
            font-weight: 600;
            color: var(--bs-primary);
        }

        /* Redesign the form-select */
        .form-select {
            padding: 6px 12px !important;
            border-radius: 12px;
            /* curved corners */
            border: 1px solid #ddd;
            background-color: #fff;
            transition: all 0.3s ease-in-out;
        }

        /* Focus effect */
        .form-select:focus {
            border-color: var(--bs-primary) !important;
            /* pink border on focus */
            box-shadow: 0 0 5px rgba(255, 102, 178, 0.4);
        }

        /* Dropdown option styling */
        .form-select option {
            padding: 10px;
            border-radius: 8px;
            /* curved option edges */
        }

        /* Hover effect on dropdown options */
        .form-select option:hover {
            background-color: var(--bs-primary) !important;
            /* pink background */
            color: #fff;
            /* white text */
        }
    </style>
    </head>

    <body class="bg-light">

        <div class="container py-5">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary"><i class="bi bi-bag-check-fill me-2"></i> Order Details</h2>
                <a href="#" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
            </div>
            <div class="row">
                <div class="col-md-9">
                    @php
                        // Define the steps
                        $steps = [
                            'pending' => ['icon' => 'bi-hourglass-split', 'label' => 'Pending'],
                            'confirmed' => ['icon' => 'bi-check2', 'label' => 'Confirmed'],
                            'shipped' => ['icon' => 'bi-truck', 'label' => 'Shipped'],
                            'delivered' => ['icon' => 'bi-check2-circle', 'label' => 'Delivered'],
                        ];

                        // Cancelled step (alwasy separate)
                        $cancelledStep = ['icon' => 'bi-x-circle', 'label' => 'Cancelled'];

                        // Get current status
                        $currentStatus = strtolower($order->status ?? 'pending');

                        // Define order of statuses
                        $statusOrder = array_keys($steps);

                        // Find the current index
                        $currentIndex = array_search($currentStatus, $statusOrder);
                    @endphp

                    <!-- Tracking System -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <i class="bi bi-truck me-2"></i> Tracking Status
                        </div>
                        <div class="card-body">
                            <div class="track">

                                {{-- If Cancelled → Only show Cancelled --}}
                                @if ($currentStatus === 'cancelled')
                                    <div class="step active text-danger">
                                        <div class="icon"><i class="bi {{ $cancelledStep['icon'] }}"></i></div>
                                        <span>{{ $cancelledStep['label'] }}</span>
                                    </div>
                                @else
                                    {{-- Otherwise show normal flow --}}
                                    @foreach ($steps as $key => $step)
                                        <div class="step {{ $loop->index <= $currentIndex ? 'active' : '' }}">
                                            <div class="icon"><i class="bi {{ $step['icon'] }}"></i></div>
                                            <span>{{ $step['label'] }}</span>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-body row g-4">
                            <div class="col-md-3">
                                <p class="mb-1 text-muted">Order No</p>
                                <h6 class="fw-bold">#{{ $order->order_number ?? '' }}</h6>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1 text-muted">Date</p>
                                <h6 class="fw-bold">{{ $order->created_at->format('d F Y') }}</h6>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1 text-muted">Status</p>
                                <span class="badge rounded-pill bg-success px-3 py-2">{{ $order->status ?? '' }}</span>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1 text-muted">Payment</p>
                                <span
                                    class="badge rounded-pill bg-primary px-3 py-2">{{ $order->payment_status ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <i class="bi bi-person-fill me-2"></i> Customer Information / Mailing Address
                        </div>
                        <div class="card-body row g-4">
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Name</p>
                                <h6 class="fw-semibold">{{ $user->userAddress->name ?? '' }}</h6>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Email</p>
                                <h6 class="fw-semibold">{{ $user->email ?? '' }}</h6>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Phone</p>
                                <h6 class="fw-semibold">{{ $user->userAddress->phone ?? '' }}</h6>
                            </div>
                            <div class="col-4">
                                <p class="text-muted mb-1">Address Type</p>
                                <h6 class="fw-semibold">{{ $user->userAddress->type ?? '' }}</h6>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1">Address</p>
                                <h6 class="fw-semibold">{{ $user->userAddress->address_line_1 ?? '' }},
                                    {{ $user->userAddress->address_line_2 ?? '' }}, {{ $user->userAddress->city ?? '' }},
                                    {{ $user->userAddress->state ?? '' }}-{{ $user->userAddress->postal_code ?? '' }}</h6>
                            </div>

                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <i class="bi bi-cart-fill me-2"></i> Order Items
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $item->product->name ?? 'N/A' }}</strong></td>
                                            <td>{{ $item->sku->sku_code ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>₹{{ number_format($item->price, 2) }}</td>
                                            <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-semibold">Subtotal</td>
                                        <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-semibold">Shipping</td>
                                        <td>₹{{ number_format($order->delivery_fee ?? 0, 2) }}</td>
                                    </tr>
                                    @if ($order->sgst > 0)
                                        <tr>
                                            <td colspan="5" class="text-end fw-semibold">SGST</td>
                                            <td>₹{{ number_format($order->sgst, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($order->cgst > 0)
                                        <tr>
                                            <td colspan="5" class="text-end fw-semibold">CGST</td>
                                            <td>₹{{ number_format($order->cgst, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($order->igst > 0)
                                        <tr>
                                            <td colspan="5" class="text-end fw-semibold">IGST</td>
                                            <td>₹{{ number_format($order->igst, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <td colspan="5" class="text-end fw-bold">Grand Total</td>
                                        <td class="fw-bold text-success">₹{{ number_format($order->grand_total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="text-end d-flex justify-content-end gap-2">

                        {{-- Cancel Order --}}
                        <form action="{{ route('orders.orderUpdateStatus', $order->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle me-1"></i> Cancel Order
                            </button>
                        </form>

                        {{-- Mark as Completed --}}
                        <form action="{{ route('orders.orderUpdateStatus', $order->id) }}" method="POST"
                            onsubmit="return confirm('Mark this order as completed?');">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check2-circle me-1"></i> Mark as Completed
                            </button>
                        </form>

                    </div>

                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <i class="bi bi-cart-fill me-2"></i> Status Update
                        </div>
                        <div class="card-body">
                            <form action="{{ route('orders.orderUpdateStatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <!-- Status Dropdown -->
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-semibold">Order Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>
                                            Confirmed</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>
                                            Shipped
                                        </option>
                                        <option value="delivered" disabled
                                            {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="cancelled" disabled
                                            {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>

                                <!-- Payment Status Dropdown -->
                                {{-- <div class="mb-3">
                                    <label for="payment_status" class="form-label fw-semibold">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-select">
                                        <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>
                                            Unpaid</option>
                                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="refunded"
                                            {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </div> --}}

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endsection
