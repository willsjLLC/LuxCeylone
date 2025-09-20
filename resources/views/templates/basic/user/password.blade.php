@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <h3 class="mt-10 mb-4 fw-bold text-dark mobile-view-margin">Change Password</h3>
    <div class="col-12">
        <div class="row">
            <div class="col-lg-3 col-md-4 d-none d-md-block">
                <div class="mb-4 accordion" id="sidebarAccordion">
                    <div>
                        <a href="{{ route('user.profile.setting') }}"
                            class="d-block py-2 accordion--button {{ request()->routeIs('user.profile.setting') ? 'text--warning' : '' }}">
                            <i class="la la-user"></i> @lang('Profile Setting')
                        </a>
                        <a href="{{ route('user.change.password') }}"
                            class="d-block py-2 accordion--button{{ request()->routeIs('user.change.password') ? ' text--warning active-link' : '' }}">
                            <i class="la la-lock"></i> @lang('Change Password')
                        </a>

                        <a href="{{ route('user.twofactor') }}"
                            class="d-block py-2 accordion--button{{ menuActive('user.twofactor') }}">
                            <i class="la la-key"></i> @lang('2FA Security')
                        </a>

                        <a href="{{ route('user.secondOwner') }}"
                            class="d-block py-2 accordion--button{{ menuActive('user.secondOwner') }}">
                            <i class="la la-user-tie"></i> @lang('Second Owner')
                        </a>

                        <a href="{{ route('user.logout') }}" class="py-2 d-block accordion--button">
                            <i class="la la-sign-out"></i> @lang('Sign Out')
                        </a>
                    </div>
                </div>
            </div>


            <div class="bg-white contact__form__wrapper col-lg-8">
                <form action="" method="post">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">@lang('Current Password')</label>
                        <input type="password" class="form-control form--control" name="current_password" required
                            autocomplete="current-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">@lang('Password')</label>
                        <input type="password" class="form-control form--control" name="password" required
                            autocomplete="current-password">
                        @if (gs('secure_password'))
                            <div class="input-popup">
                                <p class="error lower">@lang('1 small letter minimum')</p>
                                <p class="error capital">@lang('1 capital letter minimum')</p>
                                <p class="error number">@lang('1 number minimum')</p>
                                <p class="error special">@lang('1 special character minimum')</p>
                                <p class="error minimum">@lang('6 character password')</p>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="form-label">@lang('Confirm Password')</label>
                        <input type="password" class="form-control form--control" name="password_confirmation" required
                            autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .mobile-view-margin {
                margin-top: 70px;
                /* Adjust the value as needed */
            }
    </style>
@endsection


@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
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

        })(jQuery);
    </script>
@endpush
