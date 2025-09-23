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

            <div class="items">
                <div class="table-responsive">
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th class="product-name">Product</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order_items as $order_item)
                                <tr>
                                    <td class="product-name">
                                        <div class="product-name-wrapper">
                                            {{ $order_item->product_name }}
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="mobile-label">Price:</span>
                                        LKR {{ number_format($order_item->original_price, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="mobile-label">Qty:</span>
                                        {{ $order_item->quantity }}
                                    </td>
                                    <td class="text-end">
                                        <span class="mobile-label">Discount:</span>
                                        LKR {{ number_format($order_item->discount, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <span class="mobile-label">Total:</span>
                                        LKR {{ number_format($order_item->net_total, 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="delivery-charge-row" style="display: none;">
                                <td colspan="2" class="text-end">Delivery Charge</td>
                                <td colspan="3" class="text-end">
                                    LKR {{ number_format($productDeliveryChargers, 2) }}
                                </td>
                            </tr>

                            <tr class="net-total-row">
                                <td colspan="2" class="text-end fw-bold">Net Total</td>
                                <td colspan="3" class="fw-bold text-end">
                                    LKR {{ number_format($order->net_total, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



        </div>

        <div class="order-payments">
            <div class="order-tabs">
                <span class="tab active" data-tab="bank">PAY BY WALLET</span>
                <span class="tab" data-tab="koko">KOKO</span>
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

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Choose Delivery Method</label>
                                    <div class="form-check ms-3">
                                        <input class="form-check-input" type="radio" name="delivery_method" id="pickup"
                                            value="pickup" required>
                                        <label class="form-check-label text-secondary" for="pickup">
                                            Hand Pickup
                                        </label>
                                    </div>
                                    <div class="form-check ms-3">
                                        <input class="form-check-input" type="radio" name="delivery_method" id="door_step"
                                            value="door_step" checked>
                                        <label class="form-check-label text-secondary" for="door_step">
                                            Doorstep Delivery ( Charge: LKR: {{ $productDeliveryChargers }} )
                                        </label>
                                    </div>
                                </div>

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

                                @php
                                    $hasBonusOptions = $fullyVoucher || $voucherMain;

                                    $bonusDescription = [];

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
                                        <div class="col-12 col-md-6 text-start">
                                            @if ($hasBonusOptions)
                                                <div class="form-check mb-3 text-success">
                                                    <input class="form-check-input" type="checkbox" id="useBonus"
                                                        name="use_bonus" checked>
                                                    <label class="form-check-label" for="useBonus">
                                                        Use available Voucher bonuses
                                                    </label>
                                                    <div class="small mt-1 text-success">
                                                        Available: {{ implode(', ', $bonusDescription) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-6 d-flex flex-column align-items-end">
                                            <h4 class="mb-2">Wallet Balance</h4>
                                            <h3 class="mb-0 fw-bold">{{ showAmount(auth()->user()->balance) }}</h3>
                                        </div>
                                    </div>

                                    <hr class="my-3 border-light">

                                    <button type="submit"
                                        class="px-4 py-2 shadow-sm btn btn-light text-success fw-bold w-100">
                                        <i class="las la-shopping-cart"></i> Pay Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="koko" class="text-center order-content">

            <div class="col-sm-6 col-8 col-lg-12">
                <img class="mx-auto mb-5 img-fluid"
                    src="{{ getImage($activeTemplateTrue . 'images/cart/koko_pay.png') }}" alt="@lang('image')">
            </div>
        </div>
        <div id="crypto" class="text-center order-content">

            <div class="col-sm-6 col-8 col-lg-12">
                <img class="mx-auto mb-5 img-fluid"
                    src="{{ getImage($activeTemplateTrue . 'images/cart/card_pay.png') }}" alt="@lang('image')">
            </div>
        </div>

        <div class="total-summary">
            <div class="paynow">
                <a href="#" class="checkout-button">

                </a>
            </div>
        </div>
    </div>

    <!-- Hidden values for JavaScript -->
    <input type="hidden" id="order-total" value="{{ $order->net_total }}">
    <input type="hidden" id="wallet-balance-raw" value="{{ auth()->user()->balance }}">
    <input type="hidden" id="delivery-charge-amount" value="{{ $productDeliveryChargers }}">
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
            const pickupRadio = document.getElementById('pickup');
            const doorStepRadio = document.getElementById('door_step');
            const deliveryChargeRow = document.querySelector('.delivery-charge-row');
            const netTotalRow = document.querySelector('.net-total-row td:last-child');
            const payNowButton = document.querySelector('button[type="submit"]');
            const walletSection = document.querySelector('.wallet-section');
            const useBonusCheckbox = document.getElementById('useBonus');

            const baseOrderTotal = parseFloat(document.getElementById('order-total').value);
            const walletBalance = parseFloat(document.getElementById('wallet-balance-raw').value);
            const deliveryCharge = {{ $productDeliveryChargers }};
            const hasBonusOptions = {{ $hasBonusOptions ? 'true' : 'false' }};
            const mainOnly = {{ $mainOnly ? 'true' : 'false' }};

            function canAffordPurchase(totalAmount) {
                if (hasBonusOptions && useBonusCheckbox && useBonusCheckbox.checked) {
                    return true;
                }

                if (mainOnly && walletBalance >= totalAmount) {
                    return true;
                }

                return walletBalance >= totalAmount;
            }

            function updatePaymentState(finalTotal) {
                const canAfford = canAffordPurchase(finalTotal);
                const errorMessage = walletSection.querySelector('.insufficient-funds-message');

                if (errorMessage) {
                    errorMessage.remove();
                }

                if (canAfford) {
                    payNowButton.disabled = false;
                    payNowButton.classList.remove('opacity-50');
                    payNowButton.innerHTML = '<i class="las la-shopping-cart"></i> Pay Now';
                } else {
                    payNowButton.disabled = true;
                    payNowButton.classList.add('opacity-50');
                    payNowButton.innerHTML = '<i class="las la-shopping-cart"></i> Pay Now';

                    const insufficientMessage = document.createElement('p');
                    insufficientMessage.className = 'mb-2 text-danger text-center insufficient-funds-message';
                    insufficientMessage.textContent =
                        `Insufficient wallet balance. You need LKR ${(finalTotal - walletBalance).toFixed(2)} more to complete this purchase.`;
                    payNowButton.parentNode.insertBefore(insufficientMessage, payNowButton);
                }
            }

            function updateNetTotal() {
                let finalTotal = baseOrderTotal;

                if (doorStepRadio.checked) {
                    deliveryChargeRow.style.display = 'table-row';
                    finalTotal += deliveryCharge;
                } else {
                    deliveryChargeRow.style.display = 'none';
                }

                netTotalRow.innerHTML = 'LKR ' + finalTotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                updatePaymentState(finalTotal);

                window.currentFinalTotal = finalTotal;
            }

            pickupRadio.addEventListener('change', updateNetTotal);
            doorStepRadio.addEventListener('change', updateNetTotal);

            if (useBonusCheckbox) {
                useBonusCheckbox.addEventListener('change', function() {
                    const currentTotal = window.currentFinalTotal || baseOrderTotal;
                    updatePaymentState(currentTotal);
                });
            }

            updateNetTotal();
        });

        document.querySelector('form.register').addEventListener('submit', function(e) {
            const doorStepRadio = document.getElementById('door_step');
            const baseOrderTotal = parseFloat(document.getElementById('order-total').value);
            const deliveryCharge = {{ $productDeliveryChargers }};
            const walletBalance = parseFloat(document.getElementById('wallet-balance-raw').value);

            const finalTotal = doorStepRadio.checked ? baseOrderTotal + deliveryCharge : baseOrderTotal;

            const useBonusCheckbox = document.getElementById('useBonus');
            const hasBonusOptions = {{ $hasBonusOptions ? 'true' : 'false' }};
            const mainOnly = {{ $mainOnly ? 'true' : 'false' }};

            let canAfford = false;

            if (hasBonusOptions && useBonusCheckbox && useBonusCheckbox.checked) {
                canAfford = true;
            } else if (mainOnly && walletBalance >= finalTotal) {
                canAfford = true;
            } else if (walletBalance >= finalTotal) {
                canAfford = true;
            }

            if (!canAfford) {
                e.preventDefault();
                alert(
                    'Insufficient wallet balance to complete this purchase. Please top up your wallet or use available bonuses.'
                );
                return false;
            }

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'final_total';
            hiddenInput.value = finalTotal;
            this.appendChild(hiddenInput);

            const deliveryInput = document.createElement('input');
            deliveryInput.type = 'hidden';
            deliveryInput.name = 'delivery_charge';
            deliveryInput.value = doorStepRadio.checked ? deliveryCharge : 0;
            this.appendChild(deliveryInput);
        });

        const tabs = document.querySelectorAll('.order-tabs .tab');
        const contents = document.querySelectorAll('.order-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.getAttribute('data-tab');

                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                tab.classList.add('active');

                document.getElementById(targetTab).classList.add('active');
            });
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

        .summary-title {
            padding-left: 3px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .order-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .order-items-table th,
        .order-items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-items-table .product-name {
            text-align: left;
            max-width: 200px;
        }

        .order-items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            position: sticky;
            top: 0;
        }

        .order-items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .net-total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }

        .mobile-label {
            display: none;
            font-weight: 600;
            margin-right: 5px;
        }

        .order-payments {
            display: flex;
            justify-content: center;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            margin: 20px 0;
        }

        .order-tabs {
            display: flex;
            gap: 20px;
        }

        .order-tabs span {
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .order-tabs span.active {
            background-color: #f6c90e;
            color: #000;
        }

        .order-content {
            display: none;
            padding: 20px 0;
        }

        .order-content.active {
            display: block;
        }

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
