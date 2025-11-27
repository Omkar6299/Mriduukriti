@extends('admin_panel.layouts.app')
@section('title', 'Attribute Add')
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
                        <form class="multisteps-form__form" action="{{ route('attribute.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                data-animation="FadeIn">
                                <h5 class="font-weight-bolder">Attribute Information</h5>
                                <div class="multisteps-form__content">
                                    <div class="row my-3">
                                        <div class="col-12 col-sm-6 mb-3">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Name<span
                                                        class="text-danger">*</span></label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                    name="name" />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 mb-3">
                                             <div class="input-group input-group-dynamic">

                                                <select class="form-control" name="type" id="choices-category">
                                                    <option selected disabled>Type<span
                                                            class="text-danger">*</span> </option>
                                                    <option value="text">Text</option>
                                                    <option value="select">Select</option>
                                                    <option value="multiselect">Multiselect</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12">
                                            <label class="mt-4"></label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" name="is_variant" >
                                                <label class="form-check-label" for="statusSwitch" >
                                                    Is Variant
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                   

                                    <div class="text-end ">
                                        <button type="submit" class="btn btn-success">
                                            <i class="material-symbols-rounded align-middle">check_circle</i> Save Attribute
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
