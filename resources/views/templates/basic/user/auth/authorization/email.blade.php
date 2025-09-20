@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8 col-xl-5">
                <div class="d-flex justify-content-center">
                    <div class="verification-code-wrapper ">
                        <div class="verification-area">
                            <form action="{{ route('user.verify.email') }}" method="POST" class="submit-form">
                                @csrf
                                <p class="verification-text">@lang('A 6 digit verification code sent to your email address'): {{ showEmailAddress(auth()->user()->email) }}</p>

                                @include($activeTemplate . 'partials.verification_code')

                                <div class="mb-3">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                                <p>
                                    <span> @lang('If you don\'t get any code'),</span> <span class="countdown-wrapper">@lang('try again after') <span id="countdown" class="fw-bold">--</span> @lang('seconds')</span> <a href="{{ route('user.send.verify.code', 'email') }}" class="try-again-link text--primary d-none"> @lang('Try again')</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush
