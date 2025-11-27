@extends('frontend.layouts.app')
@section('content')
    <style>
        .error-icon {
            font-size: 90px;
            color: #dc3545;
            animation: pop 0.6s ease forwards;
        }
        @keyframes pop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }
        #error_bg {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
    </style>

    <div class="text-white text-center py-5 position-relative" id="error_bg">
        <i class="bi bi-x-circle-fill error-icon"></i>
        <h1 class="fw-bold mt-3">Payment Failed</h1>
        <p class="lead mb-0">We're sorry, but your payment could not be processed.</p>
        <p class="opacity-75">Order #{{ $order->order_number }}</p>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Payment Unsuccessful</h4>
                    <p>Your payment could not be processed. Please try again or choose a different payment method.</p>
                    @if($payment && $payment->remark)
                        <hr>
                        <p class="mb-0"><strong>Reason:</strong> {{ $payment->remark }}</p>
                    @endif
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Order Details</h5>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Amount:</strong> ₹{{ number_format($order->grand_total, 2) }}</p>
                        @if($payment)
                            <p class="mb-1"><strong>Transaction ID:</strong> {{ $payment->merchant_transaction_id }}</p>
                        @endif
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-danger">Payment Failed</span></p>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('nttdata.payment.initiate', $order->id) }}" class="btn btn-primary btn-lg rounded-pill px-5 me-3">
                        <i class="bi bi-arrow-clockwise me-2"></i> Try Payment Again
                    </a>
                    <a href="{{ route('customer.orderPageCod', $order->id) }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">
                        <i class="bi bi-arrow-left me-2"></i> Back to Order
                    </a>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="fw-bold mb-2">Need Help?</h6>
                    <p class="mb-0 small">If you continue to experience issues, please contact our support team or try using Cash on Delivery option.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

