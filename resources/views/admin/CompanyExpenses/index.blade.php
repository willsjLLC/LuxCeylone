@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('No Of Item')</th>
                                    <th>@lang('Total Debit')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($companyExpenses as $companyExpense)
                                    <tr>
                                        <td>
                                            {{ __(@$companyExpense->date) }}
                                        </td>
                                        <td>
                                            {{ @$companyExpense->no_of_items }}
                                        </td>
                                        <td>
                                            {{ showAmount(@$companyExpense->total_debit) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.expenses.edit', $companyExpense->id) }}"
                                                class="btn btn-sm btn-outline--primary" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="las la-pen"></i>@lang('Edit')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($companyExpenses->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($companyExpenses) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>


    <div class="modal subCategoryModal fade " tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Subcategory Description')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush


@push('breadcrumb-plugins')
    <form method="GET" action="{{ route('admin.expenses.index') }}" class="d-flex gap-2">
        <div class="input-group">
            <input type="text" data-range="true" name="date" 
                   class="form-control date-range"
                   value="{{ request()->date }}" 
                   placeholder="@lang('Start date - End date or YYYY-MM-DD')">
            <button class="input-group-text bg--primary" type="submit">
                <i class="las la-search text-white"></i>
            </button>
        </div>
        
        @if(request()->search || request()->date)
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline--secondary">
                <i class="las la-times"></i> Clear
            </a>
        @endif
    </form>
    
    <a href="{{ route('admin.expenses.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush

 

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
<script>
    "use strict";
    
    $('.catDescription').on('click', function() {
        let details = $(this).data('cat_details');
        let modal = $('.subCategoryModal');
        modal.find('.modal-body p').html(details)
        modal.modal('show');
    });
    
    (function($) {
        "use strict"
        
        const datePicker = $('.date-range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'MMMM DD, YYYY'
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
        
        $('.date-range').on('apply.daterangepicker', (event, picker) => {
            changeDatePickerText(event, picker.startDate, picker.endDate);
        });
        
        $('.date-range').on('cancel.daterangepicker', function(event, picker) {
            $(this).val('');
        });
        
        if ($('.date-range').val()) {
            let dateRange = $('.date-range').val().split(' - ');
            if (dateRange.length === 2) {
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
        }
        
        $('.date-range').on('input', function() {
            let value = $(this).val();
            if (value.match(/^\d{4}-\d{2}-\d{2}$/)) {
                return;
            }
        });
        
        $('form').on('submit', function() {
            let dateInput = $('.date-range').val();
            if (dateInput) {
                if (dateInput.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    return true;
                }
                if (dateInput.includes(' - ')) {
                    return true;
                }
            }
        });
        
    })(jQuery);
</script>
@endpush