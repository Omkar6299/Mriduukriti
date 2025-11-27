@extends('admin_panel.layouts.app')
@section('title', 'Product Add')
@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">arrow_back</i> Product List
                </a>
            </div>

            <div class="col-12">
                <form class="multisteps-form__form" action="{{ route('products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- SECTION 1: Product Information --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="font-weight-bolder mb-3">🛍️ Product Information</h5>
                            <div class="row">
                                <!-- Name -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Name<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="text" value="{{old('name')}}" name="name" />
                                    </div>
                                </div>


                                <!-- Category -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">

                                        <select class="form-control" name="category_id" id="categoryDropdown">
                                            <option selected disabled>Select Category<span class="text-danger">*</span>
                                            </option>
                                            @foreach ($category as $item)
                                                <option value="{{ $item->id }}">{{ $item->name ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Subcategory -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">

                                        <select class="form-control" name="subcategory_id" id="subcategoryDropdown">
                                            <option selected disabled>Select subcategory<span class="text-danger">*</span>
                                            </option>

                                        </select>
                                    </div>
                                </div>


                                <!-- Price -->

                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Price<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" value="{{old('price')}}" name="price"
                                            step="0.01" required />
                                    </div>
                                </div>

                                <!-- Sale Price -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Sale Price<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" value="{{old('sale_price')}}" name="sale_price"
                                            step="0.01" required />
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Quantity<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" value="{{old('quantity')}}" name="quantity"
                                            required />
                                    </div>
                                </div>


                                <!-- Stock Status -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">

                                        <select class="form-control" name="stock_status" id="choices-category">
                                            <option selected disabled>Stock Status<span class="text-danger">*</span>
                                            </option>
                                            <option value="in_stock">In Stock</option>
                                            <option value="out_of_stock">Out Of Stock</option>
                                            <option value="backorder">Back Order</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <!-- Short Description -->
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">Short Description<span class="text-danger">*</span></label>
                                    <textarea class="multisteps-form__input form-control" id="short-description" name="short_description" rows="4">{{old('short_description')}}</textarea>
                                </div>

                                <!-- Full Description -->
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">Description (optional)</label>
                                    <textarea class="multisteps-form__input form-control" id="edit-description" name="description" rows="4">{{old('description')}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Shipping Details --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="font-weight-bolder mb-3">🚚 Shipping & Dimensions</h5>
                            <div class="row">

                                <!-- Weight -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Weight (gm)<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" name="weight"
                                            required />
                                    </div>
                                </div>

                                <!-- Length -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Length (cm)<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" name="length"
                                            required />
                                    </div>
                                </div>


                                <!-- Width -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Width (cm)<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" name="width"
                                            required />
                                    </div>
                                </div>



                                <!-- Height -->
                                <div class="col-12 col-sm-6 mb-3">
                                    <div class="input-group input-group-dynamic">
                                        <label for="exampleFormControlInput1" class="form-label">Height (cm)<span
                                                class="text-danger">*</span></label>
                                        <input class="multisteps-form__input form-control" type="number" name="height"
                                            required />
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- SECTION 4: Product Attributes --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="font-weight-bolder mb-3">🎛️ Product Attributes</h5>
                            <div id="attribute-container">
                                <div class="row attribute-row mb-3">
                                    <div class="col-sm-5 mb-2">
                                        <label class="form-label">Attribute</label>
                                        <select class="form-control" name="attributes[0][id]" required>
                                            <option selected disabled>Select Attribute</option>
                                            @foreach ($attributes as $attribute)
                                                <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-5 mb-2">
                                        <label class="form-label">Value</label>
                                        <input type="text" class="form-control" name="attributes[0][value]"
                                            placeholder="e.g. Red, Large" required />
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-attribute w-100">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-attribute">
                                    + Add Attribute
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Media & Status --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="font-weight-bolder mb-3">🖼️ Media & Status</h5>
                            <div class="row">
                                <!-- Image Upload -->
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">Product Image</label>
                                    <div id="drop-area"
                                        class="border border-dashed rounded-3 p-4 text-center bg-light position-relative">
                                        <p class="text-muted mb-2">Drag & drop or click to select an image</p>
                                        <input type="file" id="imageInput" name="image" accept="image/*" hidden>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="document.getElementById('imageInput').click();">Select Image</button>
                                        <div id="imagePreviewContainer"
                                            class="mt-3 d-none position-relative d-inline-block">
                                            <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail"
                                                style="max-height: 200px;">
                                            <button type="button" id="removeImageBtn"
                                                class="btn-close position-absolute top-0 end-0 m-2 bg-white"
                                                aria-label="Remove image"></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="status" id="statusSwitch"
                                            value="1">
                                        <label class="form-check-label" for="statusSwitch">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="material-symbols-rounded align-middle">check_circle</i> Save Product
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        ClassicEditor
            .create(document.querySelector('#short-description'))
            .catch(error => {
                console.error(error);
            });
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
    <script>
        $(document).ready(function() {
            $('#categoryDropdown').on('change', function() {
                var categoryId = $(this).val();
                var subcategoryDropdown = $('#subcategoryDropdown');

                subcategoryDropdown.html('<option selected disabled>Loading...</option>');

                $.ajax({
                    url: "{{ route('getSucategoryByCategory') }}",
                    type: 'GET',
                    data: {
                        category_id: categoryId
                    },
                    success: function(data) {
                        subcategoryDropdown.empty();
                        subcategoryDropdown.append(
                            '<option selected disabled>Select Subcategory</option>');
                        $.each(data, function(index, item) {
                            subcategoryDropdown.append('<option value="' + item.id +
                                '">' + item.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        subcategoryDropdown.html(
                            '<option selected disabled>Error loading</option>');
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const addBtn = document.getElementById("add-attribute");
            const container = document.getElementById("attribute-container");

            let index = 1; // start from 1 because 0 is already in HTML

            // Add new attribute row
            addBtn.addEventListener("click", function() {
                const newRow = document.createElement("div");
                newRow.classList.add("row", "attribute-row", "mb-3");

                newRow.innerHTML = `
            <div class="col-sm-5 mb-2">
                <label class="form-label">Attribute</label>
                <select class="form-control" name="attributes[${index}][id]" required>
                    <option selected disabled>Select Attribute</option>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-5 mb-2">
                <label class="form-label">Value</label>
                <input type="text" class="form-control" name="attributes[${index}][value]" 
                       placeholder="e.g. Red, Large" required />
            </div>

            <div class="col-sm-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-attribute w-100">Remove</button>
            </div>
        `;

                container.appendChild(newRow);
                index++;
            });

            // Remove attribute row (event delegation)
            container.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-attribute")) {
                    e.target.closest(".attribute-row").remove();
                }
            });
        });
    </script>
@endpush
