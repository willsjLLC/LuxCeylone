@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.report.product.purchase.commission.history') }}" method="GET">
                <div class="px-4 py-3 card-body">
                    <div class="row justify-content-between">
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}" placeholder="@lang('Username')">
                                <button class="input-group-text bg--primary" type="submit"><i class="text-white las la-search"></i></button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="text" data-range="true" name="date" class="form-control date-range" value="{{ request()->date }}" placeholder="@lang('Start date - End date')">
                                <button class="input-group-text bg--primary" type="submit"><i class="text-white las la-search"></i></button>
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
                                <th>@lang('Product')</th>
                                <th>@lang('Company Commission')</th>
                                <th>@lang('Company Expenses')</th>
                                <th>@lang('Customers Commission')</th>
                                <th>@lang('Customers Voucher')</th>
                                <th>@lang('Customers Festival')</th>
                                <th>@lang('Customers Saving')</th>
                                <th>@lang('Leader Bonus')</th>
                                <th>@lang('Leader Vehicle Lease')</th>
                                <th>@lang('Leader Petrol')</th>
                                <th>@lang('Top Leader Car')</th>
                                <th>@lang('Top Leader House')</th>
                                <th>@lang('Top Leader Expenses')</th>
                                <th>@lang('Created At')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissions as $commission)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ @$commission->Product->name }}</span>
                                    </td>

                                    <td class="fw-bold">{{ showAmount($commission->company_commission) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->company_expenses) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->customers_commission) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->customers_voucher) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->customers_festival) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->customers_saving) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->leader_bonus) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->leader_vehicle_lease) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->leader_petrol) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->top_leader_car) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->top_leader_house) }}</td>
                                    <td class="fw-bold">{{ showAmount($commission->top_leader_expenses) }}</td>

                                    <td>
                                        {{ showDateTime($commission->created_at) }}<br>{{ diffForHumans($commission->created_at) }}
                                    </td>

                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.products.edit', $commission->product_id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Product Details')
                                            </a>
                                        </div>
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
            @if($commissions->hasPages())
            <div class="py-4 card-footer">
                {{ paginateLinks($commissions) }}
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

@push('breadcrumb-plugins')
    <x-search-form placeholder="Product Name" />
@endpush
