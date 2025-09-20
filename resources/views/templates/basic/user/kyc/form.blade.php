@extends($activeTemplate.'layouts.master')
@section('panel')
<h3 class="mb-4 fw-bold text-dark mt-10">KYC Form</h3>
<div class="dashboard__content contact__form__wrapper bg-white">
    <div class="campaigns__wrapper">
        <form action="{{route('user.kyc.submit')}}" method="post" enctype="multipart/form-data">
            @csrf
            <x-viser-form identifier="act" identifierValue="kyc" />
            <div class="form-group">
                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
            </div>
        </form>
    </div>
</div>

@endsection
