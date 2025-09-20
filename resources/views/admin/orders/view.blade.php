@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-4 mb-30">
            <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item text-center fw-bold">@lang('Order Details')</li>
                                @foreach (['code' => 'Order Code', 'zip' => 'Zip Code', 'city' => 'City', 'country' => 'Country', 'shipping_address' => 'Shipping Address','created_at' => 'Created Date'] as $key => $label)
                                    @if (!empty($order->$key))
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang($label) <span class="fw-bold">{{ $order->$key }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item text-center fw-bold">@lang('Payment & Order Status')</li>
                                @if (!empty($order->payment_method))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Payment Method') <span class="fw-bold">{{ $order->payment_method }}</span>
                                    </li>
                                @endif
                                @if (isset($order->status))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Order Status')
                                        @if ($order->status == Status::ORDER_COMPLETED)
                                            <span class="badge badge--success">@lang('Completed')</span>
                                        @elseif($order->status == Status::ORDER_PROCESSING)
                                            <span class="badge badge--warning">@lang('Processing')</span>
                                        @elseif($order->status == Status::ORDER_PENDING)
                                            <span class="badge badge--dark">@lang('Pending')</span>
                                        @elseif($order->status == Status::ORDER_CANCELED)
                                            <span class="badge badge--danger">@lang('Canceled')</span>
                                        @endif
                                    </li>
                                @endif
                                @if (isset($order->payment_status))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Payment Status')
                                        @if ($order->payment_status == Status::PAYMENT_INITIATE)
                                            <span class="badge badge--primary">@lang('Initiate')</span>
                                        @elseif ($order->payment_status == Status::PAYMENT_SUCCESS)
                                            <span class="badge badge--success">@lang('Completed')</span>
                                        @elseif($order->payment_status == Status::PAYMENT_PENDING)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif($order->payment_status == Status::PAYMENT_REJECT)
                                            <span class="badge badge--danger">@lang('Pending')</span>
                                        @endif
                                    </li>
                                @endif

                                @if (isset($order->delivery_status))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Delivery Status')
                                        @if ($order->delivery_status == Status::DELIVERY_COMPLETE)
                                            <span class="badge badge--success">@lang('Completed')</span>
                                        @elseif($order->delivery_status == Status::DELIVERY_CANCELED)
                                            <span class="badge badge--danger">@lang('Canceled')</span>
                                        @elseif($order->delivery_status == Status::DELIVERY_PENDING && $order->payment_status == Status::PAYMENT_SUCCESS)
                                            <form action="{{ route('admin.order.delivery.update', $order->id) }}"
                                                method="post">
                                                @csrf
                                                <div class="form-group">
                                                    <select class="form-control select2" name="delivery_status">

                                                        <option value="{{ Status::DELIVERY_PENDING }}"
                                                            @selected(old('delivery_status', $order->delivery_status) == Status::DELIVERY_PENDING)>
                                                            @lang('Pending')
                                                        </option>

                                                        <option value="{{ Status::DELIVERY_COMPLETE }}"
                                                            @selected(old('delivery_status', $order->delivery_status) == Status::DELIVERY_COMPLETE)>
                                                            @lang('Completed')
                                                        </option>

                                                        <option value="{{ Status::DELIVERY_CANCELED }}"
                                                            @selected(old('delivery_status', $order->delivery_status) == Status::DELIVERY_CANCELED)>
                                                            @lang('Canceled')
                                                        </option>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn--primary">@lang('Update Status')</button>
                                            </form>
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item text-center fw-bold">@lang('Contact Details')</li>
                                @if (!empty($order->customer_name))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Customer Name') <span class="fw-bold">{{ $order->customer_name }}</span>
                                    </li>
                                @endif
                                @if (!empty($order->email))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Customer Email') <span class="fw-bold">{{ $order->email }}</span>
                                    </li>
                                @endif
                                @if (!empty($order->mobile))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Customer Mobile') <span class="fw-bold">{{ $order->mobile }}</span>
                                    </li>
                                @endif
                                @if (!empty($order->alternative_mobile))
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Alternative Mobile') <span class="fw-bold">{{ $order->alternative_mobile }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="col-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item text-center fw-bold">@lang('Order Summary')</li>
                                @foreach (['quantity' => 'Quantity', 'sub_total' => 'Sub Total', 'discount' => 'Discount', 'net_total' => 'Net Total'] as $key => $label)
                                    @if (!empty($order->$key))
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang($label)
                                            <span class="fw-bold">
                                                @if ($key == 'quantity')
                                                    {{ $order->$key }}
                                                @else
                                                    {{ showAmount($order->$key) }}
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-8 mb-30">
            <div class="card custom--card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Product List')</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-start">@lang('Product Code')</th>
                                    <th class="text-start">@lang('Product Name')</th>
                                    <th class="text-end">@lang('Original Price')</th>
                                    <th class="text-end">@lang('Selling Price')</th>
                                    <th class="text-end">@lang('Discount')</th>
                                    <th class="text-end">@lang('Subtotal')</th>
                                    <th class="text-end">@lang('Quantity')</th>
                                    <th class="text-end">@lang('Net Total')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order_items as $order)
                                    <tr>
                                        <td class="text-start">{{ $order->product->product_code ?? '-' }}</td>
                                        <td class="text-start">{{ $order->product_name ?? '-' }}</td>
                                        <td class="text-end">{{ showAmount($order->original_price) ?? '-' }}</td>
                                        <td class="text-end">{{ showAmount($order->selling_price) ?? '-' }}</td>
                                        <td class="text-end">{{ showAmount($order->discount) ?? __('No Discount') }}</td>
                                        <td class="text-end">{{ showAmount($order->sub_total) ?? '-' }}</td>
                                        <td class="text-end">{{ $order->quantity ?? '-' }}</td>
                                        <td class="text-end">{{ showAmount($order->net_total) ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
