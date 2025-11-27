@extends('admin_panel.layouts.app')
@section('title', 'Products')
@section('content')
    <style>
        .form-control {
            border: 1px solid #00000021 !important;
            border-radius: 8px;
        }

        .page-link.active,
        .active>.page-link {
            z-index: 3;
            color: var(--bs-pagination-active-color) !important;
            background-color: var(--bs-pagination-active-bg);
            border-color: var(--bs-pagination-active-border-color);
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">add</i> Add New Product
                </a>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table  align-middle mb-0" id="dataTables">
                                <thead class="bg-gradient-primary ">
                                    <tr>
                                        <th class="text-white">#</th>
                                        <th class="text-white">Product</th>
                                        <th class="text-white">Price</th>
                                        <th class="text-white">Inventory</th>
                                        <th class="text-white">Status</th>
                                        <th class="text-center text-white">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($products as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td class="text-wrap">
                                                @php
                                                    $imagePath = public_path($item->productSkus->image);
                                                @endphp
                                                <img src="{{ file_exists($imagePath) && $item->productSkus->image ? asset($item->productSkus->image) : asset('admin_panel/assets/img/default/default_pro.png') }}"
                                                    class="img-fluid rounded-circle shadow-sm"
                                                    style="width: 40px; height: 40px; object-fit: cover;" alt="">

                                                {{ $item->name ?? '' }}


                                            </td>
                                            <td>
                                                <span class="text-success">
                                                    ₹{{ number_format($item->productSkus->sale_price ?? 0, 0) }}
                                                </span>
                                                <del>
                                                    ₹{{ number_format($item->productSkus->price ?? 0, 0) }}
                                                </del>
                                            </td>

                                            <td class="">{!! $item->productSkus->stock_quantity ?? '-' !!}</td>
                                            <td>
                                                @if ($item->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a target="_blank"
                                                    href="{{ route('frontend.product.page', ['product_slug' => $item->slug]) }}?product_id={{ $item->id }}"
                                                    class="btn btn-sm btn-outline-success"><i
                                                        class="fa-solid fa-eye"></i></a>
                                                <a href="{{ route('products.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-info"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                <form action="{{ route('products.destroy', $item->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this Product?')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
@endpush
