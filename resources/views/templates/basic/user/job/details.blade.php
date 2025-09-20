@extends($activeTemplate . 'layouts.master')
@section('panel')
<h3 class="mb-4 fw-bold text-dark mt-10">Job Details</h3>
    <div class="dashboard__content">
        <div class="finished__jobs__wrapper">
            <div class="finished__jobs__header d-flex flex-wrap justify-content-between align-items-center mb-2">
                <h4 class="pe-4 mb-2">
                    @lang('Job ID : ') {{ __($job->job_code) }}
                </h4>
                <h4 class="pe-4 mb-2">
                    @lang('Budget : ') {{ showAmount($job->total) }}
                </h4>
                <h4 class="pe-4 mb-2">
                    @lang('Workers : ') {{ __($job->workers) }}
                </h4>
                <a href="{{ route('user.job.history') }}" class="btn btn--sm btn--base mb-2">
                    @lang('Go Back')
                </a>
            </div>
            @forelse ($job->proves as $prove)
                <div class="finished__job__item bg-white">
                    <div class="row w-100 justify-content-between g-0 gy-3">
                        <div class="col-md-6 col-lg-12 col-xl-6">
                            <div class="job__header me-3">
                                <h5 class="job__header-title">
                                    <a href="{{ route('job.details', $job->id) }}">
                                        {{ __($job->title) }}
                                    </a>
                                </h5>
                                <p class="job-post-date">
                                    {{ showDateTime($job->created_at, 'M d, Y h:i:s a') }}
                                </p>
                                <h3 class="job__price d-inline-block">
                                    <sub>{{ gs('cur_sym') }}</sub>
                                    {{ showAmount($job->rate, currencyFormat: false) }}
                                </h3>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-12 col-xl-6">
                            <div class=" job__body d-flex flex-wrap justify-content-between align-items-center">
                                <div class="employer__wrapper d-inline-flex flex-wrap align-items-center">
                                    <div class="employer__thumb me-3">
                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $prove->user->image, getFileSize('userProfile'), true) }}"
                                            alt="@lang('User')">
                                    </div>
                                    <div class="content">
                                        <h6 class="employer__name">{{ $prove->user->username }}</h6>
                                        @if ($prove->status == Status::JOB_PROVE_PENDING)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif($prove->status == Status::JOB_PROVE_APPROVE)
                                            <span class="badge badge--success">@lang('Approved')</span>
                                        @elseif($prove->status == Status::JOB_PROVE_START)
                                            <span class="badge badge--success">@lang('Started')</span>
                                        @elseif($prove->status == Status::JOB_PROVE_COMPLETE)
                                            <span class="badge badge--success">@lang('Completed')</span>
                                        @elseif($prove->status == Status::JOB_PROVE_CANCEL)
                                            <span class="badge badge--danger">@lang('Cancled')</span>
                                        @elseif($prove->status == Status::JOB_PROVE_COMPLETE_CONFIRM)
                                            <span class="badge badge--success">@lang('Finish Confirmed')</span>
                                        @else
                                        @endif
                                    </div>
                                </div>
                                <div class="job__footer">
                                    <a href="{{ route('user.job.attachment', encrypt($prove->id)) }}"
                                        class="cmn--btn btn--sm">@lang('Detail')</a>
                                    <p class="take-on">@lang('Project take on')</p>
                                    <p class="take-on-date">{{ showDateTime($prove->created_at, 'Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="finished__job__item">
                    <div class="row w-100 justify-content-between g-0 gy-3">
                        <h3 class="text--base text-center">{{ __($emptyMessage) }}</h3>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
