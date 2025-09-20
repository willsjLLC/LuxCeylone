@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="mt-10 row mobile-view-margin">
        <h3 class="mb-4 fw-bold text-dark">2FA Setting</h3>
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 d-none d-md-block">
            <div class="mb-4 accordion" id="sidebarAccordion">
                <div>
                    <a href="{{ route('user.profile.setting') }}"
                        class="d-block py-2 accordion--button {{ request()->routeIs('user.profile.setting') ? 'text--warning' : '' }}">
                        <i class="la la-user"></i> @lang('Profile Setting')
                    </a>
                    <a href="{{ route('user.change.password') }}"
                        class="d-block py-2 accordion--button {{ request()->routeIs('user.change.password') ? 'text--warning' : '' }}">
                        <i class="la la-lock"></i> @lang('Change Password')
                    </a>
                    <a href="{{ route('user.twofactor') }}"
                        class="d-block py-2 accordion--button {{ menuActive('user.twofactor') }} tfa">
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
        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="campaigns__wrapper">
                @if (!auth()->user()->ts)
                    <div class="card custom--card contact__form__wrapper">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Add Your Account')</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3">@lang('Use the QR code or setup key on your Google Authenticator app to add your account. ')</h6>
                            <div class="mx-auto text-center form-group">
                                <img class="mx-auto" src="{{ $qrCodeUrl }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Setup Key')</label>
                                <div class="input-group">
                                    <input type="text" name="key" value="{{ $secret }}"
                                        class="form-control form--control referralURL" readonly>
                                    <button type="button" class="border-0 input-group-text bg--base text--white copytext"
                                        id="copyBoard">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <label><i class="fa fa-info-circle"></i> @lang('Help')</label>
                            <p>@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.') <a class="text--base"
                                    href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en"
                                    target="_blank">@lang('Download')</a></p>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->ts)
                    <div class="card custom--card contact__form__wrapper">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Disable 2FA Security')</h5>
                        </div>
                        <form action="{{ route('user.twofactor.disable') }}" method="POST">
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="key" value="{{ $secret }}">
                                <div class="mb-3 form-group">
                                    <label class="form-label">@lang('Google Authenticatior OTP')</label>
                                    <input type="text" class="form-control form--control" name="code" required>
                                </div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="card custom--card contact__form__wrapper">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Enable 2FA Security')</h5>
                        </div>
                        <form action="{{ route('user.twofactor.enable') }}" method="POST">
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="key" value="{{ $secret }}">
                                <div class="mb-3 form-group">
                                    <label class="form-label">@lang('Google Authenticatior OTP')</label>
                                    <input type="text" class="form-control form--control" name="code" required>
                                </div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .copied::after {
            background-color: #{{ gs('base_color') }};
        }

        .tfa {
            color: #28A745;
        }



        @media (max-width: 768px) {
            .mobile-view-margin {
                margin-top: 70px;
                /* Adjust the value as needed */
            }
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#copyBoard').click(function() {
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
            });
        })(jQuery);
    </script>
@endpush
