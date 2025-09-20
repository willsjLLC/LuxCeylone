@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.subcategory.store', @$subcategory->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-image-uploader name="image" :imagePath="getImage(
                                        getFilePath('subcategory') . '/' . @$subcategory->image,
                                        getFileSize('subcategory'),
                                    )" :size="false" class="w-100"
                                        id="profilePicUpload1" :required="false" />
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label> @lang('Name')</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', @$subcategory->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Category')</label>
                                            <select class=" form-control select2" name="category_id" required>
                                                <option value="">@lang('Select one')</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" @selected(@$subcategory->category_id == $category->id)>
                                                        {{ __($category->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>@lang('Support Conditions (used / new)')</label>
                                            <input type="checkbox" name="supports_condition" id="supports_condition"
                                                class="form-check-input" data-bs-toggle="toggle" data-on="@lang('Yes')"
                                                data-off="@lang('No')" data-onstyle="-success" data-offstyle="-danger"
                                                data-width="100%" data-height="50" value="1"
                                                {{ old('supports_condition', @$subcategory->supports_condition) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label> @lang('Description')</label>
                                    <textarea name="description" class="form-control" cols="30" rows="10" required>{{ old('description', @$subcategory->description) }}</textarea>
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
    <x-back route="{{ route('admin.subcategory.index') }}" />
@endpush

@push('style')
    <style>
        .profilePicUpload {
            margin-top: -20px;
        }
    </style>
@endpush
