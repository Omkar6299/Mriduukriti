@extends('admin_panel.layouts.app')
@section('title', 'Orders')
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
                {{-- <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">add</i> Add New Product
                </a> --}}
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table  align-middle mb-0" id="dataTables">
                                <thead class="bg-gradient-primary ">
                                    <tr>
                                        <th class="text-white">#</th>
                                        <th class="text-white">Order Number</th>
                                        <th class="text-white">Date</th>
                                        <th class="text-white">Order Status</th>
                                        <th class="text-white">Grand Total (₹)</th>
                                        <th class="text-white">Payment Mode</th>
                                        <th class="text-white">Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($order as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-wrap">
                                                <a href="{{ route('orders.detail', ['order_no' => $item->order_number]) }}"
                                                    class="text-primary text-decoration-none fw-semibold">
                                                    {{ $item->order_number ?? '' }}
                                                </a>
                                            </td>

                                            <td class="">{{ $item->created_at->format('d F Y') ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ \Illuminate\Support\Str::ucfirst($item->status ?? '-') }}</td>
                                            <td class="text-center">₹{{ $item->grand_total ?? '-' }}</td>
                                            <td class="text-center">{{ $item->payment_method ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ \Illuminate\Support\Str::ucfirst($item->payment_status ?? '-') }}</td>
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
