@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ isset($advertisementPackage) ? route('admin.advertisements.package.store', $advertisementPackage->id) : route('admin.advertisements.package.store') }}"
                        method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Package Name')</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', @$advertisementPackage->name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Type')</label>
                                    <select name="type" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>

                                        <option value="1" @selected(old('type', @$advertisementPackage->type) == Status::BASIC_PACKAGE) @disabled(
                                            (in_array(Status::BASIC_PACKAGE, $types) && !isset($advertisementPackage)) ||
                                                (isset($advertisementPackage) &&
                                                    $advertisementPackage->type != Status::BASIC_PACKAGE &&
                                                    in_array(Status::BASIC_PACKAGE, $types)))>
                                            @lang('Basic')
                                        </option>

                                        <option value="2" @selected(old('type', @$advertisementPackage->type) == Status::PREMIUM_PACKAGE) @disabled(
                                            (in_array(Status::PREMIUM_PACKAGE, $types) && !isset($advertisementPackage)) ||
                                                (isset($advertisementPackage) &&
                                                    $advertisementPackage->type != Status::PREMIUM_PACKAGE &&
                                                    in_array(Status::PREMIUM_PACKAGE, $types)))>
                                            @lang('Premium')
                                        </option>

                                        <option value="3" @selected(old('type', @$advertisementPackage->type) == Status::ENTERPRISE_PACKAGE) @disabled(
                                            (in_array(Status::ENTERPRISE_PACKAGE, $types) && !isset($advertisementPackage)) ||
                                                (isset($advertisementPackage) &&
                                                    $advertisementPackage->type != Status::ENTERPRISE_PACKAGE &&
                                                    in_array(Status::ENTERPRISE_PACKAGE, $types)))>
                                            @lang('Enterprise')
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Number of Advertisements')</label>
                                    <input type="number" name="no_of_advertisements" class="form-control"
                                        value="{{ old('no_of_advertisements', @$advertisementPackage->no_of_advertisements) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Price')</label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="{{ old('price', @$advertisementPackage->price) }}" required readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Package Duration (days)')</label>
                                    <input type="number" name="package_duration" class="form-control"
                                        value="{{ old('package_duration', @$advertisementPackage->package_duration) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Advertisement Duration (days)')</label>
                                    <input type="number" name="advertisement_duration" class="form-control"
                                        value="{{ old('advertisement_duration', @$advertisementPackage->advertisement_duration) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Includes Boost')</label>
                                    <select name="includes_boost" class="form-control select2" id="includesBoost" required>
                                        <option value="1" @selected(old('includes_boost', @$advertisementPackage->includes_boost) == 1)>@lang('Yes')</option>
                                        <option value="0" @selected(old('includes_boost', @$advertisementPackage->includes_boost) == 0)>@lang('No')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="boost_package_id">@lang('Boost Package')</label>
                                    <select name="boost_package_id" class="form-control select2" id="boostPackageSelect"
                                        required @if ((int) old('includes_boost', @$advertisementPackage->includes_boost) === 0) disabled @endif>
                                        <option value="">@lang('Select Boost Package')</option>
                                        @foreach ($boostPackages as $boost)
                                            <option value="{{ $boost['id'] }}" @selected(old('boost_package_id', @$advertisementPackage->boost_package_id) == $boost['id'])>
                                                {{ $boost['name'] }} - {{ $boost['price'] }} LKR / {{ $boost['duration'] }}
                                                days
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Number of Boosts')</label>
                                    <input type="number" name="no_of_boost" class="form-control" id="noOfBoost"
                                        value="{{ old('no_of_boost', @$advertisementPackage->no_of_boost) }}"
                                        @if ((int) old('includes_boost', @$advertisementPackage->includes_boost) === 0) disabled @endif>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <select name="status" class="form-control select2" required>
                                        <option value="1" @selected(old('status', @$advertisementPackage->status) == 1)>@lang('Active')</option>
                                        <option value="0" @selected(old('status', @$advertisementPackage->status) == 0)>@lang('Inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea name="description" class="form-control" rows="5">{{ old('description', @$advertisementPackage->description) }}</textarea>
                                </div>
                            </div>

                            <hr>

                            <h4 class="mb-3">Package Activation Commissions</h4>

                            <h5 class="mb-2">Company Commissions</h5>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Company Commission')</label>
                                    <input type="number" name="company_commission" class="form-control"
                                        value="{{ old('company_commission', @$commissions->company_commission) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Company Expenses')</label>
                                    <input type="number" name="company_expenses" class="form-control"
                                        value="{{ old('company_expenses', @$commissions->company_expenses) }}" required>
                                </div>
                            </div>

                            <h5 class="mb-2">Customers Commissions</h5>
                            <br>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Level 1 Customer Commission')</label>
                                    <input type="number" name="level_one_commission" class="form-control"
                                        value="{{ old('level_one_commission', @$commissions->level_one_commission) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Level 2 Customer Commission')</label>
                                    <input type="number" name="level_two_commission" class="form-control"
                                        value="{{ old('level_two_commission', @$commissions->level_two_commission) }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Level 3 Customer Commission')</label>
                                    <input type="number" name="level_three_commission" class="form-control"
                                        value="{{ old('level_three_commission', @$commissions->level_three_commission) }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Level 4 Customer Commission')</label>
                                    <input type="number" name="level_four_commission" class="form-control"
                                        value="{{ old('level_four_commission', @$commissions->level_four_commission) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Voucher')</label>
                                    <input type="number" name="customers_voucher" class="form-control"
                                        value="{{ old('customers_voucher', @$commissions->customers_voucher) }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Festival')</label>
                                    <input type="number" name="customers_festival" class="form-control"
                                        value="{{ old('customers_festival', @$commissions->customers_festival) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Saving')</label>
                                    <input type="number" name="customers_saving" class="form-control"
                                        value="{{ old('customers_saving', @$commissions->customers_saving) }}" required>
                                </div>
                            </div>

                            <h5 class="mb-2 mt-2">Leader Commissions</h5>
                            <br>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Leader Bonus')</label>
                                    <input type="number" name="leader_bonus" class="form-control"
                                        value="{{ old('leader_bonus', @$commissions->leader_bonus) }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Leader Vehicle Lease')</label>
                                    <input type="number" name="leader_vehicle_lease" class="form-control"
                                        value="{{ old('leader_vehicle_lease', @$commissions->leader_vehicle_lease) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Leader Petrol')</label>
                                    <input type="number" name="leader_petrol" class="form-control"
                                        value="{{ old('leader_petrol', @$commissions->leader_petrol) }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Max Referral Complete to Car')</label>
                                    <input type="number" name="max_ref_complete_to_car" class="form-control"
                                        value="{{ old('max_ref_complete_to_car', @$commissions->max_ref_complete_to_car) }}"
                                        required>
                                </div>
                            </div>

                            <h5 class="mb-2 mt-2">Top 10 Leaders Commissions</h5>
                            <br>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Top Leader Car')</label>
                                    <input type="number" name="top_leader_car" class="form-control"
                                        value="{{ old('top_leader_car', @$commissions->top_leader_car) }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Top Leader House')</label>
                                    <input type="number" name="top_leader_house" class="form-control"
                                        value="{{ old('top_leader_house', @$commissions->top_leader_house) }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Top Leader Expenses')</label>
                                    <input type="number" name="top_leader_expenses" class="form-control"
                                        value="{{ old('top_leader_expenses', @$commissions->top_leader_expenses) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit"
                                    class="btn btn--primary w-100 h-45 mt-3">@lang('Submit')</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.advertisements.package.index') }}" />
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            const $includesBoostSelect = $('#includesBoost');
            const $noOfBoostInput = $('#noOfBoost');
            const $boostPackageSelect = $('#boostPackageSelect');

            function toggleBoostFields() {
                const val = $includesBoostSelect.val();
                const shouldEnable = val === '1';

                $noOfBoostInput.prop('disabled', !shouldEnable);
                $boostPackageSelect.prop('disabled', !shouldEnable);

                if (!shouldEnable) {
                    $noOfBoostInput.val('');
                    $boostPackageSelect.val('').trigger('change');
                }
            }

            toggleBoostFields();

            $includesBoostSelect.on('change', toggleBoostFields);
        });
    </script>

    <script>
        function calculatePrice() {
            let total = 0;

            const fields = [
                'company_commission',
                'company_expenses',
                'level_one_commission',
                'level_two_commission',
                'level_three_commission',
                'level_four_commission',
                'customers_voucher',
                'customers_festival',
                'customers_saving',
                'leader_bonus',
                'leader_vehicle_lease',
                'leader_petrol',
                'top_leader_car',
                'top_leader_house',
                'top_leader_expenses'
            ];

            fields.forEach(function(name) {
                const field = document.getElementsByName(name)[0];
                if (field && field.value) {
                    total += parseFloat(field.value) || 0;
                }
            });

            const priceInput = document.getElementsByName('price')[0];
            if (priceInput) {
                priceInput.value = total.toFixed(2);
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            const fields = document.querySelectorAll('input[name]');
            fields.forEach(function(input) {
                input.addEventListener('input', calculatePrice);
            });

            calculatePrice();
        });
    </script>
@endpush
