@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('TRX/Username')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Payment Method')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1"
                                    name="payment_method">
                                    <option value="">@lang('All')</option>
                                    @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->payment_method }}" @selected(request()->payment_method == $paymentMethod->payment_method)>
                                            {{ __(keyToTitle($paymentMethod->payment_method)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Status')</label>
                                <select name="activation_expired" class="form-control select2"
                                    data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @selected(request()->activation_expired == 1)>@lang('Expired')</option>
                                    <option value="0" @selected(request()->activation_expired == 0)>@lang('Active')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Total Jobs Did')</th>
                                    <th>@lang('Package Status')</th>
                                    <th>@lang('Created At')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $log->user->fullname }}</span>
                                            <br>
                                            <span class="small"> <a
                                                    href="{{ appendQuery('search', $log->user->username) }}"><span>@</span>{{ $log->user->username }}</a>
                                            </span>
                                        </td>

                                        <td>
                                            <strong>{{ $log->payment_method }}</strong>
                                        </td>

                                        <td class="budget">
                                            <span
                                                class="fw-bold @if ($log->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $log->trx_type }} @if ($log->transaction)
                                                    {{ showAmount($log->transaction->amount) }}
                                                @else
                                                    {{ __('No transaction data available') }}
                                                @endif
                                            </span>
                                        </td>

                                        <td>{{ $log->total_jobs_did }}</td>

                                        <td class="budget">
                                            <span
                                                class="fw-bold @if ($log->activation_expired == 1) text--danger @else text--success @endif">
                                                @if ($log->activation_expired == 1)
                                                    Expired
                                                @else
                                                    Active
                                                @endif
                                            </span>
                                        </td>

                                        <td>
                                            {{ showDateTime($log->created_at) }}<br>{{ diffForHumans($log->created_at) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush
