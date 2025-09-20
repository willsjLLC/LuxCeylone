@extends($activeTemplate . 'layouts.app')
@php
    $registerContent = getContent('register.content', true);
@endphp

@section('app')
    <section class="account-section ">

        <div class="account__right bg_img"
            style="background: url({{ getImage('assets/images/frontend/register/' . @$registerContent->data_values->image, '1920x1080') }}) center;">
        </div>

        <div class="account-left sign-up {{ session('registerStatus') != '1' ? 'd-none' : '' }}">
            <div class="account__header text-center">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ siteLogo() }}" alt="@lang('logo')">
                </a>
                <h2 class="account__header-title">Select a Category</h2>
            </div>
            <form class="account-form row gx-3" action="{{ route('user.switchroleNext') }}" method="POST"
                onsubmit="return submitUserForm();">
                @csrf
                <div class="col-sm-12 mt-4 px-4" style="min-height: 450px;">
                    <div class="form-group">
                        <label for="country" class="form-label mb-3">@lang('Categories')
                            <span class="text--danger">*</span>
                        </label>

                        <!-- Search Box for Categories -->
                        <input type="text" id="category-search" class="form-control mb-4"
                            placeholder="Search categories...">

                        <div class="row px-3 categorybox" id="category-list">
                            @foreach ($categories as $key => $category)
                                <div class="mb-3 text-center col-12 border border-3 rounded-pill form-check checkbox-container text-muted"
                                    data-id="{{ $category->id }}" data-category="{{ strtolower($category->name) }}">
                                    <div class="pt-2 pb-2 pe-2 ps-4">
                                        <input name="category[]" type="checkbox" class="form-check-input invisible"
                                            id="subCheck{{ $category->id }}" value="{{ $category->id }}"
                                            @if (old('category') == $category->id) checked="checked" @endif>
                                        <label class="form-check-label me-3"
                                            for="subCheck{{ $category->id }}">{{ $category->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn--base btn--round w-100">@lang('Next')</button>
                </div>
            </form>
        </div>

        <div class="account-left sign-up {{ session('registerStatus') != '2' ? 'd-none' : '' }}">
            <div class="account__header text-center">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ siteLogo() }}" alt="@lang('logo')">
                </a>
                <h2 class="account__header-title">Select a Sub Category</h2>
            </div>
            <form class="account-form row gx-3" action="{{ route('user.switchroleNext') }}" method="POST"
                onsubmit="return submitUserForm();">
                @csrf
                <div class="col-sm-12 mt-4 px-4" style="min-height: 450px;">
                    <div class="form-group">
                        <label for="country" class="form-label mb-4">@lang('Sub Categories')
                            <span class="text--danger">*</span>
                        </label>

                        <!-- Search Box for Sub Categories -->
                        <input type="text" id="sub-category-search" class="form-control mb-4"
                            placeholder="Search sub categories...">

                        <div class="row px-3 categorybox" id="sub-category-list">
                            @foreach ($categories as $category)
                                @if (isset($category->subcategory))
                                    <!-- Make sure subcategory is not empty -->
                                    @foreach ($category->subcategory as $subcategory)
                                        <div class="mb-3 text-center col-12 border border-3 rounded-pill form-check checkbox-container text-muted"
                                            data-id="{{ $subcategory->id }}"
                                            data-sub-category="{{ strtolower($subcategory->name) }}">
                                            <div class="pt-2 pb-2 pe-2 ps-4">
                                                <input type="checkbox" name="subCategory[]"
                                                    class="form-check-input invisible"
                                                    id="cateCheck{{ __($subcategory->id) }}"
                                                    value="{{ $subcategory->id }}"
                                                    @if (old('subCategory') == $subcategory->id) checked="checked" @endif>
                                                <label class="form-check-label me-3"
                                                    for="cateCheck{{ __($subcategory->id) }}">{{ __($subcategory->name) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach

                        </div>

                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn--base btn--round w-100">@lang('Next')</button>
                </div>
            </form>

            <form class="account-form row gx-3" action="{{ route('user.switchroleBack') }}" method="GET">
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-dark btn--round w-100">@lang('Back')</button>
                </div>
            </form>
        </div>

    </section>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {

            @if (gs('secure_password'))
                $('input[name=password]').on('input', function() {
                    secure_password($(this));
                });
                $('[name=password]').focus(function() {
                    $(this).closest('.form-group').addClass('hover-input-popup');
                });
                $('[name=password]').focusout(function() {
                    $(this).closest('.form-group').removeClass('hover-input-popup');
                });
            @endif



            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });
        })(jQuery);

        document.querySelectorAll('.checkbox-container').forEach(function(container) {
            container.addEventListener('click', function() {
                const checkbox = container.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked; // Toggle the checkbox state

                // Add or remove classes based on the checkbox state
                if (checkbox.checked) {
                    container.classList.add('border--secondary', 'bg--secondary', 'text--white');
                } else {
                    container.classList.remove('border--secondary', 'bg--secondary', 'text--white');
                }
            });
        });

        document.getElementById('category-search').addEventListener('input', function() {
            const searchQuery = this.value.toLowerCase();
            const categoryItems = document.querySelectorAll('#category-list .checkbox-container');

            categoryItems.forEach(item => {
                const categoryName = item.getAttribute('data-category');
                if (categoryName.includes(searchQuery)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        document.getElementById('sub-category-search').addEventListener('input', function() {
            const searchQuery = this.value.toLowerCase();
            const subCategoryItems = document.querySelectorAll('#sub-category-list .checkbox-container');

            subCategoryItems.forEach(item => {
                const subCategoryName = item.getAttribute('data-sub-category');
                if (subCategoryName.includes(searchQuery)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
@endpush

@push('style')
    <style>
        .agree::after {
            display: none
        }

        .register-disable {
            height: 100vh;
            width: 100%;
            background-color: #fff;
            color: black;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-disable-image {
            max-width: 300px;
            width: 100%;
            margin: 0 auto 32px;
        }

        .register-disable-title {
            color: rgb(0 0 0 / 80%);
            font-size: 42px;
            margin-bottom: 18px;
            text-align: center
        }

        .register-disable-icon {
            font-size: 16px;
            background: rgb(255, 15, 15, .07);
            color: rgb(255, 15, 15, .8);
            border-radius: 3px;
            padding: 6px;
            margin-right: 4px;
        }

        .register-disable-desc {
            color: rgb(0 0 0 / 50%);
            font-size: 18px;
            max-width: 565px;
            width: 100%;
            margin: 0 auto 32px;
            text-align: center;
        }

        .register-disable-footer-link {
            color: #fff;
            background-color: #5B28FF;
            padding: 13px 24px;
            border-radius: 6px;
            text-decoration: none
        }

        .register-disable-footer-link:hover {
            background-color: #440ef4;
            color: #fff;
        }

        .categorybox {
            max-height: 350px;
            overflow: scroll;
        }
    </style>
@endpush
