@extends('admin_panel.layouts.app')
@section('title', 'Attribute Edit')
@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <a href="{{ route('category.index') }}" class="btn btn-primary">
                    <i class="material-symbols-rounded align-middle">arrow_back</i> Attribute List
                </a>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form class="multisteps-form__form" action="{{ route('attribute.update', $attribute->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                data-animation="FadeIn">
                                <h5 class="font-weight-bolder">Attribute Information</h5>
                                <div class="multisteps-form__content">
                                    <div class="row my-3">
                                        <!-- Name -->
                                        <div class="col-12 col-sm-6 mb-3">
                                            <div class="input-group input-group-dynamic">
                                                <label for="name" class="form-label">Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="name" name="name"
                                                    class="multisteps-form__input form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $attribute->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Type -->
                                        <div class="col-12 col-sm-6 mb-3">
                                            <div class="input-group input-group-dynamic">
                                                
                                                <select class="form-control @error('type') is-invalid @enderror"
                                                    name="type" id="type" required>
                                                    <option disabled selected>Select Type</option>
                                                    <option value="text"
                                                        {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>Text
                                                    </option>
                                                    <option value="select"
                                                        {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>
                                                        Select</option>
                                                    <option value="multiselect"
                                                        {{ old('type', $attribute->type) == 'multiselect' ? 'selected' : '' }}>
                                                        Multiselect</option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Is Variant Switch -->
                                        <div class="col-sm-12 mt-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_variant"
                                                    name="is_variant" value="1"
                                                    {{ old('is_variant', $attribute->is_variant) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_variant">Is Variant</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit -->
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="material-symbols-rounded align-middle">check_circle</i> Update
                                            Attribute
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
@endpush
