@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-7">
                <div class="mb-3 alert alert-primary" role="alert">
                    <strong>
                        @lang('Complete your profile')
                    </strong>
                    <p>@lang('You need to complete your profile by providing below information.')</p>
                </div>
                <div class="p-2 card custom--card contact__form__wrapper">
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.data.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="mb-2 col-md-12">
                                    <div class="mb-1 form-group">
                                        <label class="mb-0 form-label">@lang('Username')</label>
                                        <input type="text" class="bottom-border-input" name="username"
                                            value="{{ old('username') }}">
                                        <small class="text--danger usernameExist"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-0 form-label">@lang('Country')</label>
                                        <input type="text" class="form-control country-name" name="country"
                                            value="Sri Lanka" readonly>
                                        <input type="hidden" name="country_code" value="LK">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Mobile')</label>
                                        <div class="input-group">
                                            <span class="input-group-text mobile-code">+94</span>
                                            <input type="hidden" name="mobile_code" value="94">
                                            <input type="number" name="mobile" value="{{ old('mobile') }}"
                                                class="form-control form--control checkUser" required>
                                        </div>
                                        <small class="text--danger mobileExist"></small>
                                    </div>
                                </div>
                                <div class="mb-3 col-6">
                                    <label class="form-label">District</label>
                                    <div class="select-dropdown">
                                        <select name="district_id" id="district-selector" class="form-select select2">
                                            <option value="">Select District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}" data-name="{{ $district->name }}">
                                                    {{ $district->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="district_name" id="district-name">
                                <div class="mb-3 col-6">
                                    <label class="form-label">City</label>
                                    <div class="select-dropdown">
                                        <select name="city_id" id="city-selector" class="form-select select2">
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" data-name="{{ $city->name }}">
                                                    {{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="city_name" id="city-name">
                                <div class="mb-2 col-md-12">
                                    <div class="mb-1 form-group">
                                        <label class="mb-0 form-label">@lang('Zip Code')</label>
                                        <input type="text" class="bottom-border-input" name="zip"
                                            value="{{ old('zip') }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn w-100">
                                @lang('Submit')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('style')
    <style>
        .country-name{
            background-color: #fff;
            border: 1px solid #aaa;
            border-radius: 4px;
            display: block;
            padding: 11px;
            opacity: 0.7;
            padding-left: 33px;
        }
        .form-group {
            margin-bottom: 5px !important;
        }

        .form-label {
            color: #003333;
            display: block;
        }

        .bottom-border-input {
            width: 100%;
            padding: 0;
            background: transparent;
            border: none;
            border-bottom: 1px solid #027c68;
            outline: none;
            transition: border-color 0.3s;
        }

        .btn {
            margin-top: 15px;
            background-color: #016a59;
            color: #fff;
        }

        .btn:hover {
            background-color: #105a43;
            color: #fff;
        }
    </style>
@endpush


@push('script')
    <script>
        $(document).ready(function() {
            // When the district is changed, update the hidden field with the district's name
            $('#district-selector').on('change', function() {
                var selectedDistrict = $(this).find('option:selected');
                var districtName = selectedDistrict.data('name');
                $('#district-name').val(districtName);
            });

            // When the city is changed, update the hidden field with the city's name
            $('#city-selector').on('change', function() {
                var selectedCity = $(this).find('option:selected');
                var cityName = selectedCity.data('name');
                $('#city-name').val(cityName);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // District and City dynamic loading
            $('#district-selector').on('change', function() {
                var districtId = $(this).val();

                $.ajax({
                    url: "{{ url('advertisement/get-cities') }}/" + districtId,
                    type: "GET",
                    success: function(response) {
                        var citySelector = $('#city-selector');
                        citySelector.empty(); // Clear current options

                        // Append cities based on selected district
                        $.each(response, function(index, city) {
                            citySelector.append($('<option>', {
                                value: city.id,
                                text: city.name,
                                'data-name': city.name
                            }));
                        });
                    },
                    error: function(xhr) {
                        console.log('Error loading cities');
                    }
                });
            });
        });
    </script>

    <script>
        "use strict";
        (function($) {

            // Set country and mobile code defaults
            $('input[name=country]').val('Sri Lanka');
            $('input[name=country_code]').val('LK'); // ISO code for Sri Lanka
            $('input[name=mobile_code]').val('+94');
            $('.mobile-code').text('+94');

            $('.select2').select2(); // You can keep this if other selects use Select2

            // Remove country change event because country is fixed
            // $('select[name=country]').on('change', function() {...}); --> Not needed

            // On focus out of mobile field, check if mobile exists
            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name');
                checkUser(value, name);
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                var data = {};

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    data = {
                        mobile: mobile,
                        mobile_code: '+94',
                        _token: token
                    };
                }

                if (name == 'username') {
                    data = {
                        username: value,
                        _token: token
                    };
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
