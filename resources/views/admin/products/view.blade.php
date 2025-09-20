{{-- @extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-4 col-md-4 mb-30">
            <div class="overflow-hidden card custom--card b-radius--10 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Product created by')
                        {{ $product->admin->name ?? 'N/A' }}
                    </h5>
                    @if (!empty($product->admin->image))
                        <div class="p-3 bg--white">
                            <div class="text-center side_Image">
                                @if ($product->image_url)
                                    <img src="{{ getImage(getFilePath('product') . '/' . $product->image_url, getFileSize('product')) }}"
                                        class="product-image">
                                @else
                                    <img src="{{ asset('assets/admin/images/empty.png') }}"
                                        class="b-radius--10 product-image">
                                @endif
                            </div>
                        </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        @if (!empty($product->product_code))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Product Code')
                                <span class="fw-bold">{{ $product->product_code }}</span>
                            </li>
                        @endif

                        @if (!empty($product->admin->username))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Username')
                                <span class="fw-bold">{{ $product->admin->username }}</span>
                            </li>
                        @endif

                        @if (!empty($product->quantity))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Quantity')
                                <span class="fw-bold">{{ $product->quantity }}</span>
                            </li>
                        @endif

                        @if (!empty($product->quantity))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Weight')
                                <span class="fw-bold">{{ intval($product->weight) }} {{ $product->unit }}</span>
                            </li>
                        @endif

                        @if (!empty($product->cost))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Cost')
                                <span class="fw-bold">{{ showAmount($product->cost) }}</span>
                            </li>
                        @endif

                        @if (!empty($product->profit))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Profit')
                                <span class="fw-bold">{{ showAmount($product->profit) }}</span>
                            </li>
                        @endif

                        @if (!empty($product->selling_price))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Selling Price')
                                <span class="fw-bold">{{ showAmount($product->selling_price) }} </span>
                            </li>
                        @endif

                        @if (isset($product->status))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Status')
                                @if ($product->status == 'active')
                                    <span class="badge badge--success">@lang('Active')</span>
                                @else
                                    <span class="badge badge--danger">@lang('Inactive')</span>
                                @endif
                            </li>
                        @endif

                        @if (!empty($product->created_at))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Date')
                                <span class="fw-bold">{{ showDateTime($product->created_at) }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-8 mb-30">
            <div class="overflow-hidden card custom--card box--shadow1">
                <div class="card-body">
                    <h5 class="pb-2 card-title border-bottom">@lang('Product More Information')</h5>
                    <div class="row gy-3">
                        @if (!empty($product->name))
                            <div class="col-md-8">
                                <h6>@lang('Product Name')</h6>
                                <p>{{ $product->name }}</p>
                            </div>
                        @endif

                        @if (!empty($product->description))
                            <div class="col-md-8">
                                <h6>@lang('Product Description')</h6>
                                <p>{!! $product->description !!}</p>
                            </div>
                        @endif

                        @if (!empty($product->weight))
                            <div class="col-md-8">
                                <h6>@lang('Weight')</h6>
                                <p>{{ intval($product->weight) }} {{ $product->unit }}</p>
                            </div>
                        @endif

                        @if (!empty($product->category->name))
                            <div class="col-md-8">
                                <h6>@lang('Category')</h6>
                                <p>{{ $product->category->name }}</p>
                            </div>
                        @endif

                        @if (!empty($product->image))
                            <div class="col-md-8">
                                <h6>@lang('Product Image')</h6>
                                <img src="{{ getImage(getFilePath('jobPoster') . '/' . $product->image, getFileSize('jobPoster')) }}"
                                    class="w-50">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        .product-image {
            width: 200px;
            height: 200px;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
@endpush --}}


@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-4 col-md-4 mb-30">
            <div class="overflow-hidden card custom--card b-radius--10 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Product created by')
                        {{ $product->admin->name ?? 'N/A' }}
                    </h5>
                    <div class="p-3 bg--white">
                        <!-- Main Product Image -->
                        <div class="text-center side_Image">
                            @if ($product->image_url)
                                <img src="{{ getImage(getFilePath('product') . '/' . $product->image_url, getFileSize('product')) }}"
                                    class="product-image main-image" data-bs-toggle="modal" data-bs-target="#imageModal"
                                    data-img="{{ getImage(getFilePath('product') . '/' . $product->image_url, getFileSize('product')) }}">
                            @else
                                <img src="{{ asset('assets/admin/images/empty.png') }}"
                                    class="b-radius--10 product-image">
                            @endif
                        </div>
                    </div>

                    <!-- Image Thumbnails -->
                    @if(isset($additional_images) && count($additional_images) > 0)
                    <div class="mt-3 product-thumbnails">
                        <div class="flex-wrap d-flex justify-content-center">
                            @foreach($additional_images as $image)
                                <div class="m-1 thumbnail-container">
                                    <img src="{{ getImage(getFilePath('product') . '/' . $image->image, getFileSize('product')) }}"
                                        class="product-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal"
                                        data-img="{{ getImage(getFilePath('product') . '/' . $image->image, getFileSize('product')) }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <ul class="mt-3 list-group list-group-flush">
                        @if (!empty($product->product_code))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Product Code')
                                <span class="fw-bold">{{ $product->product_code }}</span>
                            </li>
                        @endif

                        @if (!empty($product->admin->username))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Username')
                                <span class="fw-bold">{{ $product->admin->username }}</span>
                            </li>
                        @endif

                        @if (!empty($product->quantity))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Quantity')
                                <span class="fw-bold">{{ $product->quantity }}</span>
                            </li>
                        @endif

                        @if (!empty($product->quantity))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Weight')
                                <span class="fw-bold">{{ intval($product->weight) }} {{ $product->unit }}</span>
                            </li>
                        @endif

                        @if (!empty($product->cost))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Cost')
                                <span class="fw-bold">{{ showAmount($product->cost) }}</span>
                            </li>
                        @endif

                        @if (!empty($product->profit))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Profit')
                                <span class="fw-bold">{{ showAmount($product->profit) }}</span>
                            </li>
                        @endif

                        @if (!empty($product->selling_price))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Selling Price')
                                <span class="fw-bold">{{ showAmount($product->selling_price) }} </span>
                            </li>
                        @endif

                        @if (isset($product->status))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Status')
                                @if ($product->status == 'active')
                                    <span class="badge badge--success">@lang('Active')</span>
                                @else
                                    <span class="badge badge--danger">@lang('Inactive')</span>
                                @endif
                            </li>
                        @endif

                        @if (!empty($product->created_at))
                            <li class="pt-2 pb-2 list-group-item d-flex justify-content-between ">
                                @lang('Date')
                                <span class="fw-bold">{{ showDateTime($product->created_at) }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-8 mb-30">
            <div class="overflow-hidden card custom--card box--shadow1">
                <div class="card-body">
                    <h5 class="pb-2 card-title border-bottom">@lang('Product More Information')</h5>
                    <div class="row gy-3">
                        @if (!empty($product->name))
                            <div class="col-md-8">
                                <h6>@lang('Product Name')</h6>
                                <p>{{ $product->name }}</p>
                            </div>
                        @endif

                        @if (!empty($product->description))
                            <div class="col-md-8">
                                <h6>@lang('Product Description')</h6>
                                <p>{!! $product->description !!}</p>
                            </div>
                        @endif

                        @if (!empty($product->weight))
                            <div class="col-md-8">
                                <h6>@lang('Weight')</h6>
                                <p>{{ intval($product->weight) }} {{ $product->unit }}</p>
                            </div>
                        @endif

                        @if (!empty($product->category->name))
                            <div class="col-md-8">
                                <h6>@lang('Category')</h6>
                                <p>{{ $product->category->name }}</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">@lang('Product Image')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center modal-body">
                    <img src="" id="modalImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        .product-image {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }

        .main-image {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .main-image:hover {
            transform: scale(1.05);
        }

        .product-thumbnails {
            margin-top: 15px;
        }

        .thumbnail-container {
            width: 60px;
            height: 60px;
            overflow: hidden;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .product-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .product-thumbnail:hover {
            transform: scale(1.1);
        }

        .product-gallery-item {
            position: relative;
            height: 150px;
            overflow: hidden;
            margin-bottom: 15px;
            border: 1px solid #e5e5e5;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-gallery-item img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .product-gallery-item img:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // Handle image modal
            $('#imageModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var imgSrc = button.data('img');
                var modal = $(this);
                modal.find('#modalImage').attr('src', imgSrc);
            });

        })(jQuery);
    </script>
@endpush
