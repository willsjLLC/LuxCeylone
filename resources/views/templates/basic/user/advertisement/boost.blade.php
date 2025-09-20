@extends($activeTemplate . 'layouts.master')

@section('panel')
    @include('partials.preloader')
    <div class="container">
        <div class="mb-2 align-items-center" style="padding-top:1.5rem!important">
            <div class="p-3 header d-flex align-items-center">
                <a href="{{ route('user.advertisement.preview', [$advertisement->id, $advertisement->account_type]) }}" class="text-dark me-3">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h3 class="mb-0">Boost Advertisement</h3>
            </div>
        </div>
    </div>

    <!-- Boost Confirmation Modal -->
    <div class="mt-4 row">
        <div class="mx-auto col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Select Boost Package</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4 advertisement-preview">

                        <div class="ad-card">
                            <div class="ad-image-container">
                                @if ($advertisement->file_name ?? false)
                                    <img src="{{ asset('assets/admin/images/advertisementImages/' . $advertisement->file_name) }}"
                                        class="card-img-top ad-image" alt="{{ $advertisement->title ?? 'Advertisement' }}">
                                @else
                                    <img src="{{ asset('assets/images/default-ad-image.jpg') }}"
                                        class="card-img-top ad-image" alt="Default Ad Image">
                                @endif
                            </div>
                            <div class="card-container">
                                <div class="card-details">
                                    <p class="card-title">{{ $advertisement->title ?? 'Advertisement Title' }}</p>
                                    <p class="card-location text-muted">
                                        {{ $advertisement->city->name ?? 'Location' }},
                                        {{ $advertisement->district->name ?? 'District' }}
                                    </p>
                                    @if (@number_format($advertisement->price ?? 0) > 0 )
                                    <div class="flex-row d-flex">
                                        <p class="card-text ad-price">LKR
                                            {{ number_format($advertisement->price ?? 0, 2) }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="" method="POST" id="boostForm">
                        @csrf
                        <input type="hidden" name="advertisement_id" value="{{ $advertisement->id ?? '' }}">

                        <div class="boost-packages">
                            @foreach ($all_boost_packages as $package)
                                <div class="mb-4  n package-option">

                                    <input class="form-check-input d-none" type="radio" name="boost_package"
                                        id="boost{{ $package['id'] }}" value="{{ $package['id'] }}"
                                        @checked(old('boost_package', @$advertisementPackage->boost_package_id) == $package['id'])>

                                    <label class="form-check-label package-label w-100" for="boost{{ $package['id'] }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="package-name mb-2">{{ $package['name'] }}</h5>

                                                <p class="package-description mb-1">
                                                    <i class="fa fa-check-circle text-success"></i>
                                                    <strong>{{ $package['duration'] }} days</strong>
                                                </p>

                                                @if ($package['highlighted_color'] == 1)
                                                    <p class="package-description mb-1">
                                                        <i class="fa fa-check-circle text-success"></i> Highlighted
                                                    </p>
                                                @endif

                                                <p class="package-description mb-1">
                                                    <i class="fa fa-check-circle text-success"></i> Priority:
                                                    @switch($package['priority_level'])
                                                        @case(1)
                                                            <span class="text-danger">High</span>
                                                        @break

                                                        @case(2)
                                                            <span class="text-warning">Medium</span>
                                                        @break

                                                        @case(3)
                                                            <span class="text-secondary">Low</span>
                                                        @break
                                                    @endswitch
                                                </p>

                                                <p class="package-description mb-1">
                                                    <i class="fa fa-check-circle text-success"></i> Type:
                                                    @switch($package['type'])
                                                        @case(1)
                                                            <span class="text-primary">Top Package</span>
                                                        @break

                                                        @case(2)
                                                            <span class="text-info">Featured Package</span>
                                                        @break

                                                        @case(3)
                                                            <span class="text-danger">Urgent Package</span>
                                                        @break
                                                    @endswitch
                                                </p>
                                            </div>

                                            <div class="package-price text-end">
                                                <strong>LKR {{ number_format($package['price'], 2) }}</strong>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="mt-2">
                                            <p>{{ $package['description'] }}</p>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary confirm-boost-btn">Confirm Boost</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mx-auto col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Boost Benefits</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" id="boostForm">
                        @csrf
                        <input type="hidden" name="advertisement_id" value="{{ $advertisement->id ?? '' }}">

                        <div class="boost-Benefits">
                            <div class="row row-cols-1 gy-3">
                                <div class="col">
                                    <div class="border rounded p-3 bg-light">
                                        <i class="fa fa-star text-warning me-2"></i> Your ad appears at the top of
                                        listings
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="border rounded p-3 bg-light">
                                        <i class="fa fa-bolt text-danger me-2"></i> Increased visibility & buyer
                                        interest
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="border rounded p-3 bg-light">
                                        <i class="fa fa-eye text-primary me-2"></i> Up to 5x more views
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="border rounded p-3 bg-light">
                                        <i class="fa fa-paint-brush text-info me-2"></i> Option for highlighted
                                        appearance
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="border rounded p-3 bg-light">
                                        <i class="fa fa-rocket text-success me-2"></i> Faster response from buyers
                                    </div>
                                </div>
                            </div>

                        </div>


                    </form>
                </div>
            </div>
        </div>
        <!-- Boost Confirmation Modal -->
        <div class="modal fade" id="boostConfirmationModal" tabindex="-1" aria-labelledby="boostConfirmationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="boostConfirmationModalLabel">Confirm Your Boost</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p> </p>

                        <div class="confirmation-details">
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Package:</span>
                                <span id="confirmPackageName"> </span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <span>Price:</span>
                                <span id="confirmPackagePrice"> </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="finalBoostConfirm">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Ad card preview styles */
        .ad-card {
            display: flex;
            flex-direction: row;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
            background-color: #fff;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            height: 150px;
            transition: box-shadow 0.3s ease-in-out, transform 0.1s;
        }

        .ad-image-container {
            width: 30%;
            overflow: hidden;
        }

        .ad-image {
            height: 125px;
            width: 100%;
            border-radius: 3px;
            object-fit: cover;
        }

        .card-container {
            width: 70%;
            margin-left: 10px;
            min-height: 90px;
        }

        .card-details {
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            color: #000;
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        .card-location {
            font-size: 14px;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .ad-price {
            font-size: 14px;
            font-weight: 600;
            color: #027c68;
            margin-bottom: 2px;
        }

        /* Boost packages styling */
        .package-option {
            position: relative;
        }

        .form-check-input {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            z-index: 2;
        }

        .package-label {
            display: block;
            padding: 15px 15px 15px 45px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0;
            width: 100%;
        }
        .package-label:hover {
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .form-check-input:checked+.package-label {
            border-color: #027c68;
            background-color: rgba(2, 124, 104, 0.05);
            box-shadow: 0 0 0 1px #027c68;
        }

        .package-name {
            font-weight: 600;
            font-size: 16px;
            color: #000;
        }

        .package-description {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .package-price {
            font-weight: 700;
            font-size: 16px;
            color: #027c68;
        }

        /* Boost benefits section */
        .boost-benefits {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .boost-benefits h6 {
            margin-bottom: 10px;
            color: #333;
        }

        .boost-benefits ul {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .boost-benefits li {
            margin-bottom: 5px;
            color: #555;
        }

        /* Button styling */
        .btn-primary {
            background-color: #027c68;
            border-color: #027c68;
            padding: 4px 10px;
        }

        .btn-primary:hover {
            background-color: #016353;
            border-color: #016353;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 6px 12px;
        }

        /* Modal styling */
        .confirmation-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .ad-card {
                margin-bottom: 15px;
            }

            .card-title,
            .card-location {
                font-size: 14px;
            }

            .ad-price {
                font-size: 12px;
            }

            .package-name {
                font-size: 14px;
            }

            .package-price {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .package-label {
                padding: 12px 12px 12px 40px;
            }

            .form-check-input {
                left: 12px;
            }
            .ad-card{
                height: 130px;
            }
            .ad-image {
                height: 105px;
            }
        }

        .btn-primary:disabled {
            background-color: #cccccc !important;
            border-color: #cccccc !important;
            color: #666666 !important;
            cursor: not-allowed;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            const confirmBoostBtn = $('.confirm-boost-btn');
            const boostConfirmationModal = $('#boostConfirmationModal');
            const confirmPackageName = $('#confirmPackageName');
            const confirmPackagePrice = $('#confirmPackagePrice');
            const boostForm = $('#boostForm');
            const finalBoostConfirmBtn = $('#finalBoostConfirm');
            let selectedPackageName = '';
            let selectedPackagePrice = '';
            let selectedPackageId = ''; // To store the selected package ID (which will be used as boost_option_id)
            const advertisementId = $('input[name="advertisement_id"]').val(); // Get the advertisement ID

            // Initially disable the confirm boost button
            confirmBoostBtn.prop('disabled', true);

            // Update confirmation modal details and enable button when radio selection changes
            $('input[name="boost_package"]').on('change', function() {
                const selectedLabel = $(this).closest('.package-option').find('.package-label');
                selectedPackageName = selectedLabel.find('.package-name').text();
                selectedPackagePrice = selectedLabel.find('.package-price strong').text();
                selectedPackageId = $(this).val(); // Get the selected package ID

                confirmPackageName.text(selectedPackageName);
                confirmPackagePrice.text(selectedPackagePrice);
                confirmBoostBtn.prop('disabled', false);
            });

            // Open confirmation modal when clicking confirm boost button
            confirmBoostBtn.on('click', function(e) {
                e.preventDefault();
                boostConfirmationModal.modal('show');
            });

            // Redirect to the payment confirmation page when final confirmation is clicked
            finalBoostConfirmBtn.on('click', function() {
                if (advertisementId && selectedPackageId) {
                    const paymentUrl = `{{ route('user.deposit.advertisement.boost', ['ad_id' => ':adId', 'boost_option_id' => ':boostOptionId']) }}`
                        .replace(':adId', advertisementId)
                        .replace(':boostOptionId', selectedPackageId); // Use 'boost_option_id' here
                    window.location.href = paymentUrl;
                } else {
                    // Handle the case where advertisementId or selectedPackageId is not available
                    console.error('Advertisement ID or Package ID is missing.');
                    // Optionally display an error message to the user
                }
            });
        })(jQuery);
    </script>
@endpush