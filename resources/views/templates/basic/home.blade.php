@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php
$bannerContent = getContent('banner.content', true);
@endphp
<section class="overflow-hidden bg_img" .>

    <div class="container ">
        <div class="banner__wrapper d-flex align-items-center ">

            <div class="banner__thumb d-none d-lg-block">
                <img src="{{ getImage('assets/images/frontend/banner/' . @$bannerContent->data_values->banner_image, '750x600') }}">
            </div>
            <div class="banner__content">
                <h2 class="sub-heading">{{ __(@$bannerContent->data_values->heading) }}</h2>
                <h1 class="banner__content-title">{{ __(@$bannerContent->data_values->subheading) }}</h1>
                <p>{{ __(@$bannerContent->data_values->description) }}</p>
                <img class="mobile_app" src="{{ getImage('assets/image/mobileApp.png') }}" />
                <form class="job__search" action="{{ route('ads.index') }}">
                    <div class="form--group d-flex align-items-center">
                        <select class="border-0 form-select form--control select2" name="category">
                            <option value="" selected disabled>@lang('Select Category')</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control form--control" name="search" autocomplete="off" placeholder="@lang('Search Ads...')">
                        <button class="btn btn--base btn--round px-md-5" type="submit">{{ __(@$bannerContent->data_values->button_text) }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@if ($sections->secs != null)
@foreach (json_decode($sections->secs) as $sec)
@include($activeTemplate . 'sections.' . $sec)
@endforeach
@endif
@include($activeTemplate . 'partials.footer')
@endsection

@push('style-lib')
<link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
<script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        $('.select2').select2();
    })(jQuery);

</script>
@endpush

@push('style')
<style>
    .mobile_app {
        height: auto;
        width: 100%;
        border-radius: 4px;
    }

    .category-icon {
        border-radius: 10px;
    }

    .banner__thumb {
        margin-top: 70px;
    }

    .scrollToTop,
    .counter__item::before,
    .counter__item::after,
    .overview__content__wrapper.btn {
        background-color: #ff7300 !important;
    }

    ,
    .container-fluid,
    .read-more:hover::before,
    .bg--base {
        color: #ffffff !important;
        background-color: #fbe49f !important;
    }

    .overview__content__wrapper .btn {
        color: #e60c0c !important;
        background: #fbe49f !important;
        box-shadow: 1px 1px 5px #00000026;
        transition: all .3s;

    }

</style>
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        $('.select2').select2();

        // Call the API on load
        $.ajax({
            url: ADD_CITI_LITE_API + '/api/test-api'
            , type: 'GET'
            , success: function(res) {
                console.log('API Response:', res);
                // Show the result in HTML if needed
                $('.api-response-box').text(res.data);
            }
            , error: function(xhr, status, error) {
                console.error('API call failed:', error);
            }
        });

    })(jQuery);

</script>
@endpush
