@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="dashboard__content contact__form__wrapper">
        <h5>@lang('Stripe Storefront')</h5>
        <hr>

        <form action="{{ $data->url }}" method="{{ $data->method }}">
            <ul class="list-group list-group-flush text-center">
                <li class="list-group-item d-flex justify-content-between px-0">
                    @lang('You have to pay '):
                    <strong>{{showAmount($deposit->final_amount,currencyFormat:false)}} {{__($deposit->method_currency)}}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                    @lang('You will get '):
                    <strong>{{ showAmount($deposit->amount) }}</strong>
                </li>
            </ul>
            <script src="{{ $data->src }}" class="stripe-button"
                @foreach ($data->val as $key => $value)
                            data-{{ $key }}="{{ $value }}" @endforeach>
            </script>
        </form>
    </div>
@endsection
@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('button[type="submit"]').removeClass().addClass("btn btn--base w-100 mt-3").text("Pay Now");
        })(jQuery);
    </script>
@endpush
