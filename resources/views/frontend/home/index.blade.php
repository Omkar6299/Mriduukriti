@extends('frontend.layouts.app')
@section('content')
    <style>
        .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #fff;
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .carousel-indicators .active {
            opacity: 1;
            background-color: var(--primary-color);
            /* Bootstrap primary color */
        }
    </style>
    <!-- Hero Banner -->
    @if ($banners->count() > 0)
    <div class="container-fluid p-0">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <!-- Indicators -->
            <div class="carousel-indicators">
                @foreach ($banners as $index => $banner)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                        class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach ($banners as $index => $banner)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset($banner->bannner) }}" class="d-block w-100"
                            alt="{{ $banner->title ?? 'Banner' }}" style="object-fit: cover; height: 500px;">

                        <div
                            class="carousel-caption d-flex flex-column justify-content-center align-items-start h-100 text-start p-4">
                            @if ($banner->title)
                                <h1 class="display-4">{{ $banner->title }}</h1>
                            @endif
                            @if ($banner->paragraph)
                                <p class="lead">{!! $banner->paragraph !!}</p>
                            @endif
                            @if ($banner->link)
                                <a href="{{ $banner->link }}" class="btn btn-primary btn-lg">Learn More</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>


    </div>
    @endif
    

    <!-- Category Circles -->
    @if (!empty($category) && $category->count() > 0)
        <div class="container my-5">
            <div class="collection-title mb-4">Category</div>
            <div class="category-carousel-wrapper">
                <!-- Left Button -->
                <button class="carousel-scroll-btn left" onclick="scrollLeft()">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <!-- Carousel Items -->
                <div class="category-carousel" id="categoryScroll">
                    @foreach ($category as $item)
                        <a href="{{ route('frontend.shop.list', ['category' => $item->id]) }}">
                            <div class="category-carousel-item">
                                <img src="{{ asset($item->image ?: 'admin_panel/assets/img/default/default_pro.png') }}"
                                    alt="{{ $item->name ?? 'Category Image' }}" />
                                <h6 class="text-dark">{{ $item->name ?? 'Unnamed Category' }}</h6>
                            </div>
                        </a>
                    @endforeach


                </div>
                <!-- Right Button -->
                <div class="carousel-scroll-btn right" onclick="scrollRight()">
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
    @endif


    <!-- Section Template -->
    <section class="container">
        <div class="collection-title">New Arrivals</div>
        <div class="row g-3">
            @forelse ($products as $item)
                <div class="col-md-4 mb-3">
                    <div class="product-card product-hover bg-light position-relative">
                        @php
                            $imagePath = public_path($item->productSkus->image);
                        @endphp

                        <img src="{{ file_exists($imagePath) && $item->productSkus->image ? asset($item->productSkus->image) : asset('admin_panel/assets/img/default/default_pro.png') }}"
                            class="w-100" alt="{{ $item->name ?? 'Product Image' }}">

                    <div class="d-flex justify-content-between align-items-start position-absolute top-0 w-100 p-2">
                <span class="preorder-badge">
                    {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $item->productSkus->stock_status ?? '')) }}
                </span>

            <button class="btn btn-light rounded-circle shadow-sm wishlist-btn"
                    title="{{ in_array($item->id, $whislist) ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                    data-id="{{ $item->id }}">
                <i class="bi {{ in_array($item->id, $whislist) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
            </button>

            </div>

                        <div class="hover-buttons">
                            <a class="btn btn-white border"
                                href="{{ route('frontend.product.page', ['product_slug' => $item->slug]) }}?product_id={{ $item->id }}">
                                Quick view
                            </a>

                            {{-- <button class="btn btn-white border">Pre-order</button> --}}
                        </div>

                        <div class="p-2 position-relative">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mt-2 mb-1">{{ $item->name ?? '' }}</h6>
                                 <button class="btn btn-sm rounded-circle add-to-cart"
                        style="background-color: var(--primary-color) !important; color: #fff;" title="Add to Cart"
                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                        data-price="{{ $item->productSkus->sale_price ?? $item->productSkus->price }}"
                        data-image="{{ file_exists($imagePath) && $item->productSkus->image ? asset($item->productSkus->image) : asset('admin_panel/assets/img/default/default_pro.png') }}">
                        <i class="bi bi-cart-plus"></i>
                    </button>

                            </div>
                            @php
                                $price = $item->productSkus->price ?? 0;
                                $salePrice = $item->productSkus->sale_price ?? 0;
                                $discount = 0;

                                if ($price > 0 && $salePrice > 0 && $salePrice < $price) {
                                    $discount = round((($price - $salePrice) / $price) * 100);
                                }
                            @endphp
                            <div>
                                <span class="text-dark fw-medium">
                                    <span class="text-success">
                                        ₹{{ number_format($salePrice, 0) }}
                                    </span>
                                    <del class="text-danger">
                                        ₹{{ number_format($price, 0) }}
                                    </del>
                                    @if ($discount > 0)
                                        <span class="badge bg-success ms-2">
                                            {{ $discount }}% OFF
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <h5 class="text-muted">No Products Found</h5>
                </div>
            @endforelse
            <div class="text-center my-4">
                <a href="{{ route('frontend.shop.list') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">View
                    More</a>
            </div>
        </div>
    </section>
@endsection
