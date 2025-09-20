@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email-Mobile')</th>
                                    <th>@lang('Designation')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Role')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Last Login At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                @if ($admin->image)
                                                    <div class="thumb">
                                                        <img src="{{ getImage(getFilePath('adminManagement') . '/' . $admin->image, getFileSize('adminManagement')) }}"
                                                            class="b-radius--10 withdraw-detailImage">
                                                    </div>
                                                @else
                                                    <div class="thumb">
                                                        <img src="{{ asset('assets/admin/images/empty.png') }}"
                                                            class="b-radius--10 withdraw-detailImage">
                                                    </div>
                                                @endif
                                            </div>
                                        </td>



                                        <td>
                                            <span class="fw-bold">{{ $admin->name }}</span>
                                        </td>
                                        <td>
                                            {{ $admin->email }}<br>{{ $admin->phone }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $admin->designation }}</span>
                                        </td>
                                        <td class="text-center">

                                            @if ($admin->status == Status::ADMIN_ACTIVE)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @elseif ($admin->status == Status::ADMIN_BAN)
                                                <span class="badge badge--danger">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($admin->roles)
                                                <span class="fw-bold">
                                                    {{ $admin->roles->pluck('name')->join(', ') }}
                                                </span>
                                            @endif

                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $admin->username }}</span>
                                        </td>
                                        <td>
                                            {{ showDateTime($admin->last_login_at) }} <br>
                                            {{ diffForHumans($admin->last_login_at) }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-end align-items-center">
                                                <div class="dropdown">
                                                    <a href="{{ route('admin.management.permission', $admin->id) }}"
                                                        class="btn btn-sm btn-outline--success">
                                                        <i class="las la-cogs"></i>@lang('Permissions')
                                                    </a>
                                                    <button class="btn btn-sm btn-outline--primary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="las la-ellipsis-v"></i> @lang('Action')
                                                    </button>

                                                    <div class="dropdown-menu p-0">
                                                        <a href="{{ route('admin.management.edit', $admin->id) }}"
                                                            class="dropdown-item">
                                                            <i class="las la-pen"></i> @lang('Edit')
                                                        </a>

                                                        @if ($admin->status == Status::ADMIN_ACTIVE)
                                                            <a href="javascript:void(0)"
                                                                class="dropdown-item confirmationBtn"
                                                                data-action="{{ route('admin.products.status', $admin->id) }}"
                                                                data-question="@lang('Are you sure to activate this product?')">
                                                                <i class="la la-eye"></i> @lang('Activate')
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                class="dropdown-item confirmationBtn"
                                                                data-action="{{ route('admin.products.status', $admin->id) }}"
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
                @if ($admins->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($admins) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
    <a href="{{ route('admin.management.create') }}" class="btn btn-outline--primary h-45">
        <i class="las la-plus"></i>@lang('Add New')
    </a>

    <a href="{{ route('admin.management.permission.sync') }}" class="btn btn-outline--info h-45">
        <i class="las la-plus"></i>@lang('Sync Permission')
    </a>
@endpush
