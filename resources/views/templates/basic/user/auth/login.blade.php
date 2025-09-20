@extends($activeTemplate . 'layouts.app')
@php
    $loginContent = getContent('login.content', true);
@endphp

@section('app')
    <section class="account-container">
        {{-- <div class="bg-image"></div> --}}
        <div class="account__right bg_img" style="background: url({{ getImage('assets/images/frontend/login/' . $loginContent->data_values->image, '1920x1080') }})">
        </div>
        {{-- <div class="details">
        <div class="account-left"></div> --}}
        <div class="account__left">
                <div class="login-form-wrapper">
                    <h2 class="welcome-text">{{ __($loginContent->data_values->heading) }}</h2>
                    <p class="subtitle-text">{{ __($loginContent->data_values->subheading) }}</p>

                    @include($activeTemplate . 'partials.social_login')
                    <div class="details-container">
                        <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha account-form">
                            @csrf

                            <div class="form-group-details mb-4">
                                <label for="email" class="form-label mb-0">@lang('Username or Email')</label>
                                <input type="text" name="username" value="{{ old('username') }}" class="bottom-border-input" required>
                            </div>

                            <div class="form-group-details">
                                <div class="password-wrapper">
                                    <label for="password" class="form-label mb-0">@lang('Password')</label>
                                    <div class="password-input-wrapper">
                                        <input id="password" type="password" class="bottom-border-input" name="password" required>
                                        <button type="button" class="toggle-password" id="togglePassword">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <x-captcha />

                            <div class="form-group-details checkbox">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    @lang('Remember Me')
                                </label>
                            </div>

                            <div class="mb-3">
                                <button type="submit" id="recaptcha" class="btn sign-in-btn">
                                    @lang('Sign In')
                                </button>
                            </div>
                            
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

                        <p class="mb-0 not-registered">@lang('Not registered?')
                            <a href="{{ route('user.register') }}" class="sign-up">@lang('Sign Up')</a>
                        </p>

                        <a class="forgot-pass" href="{{ route('site.password.request') }}">
                            @lang('Forgot password?')
                        </a>
                    </div>
                </div>
        </div>
    {{-- </div> --}}
        
    </section>

    <style>
        
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
        .form-group-details{
            margin:5px 0;
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
            position: relative;
            width: 100%;
            min-height: 100vh;
        }

        .details-container {
            background-color: #ECF4E8;
            width: 100%;
            padding: 15px 20px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 10px;
            min-height: 300px;
            max-width: none;
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
            border-bottom: 1px solid #027c68;
            outline: none;
            transition: border-color 0.3s;
            font-size: 14px;
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

        .sign-in-btn {
            width: 100%;
            padding: 8px;
            background-color: #027c68;
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sign-in-btn:hover {
            background-color: #015d4e;
            color: #fff !important;
            outline: none;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, .04), 0 2px 4px rgba(0, 0, 0, .25);
        }

        .not-registered {
            display:flex;
            justify-content:flex-start;
            margin-top: 15px;
        }

        .sign-up {
            padding-left: 5px;
            color: #027c68;
            font-weight: 600;
            text-decoration: underline;
        }

        .forgot-pass {
            color: #027c68;
            display: block;
            margin-top: 5px;
            text-decoration: underline;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }
        .login-form-wrapper{
            width: 70%;
            max-width: none; 
        }
        @media(max-width:576px) {
            .login-form-wrapper {
                width: 95%;
            }
        }

        @media(max-width: 768px) {
            .account-left{
                display:none;
            }
            .account-right {
                width: 100%;
                padding: 0 15px; 
            }
            .details-container {
                width: 100%;
                padding: 20px;
                min-height: 270px;
            }

            .quick-access {
                margin-bottom: 8px;
            }
            .welcome-text {
                font-size: 27px;
            }

        }

        @media(min-width:769px){
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
                /* background-image: url('{{ asset('assets/images/auth/bg.png') }}'); */
                background-size: cover;
                background-position: bottom center;
                background-repeat: no-repeat;
                z-index: 0;
            }
            
            .account-right {
                width: 50%;
            }
            
            .login-form-wrapper{
                width: 70%;
            }
            .register-form-wrapper {
                width: 90%;
                margin: 0 auto;
            }
            
            .details-container {
                padding: 25px 30px;
            }
            
            .form-group-details {
                margin: 10px 0 15px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle icon
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endsection
