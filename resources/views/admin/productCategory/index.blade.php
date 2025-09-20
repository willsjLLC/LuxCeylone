@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('Image')</th>
                                    <th class="text-start">@lang('Name')</th>
                                    <th class="text-start">@lang('Description')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="text-center">
                                            <div class="product-category">
                                                @if ($category->image_url)
                                                    <div class="product-category-image">
                                                        <img src="{{ getImage(getFilePath('productCategory') . '/' . $category->image_url, getFileSize('productCategory')) }}"
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
                                            <span class="name">{{ __($category->name) }}</span>
                                        </td>

                                        <td class="text-start">
                                            {!! __(strLimit($category->description, 30)) !!}
                                            @if (strlen($category->description) > 30)
                                              <br>
                                              <small class="text--primary catDescription"
                                              role="button" data-cat_details="{!!$category->description !!}">@lang('Read More')</small>
                                            @endif
                                          </td>

                                        <td class="text-center">
                                            @if ($category->status == 'active')
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Inactive')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown">
                                                <i class="las la-ellipsis-v"></i> @lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('admin.productCategories.edit', $category->id) }}"
                                                    class="dropdown-item">
                                                    <i class="las la-pen"></i> @lang('Edit')
                                                </a>
                                                @if ($category->status == 'inactive')
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.productCategories.status', $category->id) }}"
                                                        data-question="@lang('Are you sure to enable this category?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.productCategories.status', $category->id) }}"
                                                        data-question="@lang('Are you sure to disable this category?')">
                                                        <i class="la la-eye-slash"></i> @lang('Inactive')
                                                    </a>
                                                @endif
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($categories) @endphp
                    </div>
                @endif
            </div>
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
    <x-search-form placeholder="Category name" />
    <a href="{{ route('admin.productCategories.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.catDescription').on('click', function() {
                let details = $(this).data('cat_details');
                let modal = $('.categoryModal');
                modal.find('.modal-body p').html(details)
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .table {
            background-color: #fff;
            border-radius: 10px;
        }

        .table-responsive--sm.table-responsive {
            min-height: 200px;
        }

        .card {
            background-color: transparent;
            box-shadow: none;
        }

        .product-category {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .product-category-image {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .product-category-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
@endpush
