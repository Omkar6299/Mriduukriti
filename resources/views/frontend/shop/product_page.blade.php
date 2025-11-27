@extends('frontend.layouts.app')
@section('content')
    <style>
        .product-gallery img {
            max-height: 450px;
            object-fit: contain;
        }

        .thumb-img {
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .thumb-img.active {
            border-color: var(--bs-primary);
        }

        .price {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .old-price {
            text-decoration: line-through;
            color: gray;
        }

        /* Zoom CSS */
        .zoom-container {
            overflow: hidden;
            display: inline-block;
            position: relative;
            cursor: zoom-in;
        }

        .zoom-container img {
            transition: transform 0.3s ease;
        }

        .zoom-container:hover img {
            transform: scale(1.8);
            /* Zoom level */
        }
    </style>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="bg-light py-3">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item "><a href="/">Home</a></li>
                <li class="breadcrumb-item "><a href="{{ route('frontend.shop.list') }}">Shop</a></li>
                <li class="breadcrumb-item  active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </div>
    </nav>

    <!-- Product Detail Section -->
    <div class="container py-5">
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6">
                <div class="product-gallery text-center mb-3">
                    @php
                        $imagePath = public_path($product->productSkus->image);
                    @endphp
                    <div class="zoom-container">
                        <img id="mainImage"
                            src="{{ file_exists($imagePath) && $product->productSkus->image ? asset($product->productSkus->image) : asset('admin_panel/assets/img/default/default_pro.png') }}"
                            class="img-fluid rounded" alt="Product Image">
                    </div>
                </div>
            </div>


            <!-- Product Info -->
            <div class="col-md-6">
                <h2>{{ $product->name }}</h2>
                <div class="mb-3">
                    @php
                        $price = $product->productSkus->price ?? 0;
                        $salePrice = $product->productSkus->sale_price ?? 0;
                        $discount = 0;

                        if ($price > 0 && $salePrice > 0 && $salePrice < $price) {
                            $discount = round((($price - $salePrice) / $price) * 100);
                        }
                    @endphp
                    <span class="price text-success"> ₹{{ number_format($product->productSkus->sale_price ?? 0, 0) }}</span>
                    <span class="old-price ms-2">₹{{ number_format($product->productSkus->price ?? 0, 0) }}</span>
                    <span class="badge bg-success ms-2">{{ $discount }}% OFF</span>
                </div>
                <p>
                    {!! $product->short_description !!}
                </p>

                <!-- Quantity Selector -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" id="quantity" class="form-control w-25" min="1" value="1">
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2 mb-3">

                     <button class="btn btn-primary text-primary  add-to-cart" title="Add to Cart"
                        data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                        data-price="{{ $product->productSkus->sale_price ?? $product->productSkus->price }}"
                        data-image="{{ file_exists($imagePath) && $product->productSkus->image ? asset($product->productSkus->image) : asset('admin_panel/assets/img/default/default_pro.png') }}">
                       <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                    
                </div>

                <!-- Product Details -->
                <ul class="list-group mt-4">
                    <li class="list-group-item"><strong>Category:</strong> {{ $category->name ?? '' }}</li>
                    <li class="list-group-item"><strong>SKU:</strong> {{ $product->productSkus->sku_code ?? '' }}</li>
                    <li class="list-group-item"><strong>Stock Status:</strong>
                        {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $product->productSkus->stock_status ?? '')) }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Product Description Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button" role="tab">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                            type="button" role="tab">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content border p-3" id="productTabContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <p>
                            {!! $product->description !!}
                        </p>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <p>No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function changeImage(el) {
            document.getElementById('mainImage').src = el.src;
            document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('mainImage');
            let zoomed = false;

            mainImage.addEventListener('click', function() {
                zoomed = !zoomed;
                mainImage.style.transform = zoomed ? 'scale(1.8)' : 'scale(1)';
            });
        });
    </script>
@endpush
