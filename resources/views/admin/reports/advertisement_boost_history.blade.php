@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">


        <div class="card">
            <div class="p-0 card-body">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Advertisement')</th>
                                <th>@lang('Transaction')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Impressions')</th>

                                <th>@lang('Boost Date')</th>
                                <th>@lang('Expiry Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($boostHistories as $history)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ @$history->user->fullname }}</span>
                                        <br>
                                        <span class="small"> <a href="{{ appendQuery('search',@$history->user->username) }}"><span>@</span>{{ @$history->user->username }}</a> </span>
                                    </td>

                                    <td>
                                        <strong>{{ @$history->advertisement->title ?? 'N/A' }}</strong>
                                    </td>

                                    <td>
                                        <strong>{{ $history->transaction_id }}</strong>
                                    </td>

                                    <td>
                                        @if($history->status == \App\Constants\Status::BOOST_NOT_STARTED)
                                            <span class="badge badge--warning">@lang('Not Started')</span>
                                        @elseif($history->status == \App\Constants\Status::BOOST_STARTED)
                                            <span class="badge badge--success">@lang('Started')</span>
                                        @elseif($history->status == \App\Constants\Status::BOOST_COMPLETED)
                                            <span class="badge badge--primary">@lang('Completed')</span>

                                        @else
                                            <span class="badge badge--dark">{{ __(keyToTitle($history->status)) }}</span>
                                        @endif
                                    </td>

                                    <td class="budget">
                                        @if($history->is_free_advertisement)
                                            <span class="badge badge--success">@lang('Free')</span>
                                        @else
                                            <span class="fw-bold">{{ showAmount($history->price) }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ number_format($history->impressions) }}</span>
                                            @if($history->status == \App\Constants\Status::BOOST_STARTED)

                                            @elseif($history->status == \App\Constants\Status::BOOST_COMPLETED)
                                                <span class="badge badge--primary">{{ number_format($history->impressions) }} @lang('total')</span>
                                            @endif


                                        </div>
                                    </td>

                                    <td>
                                        {{ showDateTime($history->boosted_date) }}<br>{{ diffForHumans($history->boosted_date) }}
                                    </td>

                                    <td>
                                        {{ showDateTime($history->expiry_date) }}<br>{{ diffForHumans($history->expiry_date) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-muted" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($boostHistories->hasPages())
            <div class="py-4 card-footer">
                {{ paginateLinks($boostHistories) }}
            </div>
            @endif
        </div>
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
        (function($){
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush
