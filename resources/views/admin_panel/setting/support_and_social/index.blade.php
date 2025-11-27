@extends('admin_panel.layouts.app')
@section('title', 'Setting')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* .sidebar {
                                                                min-height: 100vh;
                                                                background: #fff;
                                                                border-right: 1px solid #dee2e6;
                                                                padding: 15px;
                                                            }

                                                            .sidebar .nav-link {
                                                                color: #333;
                                                                padding: 10px;
                                                                border-radius: 8px;
                                                                font-weight: 500;
                                                            }

                                                            .sidebar .nav-link:hover,
                                                            .sidebar .nav-link.active {
                                                                background: #f1f3f5;
                                                            } */

        .card {
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }

        .social-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            color: #fff;
            border-radius: 50%;
            font-size: 16px;
        }
    </style>
    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            {{-- <div class="col-md-3 col-lg-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-shop me-2"></i> Store
                            details</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-globe me-2"></i> Domains</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-people me-2"></i> Staffs
                            accounts</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-bell me-2"></i>
                            Notifications</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-credit-card me-2"></i>
                            Payments</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-cart-check me-2"></i>
                            Checkout</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-box-seam me-2"></i>
                            Warehouse</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-truck me-2"></i> Delivery</a>
                    </li>
                    <li class="nav-item"><a href="#" class="nav-link"><i
                                class="bi bi-arrow-counterclockwise me-2"></i> Returns</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-percent me-2"></i> Tax</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-cash-coin me-2"></i> Extra
                            charges</a></li>
                </ul>
            </div> --}}

            <!-- Main Content -->
            {{-- <div class="col-md-9 col-lg-10 p-4"> --}}
            <div class="col-md-12 col-lg-4 p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card p-4 mb-2">
                        <h6 class="fw-bold">Website Branding</h6>
                        <p class="text-muted small">Upload your website logo and favicon for better brand recognition.</p>

                        <div class="row g-3">
                            <!-- Logo Upload -->
                            <div class="col-md-8">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" class="form-control-file" accept="image/*"
                                    onchange="previewImage(event, 'logoPreview')">
                            </div>
                            <div class="col-md-4">
                                <img id="logoPreview" src="{{ asset('storage/logo.png') }}" alt="Logo Preview"
                                    class="img-fluid rounded border d-none" style="max-height:100px;">
                            </div>

                            <!-- Favicon Upload -->
                            <div class="col-md-8">
                                <label class="form-label">Favicon</label>
                                <input type="file" name="favicon" class="form-control-file" accept="image/*"
                                    onchange="previewImage(event, 'faviconPreview')">
                            </div>
                            <div class="col-md-4  ">
                                <img id="faviconPreview" src="{{ asset('storage/favicon.png') }}" alt="Favicon Preview"
                                    class="img-fluid rounded border d-none" style="max-height:60px; max-width:60px;">
                            </div>
                        </div>
                    </div>
                    <div class=" text-end">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>






            <div class="col-md-12 col-lg-8 p-4">
                <form action="{{ route('setting.store') }}" method="POST">
                    @csrf
                    <!-- Customer Support -->
                    <div class="card p-4 mb-4">
                        <h6 class="fw-bold">Customer support</h6>
                        <p class="text-muted small">Stay connected and responsive to your customers’ needs.</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" placeholder="Email address"
                                    value="{{ old('email', $customerSupport->email ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile number</label>
                                <input type="text" name="contact" class="form-control" placeholder="+91-0123456789"
                                    value="{{ old('contact', $customerSupport->contact ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Social Profiles -->
                    <div class="card p-4 mb-4">
                        <h6 class="fw-bold">Social profiles</h6>
                        <p class="text-muted small">Connect with customers and grow your online presence.</p>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <div class="social-icon"><i class="bi bi-facebook"></i></div>
                            </div>
                            <div class="col">
                                <input type="text" name="facebook" class="form-control" placeholder="Enter Facebook URL"
                                    value="{{ old('facebook', $socialProfiles->facebook_link ?? '') }}">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <div class="social-icon"><i class="bi bi-twitter-x"></i></div>
                            </div>
                            <div class="col">
                                <input type="text" name="twitter" class="form-control" placeholder="Enter Twitter URL"
                                    value="{{ old('twitter', $socialProfiles->twitter_link ?? '') }}">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <div class="social-icon"><i class="bi bi-instagram"></i></div>
                            </div>
                            <div class="col">
                                <input type="text" name="instagram" class="form-control"
                                    placeholder="Enter Instagram URL"
                                    value="{{ old('instagram', $socialProfiles->instagram_link ?? '') }}">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <div class="social-icon"><i class="bi bi-linkedin"></i></div>
                            </div>
                            <div class="col">
                                <input type="text" name="linkedin" class="form-control"
                                    placeholder="Enter LinkedIn URL"
                                    value="{{ old('linkedin', $socialProfiles->linkedin_link ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-save me-2"></i>Save
                        </button>
                    </div>
                </form>


            </div>

        </div>
    </div>

@endsection
@push('scripts')
    <script>
        function previewImage(event, previewId) {
            const input = event.target;
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById(previewId);
                output.src = reader.result;
                output.classList.remove("d-none");
            };
            if (input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
