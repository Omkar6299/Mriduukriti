@extends('admin_panel.layouts.app')
@section('title', 'Subscriber')
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
                <a href="{{ route('subscribers.create') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded ">campaign</i> Campaign for Subscriber
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
                                        <th class="text-white">Email</th>
                                        <th class="text-white">Status</th>
                                        <th class="text-center text-white">Subscribe At</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($subscribers as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{!! $item->subscriber_email ?? '-' !!}</td>
                                            <td>
                                                @if ($item->status == 1)
                                                    <span class="badge bg-success">Subscribe</span>
                                                @else
                                                    <span class="badge bg-danger">Unsubscribe</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $item->created_at ? $item->created_at->format('m-d-Y') : '-' }}</td>

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
