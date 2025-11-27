@forelse ($products as $item)
    <div class="col-md-4 mb-3">
        <div class="product-card product-hover bg-light position-relative">
            @php
                $imagePath = public_path($item->productSkus->image);
            @endphp

            <img src="{{ file_exists($imagePath) && $item->productSkus->image ? asset($item->productSkus->image) : asset('admin_panel/assets/img/default/default_pro.png') }}"
                class="w-100" alt="{{ $item->name ?? 'Product Image' }}">

            {{-- Top badges (stock left, favorite right) --}}
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
