@php
    $text = isset($register) ? 'Register' : 'Login';
@endphp

@if (@gs('socialite_credentials')->linkedin->status || @gs('socialite_credentials')->facebook->status == Status::ENABLE || @gs('socialite_credentials')->google->status == Status::ENABLE)
    <p class="text-center">
        @lang("$text with")
    </p>
    <div class="social-link-wrapper">
        @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
        <div class="continue-google flex-grow-1">
            <a href="{{ route('user.social.login', 'google') }}" class="btn w-100 social-login-btn"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang("$text with Google")">
                <span class="google-icon">
                    <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google">
                </span>
            </a>
        </div>
    @endif
        @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
            <div class="continue-facebook flex-grow-1">
                <a href="{{ route('user.social.login', 'facebook') }}" class="btn w-100 social-login-btn"   data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang("$text with Facebook")">
                    <span class="facebook-icon">
                        <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook">
                    </span>
                </a>
            </div>
        @endif
        @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
            <div class="continue-facebook flex-grow-1">
                <a href="{{ route('user.social.login', 'linkedin') }}" class="btn w-100 social-login-btn"   data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang("$text with Linkedin")">
                    <span class="facebook-icon">
                        <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt="Linkedin">
                    </span>
                </a>
            </div>
        @endif
    </div>
@endif

@if (@gs('socialite_credentials')->linkedin->status || @gs('socialite_credentials')->facebook->status == Status::ENABLE || @gs('socialite_credentials')->google->status == Status::ENABLE)
    <div class="text-center mb-3">
        <span>@lang('OR')</span>
    </div>
@endif

@push('style')
    <style>
        .social-login-btn {
            border: 1px solid #cbc4c4;
        }
    </style>
@endpush
