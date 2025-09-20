@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th class="text-start">@lang('Code')</th>
                                    <th class="text-start">@lang('Name')</th>
                                    <th class="text-start">@lang('Description')</th>
                                    <th class="text-center">@lang('Highlighted Color')</th>
                                    <th class="text-end">@lang('Type')</th>
                                    <th class="text-end">@lang('Duration (Days)')</th>
                                    <th class="text-end">@lang('Price')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center">@lang('Date')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($boostPackages as $package)
                                    <tr>
                                        <td class="text-start">{{ $package->package_code }}</td>
                                        <td class="text-start">{{ $package->name }}</td>
                                        <td class="text-start">{{ strLimit($package->description, 50) }}</td>
                                        <td class="text-center">
                                            {{ $package->highlighted_color ? __('Yes') : __('No') }}
                                        </td>
                                        <td class="text-end">
                                            @if($package->type == 1)
                                                {{ __('Top') }}
                                            @elseif($package->type == 2)
                                                {{ __('Featured') }}
                                            @else($package->type == 3)
                                                {{ __('Urgent') }}
                                            @endif
                                        </td>                                        
                                        <td class="text-end">{{ $package->duration }}</td>
                                        <td class="text-end">{{ showAmount($package->price) }}</td>
                                        <td class="text-center">
                                            @if ($package->status == Status::BOOST_PACKAGE_ACTIVE)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="d-block">{{ showDateTime($package->created_at) }}</span>
                                            {{ diffForHumans($package->created_at) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-outline--primary dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    @lang('Action')
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a href="{{ route('admin.advertisements.boost.package.edit', $package->id) }}"
                                                        class="dropdown-item">
                                                        <i class="las la-pen"></i> @lang('Edit')
                                                    </a>

                                                    @if ($package->status == 0)
                                                        <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.advertisements.boost.package.status', $package->id) }}"
                                                            data-question="@lang('Are you sure to activate this package?')">
                                                            <i class="la la-eye"></i> @lang('Activate')
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.advertisements.boost.package.status', $package->id) }}"
                                                            data-question="@lang('Are you sure to deactivate this package?')">
                                                            <i class="la la-eye-slash"></i> @lang('Deactivate')
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($boostPackages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($packages) }}
                    </div>
                @endif
            </div>
        </div>
        <x-confirmation-modal />
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search boost packages..." />
    @if ($boostPackages->count() >= 3)
      <a href="{{ route('admin.advertisements.boost.package.create') }}" class="btn btn-outline--primary h-45 disabled">
        <i class="las la-plus"></i> @lang('Add New Package')
    </a>  
    @else
    <a href="{{ route('admin.advertisements.boost.package.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i> @lang('Add New Package')
    </a>
    @endif
@endpush

