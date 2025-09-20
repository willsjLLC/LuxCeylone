@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="job-details padding-top padding-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="job__details__wrapper">
                        <h3 class="job__details__wrapper-title">
                            {{ __($job->title) }}
                        </h3>
                        <div class="job__details__widget  bg-white">
                            <h4 class="job__details__widget-title">@lang('Job Description : ')</h4>
                            @php
                                echo $job->description;
                            @endphp
                        </div>

                        @if (@$job->proves->where('user_id', auth()->id())->count() < 1)
                            @if ($job->user_id != auth()->id())
                                <div class="job__details__widget  bg-white">
                                    <h4 class="job__details__widget-title">
                                        @lang('Enter The Required Proof Of Job Finished:')
                                    </h4>
                                    <form class="job__finished__form" action="{{ route('user.job.prove', $job->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-group">
                                            <textarea name="detail" class="form--control w-100" required>{{ old('detail') }}</textarea>
                                        </div>
                                        @if ($job->job_proof == 2)
                                            <div class="input-group mt-3">
                                                <input type="file" name="attachment" class="form-control form--control w-100" required accept="{{ $job->allowedExtensions(true) }}">
                                                <span class="info fs--14 mt-2">
                                                    @lang('Allowed File Extensions: '){{ $job->file_name }}
                                                </span>
                                            </div>
                                        @endif
                                        <button type="submit" class="cmn--btn btn--md mt-4  w-100">
                                            @lang('Request for Complete')
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            @if ($job->proves->where('user_id', auth()->id())->where('status', Status::JOB_PROVE_REJECT)->count())
                                <div class="job__details__widget">
                                    <h4 class="job__details__widget-title text-center text--base mb-0">
                                        @lang('Your job prove has been rejected, you can\'t resubmit any job prove')
                                    </h4>
                                </div>
                            @else
                                <div class="job__details__widget">
                                    <h4 class="job__details__widget-title text-center text--base mb-0">
                                        @lang('You are already submitted job prove please wait until the review complete')
                                    </h4>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="job__details__sidebar">
                        <div class="sidebar__widget">
                            <h5 class="sidebar__widget-title">@lang('Job Information')</h5>
                            <div class="info__wrapper bg-white">
                                <div class="info__item">
                                    <div class="icon">
                                        <img src="{{ getImage(getFilePath('jobPoster') . '/' . @$job->attachment, getFileSize('jobPoster')) }}" alt="@lang('freelancer')" class="img-fluid">
                                    </div>
                                    <div class="content">
                                        <h6 class="title">@lang('Job ID : '){{ __(@$job->job_code) }}</h6>
                                        <p>@lang('Job posted by '){{ __(@$job->user->fullname) }}</p>
                                    </div>
                                </div>
                                <div class="info__item">
                                    <div class="icon">
                                        <i class="las la-money-bill-wave-alt"></i>
                                    </div>
                                    <div class="content">
                                        <h4 class="title">{{ showAmount($job->rate) }}</h4>
                                        <p>@lang('You will Earn in this job')</p>
                                    </div>
                                </div>
                                <div class="info__item">
                                    <div class="icon">
                                        <i class="las la-calendar"></i>
                                    </div>
                                    <div class="content">
                                        <h4 class="title">{{ showDateTime($job->created_at, 'M d, Y h:i:s a') }}</h4>
                                        <p>@lang('Published Date')</p>
                                    </div>
                                </div>
                                <div class="info__item">
                                    <div class="icon">
                                        <i class="las la-users"></i>
                                    </div>
                                    <div class="content">
                                        <h4 class="title">{{ $job->quantity }}</h4>
                                        <p>@lang('This Job Vacancy')</p>
                                    </div>
                                </div>
                                <div class="info__item">
                                    <div class="icon">
                                        <i class="las la-user"></i>
                                    </div>
                                    <div class="content">
                                        <h4 class="title">{{ $job->vacancy_available }}</h4>
                                        <p>@lang('Available Job Vacancy')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar__widget">
                            <h5 class="sidebar__widget-title">@lang('Share this Post on:')</h5>
                            <ul class="social-links">
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}">
                                        <i class="lab la-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/intent/tweet?text={{ __($job->title) }}&amp;url={{ urlencode(url()->current()) }}">
                                        <i class="lab la-twitter"></i>
                                    </a>
                                </li>
                                <li><a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __($job->title) }}&media={{ getImage('assets/admin/images/job/' . $job->attachment) }}">
                                        <i class="lab la-pinterest-p"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __($job->title) }}&amp;summary=dit is de linkedin summary">
                                        <i class="lab la-linkedin-in"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
