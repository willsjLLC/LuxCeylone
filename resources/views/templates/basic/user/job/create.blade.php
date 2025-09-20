@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="col-12">
        <div class="row">
            <h3 class="mb-4 fw-bold text-dark mt-10">Job Create</h3>
            <!-- Sidebar (Left) -->
            <div class="col-lg-3 col-md-4">
                <div class="accordion mb-4" id="sidebarAccordion">

                    @php
                        $currentRoute = Route::currentRouteName();
                    @endphp

                    @if (auth()->user()->role == 1)
                        <a href="{{ route('user.job.create') }}"
                            class="d-block py-2 accordion--button {{ request()->routeIs('user.job.create') ? 'text-warning' : '' }}">
                            <i class="la la-plus-circle"></i> @lang('Create Job')
                        </a>
                    @endif
                    @if (auth()->user()->role == 1)
                        <a href="{{ route('user.job.history') }}"
                            class="d-block py-2 accordion--button {{ in_array(request()->route()->getName(), ['user.job.history', 'user.job.details']) ? 'text-warning' : '' }}">
                            <i class="la la-history"></i> @lang('Job History')
                        </a>
                    @endif
                    @if (auth()->user()->role == 2)
                        <a href="{{ route('user.job.finished') }}"
                            class="d-block py-2 accordion--button {{ $currentRoute == 'user.job.finished' ? 'text-warning' : '' }}">
                            <i class="la la-check-circle"></i> @lang('Finished Jobs')
                        </a>
                    @endif
                    @if (auth()->user()->role == 2)
                        <a href="{{ route('user.job.apply') }}"
                            class="d-block py-2 accordion--button {{ $currentRoute == 'user.job.apply' ? 'text-warning' : '' }}">
                            <i class="la la-paper-plane"></i> @lang('Applied Jobs')
                        </a>
                    @endif


                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="row">
                    <div class="dashboard__content contact__form__wrapper bg-white">
                        <div class="campaigns__wrapper">
                            <form class="create__campaigns__form" action="{{ route('user.job.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <h4 class="mb-3">Job Form</h4>
                                    <div class="col-xl-3">
                                        <div class="job-poster">
                                            <div class="job-poster-header">
                                                <div class="thumb">
                                                    <div class="avatar-preview">
                                                        <div class="profilePicPreview w-100" id="previewImg"
                                                            style="background-image: url({{ getImage(getFilePath('jobPoster'), getFileSize('jobPoster')) }})">
                                                        </div>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input type="file" name="attachment" class="profilePicUpload"
                                                            id="image" accept=".png, .jpg, .jpeg" />
                                                        <label for="image" class="bg--base"><i
                                                                class="la la-pencil text-light"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="form-group">
                                            <label>@lang('Category')</label>
                                            <select class="form-control select2 form-select form--control h-50 w-100"
                                                name="category_id" id="category" required>
                                                <option selected disabled value="">@lang('Select One')</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        data-subcategories="{{ $category->subcategory }}"
                                                        @selected(old('category_id') == $category->id)>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Subcategory')</label>
                                            <select class="form-control form-select form--control select2 h-50 w-100 "
                                                name="subcategory_id" id="subcategory"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label> @lang('Job Title')</label>
                                            <input type="text" name="title" class="form-control form--control "
                                                value="{{ old('title') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="country">
                                        @lang('Job Proof')
                                    </label>
                                    <select class="form-control form-select form--control h-50 w-100" name="job_proof"
                                        id="fileOption" required>
                                        <option value="" selected disabled>@lang('Select Type')</option>
                                        <option value="1" @selected(old('job_proof') == Status::JOB_PROVE_OPTIONAL)>
                                            @lang('Optional')
                                        </option>
                                        <option value="2" @selected(old('job_proof') == Status::JOB_PROVE_REQUIRED)>
                                            @lang('Required')</option>
                                    </select>
                                </div>
                                <div class="row" id="choiceOption">
                                    <label for="country">
                                        @lang('Select File Upload Option')
                                    </label>
                                    <div class="input-group">
                                        <div class="form-group me-2">
                                            <input type="checkbox" id="inlineRadio" value="all">
                                            <label class="form-check-label" for="inlineRadio">@lang('Select All')</label>
                                        </div>
                                        @foreach ($files as $file)
                                            <div class="form-group me-2 ">
                                                <input type="checkbox" name="file_name[]"
                                                    id="inlineRadio{{ $file->id }}" value="{{ $file->name }}">
                                                <label class="form-check-label"
                                                    for="inlineRadio{{ $file->id }}">{{ __($file->name) }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label> @lang('Quantity')</label>
                                            <input type="number" id="workers" name="quantity"
                                                class="form-control form--control workers" min="1"
                                                value="{{ old('quantity') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="form-group">
                                            <label>@lang('Worker will earn')</label>
                                            <div class="input-group">
                                                <input type="number" step="any" name="rate"
                                                    class="form-control form--control rate" min="0"
                                                    value="{{ old('rate') }}" required>
                                                <div class="input-group-text bg--base text--white border-0">
                                                    {{ gs('cur_text') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="form-group">
                                            <label>@lang('Total Budget')</label>
                                            <div class="input-group">
                                                <input type="text" name="total_budget"
                                                    class="form-control form--control total"
                                                    value="{{ old('total_budget') }}" readonly>
                                                <div class="input-group-text bg--base text--white border-0">
                                                    {{ gs('cur_text') }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="paymentOption">
                                        @lang('Payment Option')
                                    </label>
                                    <select class="form-control form-select form--control h-50 w-100"
                                        name="payment_option" id="paymentOption" required>
                                        <option value="" selected disabled>@lang('Select Payment Option')</option>
                                        @foreach ($paymentOptions as $option)
                                            <option value="{{ $option->id }}" @selected(old('payment_option') == $option->id)>
                                                {{ $option->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label>@lang('Job Description') </label>
                                            <div class="input-group">
                                                <textarea class="form-control form--control nicEdit" name="description">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="cmn--btn btn--md w-100" type="submit">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush


@push('style')
    <style>
        *:focus {
            outline: none;
        }

        .form--control {
            padding-right: 2px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.select2').select2();

            var oldSubcategory = "{{ old('subcategory_id') }}";

            $("#choiceOption").css('display', 'none');
            $("#fileOption").on('change', function() {
                $("#choiceOption").css('display', 'none');
                var value = $(this).val();
                if (value == 2) {
                    $("#choiceOption").css('display', 'block');
                }
            });


            $("#inlineRadio").click(function() {
                $("input[type=checkbox]").prop('checked', $(this).prop('checked'))
            });

            $('#category').on('change', function() {
                let subcategories = $(this).find('option:selected').data('subcategories');

                let subcategory = `<option value="" selected disabled>@lang('Select One')</option>`;
                $.each(subcategories, function(index, value) {
                    subcategory +=
                        `<option value="${value.id}" ${oldSubcategory ==value.id ? 'selected' : ''}>${value.name}</option>`;
                });
                $('[name=subcategory_id]').html(subcategory)
            }).change();

            $(".workers").on('change input', function() {
                var worker = $(this).val();
                var rate = $('.rate').val();
                var total = rate * worker;
                $('.total').val(total);
            });

            $(".rate").on('change input', function() {
                var rate = $(this).val();
                var worker = $('.workers').val();
                var total = rate * worker;
                $('.total').val(total);
            });

            $('.profilePicUpload').on('change', function() {
                previewFile($(this))

            })
        })(jQuery);

        function previewFile(input) {
            var file = $("input[type=file]").get(0).files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function() {
                    $("#previewImg").css("background-image", "url(" + reader.result + ")");
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endpush
