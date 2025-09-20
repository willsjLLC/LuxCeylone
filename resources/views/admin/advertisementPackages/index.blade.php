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
                                    <th class="text-end">@lang('Type')</th>
                                    <th class="text-end">@lang('No Of Ads')</th>
                                    <th class="text-end">@lang('PKG Duration (Days)')</th>
                                    <th class="text-end">@lang('AD Duration (Days)')</th>
                                    <th class="text-end">@lang('Boost')</th>
                                    <th class="text-end">@lang('Price')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center">@lang('Date')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr>
                                        <td class="text-start">{{ $package->package_code }}</td>
                                        <td class="text-start">{{ $package->name }}</td>
                                        <td class="text-end">
                                            @if ($package->type == 1)
                                                {{ __('Basic') }}
                                            @elseif($package->type == 2)
                                                {{ __('Premium') }}
                                            @elseif($package->type == 3)
                                                {{ __('Enterprises') }}
                                            @else
                                                {{ __('Default') }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $package->no_of_advertisements }}</td>
                                        <td class="text-end">{{ $package->package_duration }}</td>
                                        <td class="text-end">{{ $package->advertisement_duration }}</td>
                                        <td class="text-end">
                                            {{ $package->includes_boost ? __('Yes') : __('No') }}
                                            @if ($package->includes_boost && $package->no_of_boost)
                                                ({{ $package->no_of_boost }})
                                            @endif
                                        </td>
                                        <td class="text-end">{{ showAmount($package->price) }}</td>
                                        <td class="text-center">
                                            @if ($package->status == Status::PACKAGE_ACTIVE)
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
                                                    <a href="{{ route('admin.advertisements.package.edit', $package->id) }}"
                                                        class="dropdown-item">
                                                        <i class="las la-pen"></i> @lang('Edit')
                                                    </a>

                                                    @if ($package->status == 0)
                                                        <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.advertisements.package.status', $package->id) }}"
                                                            data-question="@lang('Are you sure to activate this package?')">
                                                            <i class="la la-eye"></i> @lang('Activate')
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.advertisements.package.status', $package->id) }}"
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
                @if ($packages->hasPages())
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
    <x-search-form placeholder="Search packages..." />
    <a href="{{ route('admin.advertisements.package.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i> @lang('Add New Package')
    </a>
@endpush
