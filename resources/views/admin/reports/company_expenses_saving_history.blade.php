@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.report.company.expenses-saving-history') }}" method="GET">
                <div class="px-4 py-3 card-body">
                    <div class="row justify-content-between">
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}" placeholder="@lang('Username')">
                                <button class="input-group-text bg--primary" type="submit"><i class="las la-search text-white"></i></button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="text" data-range="true" name="date" class="form-control date-range" value="{{ request()->date }}" placeholder="@lang('Start date - End date')">
                                <button class="input-group-text bg--primary" type="submit"><i class="las la-search text-white"></i></button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <select name="trx_type" class="form-control">
                                    <option value="">@lang('All Type')</option>
                                    <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                                    <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                                </select>
                                <button class="input-group-text bg--primary" type="submit"><i class="las la-search text-white"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="p-0 card-body">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('TRX')</th>
                                <th>@lang('Remark')</th>
                                <th>@lang('Transaction Type')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Charge')</th>
                                <th>@lang('Current Expenses Balance')</th>
                                <th>@lang('Post Saving Balance')</th>
                                <th>@lang('Details')</th>
                                <th>@lang('Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $history)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ @$history->user->fullname }}</span>
                                        <br>
                                        <span class="small"> <a href="{{ appendQuery('search',@$history->user->username) }}"><span>@</span>{{ @$history->user->username }}</a> </span>
                                    </td>

                                    <td class="fw-bold">{{ $history->trx }}</td>

                                    <td class="fw-bold">{{ __($history->remark) }}</td>

                                    <td>
                                        @if($history->trx_type == '+')
                                            <span class="badge badge--success">@lang('Plus')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Minus')</span>
                                        @endif
                                    </td>

                                    <td class="fw-bold {{ $history->trx_type == '+' ? 'text--success' : 'text--danger' }}">
                                        {{ $history->trx_type }} {{ showAmount($history->amount) }}
                                    </td>

                                    <td class="fw-bold">{{ showAmount($history->charge) }}</td>

                                    <td class="fw-bold">{{ showAmount($history->current_expenses_balance) }}</td>

                                    <td class="fw-bold">{{ showAmount($history->post_saving_balance) }}</td>

                                    <td>{{ __($history->details) }}</td>

                                    <td>
                                        {{ showDateTime($history->created_at) }}<br>{{ diffForHumans($history->created_at) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center text-muted" colspan="100%">{{ __($emptyMessage ?? 'No data found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($histories->hasPages())
            <div class="py-4 card-footer">
                {{ paginateLinks($histories) }}
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
