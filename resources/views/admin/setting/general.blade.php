@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <div class="row">

                            <h4 class="mb-2">Basic Settings</h4>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required
                                        value="{{ gs('site_name') }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" type="text" name="cur_text" required
                                        value="{{ gs('cur_text') }}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" type="text" name="cur_sym" required
                                        value="{{ gs('cur_sym') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required"> @lang('Timezone')</label>
                                <select class="select2 form-control" name="timezone">
                                    @foreach ($timezones as $key => $timezone)
                                        <option value="{{ @$key }}" @selected(@$key == $currentTimezone)>{{ __($timezone) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required"> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ gs('base_color') }}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color"
                                        value="{{ gs('base_color') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label> @lang('Record to Display Per page')</label>
                                <select class="select2 form-control" name="paginate_number"
                                    data-minimum-results-for-search="-1">
                                    <option value="20" @selected(gs('paginate_number') == 20)>@lang('20 items per page')</option>
                                    <option value="50" @selected(gs('paginate_number') == 50)>@lang('50 items per page')</option>
                                    <option value="100" @selected(gs('paginate_number') == 100)>@lang('100 items per page')</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-3 col-sm-6 ">
                                <label class="required"> @lang('Currency Showing Format')</label>
                                <select class="select2 form-control" name="currency_format"
                                    data-minimum-results-for-search="-1">
                                    <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>@lang('Show Currency Text and Symbol Both')</option>
                                    <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>@lang('Show Currency Text Only')</option>
                                    <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>@lang('Show Currency Symbol Only')</option>
                                </select>
                            </div>

                            <!-- Add the new watermark text field -->
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Default Watermark Text')</label>
                                    <input class="form-control" type="text" step="any" name="default_watermark_text"
                                        required value="{{ getValue('DEFAULT_WATERMARK_TEXT') }}"
                                        placeholder="@lang('Default Watermark Text')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Voucher Remaining Dates')</label>
                                    <input class="form-control" type="text" step="any" name="voucher_remaining_date"
                                        required value="{{ getValue('VOUCHER_REMAINING_DATE') }}"
                                        placeholder="@lang('Voucher Remaining Date')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('User Recursive Top-Up Range')</label>
                                    <input class="form-control" type="text" step="any" name="user_recursive_top_up_range"
                                        required value="{{ getValue('USER_RECURSIVE_TOP_UP_RANGE') }}"
                                        placeholder="@lang('User Recursive Top-Up Range')">
                                </div>
                            </div>

                            {{-- boost commission setup --}}

                            <hr>

                            <h4 class="mb-2">Boost Post / Ticket Purchase Commissions</h4>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Percentage For Company')</label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="number" min="0" step="any"
                                            name="ad_boost_commission_percentage_for_company" required
                                            value="{{ getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_COMPANY') }}"
                                            placeholder="@lang('Ad Boost Commission Percentage For Company')">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Referred User Commissions')</label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="number" min="0" step="any"
                                            name="ad_boost_commission_percentage_for_referred_user" required
                                            value="{{ getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_REFERRED_USER') }}"
                                            placeholder="@lang('Ad Boost Commission Percentage For Referred User')">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Non Direct Users Commission')</label>
                                    <div class="input-group mb-3">
                                        <input class="form-control" type="number" min="0" step="any"
                                            name="ad_boost_commission_percentage_for_none_direct_users" required
                                            value="{{ getValue('AD_BOOST_COMMISSION_PERCENTAGE_FOR_NON_DIRECT_USERS') }}"
                                            placeholder="@lang('Ad Boost Commission Percentage For Non Direct Users')">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Number of Eligible Users')</label>
                                    <input class="form-control" type="number" min="0"
                                        name="number_of_non_direct_users_eligible_for_ad_boost" required
                                        value="{{ getValue('NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_AD_BOOST_COMMISSION') }}"
                                        placeholder="@lang('Enter Number of Eligible Non-Direct Users')">
                                </div>
                            </div>

                            <hr>

                            <h4 class="mb-2">Religion Festival Bonuses Release Durations </h4>

                            {{-- <h5 class="mb-2">Sinhalise Bonus Release Duration</h5> --}}

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Sinhalise Bonus From')</label>
                                <select class="select2 form-control" name="sinhalise_bonus_from"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('SINHALISE_BONUS_FROM') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Sinhalise Bonus To')</label>
                                <select class="select2 form-control" name="sinhalise_bonus_to"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('SINHALISE_BONUS_TO') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <h5 class="mb-2">Tamil Bonus Release Duration</h5> --}}

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Tamil Bonus From')</label>
                                <select class="select2 form-control" name="tamil_bonus_from"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('TAMIL_BONUS_FROM') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Tamil Bonus To')</label>
                                <select class="select2 form-control" name="tamil_bonus_to"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('TAMIL_BONUS_TO') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <h5 class="mb-2">Muslims Bonus Release Duration</h5> --}}

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Muslims Bonus From')</label>
                                <select class="select2 form-control" name="muslims_bonus_from"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('MUSLIMS_BONUS_FROM') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Muslims Bonus To')</label>
                                <select class="select2 form-control" name="muslims_bonus_to"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('MUSLIMS_BONUS_TO') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- <h5 class="mb-2">Muslims Bonus Release Duration</h5> --}}

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Christians Bonus From')</label>
                                <select class="select2 form-control" name="christian_bonus_from"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('CHRISTIAN_BONUS_FROM') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required">@lang('Christians Bonus To')</label>
                                <select class="select2 form-control" name="christian_bonus_to"
                                    data-minimum-results-for-search="-1">
                                    @foreach ($months as $month)
                                        <option value="{{ $month['id'] }}" @selected(getValue('CHRISTIAN_BONUS_TO') == $month['id'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- <hr> --}}

                            {{-- <h4 class="mb-2">Package Activation Commissions</h4>

                            <h5 class="mb-2">Company Commissions</h5>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Commission For Company')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_commission_for_company" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_COMMISSION_FOR_COMPANY') }}"
                                        placeholder="@lang('Package Activation Commission For Company')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Expenses Commission For Company')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_expenses_commission_for_company" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_EXPENSES_COMMISSION_FOR_COMPANY') }}"
                                        placeholder="@lang('Package Activation Expenses Commission For Company')">
                                </div>
                            </div>

                            <h5 class="mb-2">Customer Commissions</h5>
                            <br>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Cash Commission For Users')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_cash_commission_for_users" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_CASH_COMMISSION_FOR_USERS') }}"
                                        placeholder="@lang('Package Activation Cash Commission For Users')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Voucher Commission For Users')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_voucher_commission_for_users" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_VOUCHER_COMMISSION_FOR_USERS') }}"
                                        placeholder="@lang('Package Activation Voucher Commission For Users')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Festival Commission For Users')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_festival_commission_for_users" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_FESTIVAL_COMMISSION_FOR_USERS') }}"
                                        placeholder="@lang('Package Activation Festival Commission For Users')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Saving Commission For Users')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_saving_commission_for_users" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_SAVING_COMMISSION_FOR_USERS') }}"
                                        placeholder="@lang('Package Activation Saving Commission For Users')">
                                </div>
                            </div>

                            <h5 class="mb-2 mt-2">Leader Commissions</h5>
                            <br>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Bonus For Leader')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_bonus_for_leader" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_BONUS_FOR_LEADER') }}"
                                        placeholder="@lang('Package Activation Bonus For Leader')">
                                </div>
                            </div>


                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Vehicle Leasing Commission For Leader')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_vehicle_leasing_commission_for_leader" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_VEHICLE_LEASING_COMMISSION_FOR_LEADER') }}"
                                        placeholder="@lang('Package Activation Vehicle Leasing Commission For Leader')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Petrol Commission For Leader')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_petrol_commission_for_leader" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_PETROL_COMMISSION_FOR_LEADER') }}"
                                        placeholder="@lang('Package Activation Petrol Commission For Leader')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Max Referral Complete To Car')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="max_referral_complete_to_car" required
                                        value="{{ getValue('MAX_REFERRAL_COMPLETE_TO_CAR') }}"
                                        placeholder="@lang('Max Referral Complete To Car')">
                                </div>
                            </div>

                            <h5 class="mb-2 mt-2">Top 10 Leaders Commissions</h5>
                            <br>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Car Commission For Selected Leader')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_car_commission_for_selected_leader" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_CAR_COMMISSION_FOR_SELECTED_LEADER') }}"
                                        placeholder="@lang('Package Activation Car Commission For Selected Leader')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation House Commission For Selected Leader')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_house_commission_for_selected_leader" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_HOUSE_COMMISSION_FOR_SELECTED_LEADER') }}"
                                        placeholder="@lang('Package Activation House Commission For Selected Leader')">
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Package Activation Expenses Commission For Selected Leader')</label>
                                    <input class="form-control" type="number" min="0" step="any"
                                        name="package_activation_expenses_commission_for_selected_leader" required
                                        value="{{ getValue('PACKAGE_ACTIVATION_EXPENSES_COMMISSION_FOR_SELECTED_LEADER') }}"
                                        placeholder="@lang('Package Activation Expenses Commission For Selected Leader')">
                                </div>
                            </div>
 --}}




                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });
        })(jQuery);
    </script>
@endpush
