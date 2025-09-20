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
                                    <th class="text-center">@lang('Image')</th>
                                    <th class="text-start">@lang('Code')</th>
                                    <th class="text-start">@lang('Name')</th>
                                    <th class="text-start">@lang('Category')</th>
                                    <th class="text-end">@lang('Quantity')</th>
                                    <th class="text-end">@lang('Weight')</th>
                                    <th class="text-end">@lang('Cost')</th>
                                    <th class="text-end">@lang('Profit')</th>
                                    <th class="text-end">@lang('Original Price')</th>
                                    <th class="text-end">@lang('Selling Price')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center">@lang('Date')</th>
                                    <th class="text-start">@lang('Created by')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="product">
                                                @if ($product->image_url)
                                                    <div class="product-image">
                                                        <img src="{{ getImage(getFilePath('product') . '/' . $product->image_url, getFileSize('product')) }}"
                                                            class="b-radius--10 withdraw-detailImage">
                                                    </div>
                                                @else
                                                    <div class="product-category-image">
                                                        <img src="{{ asset('assets/admin/images/empty.png') }}"
                                                            class="b-radius--10 withdraw-detailImage">
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="user">
                                                <strong>{{ __($product->product_code) }}</strong> <br>
                                                {{ strLimit($product->name, 50) }}
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <span class="fw-bold">{{ @$product->name }}</span>
                                        </td>
                                        <td class="text-start">
                                            @if ($product->category?->name)
                                                {{ $product->category->name }}
                                            @else
                                                <span class="text-muted">@lang('No Category Available')</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold" class="text-right"> {{ $product->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold" class="text-right"> {{ $product->weight }}
                                                {{ $product->unit }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if ($product->cost)
                                                {{ showAmount($product->cost) }}
                                            @else
                                                <span class="text-muted">@lang('No Cost Data')</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($product->profit)
                                                {{ showAmount($product->profit) }}
                                            @else
                                                <span class="text-muted">@lang('No Profit Data')</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($product->original_price)
                                                {{ showAmount($product->original_price) }}
                                            @else
                                                <span class="text-muted">@lang('No Original Price')</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($product->selling_price)
                                                {{ showAmount($product->selling_price) }}
                                            @else
                                                <span class="text-muted">@lang('No Price Data')</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($product->status == 'active')
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="d-block">{{ showDateTime($product->created_at) }}</span>
                                            {{ diffForHumans($product->created_at) }}
                                        </td>
                                        <td class="text-start">
                                            <span class="fw-bold">{{ @$product->admin->name ?? __('N/A') }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-end align-items-center">
                                                <a href="{{ route('admin.products.view', $product->id) }}"
                                                    class="btn btn-sm btn-outline--success">
                                                    <i class="las la-eye"></i> @lang('View')
                                                </a>

                                                 <!-- Add Watermark Button -->
                                                 <a href="{{ route('admin.products.apply-watermark', $product->id) }}"
                                                    class="btn btn-sm btn-outline--info"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Apply Watermark">
                                                     <i class="las la-image"></i>@lang('Watermark')
                                                 </a>

                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline--primary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="las la-ellipsis-v"></i> @lang('Action')
                                                    </button>
                                                    <div class="dropdown-menu p-0">
                                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                                            class="dropdown-item">
                                                            <i class="las la-pen"></i> @lang('Edit')
                                                        </a>

                                                        @if ($product->status == 'inactive')
                                                            <a href="javascript:void(0)"
                                                                class="dropdown-item confirmationBtn"
                                                                data-action="{{ route('admin.products.status', $product->id) }}"
                                                                data-question="@lang('Are you sure to activate this product?')">
                                                                <i class="la la-eye"></i> @lang('Activate')
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                class="dropdown-item confirmationBtn"
                                                                data-action="{{ route('admin.products.status', $product->id) }}"
                                                                data-question="@lang('Are you sure to deactivate this product?')">
                                                                <i class="la la-eye-slash"></i> @lang('Deactivate')
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
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
                @if ($products->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($products) }}
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
    <a href="{{ route('admin.products.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush


@push('style')
    <style>
        .product {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .product-image {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
@endpush
