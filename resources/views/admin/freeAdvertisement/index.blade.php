@extends('admin.layouts.app')
@section('panel')
    <div class="container">

        {{-- Form for Free Ad Configuration --}}
        <div class="col">
            <div class="col-md-12">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="productForm"
                                action="{{ isset($freeAd) ? route('admin.advertisements.package.freeStore', $freeAd->id) : route('admin.advertisements.package.freeStore') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>@lang('No of Advertisements')</label>
                                                    <input type="text" name="no_of_advertisements" class="form-control"
                                                        value="{{ old('no_of_advertisements', @$freeAd->no_of_advertisements) }}"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>@lang('Duration (days)')</label>
                                                    <input type="text" name="advertisement_duration" class="form-control"
                                                        value="{{ old('advertisement_duration', @$freeAd->advertisement_duration) }}"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>@lang('Status')</label>
                                                    <select class="form-control select2" name="status" required>
                                                        <option value="">@lang('Select one')</option>
                                                        <option value="1" @selected(old('status', @$freeAd->status) == '1')>
                                                            @lang('Active')
                                                        </option>
                                                        <option value="0" @selected(old('status', @$freeAd->status) == '0')>
                                                            @lang('Inactive')
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>@lang('Supported Categories')</label>
                                                    <select name="categories[]" class="form-control select2" multiple
                                                        required>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                @if (old('categories') && in_array($category->id, old('categories'))) selected
                                                                @elseif (isset($freeAd) && $freeAd->categories->contains($category->id))
                                                                    selected @endif>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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


        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
@endpush
