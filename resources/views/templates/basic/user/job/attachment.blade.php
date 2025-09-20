@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="row mt-10">
        <div class="col-lg-7 col-xl-8 col-md-12">
            <div class="announcement__details bg-white">
                <h3 class="blog-title">{{ __($prove->job->title) }}</h3>
                <ul class="announcement__meta d-flex flex-wrap mt-2 mb-3 align-items-center">
                    <li><i class="far fa-calendar"></i>
                        {{ showDateTime($prove->job->created_at, 'j F,Y g:i a') }}
                    </li>
                </ul>
                <div class="announcement__details__content">
                    <h6 class="mb-2">@lang('Details') :</h6>
                    <p>{{ $prove->detail }}</p>
                </div>
                @if ($prove->attachment != null)
                    <div class="announcement__details__content">
                        <h6 class="my-2">@lang('Attachment : ')</h6>
                        <a href="{{ route('user.job.download.attachment', encrypt($prove->id)) }}"
                            class="mr-3 text--base"><i class="las la-file"></i>
                            @lang('Attachment')
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-xl-4 col-lg-5 col-md-12 mt-3 sidebar-right theiaStickySidebar">
            <div class="widget-box post-widget attachment-widget">
                <h4 class="pro-title">@lang('Job Request')</h4>
                <ul class="latest-posts m-0">
                    <li class="flex-wrap">
                        <div class="post-thumb">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . @$prove->user->image, avatar: true) }}"
                                alt="@lang('User')">
                        </div>
                        <div class="post-info attachment-info">
                            <h6>{{ $prove->user->username }}</h6>
                            <p>@lang('Rate : '){{ showAmount($prove->job->rate) }}</p>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex flex-wrap w-100" style="gap:7px">
                            @if ($prove->status == Status::JOB_PROVE_PENDING)
                                <button href="javascript:void(0)" class="btn btn--base btn--sm confirmationBtn"
                                    data-action="{{ route('user.job.approve', encrypt($prove->id)) }}"
                                    data-question="@lang('Are you sure to approve job?')">
                                    <i class="las la-check"></i>
                                    @lang('Approve')
                                </button>
                                <button href="javascript:void(0)" class="btn btn--danger btn--sm confirmationBtn"
                                    data-action="{{ route('user.job.reject', encrypt($prove->id)) }}"
                                    data-question="@lang('Are you sure to rejected this job?')">
                                    <i class="las la-times"></i>
                                    @lang('Reject')
                                </button>
                            @elseif($prove->status == Status::JOB_PROVE_APPROVE)
                                <span class="badge badge--success">@lang('Approved')</span>
                            @elseif($prove->status == Status::JOB_PROVE_START)
                                <span class="badge badge--success">@lang('Started')</span>
                            @elseif($prove->status == Status::JOB_PROVE_COMPLETE)
                                <span class="badge badge--success">@lang('Completed')</span>
                            @elseif($prove->status == Status::JOB_PROVE_CANCEL)
                                <span class="badge badge--danger">@lang('Cancled')</span>
                            @else
                            @endif

                        </div>
                        @if ($prove->status == Status::JOB_PROVE_COMPLETE)
                            <div class="mt-3">
                                <button class="btn btn--warning btn--sm finishWorkBtn"
                                    data-job-id="{{ encrypt($prove->id) }}" data-action="finish" data-bs-toggle="modal"
                                    data-bs-target="#finishWorkModal">
                                    <i class="las la-check-circle"></i>
                                    @lang('Confirme Work Finished')
                                </button>
                            </div>
                        @elseif($prove->status == Status::JOB_PROVE_COMPLETE_CONFIRM)
                            <div class="mt-3">
                                <span class="badge badge--success">@lang('Finish Confirmed')</span>
                            </div>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
    <div class="modal fade" id="finishWorkModal" tabindex="-1" aria-labelledby="finishWorkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finishWorkModalLabel">@lang('Confirm Action')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="finishModalMessage">@lang('Are you sure you want to confirm that this job is finished?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <form id="finishWorkForm" method="POST" action="{{ route('user.job.update-status') }}">
                        @csrf
                        <input type="hidden" name="job_prove_id" value="{{ $prove->id }}" id="finishJobId">
                        <input value="confirm_finish" type="hidden" name="action" id="finishAction">
                        <button type="submit" class="btn btn--warning" id="finishActionButton">@lang('Confirm Finish')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            let modal = $('#confirmationModal')
            modal.find('.modal-footer').find('button').addClass('btn--sm');
            modal.find('.modal-header').find('button').replaceWith(
                '<span class="las la-times" data-bs-dismiss="modal"><span>');
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .latest-posts .post-thumb {
            width: 45px;
        }
    </style>
@endpush
