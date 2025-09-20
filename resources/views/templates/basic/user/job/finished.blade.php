@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="col-12">
        <div class="row">
            <h3 class="mb-4 fw-bold text-dark mt-10">Job Finished</h3>
            <!-- Sidebar (Left) -->
            <div class="col-lg-3 col-md-4">
                <div class="accordion mb-4" id="sidebarAccordion">

                    @php
                        $currentRoute = Route::currentRouteName();
                    @endphp


                    @if (auth()->user()->role == 1)
                        <a href="{{ route('user.job.create') }}"
                            class="d-block py-2 accordion--button {{ request()->routeIs('user.job.create') ? 'text-warning' : '' }}">
                            <i class="la la-plus-circle"></i> @lang('Create Job')
                        </a>
                    @endif

                    @if (auth()->user()->role == 1)
                        <a href="{{ route('user.job.history') }}"
                            class="d-block py-2 accordion--button {{ in_array(request()->route()->getName(), ['user.job.history', 'user.job.details']) ? 'text-warning' : '' }}">
                            <i class="la la-history"></i> @lang('Job History')
                        </a>
                    @endif

                    @if (auth()->user()->role == 2)
                    <a href="{{ route('user.job.finished') }}"
                        class="d-block py-2 accordion--button {{ $currentRoute == 'user.job.finished' ? 'text-warning' : '' }}">
                        <i class="la la-check-circle"></i> @lang('Finished Jobs')
                    </a>
                @endif
                @if (auth()->user()->role == 2)
                    <a href="{{ route('user.job.apply') }}"
                        class="d-block py-2 accordion--button {{ $currentRoute == 'user.job.apply' ? 'text-warning' : '' }}">
                        <i class="la la-paper-plane"></i> @lang('Applied Jobs')
                    </a>
                @endif


                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="row">

                    <div class="dashboard__content">
                        <table class="table transaction__table">
                            <thead>
                                <tr>
                                    <th>@lang('Job Code')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Change Status')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jobs as $job)
                                    <tr>
                                        <td>
                                            <span class="invoice-id">{{ __(@$job->job->job_code) }}</span>
                                        </td>
                                        <td>
                                            <span class="amount">
                                                {{ showAmount(@$job->job->rate) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($job->status == Status::JOB_PROVE_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($job->status == Status::JOB_PROVE_APPROVE)
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($job->status == Status::JOB_PROVE_START)
                                                <span class="badge badge--success">@lang('Started')</span>
                                            @elseif($job->status == Status::JOB_PROVE_COMPLETE)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($job->status == Status::JOB_PROVE_CANCEL)
                                                <span class="badge badge--danger">@lang('Cancled')</span>
                                            @elseif($job->status == Status::JOB_PROVE_COMPLETE_CONFIRM)
                                                <span class="badge badge--success">@lang('Finish Confirmed')</span>
                                            @else
                                            @endif
                                        </td>
                                        <h1>
                                            <td>
                                                @if ($job->status == Status::JOB_PROVE_APPROVE)
                                                    <button type="button" class="btn btn-sm btn--warning"
                                                        data-bs-toggle="modal" data-bs-target="#startWorkModal"
                                                        data-job-id="{{ $job->id }}" data-action="start">
                                                        @lang('Start Work')
                                                    </button>
                                                @elseif($job->status == Status::JOB_PROVE_START)
                                                    <button type="button" class="btn btn-sm btn--success"
                                                        data-bs-toggle="modal" data-bs-target="#startWorkModal"
                                                        data-job-id="{{ $job->id }}" data-action="finish">
                                                        @lang('Finish Work')
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="time">{{ showDateTime($job->created_at, 'M d, Y h:i:s a') }}</span>
                                            </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="justify-content-center text-center" colspan="100%">
                                            {{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Start/Cancel/Finish Work Modal -->
                    <div class="modal fade" id="startWorkModal" tabindex="-1" aria-labelledby="startWorkModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="startWorkModalLabel">@lang('Confirm Action')</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p id="modalMessage">@lang('Are you sure you want to start working on this job?')</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">@lang('Cancel')</button>
                                    <form id="startWorkForm" method="POST" action="{{ route('user.job.update-status') }}">
                                        @csrf
                                        <input type="hidden" name="job_prove_id" id="jobId">
                                        <input type="hidden" name="action" id="action">
                                        <button type="submit" class="btn btn--warning"
                                            id="actionButton">@lang('Confirm')</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($jobs->hasPages())
        {{ paginateLinks($jobs) }}
    @endif
@endsection

@push('script')
    <script>
        const startWorkModal = document.getElementById('startWorkModal');
        startWorkModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const jobId = button.getAttribute('data-job-id');
            const action = button.getAttribute('data-action');
            const jobIdInput = document.getElementById('jobId');
            const actionInput = document.getElementById('action');
            const actionButton = document.getElementById('actionButton');
            const modalMessage = document.getElementById('modalMessage');

            jobIdInput.value = jobId;
            actionInput.value = action;

            // Update modal text and button based on action
            if (action === 'start') {
                modalMessage.textContent = '@lang('Are you sure you want to start working on this job?')';
                actionButton.textContent = '@lang('Start Work')';
            } else if (action === 'finish') {
                modalMessage.textContent = '@lang('Are you sure you want to finish this job?')';
                actionButton.textContent = '@lang('Finish Work')';
            }
        });
    </script>
@endpush
