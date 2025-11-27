@extends('frontend.layouts.app')
@section('content')
    <style>
        .contact-card {
            border: 0;
            border-radius: 1rem;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .contact-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1.2rem rgba(0, 0, 0, .08);
        }

        .icon-wrap {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            border-radius: 1rem;
            background: var(--primary-color);
            color: #fff;
        }

        .icon-wrap.mail {
            background: var(--primary-color);
        }

        .contact-link {
            word-break: break-word;
        }
    </style>
    </head>

    <body>

        <section class="py-5">
            <div class="container">
                <!-- Page Title -->
                <div class="text-center mb-5">
                    <h1 class="fw-bold mb-2">Get in Touch</h1>
                    <p class="text-muted mb-0">Reach us via phone or email — we’d love to hear from you.</p>
                </div>

                <!-- Contact Cards -->
                <div class="row g-4 justify-content-center">

                    <!-- Phone -->
                    <div class="col-md-6 col-lg-5">
                        <div class="card contact-card h-100 shadow-sm">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="icon-wrap">
                                    <i class="bi bi-telephone fw-bold fs-3 "></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Call Us</h5>
                                    <p class="text-muted mb-2">Mon–Sat, 10:00 AM – 6:00 PM</p>
                                    <a class="contact-link fw-semibold text-decoration-none" href="tel:+919454548741">
                                        +91 94545 48741
                                    </a>
                                </div>
                                <a href="tel:+919454548741" class="btn btn-outline-success">
                                    <i class="bi bi-telephone-outbound me-1 "></i> Call
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 col-lg-5">
                        <div class="card contact-card h-100 shadow-sm">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="icon-wrap mail">
                                    <i class="bi bi-envelope fw-bold fs-3 text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Email Us</h5>
                                    <p class="text-muted mb-2">We typically reply within 24 hours</p>
                                    <a class="contact-link fw-semibold text-decoration-none"
                                        href="mailto:mriduukriti@gmail.com">
                                        mriduukriti@gmail.com
                                    </a>
                                </div>
                                <a href="mailto:mriduukriti@gmail.com" class="btn btn-outline-primary">
                                    <i class="bi bi-send me-1"></i> Email
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Optional small note -->
                <div class="text-center text-muted small mt-4">
                    Prefer WhatsApp? Reach us on the number above.
                </div>
            </div>
        </section>
    @endsection
