@extends($activeTemplate . 'layouts.app')
@php
    $registerContent = getContent('register.content', true);
@endphp


@section('app')
<section class="account-section">
    <div class="account__right bg_img"
    style="background: url({{ getImage('assets/images/frontend/register/' . @$registerContent->data_values->image, '1920x1080') }}) center;">

</div>

    <div class="account-left">
        <div class="account__header text-center">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            <h2 class="account__header-title">Add Custom Category</h2>
            <p>Please enter the name of your custom category</p>
        </div>

        <form class="account-form" action="{{ route('user.storeCustomCategory') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-12 mt-4">
                    <div class="form-group">
                        <label class="form-label">Custom Category Name</label>
                        <input type="text" class="form-control form--control" name="custom_category"
                            placeholder="Enter your custom category" required>
                        @error('custom_category')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn--base btn--round w-100">@lang('Save & Continue')</button>
                </div>
            </div>
        </form>

        <form class="account-form row gx-3" action="{{ route('user.registerBack') }}" method="GET">
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-dark btn--round w-100">@lang('Back')</button>
            </div>
        </form>
    </div>
</section>
@endsection
