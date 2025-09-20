@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.productSubCategory.store', @$subcategory->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-image-uploader name="image" :imagePath="getImage(
                                        getFilePath('productSubCategory') . '/' . @$subcategory->image,
                                        getFileSize('productSubCategory'),
                                    )" :size="false" class="w-100"
                                        id="profilePicUpload1" :required="false" />
                                </div>
                            </div>
                            <div class="col-md-9">

                                <div class="form-group">
                                    <label> @lang('Name')</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', @$subcategory->name) }}" required>
                                </div>
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
                                <div class="form-group">
                                    <label> @lang('Description')</label>
                                    <textarea name="description" class="form-control" cols="30" rows="10" required>{{ old('description', @$subcategory->description) }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary w-100 h-45">
                                @lang('Submit')
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.productSubCategory.index') }}" />
@endpush

@push('style')
    <style>
        .profilePicUpload {
            margin-top: -20px;
        }
    </style>
@endpush
