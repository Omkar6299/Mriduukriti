@extends('admin_panel.layouts.app')
@section('title', 'Banner Add')
@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <a href="{{ route('banners.index') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">arrow_back</i> Banner List
                </a>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form class="multisteps-form__form" action="{{ route('banners.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                data-animation="FadeIn">
                                <h5 class="font-weight-bolder">Banner Information</h5>
                                <div class="multisteps-form__content">
                                    <div class="row mt-3 mb-3">
                                        <div class="col-12 col-sm-12">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Title<span
                                                        class="text-danger">*</span></label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                    name="title" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-12 mb-3">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Link</label>
                                                <input class="multisteps-form__input form-control" type="link"
                                                    name="link" />
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="mt-4">Description</label>
                                            <p class="form-text text-muted text-xs ms-1 d-inline">
                                                (optional)
                                            </p>
                                            <textarea id="edit-description" name="description"></textarea>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="mt-4">Banner</label>
                                            <p class="form-text text-danger text-xs ms-1 d-inline">*</p>

                                            <div id="drop-area"
                                                class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative">
                                                <p class="text-muted mb-2">Drag & drop an image here or click to browse</p>
                                                <input type="file" id="imageInput" name="image" accept="image/*"
                                                    hidden>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="document.getElementById('imageInput').click();">
                                                    Select Image
                                                </button>

                                                <div id="imagePreviewContainer"
                                                    class="mt-3 d-none position-relative d-inline-block">
                                                    <img id="imagePreview" src="#" alt="Preview"
                                                        class="img-thumbnail" style="max-height: 200px;">
                                                    <button type="button" id="removeImageBtn"
                                                        class="btn-close position-absolute top-0 end-0 m-2 bg-white"
                                                        aria-label="Remove image"></button>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-sm-12">
                                            <label class="mt-4">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="statusSwitch" name="status" value="1"
                                                    onchange="toggleStatusLabel()">
                                                <label class="form-check-label" for="statusSwitch" id="statusLabel">
                                                    Inactive
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="material-symbols-rounded align-middle">check_circle</i> Save Banner
                                        </button>
                                    </div>
                                </div>
                            </div>


                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        ClassicEditor
            .create(document.querySelector('#edit-description'))
            .catch(error => {
                console.error(error);
            });
    </script>
    <script>
        const dropArea = document.getElementById('drop-area');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const removeImageBtn = document.getElementById('removeImageBtn');

        // Drag and drop highlighting
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.add('bg-white', 'shadow-sm');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.remove('bg-white', 'shadow-sm');
            });
        });

        // File drop handler
        dropArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                previewImage(files[0]);
            }
        });

        // File selection
        imageInput.addEventListener('change', () => {
            if (imageInput.files[0]) {
                previewImage(imageInput.files[0]);
            }
        });

        // Preview image function
        function previewImage(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }

        // Remove preview and clear input
        removeImageBtn.addEventListener('click', () => {
            imageInput.value = '';
            imagePreview.src = '#';
            imagePreviewContainer.classList.add('d-none');
        });

        function toggleStatusLabel() {
            const checkbox = document.getElementById('statusSwitch');
            const label = document.getElementById('statusLabel');
            label.textContent = checkbox.checked ? 'Active' : 'Inactive';
        }

        // Initialize label on page load
        document.addEventListener('DOMContentLoaded', () => {
            toggleStatusLabel();
        });
    </script>
@endpush
