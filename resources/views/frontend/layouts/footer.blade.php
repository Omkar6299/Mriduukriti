@php
    use App\Models\SocialProfiles;
    use App\Models\CustomerSupport;
    $customerSupport = CustomerSupport::first();
    $socialProfileslinks = SocialProfiles::first();

@endphp
<!-- Footer -->
<footer class="" id="subcriber">
    <div class="container py-5">
        <div class="row gy-4">

            <!-- Logo and Email -->
            <div class="col-md-3">
                <img src="{{ asset('frontend/assets/images/logo/mriduukriti logo_1.jpg') }}" alt="Logo"
                    style="width: 110px;border-radius:50%;" class="mb-3">
                <!-- Replace with actual logo -->
                <div class="d-flex align-items-center">
                    <i class="bi bi-envelope me-2"></i>
                    <a href="mailto:{{ $customerSupport->email ?? '' }}"
                        class="text-decoration-none text-white">{{ $customerSupport->email ?? '' }}</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-md-2">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="/" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="{{ route('frontend.shop.list') }}"
                            class="text-white text-decoration-none">Collections</a></li>
                    <li><a href="{{ route('frontend.about.index') }}" class="text-white text-decoration-none">About
                            Us</a></li>
                    <li><a href="{{ route('frontend.faqs.index') }}" class="text-white text-decoration-none">FAQs</a>
                    </li>
                    <li><a href="{{ route('frontend.contact.index') }}"
                            class="text-white text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <!-- Site Links -->
            <div class="col-6 col-md-2">
                <h6 class="fw-bold">Site Links</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Privacy Policy</a></li>
                    <li><a href="{{ route('frontend.other_pages.term_condition') }}"
                            class="text-white text-decoration-none">Terms and Conditions</a></li>
                    <li><a href="{{ route('frontend.other_pages.shipping') }}"
                            class="text-white text-decoration-none">Shipping Policy</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Returns & Refund Policy</a></li>
                </ul>
            </div>

            <!-- Category Links -->
            <div class="col-md-2">
                <h6 class="fw-bold">Category Links</h6>
                @php
                    use App\Models\Category;

                    $categories = Category::where('status', 1)->orderBy('created_at', 'DESC')->paginate(6);
                @endphp
                <ul class="list-unstyled">
                    @foreach ($categories as $item)
                        <li><a href="{{ route('frontend.shop.list', ['category' => $item->id]) }}"
                                class="text-white text-decoration-none">{{ $item->name }}</a></li>
                    @endforeach

                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-md-3">
                <h6 class="fw-bold">Newsletter</h6>
                <p class="small">Be the first to know! Get exclusive updates on offers and sales straight to your
                    inbox.</p>


                <form action="{{ route('new_letter_subscribe.store') }}#subcriber" method="POST">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="email" class="form-control rounded-start-pill" required
                            placeholder="Your email address" name="subscriber_email">
                        <button class="btn btn-dark rounded-end-pill px-4 fw-bold" type="submit">Subscribe</button>

                    </div>
                    @error('subscriber_email')
                        <p>{{ $message }}</p>
                    @enderror
                    <div class="form-check small">
                        <input class="form-check-input" type="checkbox" required id="agreeCheck">
                        <label class="form-check-label" for="agreeCheck">
                            I agree with the terms and conditions.
                        </label>
                    </div>
                </form>
                @if (session('success_subcriber'))
                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                        {{ session('success_subcriber') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

            </div>

        </div>
        <!-- Bottom Footer -->
    </div>
</footer>
<div class="container">
    <div class="row  align-items-center border-top ">
        <div class="col-md-6 small text-muted">
            &copy; 2025 <span class="text-white">Mriduukriti</span><br>
            Design & Developed By : <a href="https://thetabyte.com/" target="_blank"
                class="text-decoration-none text-primary">ThetaByte Solutions Pvt. Ltd.</a>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <img src="{{ asset('frontend/assets/images/payment/paymentmodes.webp') }}" alt="Payments"
                style="height: 25px;">
            <!-- Replace with actual image -->
            <div class="mt-2">
                <span class="me-2">Find us on</span>


                <a href="{{ $socialProfileslinks->instagram_link ?? '' }}" target="_blank"
                    class="text-dark fs-5 me-2"><i class="bi bi-instagram"></i></a>
                <a href="{{ $socialProfileslinks->facebook_link ?? '' }}" target="_blank" class="text-dark fs-5"><i
                        class="bi bi-facebook"></i></a>
            </div>
        </div>
    </div>
</div>
