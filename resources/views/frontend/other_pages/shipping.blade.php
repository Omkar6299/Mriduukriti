@extends('frontend.layouts.app')

@section('title', 'Shipping Policy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            
            <!-- Page Title -->
            <div class="mb-5 text-center">
                <h1 class="fw-bold mb-3">Shipping Policy</h1>
                <p class="text-muted">Please read our shipping terms carefully before placing an order.</p>
            </div>

            <!-- Shipping Policy Content -->
            <div class="card border-0 shadow-sm p-4">

                <!-- Domestic Shipping -->
                <h4 class="fw-semibold mb-3">Domestic Shipping</h4>
                <ul class="list-unstyled">
                    <li>✔ Orders are shipped only through registered domestic courier companies and/or Speed Post.</li>
                    <li>✔ All orders are processed and shipped within <strong>8+ days</strong> from the date of order confirmation and payment, or as per the delivery date agreed at the time of order confirmation.</li>
                    <li>✔ Delivery timelines are subject to courier company or postal authority norms.</li>
                    <li>✔ <strong>Mriduukriti</strong> is not liable for delays caused by courier companies or postal authorities. Our responsibility is limited to handing over the consignment within the agreed dispatch time.</li>
                </ul>

                <hr>

                <!-- Delivery Address -->
                <h4 class="fw-semibold mb-3">Delivery Address</h4>
                <p>
                    All orders will be delivered to the shipping address provided by the buyer at the time of order.
                    Delivery confirmation will be sent to your registered email ID provided during registration.
                </p>

                <hr>

                <!-- Support -->
                <h4 class="fw-semibold mb-3">Support</h4>
                <p>
                    For any issues in using our services or tracking your shipment, you may contact our helpdesk:
                </p>
                <ul class="list-unstyled mb-3">
                    <li>📞 <strong>+91-9454548741</strong></li>
                    <li>📧 <strong>mriduukriti@gmail.com</strong></li>
                </ul>

                <hr>

                <!-- Shipping Rates & Delivery Estimates -->
                <h4 class="fw-semibold mb-3">Shipping Rates & Delivery Estimates</h4>
                <p>
                    Shipping charges and estimated delivery times will be displayed at checkout before you complete your purchase. 
                    Delivery estimates may vary depending on the destination and courier service availability.
                </p>

            </div>

        </div>
    </div>
</div>
@endsection
