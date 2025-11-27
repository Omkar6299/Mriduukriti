@extends('admin_panel.layouts.app')
@section('title', 'Users')
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
                {{-- <a href="{{ route('category.create') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">add</i> Add Category
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
                                        <th class="text-white">Name</th>
                                        <th class="text-white">Email</th>
                                        <th class="text-white">Phone</th>
                                        <th class="text-white">Role</th>
                                        <th class="text-center text-white">Registration Date</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($user as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="d-flex align-items-center">
                                                @php
                                                    $imagePath = public_path($item->image);
                                                @endphp
                                                <img src="{{ $item->image ?? asset('admin_panel/assets/img/default/default_pro.png') }}"
                                                    class="img-fluid rounded-circle shadow-sm me-2"
                                                    style="width: 40px; height: 40px; object-fit: cover;"
                                                    alt="{{ $item->name ?? 'Name' }}">
                                                {{ $item->name ?? '' }}

                                            </td>

                                            <td>{!! $item->email ?? '-' !!}</td>
                                            <td>{!! $item->phone ?? '-' !!}</td>
                                            <td>
                                                @if ($item->role == 'Customer')
                                                <span class="badge bg-success">Customer</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->created_at ? $item->created_at->format('m-d-Y') : '-' }}</td>

                                            {{-- <td class="text-center">
                                                <a href="{{ route('users.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-info"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                <form action="{{ route('users.destroy', $item->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this User?')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>

                                            </td> --}}
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
