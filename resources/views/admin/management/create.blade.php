@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="permissionManagementForm"
                        action="{{ isset($admin) ? route('admin.management.store', $admin->id) : route('admin.management.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row d-flex">
                            @if (isset($admin->last_login_at))
                                <div class="col-md-12 d-flex">
                                    <div class="form-group text-right">
                                        Last Login: {{ $admin->last_login_at }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            {{-- Image --}}
                            <div class="col-md-4 text-center">
                                <div class="container image-container text-center">
                                    <x-image-uploader name="image" :imagePath="getImage(
                                        getFilePath('adminManagement') . '/' . @$admin->image,
                                        getFileSize('adminManagement'),
                                    )" :size="false"
                                        class="w-100 custom-rounded-image text-center" id="productImageUpload"
                                        :required="!isset($admin) || !$admin->image" value="{{ old('image', @$admin->image) }}" />
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    {{-- Name --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Name')</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', @$admin->name) }}" required>
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email')</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', @$admin->email) }}" required>
                                        </div>
                                    </div>

                                    {{-- Username --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Username')</label>
                                            <input type="text" name="username" class="form-control"
                                                value="{{ old('username', @$admin->username) }}" required>
                                        </div>
                                    </div>

                                    {{-- Phone --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Phone')</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone', @$admin->phone) }}">
                                        </div>
                                    </div>

                                    {{-- Address --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Address')</label>
                                            <textarea name="address" class="form-control" rows="2">{{ old('address', @$admin->address) }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Designation --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Designation')</label>
                                            <input type="text" name="designation" class="form-control"
                                                value="{{ old('designation', @$admin->designation) }}">
                                        </div>
                                    </div>

                                    {{-- Role --}}
                                    @if (isset($admin))
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Role')</label>
                                                <select name="role" class="form-control select2" required>
                                                    <option value="">@lang('Select Role')</option>
                                                    {{-- <option value="super admin" @selected(old('role', @$admin->roles->first()->name ?? @$admin->role) == 'super admin')>@lang('Super Admin') --}}
                                                    </option>

                                                    @php
                                                        $currentAdmin = auth()->guard('admin')->user();
                                                    @endphp

                                                    @if ($currentAdmin->hasRole('super admin'))
                                                        <option value="admin" @selected(old('role', @$admin->roles->first()->name ?? @$admin->role) == 'admin')>
                                                            @lang('Admin')
                                                        </option>
                                                        <option value="sub admin" @selected(old('role', @$admin->roles->first()->name ?? @$admin->role) == 'sub admin')>
                                                            @lang('Sub Admin')
                                                        </option>
                                                    @elseif($currentAdmin->hasRole('admin'))
                                                        <option value="sub admin" @selected(old('role', @$admin->roles->first()->name ?? @$admin->role) == 'sub admin')>
                                                            @lang('Sub Admin')
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Role')</label>
                                                <select name="role" class="form-control select2" required>
                                                    <option value="">@lang('Select Role')</option>

                                                    @php
                                                        $currentAdmin = auth()->guard('admin')->user();
                                                    @endphp

                                                    @if ($currentAdmin->hasRole('super admin'))
                                                        <option value="admin">@lang('Admin')</option>
                                                        <option value="sub admin">@lang('Sub Admin')</option>
                                                    @elseif($currentAdmin->hasRole('admin'))
                                                        <option value="sub admin">@lang('Sub Admin')</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Status --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Status')</label>
                                            <select name="status" class="form-control select2" required>
                                                <option value="">@lang('Select Status')</option>
                                                <option value="1" @selected(old('status', @$admin->status) == 1)>@lang('Active')
                                                </option>
                                                <option value="0" @selected(old('status', @$admin->status) == 0)>@lang('Inactive')
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Joined At --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Joined At')</label>
                                            <input type="date" name="joined_at" class="form-control"
                                                value="{{ old('joined_at', @$admin->joined_at) }}">
                                        </div>
                                    </div>

                                    {{-- Password --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Password')</label>
                                            <input type="password" name="password" class="form-control"
                                                @if (!isset($admin)) required @endif>
                                            @if (isset($admin))
                                                <small class="text-muted">@lang('Leave blank to keep current password')</small>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Confirm Password')</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if (isset($admin))
        <a href="{{ route('admin.management.permission', $admin->id) }}" class="btn btn-sm btn-outline--success">
            <i class="las la-cogs"></i>@lang('Permissions')
        </a>
    @endif
    <x-back route="{{ route('admin.management.index') }}" class="btn btn-outline--dark" />
@endpush



@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nicedit/0.9/nicEdit.min.js"></script>
@endpush


@push('style')
    <style>
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .body {
            overflow-y: auto;
            height: 100%;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-group input.form-control {
            border-radius: 7px;
        }

        .image-upload-wrapper {
            width: 280px;
            height: 280px;
            position: relative;
            border-radius: 50%;
            overflow: visible;
            border: 3px solid #f1f1f1;
            box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
            margin: 0 auto;
        }

        .image-upload-preview {
            width: 100%;
            height: 100%;
            display: block;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            border-radius: 50%;
            border: none;
            box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.25);
            justify-content: center;
            align-items: center;
        }

        .image-upload-input-wrapper {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10;
        }

        .image-upload-input-wrapper input {
            width: 0;
            opacity: 0;
        }

        .image-upload-input-wrapper label {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            text-align: center;
            border: 2px solid #fff;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0;
            background-color: #fff;
        }

        i.fa-solid fa-arrow-up-from-bracket {
            color: #111;
        }

        .dashboard__content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 20px;
        }

        @media (max-width: 768px) {
            .mobile-view-margin {
                margin-top: 50px;
            }
        }
    </style>
@endpush
