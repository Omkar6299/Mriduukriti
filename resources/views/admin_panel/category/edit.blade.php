@extends('admin_panel.layouts.app')
@section('title', 'Category Edit')
@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <a href="{{ route('category.index') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">arrow_back</i> Category List
                </a>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form class="multisteps-form__form" action="{{ route('category.update', $category->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                data-animation="FadeIn">
                                <h5 class="font-weight-bolder">Category Information</h5>
                                <div class="multisteps-form__content">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-12">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Name<span
                                                        class="text-danger">*</span></label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                    name="name" value="{{ $category->name ?? '' }}" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <label class="mt-4">Description</label>
                                            <p class="form-text text-muted text-xs ms-1 d-inline">
                                                (optional)
                                            </p>
                                            <textarea id="edit-description" name="description">{{ $category->description ?? '' }}</textarea>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="mt-4">Image</label>
                                            <p class="form-text text-muted text-xs ms-1 d-inline">(optional)</p>

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
                                                    class="mt-3 {{ $category->image ? '' : 'd-none' }} position-relative d-inline-block">
                                                    <img id="imagePreview"
                                                        src="{{ $category->image ? asset($category->image) : '#' }}"
                                                        alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                                    <button type="button" id="removeImageBtn"
                                                        class="btn-close position-absolute top-0 end-0 m-2 bg-white"
                                                        aria-label="Remove image"></button>
                                                </div>
                                                <input type="hidden" name="remove_image" id="removeImageFlag"
                                                    value="0">


                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="mt-4">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="statusSwitch" name="status" value="1"
                                                    {{ $category->status ? 'checked' : '' }}>
                                                <label class="form-check-label" for="statusSwitch" id="statusLabel">
                                                    {{ $category->status ? 'Active' : 'Inactive' }}
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="material-symbols-rounded align-middle">check_circle</i> Update
                                            Category
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
        document.addEventListener('DOMContentLoaded', () => {
            const dropArea = document.getElementById('drop-area');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const removeImageBtn = document.getElementById('removeImageBtn');
            const removeImageFlag = document.getElementById('removeImageFlag'); // Optional hidden input

            if (!dropArea || !imageInput || !imagePreview || !imagePreviewContainer || !removeImageBtn) return;

            // Highlight on drag
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.classList.add('bg-white', 'shadow-sm', 'drag-over');
                });
            });

            // Remove highlight
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.classList.remove('bg-white', 'shadow-sm', 'drag-over');
                });
            });

            // Handle file drop
            dropArea.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    previewImage(files[0]);
                }
            });

            // Manual file selection
            imageInput.addEventListener('change', () => {
                if (imageInput.files[0]) {
                    previewImage(imageInput.files[0]);
                }
            });

            // Preview image
            function previewImage(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('d-none');
                    if (removeImageFlag) removeImageFlag.value = "0";
                };
                reader.readAsDataURL(file);
            }

            // Remove image and reset
            removeImageBtn.addEventListener('click', () => {
                imageInput.value = '';
                imagePreview.src = '';
                imagePreviewContainer.classList.add('d-none');
                if (removeImageFlag) removeImageFlag.value = "1";
            });
        });
    </script>
    <script>
        const statusSwitch = document.getElementById('statusSwitch');
        const statusLabel = document.getElementById('statusLabel');

        statusSwitch.addEventListener('change', function() {
            statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
        });
    </script>
@endpush
