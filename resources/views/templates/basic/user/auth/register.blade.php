@extends($activeTemplate . 'layouts.app')
@php
    $registerContent = getContent('register.content', true);
    $policyPages = getContent('policy_pages.element', false, null, true);
@endphp

@section('app')
    @if (gs('registration'))
        <section class="account-container">
            {{-- <div class="bg-image"></div> --}}
            <div class="account__right bg_img"
                style="background: url({{ getImage('assets/images/frontend/register/' . @$registerContent->data_values->image, '1920x1080') }}) center;">
            </div>
            <div class="account__left">
                {{-- <div class="account-left"></div> --}}
                <div class="account-right">
                    <div class="register-form-wrapper">
                        <h2 class="welcome-text">{{ __($registerContent->data_values->heading) }}</h2>
                        <p class="subtitle-text">{{ __($registerContent->data_values->subheading) }}</p>

                        @include($activeTemplate . 'partials.social_login')
                        <div class="details-container">
                            <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha account-form" id="registrationForm">
                                @csrf
                                <div class="mb-1 form-group-details">
                                    {{-- @if (request('affiliated_by'))
                                        <label class="mb-0 form-label">@lang('Affiliated By')</label>
                                        <input type="text" class="bottom-border-input" name="referredby"
                                            value="{{ request('affiliated_by') }}">
                                    @endif --}}
                                     @if (request('affiliated_by'))
                                        <label class="mb-0 form-label">@lang('Affiliated By')</label>
                                        <input type="hidden" name="referredby" value="{{ request('affiliated_by') }}">
                                        <?php
                                        try {
                                            $referredByUsername = hex2bin(request('affiliated_by'));
                                            $referrer = \App\Models\User::where('username', $referredByUsername)->first();
                                            $referrerName = $referrer ? $referrer->firstname . ' ' . $referrer->lastname : 'Unknown';
                                        } catch (\Exception $e) {
                                            $referrerName = 'Unknown';
                                        }
                                        ?>
                                        <input type="text" class="bottom-border-input" value="{{ $referrerName }}" readonly>
                                    @endif
                                </div>
                                <div class="mb-1 form-group-details">
                                    <label class="mb-0 form-label">@lang('First Name')</label>
                                    <input type="text" class="bottom-border-input" name="firstname"
                                        value="{{ old('firstname') }}" required>
                                </div>
                                <div class="mb-1 form-group-details">
                                    <label class="mb-0 form-label">@lang('Last Name')</label>
                                    <input type="text" class="bottom-border-input" name="lastname"
                                        value="{{ old('lastname') }}" required>
                                </div>
                                <div class="mb-1 form-group-details">
                                    <label class="mb-0 form-label">@lang('Email')</label>
                                    <input type="email" class="bottom-border-input checkUser" name="email"
                                        value="{{ old('email') }}" required>
                                </div>
                                <div class="mb-1 form-group-details">
                                    <label class="mb-0 form-label">@lang('Password')</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="mobile-password"
                                            class="bottom-border-input @if (gs('secure_password')) secure-password @endif"
                                            name="password" required>
                                        <button type="button" class="toggle-password" data-target="mobile-password">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3 form-group-details">
                                    <label class="mb-0 form-label">@lang('Confirm Password')</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="mobile-confirm-password" class="bottom-border-input"
                                            name="password_confirmation" required>
                                        <button type="button" class="toggle-password"
                                            data-target="mobile-confirm-password">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <x-captcha />
                                @if (gs('agree'))
                                    @php
                                        $policyPages = getContent('policy_pages.element', false, orderById: true);
                                    @endphp
                                    <div class="form-group">
                                        <input type="checkbox" id="agree" @checked(old('agree')) name="agree" required>
                                        <label for="agree">@lang('I agree with')</label> <span>
                                            @foreach ($policyPages as $policy)
                                                <a href="{{ route('policy.pages', $policy->slug) }}"
                                                    target="_blank">{{ __($policy->data_values->title) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </span>
                                    </div>
                                @endif

                                <div class="mb-1">
                                    <button type="submit" id="recaptcha" class="btn sign-up-btn">
                                        @lang('Register')
                                    </button>
                                </div>
                                <p class="text-center quick-access">Quick Access</p>
                            </form>
                            <div class="mb-3">
                                <button type="submit" id="recaptcha" class="sign-in-btn-google">
                                    <img src="{{ asset('assets/images/auth/google.png') }}" alt="Google"
                                        style="width: 20px; height: 20px; margin-right: 5px;">
                                        <a href="{{ route('user.auth.google.redirect') }}">
                                            @lang('Continue with Google')
                                        </a>
                                </button>
                            </div>

                            <p class="mb-0 not-registered">@lang('Already have an account?')
                                <a href="{{ route('user.login') }}" class="sign-up">@lang('Login')</a>
                            </p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Direct Registration Confirmation Modal -->
            <div class="modal fade" id="directRegisterConfirmModal" tabindex="-1" aria-labelledby="directRegisterConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="directRegisterConfirmModalLabel">Registration Confirmation</h5>
                            <button class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-center">You're about to register directly into the system without using an affiliate link. Are you sure you want to continue?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">No</button>
                            <button type="button" id="confirmDirectRegistration" class="text-white btn btn--sm" style="background-color: #1e3774;">Yes, Continue</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif
    {{-- check whether user exist or not --}}
    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <button class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm"
                        data-bs-dismiss="modal">@lang('Close')</button>
                        <a href="{{ route('user.login') }}" class="text-white btn btn--sm" style="background-color: #1e3774;">
                            @lang('Login')
                        </a>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <style>
        .form-group label a{
            font-size:12px;
        }
        .bg-image {
            position: absolute;
            width: 100%;
            height: 50vh;
            background-image: url('{{ asset('assets/images/auth/bg.png') }}');
            background-size: cover;
            background-position: bottom center;
            background-repeat: no-repeat;
            z-index: 0;
        }

        .account-right {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
        }

        .account-container {
            display: flex;
        }

        .not-registered {
            display: flex;
            justify-content: center;
            color: #000;
        }

        .welcome-text,
        .subtitle-text {
            color: #fff;
        }

        .account-container {
            /* background: linear-gradient(to bottom, #7ece92, #359e72); */
            position: relative;
            width: 100%;
            min-height: 100vh;
        }

        .details-container {
            background-color: #ECF4E8;
            width: 100%;
            max-width: 600px;
            padding: 15px 20px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 10px;
            min-height: auto;
        }

        .welcome-text {
            font-size: 30px;
            font-weight: 800;
        }

        .subtitle-text {
            margin-bottom: 8px;
        }

        .form-label {
            color: #003333;
            display: block;
        }

        .bottom-border-input {
            width: 100%;
            padding: 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid #1e3774;
            outline: none;
            transition: border-color 0.3s;
        }

        .password-wrapper {
            position: relative;
        }

        .password-input-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }

        .sign-up-btn {
            width: 100%;
            padding: 8px;
            background-color: #1e3774;
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 1px 1px rgba(0, 0, 0, .25);
        }
        .sign-in-btn-google{
            background-color: #fff;
            width: 100%;
            padding: 8px;
            color: #000;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 1px 1px rgba(0, 0, 0, .25);
        }
        .sign-in-btn-google:hover{
            outline: none;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 2px 4px rgba(0, 0, 0, .25);
        }
        .sign-up-btn:hover {
            background-color: #015d4e;
            color:#fff !important;
            outline: none;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 2px 4px rgba(0, 0, 0, .25);
        }

        .not-registered {
            margin-top: 5px;
            display: flex;
            justify-content: flex-start;
        }

        .sign-up {
            padding-left: 5px;
            color: #1e3774;
            font-weight: 600;
            text-decoration: underline;
        }

        .forgot-pass {
            color: #1e3774;
            display: block;
            margin-top: 5px;
            text-decoration: underline;
        }

        .quick-access {
            margin-bottom: 4px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        @media(max-width: 768px) {
            .form-group{
                font-size: 12px;
            }
            .account-left {
                display: none;
            }

            .account-right {
                height: 100vh;
                padding: 10px 0;
                align-items: center;
                justify-content: center;
                /* overflow-y: auto;  */
            }

            .register-form-wrapper {
                transform: scale(0.95);
                margin: 0 auto;
                max-height: 98vh;
            }

            .details-container {
                width: 100%;
                max-width: none;
                padding: 10px 15px;
                min-height: auto;
                margin-bottom: 10px;
            }

            .welcome-text {
                font-size: 22px;
                margin-bottom: 0;
            }

            .subtitle-text {
                font-size: 14px;
                margin-bottom: 5px;
            }

            .form-group-details {
                margin-bottom: 5px !important;
            }

            .form-label {
                font-size: 12px;
                margin-bottom: 0;
            }

            .bottom-border-input {
                font-size: 14px;
                margin-bottom: 0;
                height: 25px;
            }

            .sign-in-btn {
                padding: 6px;
                margin-bottom: 5px;
                font-size: 12px;
            }
            .sign-in-btn-google{
                font-size: 14px;
            }

            .mb-3 {
                margin-bottom: 0.5rem !important;
            }

            .mb-1 {
                margin-bottom: 0.2rem !important;
            }

            .quick-access {
                font-size: 13px;
                margin: 5px 0;
            }

            .not-registered {
                font-size: 13px;
                margin-top: 5px;
                margin-bottom: 0;
            }
        }

        @media(max-height: 600px) {
                .welcome-text {
                    font-size: 20px;
                }

                .subtitle-text {
                    font-size: 12px;
                }

                .form-label {
                    font-size: 12px;
                }

                .bottom-border-input {
                    height: 22px;
                }

                .register-form-wrapper {
                    transform: scale(0.9);
                }

                .sign-in-btn {
                    padding: 5px;
                }
        }

        @media(min-width:769px) {
            .bg-image {
                background-image: none;
            }

            .details {
                display: flex;
                flex-direction: row;
            }

            .account-left {
                width: 50%;
                height: 100vh;
                background-image: url('{{ asset('assets/images/auth/bg.png') }}');
                background-size: cover;
                background-position: bottom center;
                background-repeat: no-repeat;
                z-index: 0;
            }

            .details-container {
                max-width: 100%;
                /* Allow container to use available width */
                padding: 20px 25px;
                /* Add more padding for better spacing */
            }

            .register-form-wrapper {
                width: 90%;
                /* Allow wrapper to use more of the available space */
                max-width: 600px;
                transform: scale(0.90);
                /* Increase maximum width */
            }
        }
    </style>
@endpush


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
                    $(this).closest('.form-group-details').addClass('hover-input-popup');
                });
                $('[name=password]').focusout(function() {
                    $(this).closest('.form-group-details').removeClass('hover-input-popup');
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

        document.addEventListener('DOMContentLoaded', function() {
            // Get all toggle password buttons
            const toggleButtons = document.querySelectorAll('.toggle-password');

            // Add click event to each button
            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    // Get the target input field from data-target attribute
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);

                    // Toggle the input type between password and text
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' :
                        'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle the icon between eye and eye-slash
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>

{{-- <script>
  // Direct registration confirmation script
    document.addEventListener('DOMContentLoaded', function() {
        // Store original form submission
        const registerForm = document.querySelector('form.verify-gcaptcha');
        const googleButton = document.querySelector('a[href*="google.redirect"]');
        let isDirectRegistration = true;

        // Check if there's a referral parameter in the URL or form
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('affiliated_by') || document.querySelector('input[name="referredby"]')) {
            isDirectRegistration = false;
        }

        if (registerForm) {
            const originalSubmitHandler = registerForm.onsubmit;

            registerForm.onsubmit = function(e) {
                // If direct registration and modal not shown yet
                if (isDirectRegistration && !sessionStorage.getItem('directRegistrationConfirmed')) {
                    e.preventDefault();
                    $('#directRegisterConfirmModal').modal('show');
                    return false;
                }

                // If modal was confirmed or not direct registration, proceed with original submission
                if (originalSubmitHandler) {
                    return originalSubmitHandler.call(this, e);
                }
                return true;
            };
        }

        // Handle Google button click - using jQuery for more reliable event capturing
        // Using both button and parent container to ensure we catch the event
        $('.sign-in-btn-google, .sign-in-btn-google a').on('click', function(e) {
            if (isDirectRegistration && !sessionStorage.getItem('directRegistrationConfirmed')) {
                e.preventDefault();
                e.stopPropagation(); // Stop event bubbling

                // Store the Google URL to redirect after confirmation
                const googleHref = $(this).attr('href') || $(this).find('a').attr('href');
                if (googleHref) {
                    sessionStorage.setItem('pendingGoogleRedirect', googleHref);
                }

                $('#directRegisterConfirmModal').modal('show');
                return false;
            }
        });

        // Add event listener for confirmation button
        const confirmButton = document.getElementById('confirmDirectRegistration');
        if (confirmButton) {
            confirmButton.addEventListener('click', function() {
                // Mark as confirmed in session storage
                sessionStorage.setItem('directRegistrationConfirmed', 'true');

                // Close the modal
                $('#directRegisterConfirmModal').modal('hide');

                // Check if we have a pending Google redirect
                const pendingGoogleRedirect = sessionStorage.getItem('pendingGoogleRedirect');
                if (pendingGoogleRedirect) {
                    sessionStorage.removeItem('pendingGoogleRedirect');
                    window.location.href = pendingGoogleRedirect;
                } else {
                    // Submit the form
                    registerForm.submit();
                }
            });
        }
    });
</script> --}}

<script>
    // Direct registration confirmation script
    document.addEventListener('DOMContentLoaded', function() {
        // Store original form submission
        const registerForm = document.getElementById('registrationForm');
        let isDirectRegistration = true;

        // Check if there's a referral parameter in the URL or form
        const urlParams = new URLSearchParams(window.location.search);
        const referredByInput = document.querySelector('input[name="referredby"]');

        if (urlParams.has('affiliated_by') || (referredByInput && referredByInput.value)) {
            isDirectRegistration = false;
        }

        // Handle form submission
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                // If direct registration and not confirmed yet
                if (isDirectRegistration && !sessionStorage.getItem('directRegistrationConfirmed')) {
                    e.preventDefault();
                    $('#directRegisterConfirmModal').modal('show');
                    return false;
                }

                // Otherwise let the form submit normally
                return true;
            });
        }

        // Handle Google button click
        const googleButton = document.querySelector('.sign-in-btn-google');
        const googleLink = document.querySelector('.sign-in-btn-google a');

        if (googleButton) {
            googleButton.addEventListener('click', function(e) {
                if (isDirectRegistration && !sessionStorage.getItem('directRegistrationConfirmed')) {
                    e.preventDefault();

                    // Store the Google URL to redirect after confirmation
                    const googleHref = googleLink.getAttribute('href');
                    sessionStorage.setItem('pendingGoogleRedirect', googleHref);

                    $('#directRegisterConfirmModal').modal('show');
                    return false;
                }
            });
        }

        if (googleLink) {
            googleLink.addEventListener('click', function(e) {
                if (isDirectRegistration && !sessionStorage.getItem('directRegistrationConfirmed')) {
                    e.preventDefault();

                    // Store the Google URL to redirect after confirmation
                    const googleHref = this.getAttribute('href');
                    sessionStorage.setItem('pendingGoogleRedirect', googleHref);

                    $('#directRegisterConfirmModal').modal('show');
                    return false;
                }
            });
        }

        // Confirmation button click handler
        const confirmButton = document.getElementById('confirmDirectRegistration');
        if (confirmButton) {
            confirmButton.addEventListener('click', function() {
                // Mark as confirmed in session storage
                sessionStorage.setItem('directRegistrationConfirmed', 'true');

                // Close the modal
                $('#directRegisterConfirmModal').modal('hide');

                // Check if we have a pending Google redirect
                const pendingGoogleRedirect = sessionStorage.getItem('pendingGoogleRedirect');
                if (pendingGoogleRedirect) {
                    sessionStorage.removeItem('pendingGoogleRedirect');
                    window.location.href = pendingGoogleRedirect;
                } else {
                    // Submit the form
                    registerForm.submit();
                }
            });
        }
    });
</script>
@endpush
