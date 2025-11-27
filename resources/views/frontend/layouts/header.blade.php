 <div class="topbar">
     Delivering Worldwide !
 </div>
 <!-- Header -->

 <nav id="mainNavbar" class="navbar navbar-expand-lg shadow-sm">

     <div class="container">
         <a class="navbar-brand" href="/">
             <img src="{{ asset('frontend/assets/images/logo/mriduukriti logo_1.jpg') }}" class="w-100" alt="Mriduukriti">
         </a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
             <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse justify-content-between" id="navbarContent">
             <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                 <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('frontend.shop.list') }}">Shop</a></li>
                 {{-- <li class="nav-item"><a class="nav-link" href="#">Sale</a></li> --}}
                 <li class="nav-item"><a class="nav-link" href="{{ route('frontend.faqs.index') }}">FAQs</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('frontend.about.index') }}">About Us</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{ route('frontend.contact.index') }}">Contact Us</a>
                 </li>
             </ul>

             <div class="icon-group d-flex align-items-center">
                 <i class="bi bi-search"></i>
                 <a href="{{ route('user_dashboard') }}#v-pills-wishlist">

                     <i class="bi bi-heart position-relative">
                         <span class="badge rounded-pill position-absolute top-0 start-100 translate-middle"
                             id="wishlist-count">
                             {{ Auth::guard('customer')->check()
                                 ? \App\Models\Whistlist::where('user_id', Auth::guard('customer')->id())->count()
                                 : 0 }}
                         </span>
                     </i>
                 </a>

                 <a href="{{ route('cart.index') }}">
                     <i class="bi bi-cart">
                         <span class="badge" id="cart-count">
                             {{ Auth::guard('customer')->check()
                                 ? \App\Models\CartItem::whereHas('cart', function ($q) {
                                     $q->where('user_id', Auth::guard('customer')->id())->where('status', 'active');
                                 })->sum('quantity')
                                 : collect(session('cart', []))->sum('quantity') }}

                         </span>
                     </i>
                 </a>
                 @if (Route::has('landingPage'))
                     @auth('customer')
                         <a href="{{ route('user_dashboard') }}"
                             class="d-inline-flex align-items-center ms-3 py-1 rounded text-white text-decoration-none"
                             style="background-color:var(--primary-color)">
                             <i class="bi bi-person text-white mx-2"></i>
                             <span class="me-2 text-white">{{ Auth::guard('customer')->user()->name }}</span>
                         </a>
                     @else
                         <a href="" data-bs-toggle="modal" data-bs-target="#loginModal">
                             <i class="bi bi-person"></i>
                         </a>
                     @endauth
                 @endif

             </div>
         </div>
     </div>
 </nav>

 {{-- Login & Register Popup --}}
 <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content rounded-4 shadow">

             <!-- Header -->
             <div class="modal-header border-0">
                 <h5 class="modal-title fw-bold" id="loginModalLabel">Welcome Back 👋</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>

             <!-- Body -->
             <div class="modal-body px-4">
                 <form action="{{ route('customer.loginPost') }}" method="POST">
                     @csrf
                     <!-- Email -->
                     <div class="mb-3">
                         <label for="email" class="form-label fw-semibold">Email Address</label>
                         <input type="email" name="email" class="form-control rounded-3" id="email"
                             placeholder="john@gmail.com" required>
                     </div>

                     <!-- Password -->
                     <div class="mb-3">
                         <label for="password" class="form-label fw-semibold">Password</label>
                         <input type="password" name="password" class="form-control rounded-3" id="password"
                             placeholder="••••••••" required>
                     </div>
                     <!-- Remember & Forgot -->
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="rememberMe">
                             <label class="form-check-label" for="rememberMe">Remember me</label>
                         </div>
                         <a href="#" class="text-decoration-none small">Forgot Password?</a>
                     </div>

                     <!-- Login Button -->
                     <button type="submit" class="btn btn-dark w-100 rounded-3">Login</button>
                 </form>

                 <!-- Divider -->
                 <div class="text-center my-3">
                     <span class="text-muted">or</span>
                 </div>

                 <!-- Social Login -->
                 <div class="d-grid gap-2">


                     <a href="{{ route('google.login') }}" class="btn btn-primary-dark rounded-3"><i
                             class="bi bi-google me-2"></i> Continue with
                         Google</a>
                 </div>
             </div>

             <!-- Footer -->
             {{-- <div class="modal-footer border-0 justify-content-center">
                 <p class="small mb-0">Don’t have an account? <a href="#" class="fw-semibold">Sign up</a></p>
             </div> --}}

         </div>
     </div>
 </div>


 {{-- check out popup --}}
 <!-- Toast Container (bottom right) -->
 <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
     <div id="cartToast" class="toast align-items-center text-bg-light border-0 shadow" role="alert"
         aria-live="assertive" aria-atomic="true">
         <div class="d-flex align-items-center">
             <img id="cartToastImage" src="" alt="Product"
                 style="width: 60px; height: 60px; object-fit: cover;" class="rounded-start m-1">
             <div class="toast-body">
                 <span id="cartToastName"></span>
                 <div class="mt-2">
                     <a href="{{ route('cart.index') }}" class="btn btn-sm btn-dark">Checkout</a>
                 </div>
             </div>
             <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                 aria-label="Close"></button>
         </div>
     </div>
 </div>
