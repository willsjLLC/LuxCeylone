@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div style="margin-top:20px;">
        <h2>{{ __($pageTitle) }}</h2>

        <div class="item-details">
            <p style="padding-left: 3px;">SUMMARY</p>
            @php
                $currentOrderItems = [['name' => 'Cake', 'price' => 10], ['name' => 'Burger', 'price' => 5]];
                $totalPrice = array_sum(array_column($currentOrderItems, 'price'));
            @endphp

            <div class="order-card">
                <div class="order-header">
                    <span class="item-name">Product Name</span>
                    <span class="item-price">Price</span>
                    <span class="item-quantity">Qty</span>
                    <span class="item-discount">Discount</span>
                    <span class="item-total">Total</span>
                </div>

                @foreach ($order_items as $order_item)
                    <div class="order-item">
                        <span class="product-name">{{ $order_item->product_name }}</span>
                        <span class="product-price">LKR {{ number_format($order_item->original_price, 2) }}</span>
                        <span class="product-quantity">{{ $order_item->quantity }}</span>
                        <span class="product-discount">LKR {{ $order_item->discount }}</span>
                        <span class="product-total">LKR {{ number_format($order_item->net_total, 2) }}</span>
                    </div>
                @endforeach

                <div class="total-section">
                    <strong>NET TOTAL:</strong>
                    <span class="total-price">LKR {{ number_format($order->net_total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="order-payments">
            <div class="order-tabs">
                <span class="tab active" data-tab="bank">PAY BY WALLET</span>
                <span class="tab" data-tab="koko">KOKO</span>
                {{-- <span class="tab" data-tab="crypto">CREDIT/DEBIT</span> --}}
            </div>
        </div>

        <div id="bank" class="order-content active">

            <div class="col-lg-12">
                <div class="row">
                    <div class="p-4 bg-white rounded shadow-sm dashboard__content contact__form__wrapper">
                        <div class="profile__edit__wrapper">
                            <h4 class="mb-3">Shipping Details</h4>
                            <form class="register" action="{{ route('user.deposit.customer.purchase.order', $order->id) }}"
                                method="post">
                                @csrf
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control form--control" name="firstname"
                                            value="{{ $user->firstname }}" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control form--control" name="lastname"
                                            value="{{ $user->lastname }}" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">E-mail Address</label>
                                        <input type="email" class="form-control form--control"
                                            value="{{ $user->email }}" name="email">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">@lang('Mobile')</label>
                                        <div class="input-group ">
                                            <span class="input-group-text mobile-code">
                                            </span>
                                            <input type="hidden" name="mobile_code">
                                            <input type="hidden" name="country_code">
                                            <input type="number" name="mobile" value="{{ $user->mobile }}"
                                                class="form-control form--control checkUser" required>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3" id="extra-mobile-field">
                                        <label class="form-label">@lang('Alternative Mobile')</label>
                                        <div class="input-group ">
                                            <span class="input-group-text mobile-code">

                                            </span>
                                            <input type="hidden" name="mobile_code">
                                            <input type="hidden" name="country_code">
                                            <input type="number" name="alternative_mobile"
                                                value="{{ old('alternative_mobile') }}"
                                                class="form-control form--control checkUser">
                                        </div>

                                    </div>

                                    <div class="form-group col-12">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control form--control" name="address"
                                            value="{{ @$user->address }}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" class="form-control form--control" name="zip"
                                            value="{{ @$user->zip }}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">@lang('City')</label>
                                        <input type="text" class="form-control form--control" name="city"
                                            value="{{ @$user->city }}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">@lang('Country')</label>
                                        <select name="country" class="form-control form--control select2" required
                                            value="{{ @$user->country_name }}">
                                            @foreach ($countries as $key => $country)
                                                <option data-mobile_code="{{ $country->dial_code }}"
                                                    value="{{ $country->country }}" data-code="{{ $key }}">
                                                    {{ __($country->country) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- 
                                <div class="mt-4 text-white rounded wallet-section bg-gradient text-end"
                                    style="background: linear-gradient(135deg, #28a745, #218838);">
                                    <div class="gap-2 d-flex flex-column align-items-end">
                                        <h4 class="mb-2">Wallet Balance</h4>
                                        <h3 class="mb-0 fw-bold">{{ showAmount(auth()->user()->balance) }}</h3>
                                    </div>
                                    <hr class="my-3 border-light">

                            
                                    @if ($fullyFestival)
                                        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="Try With Festival Bonus"
                                            class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                            <i class="las la-shopping-cart"></i> Pay Now

                                        </button>
                                    @elseif($withVoucher)
                                        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="Try With Festival & Voucher Bonus"
                                            class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                            <i class="las la-shopping-cart"></i> Pay Now

                                        </button>
                                    @elseif($withVoucherMain)
                                        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="Try With Festival Bonus + Voucher Balance & Wallet"
                                            class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                            <i class="las la-shopping-cart"></i> Pay Now

                                        </button>
                                    @elseif($fullyVoucher)
                                        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="Try With Voucher Bonus"
                                            class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                            <i class="las la-shopping-cart"></i> Pay Now

                                        </button>
                                    @elseif($voucherMain)
                                        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Try With Voucher Bonus & Wallet Balance"
                                            class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                            <i class="las la-shopping-cart"></i> Pay Now
                                        </button>
                                    @elseif($mainOnly)
                                        <button type="submit" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="Pay Now"
                                            class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                            <i class="las la-shopping-cart"></i> Pay Now
                                        </button>
                                    @else
                                        <p class="mb-2 text-danger">You can't complete this payment right now. Please top
                                            up
                                            your wallet.</p>
                                        <button type="submit"
                                            class="px-4 py-2 shadow-sm opacity-50 btn btn-light text-success fw-bold w-100"
                                            disabled>
                                            <i class="las la-shopping-cart"></i> Pay Now
                                        </button>
                                    @endif
                                </div> --}}

                                @php
                                    $hasBonusOptions =
                                        // $fullyFestival ||
                                        // $withVoucher ||
                                        // $withMain ||
                                        // $withVoucherMain ||
                                        $fullyVoucher ||
                                        $voucherMain;

                                    $bonusDescription = [];

                                    // if ($fullyFestival) {
                                    //     $bonusDescription[] = 'Festival Bonus';
                                    // }

                                    // if ($withVoucher) {
                                    //     $bonusDescription[] = 'Festival + Voucher Bonus';
                                    // }

                                    // if ($withMain) {
                                    //     $bonusDescription[] = 'Festival + Wallet Balance';
                                    // }

                                    // if ($withVoucherMain) {
                                    //     $bonusDescription[] = 'Festival + Voucher Bonus + Wallet Balance';
                                    // }

                                    if ($fullyVoucher) {
                                        $bonusDescription[] = 'Voucher Bonus';
                                    }

                                    if ($voucherMain) {
                                        $bonusDescription[] = 'Voucher Bonus + Wallet Balance';
                                    }
                                @endphp

                                <div class="mt-4 text-white rounded wallet-section bg-gradient"
                                    style="background: linear-gradient(135deg, #28a745, #218838);">
                                    <div class="row align-items-start gy-3">
                                        {{-- Left Side (stacked first on mobile): Bonus --}}
                                        <div class="col-12 col-md-6 text-start">
                                            @if ($hasBonusOptions)
                                                <div class="form-check mb-3 text-success">
                                                    <input class="form-check-input" type="checkbox" id="useBonus"
                                                        name="use_bonus" checked>
                                                    <label class="form-check-label" for="useBonus">
                                                        Use available Voucher bonuses
                                                    </label>
                                                    <div class="small mt-1   text-success">
                                                        Available: {{ implode(', ', $bonusDescription) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Right Side: Wallet Balance --}}
                                        <div class="col-12 col-md-6 d-flex flex-column align-items-end">
                                            <h4 class="mb-2">Wallet Balance</h4>
                                            <h3 class="mb-0 fw-bold">{{ showAmount(auth()->user()->balance) }}</h3>
                                        </div>
                                    </div>

                                    <hr class="my-3 border-light">

                                    @if($hasBonusOptions || $mainOnly)
                                    <button type="submit"
                                        class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                        <i class="las la-shopping-cart"></i> Pay Now
                                    </button>
                                    @endif
                                    
                                    @unless ($hasBonusOptions || $mainOnly)
                                        <p class="mb-2 text-danger text-center">You can't complete this payment right now.
                                            Please top up your wallet.</p>
                                        <button type="submit"
                                            class="px-4 py-2 shadow-sm opacity-50 btn btn-light text-success fw-bold w-100"
                                            disabled>
                                            <i class="las la-shopping-cart"></i> Pay Now
                                        </button>
                                    @endunless
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="koko" class="text-center order-content">
            {{-- <p>Coming soon...</p> --}}
            <div class="col-sm-6 col-8 col-lg-12">
                <img class="mx-auto mb-5 img-fluid"
                    src="{{ getImage($activeTemplateTrue . 'images/cart/koko_pay.png') }}" alt="@lang('image')">
            </div>
        </div>
        <div id="crypto" class="text-center order-content">
            {{-- <p>Coming soon...</p> --}}
            <div class="col-sm-6 col-8 col-lg-12">
                <img class="mx-auto mb-5 img-fluid"
                    src="{{ getImage($activeTemplateTrue . 'images/cart/card_pay.png') }}" alt="@lang('image')">
            </div>
        </div>

        <div class="total-summary">
            <div class="paynow">
                <a href="#" class="checkout-button">
                    {{-- Purchase Order --}}
                </a>
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


@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.order-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.dataset.tab).classList.add('active');
                });
            });
        });

        document.getElementById('add-mobile-btn').addEventListener('click', function() {
            document.getElementById('extra-mobile-field').style.display = 'block';
            this.style.display = 'none';
        });
    </script>

    <script>
        "use strict";
        (function($) {

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('.select2').select2();

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value, name);
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
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

    <style>
        .img-fluid {}

        .order-payments {
            display: flex;
            justify-content: center;
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-tabs span {
            margin-right: 15px;
            cursor: pointer;
            padding-bottom: 8px;
            position: relative;
        }

        .order-tabs span.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -3px;
            width: 100%;
            height: 3px;
            background-color: #f6c90e;
        }

        .order-content {
            display: none;
            padding: 10px;
        }

        .order-content.active {
            display: block;
        }

        .order-card {
            background-color: #ececec;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid white;
        }

        .item-name,
        .item-price {
            color: #000;
        }

        .product-name {
            display: flex;
            justify-content: flex-start;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            font-weight: bold;
            align-items: center;
        }

        .total-section strong {
            margin-right: 10px;
            color: #3a4750;
            font-size: 14px;
        }

        .total-price {
            color: #000;
        }

        .total-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #3a4750;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .paynow a {
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }

        @media(max-width:768px) {
            .total-section strong {
                font-size: 12px;
            }

            .item-name {
                font-size: 14px;
            }

            .item-price {
                font-size: 14px;
            }

            .order-tabs span {
                font-size: 12px;
            }


        }

        @media(max-width:375px) {
            .order-header {
                font-size: 11px;
                padding: 6px 6px;
            }

            .item-price,
            .item-name {
                font-size: 11px;
            }

            .product-name,
            .product-quantity,
            .product-price {
                display: flex;
                justify-content: flex-start;
                font-size: 12px;
            }

            .product-price,
            .product-discount,
            .product-total {
                font-size: 12px;
            }
        }

        .order-header,
        .order-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 10px;
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
            text-align: right;
        }

        .order-header {
            font-weight: bold;
            background-color: #f8f8f8;
        }

        .order-header span,
        .order-item span {
            padding: 5px;
        }

        .item-name {
            text-align: left;
        }

        .total-section {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            font-weight: bold;
        }
    </style>
@endpush
