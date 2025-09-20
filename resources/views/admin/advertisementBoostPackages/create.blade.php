@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ isset($advertisementBoostPackage) ? route('admin.advertisements.boost.package.store', $advertisementBoostPackage->id) : route('admin.advertisements.boost.package.store') }}"
                        method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Package Name')</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', @$advertisementBoostPackage->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Type')</label>
                                    <select name="type" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>

                                        <option value="1" @selected(old('type', @$advertisementBoostPackage->type) == Status::TOP_PACKAGE) @disabled(
                                            (in_array(Status::TOP_PACKAGE, $types) && !isset($advertisementBoostPackage)) ||
                                                (isset($advertisementBoostPackage) &&
                                                    $advertisementBoostPackage->type != Status::TOP_PACKAGE &&
                                                    in_array(Status::TOP_PACKAGE, $types)))>
                                            @lang('Top')
                                        </option>

                                        <option value="2" @selected(old('type', @$advertisementBoostPackage->type) == Status::FEATURED_PACKAGE) @disabled(
                                            (in_array(Status::FEATURED_PACKAGE, $types) && !isset($advertisementBoostPackage)) ||
                                                (isset($advertisementBoostPackage) &&
                                                    $advertisementBoostPackage->type != Status::FEATURED_PACKAGE &&
                                                    in_array(Status::FEATURED_PACKAGE, $types)))>
                                            @lang('Featured')
                                        </option>

                                        <option value="3" @selected(old('type', @$advertisementBoostPackage->type) == Status::URGENT_PACKAGE) @disabled(
                                            (in_array(Status::URGENT_PACKAGE, $types) && !isset($advertisementBoostPackage)) ||
                                                (isset($advertisementBoostPackage) &&
                                                    $advertisementBoostPackage->type != Status::URGENT_PACKAGE &&
                                                    in_array(Status::URGENT_PACKAGE, $types)))>
                                            @lang('Urgent')
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>@lang('Priority Level')</label>
                                <div class="form-group">
                                    <select name="priority_level" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @if ($types == 1)
                                        @endif
                                        <option value="3" @selected(old('priority_level', @$advertisementBoostPackage->priority_level) == Status::LOW_PRIORITY)>@lang('Low')</option>
                                        <option value="2" @selected(old('priority_level', @$advertisementBoostPackage->priority_level) == Status::MEDIUM_PRIORITY)>@lang('Medium')</option>
                                        <option value="1" @selected(old('priority_level', @$advertisementBoostPackage->priority_level) == Status::HIGH_PRIORITY)>@lang('High')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Price')</label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="{{ old('price', @$advertisementBoostPackage->price) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Duration (days)')</label>
                                    <input type="number" name="duration" class="form-control"
                                        value="{{ old('advertisement_duration', @$advertisementBoostPackage->duration) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <select name="status" class="form-control select2" required>
                                        <option value="1" @selected(old('status', @$advertisementBoostPackage->status) == 1)>@lang('Active')</option>
                                        <option value="0" @selected(old('status', @$advertisementBoostPackage->status) == 0)>@lang('Inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Highlighted Color')</label>
                                    <select name="highlighted_color" class="form-control select2" id="includesBoost"
                                        required>
                                        <option value="1" @selected(old('highlighted_color', @$advertisementBoostPackage->highlighted_color) == 1)>@lang('Yes')</option>
                                        <option value="0" @selected(old('highlighted_color', @$advertisementBoostPackage->highlighted_color) == 0)>@lang('No')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea name="description" class="form-control" rows="5">{{ old('description', @$advertisementBoostPackage->description) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45 mt-3">@lang('Submit')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.advertisements.boost.package.index') }}" />
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            const $includesBoostSelect = $('#includesBoost');
            const $noOfBoostInput = $('#noOfBoost');

            function toggleBoostInput() {
                const val = $includesBoostSelect.val();
                console.log('Includes Boost selected value:', val);
                if (val === '1') {
                    $noOfBoostInput.prop('disabled', false);
                } else {
                    $noOfBoostInput.prop('disabled', true);
                    $noOfBoostInput.val('');
                }
            }

            toggleBoostInput();

            $includesBoostSelect.on('change', toggleBoostInput);
        });
    </script>
@endpush
