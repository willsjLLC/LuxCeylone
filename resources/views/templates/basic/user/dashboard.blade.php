@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <div class="dashboard__content">
        @if (auth()->user()->kv != Status::KYC_VERIFIED)
            @php $kyc = getContent('kyc.content', true); @endphp
            @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="alert-heading">@lang('KYC Documents Rejected')</h4>
                        <button class="btn btn--dark btn--sm" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Show Reason')</button>
                    </div>
                    <p class="mb-0">{{ __(@$kyc->data_values->reject) }} <a href="{{ route('user.kyc.form') }}" class="text--base">@lang('Click Here to Re-submit Documents')</a>.</p>
                    <a href="{{ route('user.kyc.data') }}" class="text--base">@lang('See KYC Data.')</a>
                </div>
            @elseif(auth()->user()->kv == Status::KYC_UNVERIFIED)
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification required')</h4>
                    <p class="mb-0">{{ __(@$kyc->data_values->required) }} <a href="{{ route('user.kyc.form') }}" class="text--base">@lang('Click Here to Submit Documents.')</a></p>
                </div>
            @elseif(auth()->user()->kv == Status::KYC_PENDING)
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification pending')</h4>
                    <p class="mb-0">{{ __(@$kyc->data_values->pending) }} <a href="{{ route('user.kyc.data') }}" class="text--base">@lang('See KYC Data.')</a></p>
                </div>
            @endif
        @endif
        @include($activeTemplate . 'partials.employee_notifications')
        @include($activeTemplate . 'partials.user_history')
        <div class="job__completed card custom--card contact__form__wrapper">
            <div class="job__completed-header d-flex align-items-center justify-content-between">
                <h5>@lang('Jobs Completed')</h5>
            </div>
            <div class="job__completed-body">
                <div id="chartProfile"></div>
            </div>
        </div>
        <div class="finished__jobs__wrapper mt-5">
            <div class="finished__jobs__header d-flex flex-wrap justify-content-between align-items-center mb-2">
                <h4 class="pe-4 mb-2">@lang('Recent Earnings Jobs')</h4>
                <a href="{{ route('user.job.finished') }}" class="btn btn--sm btn--base mb-2">@lang('View All')</a>
            </div>
            <table class="table transaction__table">
                <thead>
                    <tr>
                        <th>@lang('Job Code')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Date')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $job)
                        <tr>
                            <td>
                                <span class="invoice-id">{{ $job->job->job_code }}</span>
                            </td>
                            <td>
                                <span class="amount">
                                    {{ showAmount($job->job->rate) }}
                                </span>
                            </td>
                            <td>
                                @if ($job->status == Status::JOB_PROVE_PENDING)
                                    <span class="badge badge--warning">@lang('Pending')</span>
                                @elseif($job->status == Status::JOB_PROVE_APPROVE)
                                    <span class="badge badge--success">@lang('Approved')</span>
                                @else
                                    <span class="badge badge--danger">@lang('Rejected')</span>
                                @endif
                            </td>
                            <td>
                                <span class="time">{{ showDateTime($job->created_at) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="justify-content-center text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            var color = "{{ gs('base_color') }}";
            var options = {
                series: [{
                    name: "Jobs Completed",
                    data: [
                        @foreach ($jobArr as $job)
                            @json($job['count']),
                        @endforeach
                    ]
                }],
                chart: {
                    height: 360,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: true,

                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#" + color],
                stroke: {
                    curve: 'straight',
                    width: [1]
                },
                markers: {
                    size: 5,
                    colors: ["#" + color],
                    strokeColors: "#" + color,
                    strokeWidth: 1,
                    hover: {
                        size: 6,
                    }
                },
                grid: {
                    position: 'front',
                    borderColor: '#ddd',
                    strokeDashArray: 7,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                xaxis: {
                    categories: [
                        @foreach ($jobArr as $job)
                            @json($job['month']),
                        @endforeach
                    ],
                    lines: {
                        show: true,
                    }
                },
                yaxis: {
                    lines: {
                        show: true,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chartProfile"), options);
            chart.render();

        });
    </script>
@endpush
