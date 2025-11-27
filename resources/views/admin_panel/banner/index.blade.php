@extends('admin_panel.layouts.app')
@section('title', 'Banners')
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
                <a href="{{ route('banners.create') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">add</i> Add Banner
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
                                        <th class="text-white">Banner</th>
                                        <th class="text-white">Title</th>
                                        <th class="text-white">Paragraph</th>
                                        <th class="text-white">Link</th>
                                        <th class="text-white">Status</th>
                                        <th class="text-center text-white">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($banners as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @php
                                                    $imagePath = public_path($item->bannner);
                                                    $imageUrl =
                                                        file_exists($imagePath) && $item->bannner
                                                            ? asset($item->bannner)
                                                            : asset('admin_panel/assets/img/default/default_pro.png');
                                                @endphp
                                                <img src="{{ $imageUrl }}" class="img-fluid rounded shadow-sm"
                                                    style="height: 40px; object-fit: cover; cursor: pointer;" alt="Banner"
                                                    onclick="showBannerModal('{{ $imageUrl }}')">
                                            </td>

                                            <td>{!! $item->title ?? '-' !!}</td>
                                            <td>{!! $item->paragraph ?? '-' !!}</td>
                                            <td>{!! $item->link ?? '-' !!}</td>
                                            <td>
                                                @if ($item->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('banners.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-info"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                <form action="{{ route('banners.destroy', $item->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this banners?')">
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

    <!-- Banner Preview Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  text-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="bannerModalLabel">Banner Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="bannerPreviewImage" src="" alt="Banner" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        function showBannerModal(imageUrl) {
            document.getElementById('bannerPreviewImage').src = imageUrl;
            let bannerModal = new bootstrap.Modal(document.getElementById('bannerModal'));
            bannerModal.show();
        }
    </script>
@endpush
