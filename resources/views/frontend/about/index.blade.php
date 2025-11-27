@extends('frontend.layouts.app')
@section('content')
    <!-- About Us Page -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold">About Us</h1>
                <p class="text-muted">Learn more about who we are, what we stand for, and how we serve our customers.</p>
            </div>

            <!-- Mission Section -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <img src="{{ asset('frontend/assets/images/about/aboutimage.jpg') }}" alt="Our Story"
                        class="img-fluid rounded shadow">
                </div>
                <div class="col-md-6">
                    <h3 class="fw-bold">Our Vision </h3>
                    <p>This vision of <strong>MRIDUUKRITI</strong> is not just a mere saree shop but it's an effort towords
                        promoting our heritage, cultivating our tradition and giving honour to our rich craftsmanship.
                        Mriduukriti sarees celebrates our women who reimagined the world on their own terms and are still
                        holding the ground that gives them wings to fly.We the Mriduukriti from Banaras are proud that we
                        have this opportunity to provide pure banarsi sarees to the world, wether it's a rich banarasi Zari
                        and Resham work, pure khaddi, tissue, Katan and chiffon,Chiniya Silk, inspired drape or budget
                        friendly beauty for your everyday moments. Our sarees are thoughtfully curated for every women out
                        there.
                        We understand that for you it's not just a saree it's an emotion which you carry with utmost grace
                        and elegance..
                        From our loom to your wardrobe presenting heritage and tradition wraped in elegance and purity
                        for you from us...</p>
                    <ul class="list-unstyled">
                        <li>✔ Customer satisfaction</li>
                        <li>✔ Reliable delivery & support</li>
                        <li>✔ Secure and easy checkout</li>
                    </ul>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="row text-center mb-5">
                <div class="col-md-3 col-6 mb-4">
                    <h2 class="fw-bold">1M+</h2>
                    <p class="text-muted">Products Sold</p>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <h2 class="fw-bold">500K+</h2>
                    <p class="text-muted">Happy Customers</p>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <h2 class="fw-bold">99%</h2>
                    <p class="text-muted">Positive Reviews</p>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <h2 class="fw-bold">24/7</h2>
                    <p class="text-muted">Support Available</p>
                </div>
            </div>

            <!-- Team Section -->
            <div class="mb-5">
                <h3 class="fw-bold text-center mb-4">Meet Our Team</h3>
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-4">
                        <img src="https://avatar-placeholder.iran.liara.run/logo.png" class="rounded-circle mb-2"
                            alt="Team Member">
                        <h6 class="fw-bold">Mridula Missra</h6>
                        <p class="text-muted small">CEO & Founder</p>
                    </div>
                    <div class="col-md-3 col-6 mb-4">
                        <img src="https://avatar-placeholder.iran.liara.run/logo.png" class="rounded-circle mb-2"
                            alt="Team Member">
                        <h6 class="fw-bold">Nikita Verma</h6>
                        <p class="text-muted small">Head of Marketing</p>
                    </div>
                    <div class="col-md-3 col-6 mb-4">
                        <img src="https://avatar-placeholder.iran.liara.run/logo.png" class="rounded-circle mb-2"
                            alt="Team Member">
                        <h6 class="fw-bold">Aman Gupta</h6>
                        <p class="text-muted small">Product Manager</p>
                    </div>
                    <div class="col-md-3 col-6 mb-4">
                        <img src="https://avatar-placeholder.iran.liara.run/logo.png" class="rounded-circle mb-2"
                            alt="Team Member">
                        <h6 class="fw-bold">Priya Yadav</h6>
                        <p class="text-muted small">Customer Support</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center bg-white p-5 rounded shadow">
                <h4 class="fw-bold">Want to know more or work with us?</h4>
                <p>We love connecting with customers and partners. Feel free to reach out.</p>
                <a href="" class="btn btn-outline-primary ">Contact Us</a>
            </div>
        </div>
    </section>
@endsection
