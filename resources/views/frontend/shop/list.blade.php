@extends('frontend.layouts.app')
@section('content')
    <section class="container-fluid">
        <div class="collection-title">Products</div>
        <div class="row">
            <div class="col-md-3">
                <form method="GET" action="{{ route('frontend.shop.list') }}">

                    <div class="card shadow-sm p-3 mb-4">
                        <h5 class="mb-3">Filters</h5>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Category</h6>
                            @foreach ($category as $item)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cat{{ $item->id }}"
                                        name="category[]" value="{{ $item->id }}"
                                        {{ in_array($item->id, (array) request()->category) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cat{{ $item->id }}">
                                        {{ $item->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Filter -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Price</h6>
                            <div class="d-flex align-items-center mb-2">
                                <input type="number" class="form-control me-2" id="minPrice" name="min_price"
                                    value="{{ request('min_price', 0) }}" min="0" max="10000">
                                <span class="mx-1">to</span>
                                <input type="number" class="form-control ms-2" id="maxPrice" name="max_price"
                                    value="{{ request('max_price', 10000) }}" min="0" max="10000">
                            </div>
                            <input type="range" class="form-range" min="0" max="10000" step="100"
                                id="priceRange" value="{{ request('max_price', 10000) }}">
                            <div class="d-flex justify-content-between small">
                                <span id="minPriceLabel">₹{{ request('min_price', 0) }}</span>
                                <span id="maxPriceLabel">₹{{ request('max_price', 10000) }}</span>
                            </div>
                        </div>

                        <!-- Availability Filter -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Availability</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="inStock" name="in_stock"
                                    {{ request('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label" for="inStock">In Stock</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100 mt-3">Apply Filters</button>
                    </div>
                </form>


            </div>
            <div class="col-md-9 row pb-3" id="product-list">

                @include('frontend.shop.partials.products')

                <div class="text-center my-4">
                    @if ($products->hasMorePages())
                        <button id="load-more" data-next-page="{{ $products->currentPage() + 1 }}"
                            class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                            View More
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const priceRange = document.getElementById('priceRange');
        const minInput = document.getElementById('minPrice');
        const maxInput = document.getElementById('maxPrice');
        const minLabel = document.getElementById('minPriceLabel');
        const maxLabel = document.getElementById('maxPriceLabel');

        // Update the labels when number inputs change
        minInput.addEventListener('input', () => {
            minLabel.textContent = `₹${minInput.value}`;
        });

        maxInput.addEventListener('input', () => {
            maxLabel.textContent = `₹${maxInput.value}`;
            priceRange.value = maxInput.value;
        });

        // Optionally update maxInput when slider moves
        priceRange.addEventListener('input', () => {
            maxInput.value = priceRange.value;
            maxLabel.textContent = `₹${priceRange.value}`;
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '#load-more', function() {
            let button = $(this);
            let nextPage = button.data('next-page');

            $.ajax({
                url: "{{ route('frontend.shop.list') }}?page=" + nextPage,
                type: 'GET',
                beforeSend: function() {
                    button.text('Loading...').prop('disabled', true);
                },
                success: function(data) {
                    $('#product-list').append(data);

                    let totalPages = {{ $products->lastPage() }};
                    if (nextPage >= totalPages) {
                        button.remove();
                    } else {
                        button.data('next-page', nextPage + 1)
                            .text('View More')
                            .prop('disabled', false);
                    }
                },
                error: function() {
                    button.text('View More').prop('disabled', false);
                }
            });
        });
    </script>


@endpush
