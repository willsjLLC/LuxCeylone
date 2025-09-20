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
                                    <th class="text-center">@lang('Code')</th>
                                    <th class="text-center">@lang('Order Status')</th>
                                    <th class="text-center">@lang('Payment Status')</th>
                                    <th class="text-center">@lang('Delivery Status')</th>
                                    <th class="text-start">@lang('Customer Name')</th>
                                    <th class="text-end">@lang('Total Discount')</th>
                                    <th class="text-end">@lang('Quantity')</th>
                                    <th class="text-end">@lang('Sub Total')</th>
                                    <th class="text-end">@lang('Net Total')</th>
                                    <th class="text-end">@lang('Payment Method')</th>
                                    <th class="text-center">@lang('Date')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="text-center">
                                            <strong>{{ __($order->code) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if ($order->status == Status::ORDER_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($order->status == Status::ORDER_PROCESSING)
                                                <span class="badge badge--warning">@lang('Processing')</span>
                                            @elseif($order->status == Status::ORDER_PENDING)
                                                <span class="badge badge--dark">@lang('Pending')</span>
                                            @elseif($order->status == Status::ORDER_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($order->payment_status == Status::PAYMENT_INITIATE)
                                                <span class="badge badge--primary">@lang('Initiate')</span>
                                            @elseif ($order->payment_status == Status::PAYMENT_SUCCESS)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($order->payment_status == Status::PAYMENT_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($order->payment_status == Status::PAYMENT_REJECT)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($order->delivery_status == Status::DELIVERY_PENDING)
                                                <span class="badge badge--warning">@lang('Processing')</span>
                                            @elseif ($order->delivery_status == Status::DELIVERY_COMPLETE)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($order->delivery_status == Status::DELIVERY_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-start">
                                            <span class="fw-bold">{{ @$order->customer_name }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{ $order->discount ?? __('No Discount Data') }}
                                        </td>
                                        <td class="text-end">
                                            {{ $order->quantity ?? __('No Quantity Data') }}
                                        </td>
                                        <td class="text-end">
                                            {{ $order->sub_total ? showAmount($order->sub_total) : __('No Sub Total Data') }}
                                        </td>
                                        <td class="text-end">
                                            {{ $order->net_total ? showAmount($order->net_total) : __('No Net Total Data') }}
                                        </td>
                                        <td class="text-end">
                                            {{ $order->payment_method ? $order->payment_method : __('No Payment Method Data') }}
                                        </td>

                                        <td class="text-center">
                                            <span>{{ showDateTime($order->created_at) }}</span><br>
                                            <small class="text-muted">{{ diffForHumans($order->created_at) }}</small>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.order.view', $order->id) }}"
                                                class="btn btn-sm btn-outline--success">
                                                <i class="las la-eye"></i>@lang('view')
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
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <div class="modal categoryModal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Category Description')</h5>
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

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    {{-- <a href="{{ route('admin.products.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i>@lang('Add New')
    </a> --}}
@endpush
