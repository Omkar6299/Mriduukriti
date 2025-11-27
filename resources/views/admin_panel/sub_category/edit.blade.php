@extends('admin_panel.layouts.app')
@section('title', 'Sub Category Add')
@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <a href="{{ route('sub_category.index') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">arrow_back</i> Sub Category List
                </a>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form class="multisteps-form__form" action="{{ route('sub_category.update', $subcategory->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                data-animation="FadeIn">
                                <h5 class="font-weight-bolder">Sub Category Information</h5>
                                <div class="multisteps-form__content">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-6">
                                            <div class="input-group input-group-dynamic">
                                                <select class="form-control" name="category_id" id="choices-category"
                                                    required>
                                                    <option disabled {{ isset($subcategory) ? '' : 'selected' }}>Select
                                                        Category</option>
                                                    @foreach ($category as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ isset($subcategory) && $subcategory->category_id == $item->id ? 'selected' : '' }}>
                                                            {{ $item->name ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="input-group input-group-dynamic">
                                                <label for="name" class="form-label">Name<span
                                                        class="text-danger">*</span></label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                    name="name" id="name"
                                                    value="{{ old('name', $subcategory->name ?? '') }}" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <label class="mt-4">Description</label>
                                            <p class="form-text text-muted text-xs ms-1 d-inline">(optional)</p>
                                            <textarea id="edit-description" name="description">{{ old('description', $subcategory->description ?? '') }}</textarea>
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

                                                @php
                                                    $imageExists = isset($subcategory) && $subcategory->image;
                                                @endphp

                                                <div id="imagePreviewContainer"
                                                    class="mt-3 {{ $imageExists ? '' : 'd-none' }} position-relative d-inline-block">
                                                    <img id="imagePreview"
                                                        src="{{ $imageExists ? asset($subcategory->image) : '#' }}"
                                                        alt="Preview" class="img-thumbnail" style="max-height: 200px;">
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
                                                    onchange="toggleStatusLabel()"
                                                    {{ old('status', $subcategory->status ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="statusSwitch" id="statusLabel">
                                                    {{ old('status', $subcategory->status ?? false) ? 'Active' : 'Inactive' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="material-symbols-rounded align-middle">check_circle</i>
                                            {{ isset($subcategory) ? 'Update' : 'Save' }}
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

        // On page load
        document.addEventListener('DOMContentLoaded', () => {
            toggleStatusLabel();

            const input = document.getElementById('imageInput');
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            const removeBtn = document.getElementById('removeImageBtn');

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        container.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeBtn.addEventListener('click', function() {
                input.value = '';
                preview.src = '#';
                container.classList.add('d-none');
            });
        });
    </script>
@endpush
