{{-- @extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div class="container">
        <div class="mt-5 col-lg-12 col-12">
            <div class="row">
                <!-- Left Side: Filters Sidebar (Desktop Only) -->
                <div class="col-lg-4 col-md-4 d-none d-md-block categories-container">
                    <h5 class="d-flex justify-content-between align-items-center">
                        <span>Categories</span>
                        <button class="reset-button" style="cursor:pointer;" id="resetFilters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </h5>
                    <div class="mt-3 accordion" id="categoryAccordion">
                        @foreach ($categories as $category)
                            <div class="accordion-item">
                                <h6 class="flex-row accordion-header d-flex justify-content-between">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#category-{{ $category->id }}" aria-expanded="true">
                                        <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                            alt="category image"
                                            style="width:25px; height:25px; border-radius:4px; margin-right:8px;" />
                                        {{ $category->name }}
                                    </button>
                                </h6>
                                <div id="category-{{ $category->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#categoryAccordion">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled">
                                            @foreach ($category->subcategories as $subCategory)
                                                <li class="clickable-item">
                                                    <input type="checkbox" class="category-checkbox"
                                                        name="subcategory_id[]" value="{{ $subCategory->id }}"
                                                        id="subcategory_{{ $subCategory->id }}"
                                                        data-category-name="{{ $subCategory->name }}">
                                                    <label
                                                        for="subcategory_{{ $subCategory->id }}">{{ $subCategory->name }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Side: Search and Posts -->
                <div class="col-lg-8 col-md-8 col-12 main-container">
                    @if (auth()->user()->kv != Status::KYC_VERIFIED)
                        @php
                            $kyc = getContent('kyc.content', true);
                            $userKycStatus = auth()->user()->kv;
                            $kycRejectionReason = auth()->user()->kyc_rejection_reason;
                        @endphp

                        @if ($userKycStatus == Status::KYC_UNVERIFIED && $kycRejectionReason)
                            <!-- KYC Rejected Alert -->
                            <div class="modal fade" id="kycModal" tabindex="-1" aria-labelledby="kycModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="kycModalLabel">@lang('KYC Documents Rejected')</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ __(@$kyc->data_values->reject) }}</p>
                                            <p><strong>@lang('Reason:')</strong> {{ $kycRejectionReason }}</p>
                                            <div class="mt-3 confirmation-details">
                                                <a href="{{ route('user.kyc.form') }}"
                                                    class="text--base">@lang('Click Here to Re-submit Documents')</a>
                                                <br>
                                                <a href="{{ route('user.kyc.data') }}"
                                                    class="text--base">@lang('See KYC Data.')</a>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <a href="{{ route('user.kyc.form') }}"
                                                class="btn btn-primary">Re-submit Documents</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($userKycStatus == Status::KYC_UNVERIFIED)
                            <!-- KYC Unverified Modal (Auto Opens on Page Load) -->
                            <div class="modal fade" id="kycModal" tabindex="-1" aria-labelledby="kycModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="kycModalLabel">@lang('KYC Verification Required')</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ __(@$kyc->data_values->required) }}</p>
                                            <div class="mt-3 confirmation-details">
                                                <div class="mb-2 d-flex justify-content-between">
                                                    <span>Status:</span>
                                                    <span class="text-warning">Verification Required</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Later</button>
                                            <a href="{{ route('user.kyc.form') }}"
                                                class="btn btn-primary">Submit Documents</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @php
                        $formattedBalance = showAmount(auth()->user()->balance);
                    @endphp

                    @if ($formattedBalance < 0 || !isUserEmployeePackageActivated(auth()->user()) || $needsTopUp)
                        <!-- Employee Notifications/Package Activation Modal -->
                        <div class="modal fade" id="employeeNotificationsModal" tabindex="-1"
                            aria-labelledby="employeeNotificationsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="employeeNotificationsModalLabel">Package Activation
                                            Required 
                                            <i class="fa-solid fa-circle-exclamation package-icon" 
                                            data-bs-toggle="tooltip" 
                                            title="You need to activate your package to access all features."></i>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="confirmation-details">
                                            @include($activeTemplate . 'partials.employee_notifications')
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('user.deposit.employee.package.active') }}"
                                            class="btn btn-primary">Activate Package</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @php
                        $packageActivation = session('package_activation_success');
                    @endphp

                    @if($packageActivation)
                        <div class="package-activation-alert">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <div class="package-success-content">
                                    <div class="success-icon">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                    <div class="success-details">
                                        <h6>Package Activated Successfully!</h6>
                                        <p>Your <strong>{{ $packageActivation['name'] }}</strong> package has been activated
                                            and will be valid until {{ $packageActivation['expiry_date'] }}.</p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                        @php
                            session()->forget('package_activation_success');
                        @endphp
                    @endif

                    <!-- Image Slider -->
                    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner"></div>
                        <div class="carousel-indicators-custom">
                            <span class="indicator active" data-bs-target="#imageCarousel" data-bs-slide-to="0"></span>
                            <span class="indicator" data-bs-target="#imageCarousel" data-bs-slide-to="1"></span>
                            <span class="indicator" data-bs-target="#imageCarousel" data-bs-slide-to="2"></span>
                        </div>
                    </div>

                    <div class="middle-container">
                        <div class="small-cards-row">
                            <a href="{{ route('user.advertisement.index') }}" class="small-card">
                                <div class="card-icon"><img
                                        src="{{ asset('assets/images/user/dashboard/speaker.png') }}" alt="Ads"></div>
                                <div class="small-card-text">Ads</div>
                            </a>
                            <a href="{{ route('user.wallet') }}" class="small-card">
                                <div class="card-icon"><img
                                        src="{{ asset('assets/images/user/dashboard/purse.png') }}" alt="Wallet"></div>
                                <div class="small-card-text">Wallet</div>
                            </a>
                            <a href="{{ route('user.referral.index') }}" class="small-card">
                                <div class="card-icon"><img
                                        src="{{ asset('assets/images/user/dashboard/employee.png') }}"
                                        alt="Referrals"></div>
                                <div class="small-card-text">Affiliates</div>
                            </a>

                                <a href="{{ route('user.product.index') }}" class="small-card">
                                    <div class="card-icon"><img
                                            src="{{ asset('assets/images/user/dashboard/products.png') }}"
                                            alt="Products"></div>
                                    <div class="small-card-text">Products</div>
                                </a>

                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="search-container">
                            <div class="position-relative pt-0 !important">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="What are you looking for?">
                                <div class="search-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ads-container">
                        <p>All Ads</p>
                    </div>

                    <div id="adsContainer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            if ($('.alert-success').length > 0) {
                setTimeout(function() {
                    $('.alert-success').fadeOut('slow', function() {
                        $(this).alert('close');
                    });
                }, 3000);
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const packageIcon = document.querySelector('.package-icon');
                if (packageIcon) {
                    new bootstrap.Tooltip(packageIcon, {
                        title: 'You need to activate your package to access all features.',
                        placement: 'bottom',
                        trigger: 'hover'
                    });
                }
        });
    </script>  

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var body = document.body;
            var kycModalElement = document.getElementById('kycModal');
            var employeeModalElement = document.getElementById('employeeNotificationsModal');
            var modalBackdrop = document.createElement('div');

            modalBackdrop.className = 'modal-backdrop fade show';
            modalBackdrop.style.display = 'none';

            function enableScrolling() {
                body.style.overflow = "auto";
                if (document.body.contains(modalBackdrop)) {
                    document.body.removeChild(modalBackdrop);
                }
            }

            function disableScrolling() {
                body.style.overflow = "hidden";
                document.body.appendChild(modalBackdrop);
                modalBackdrop.style.display = 'block';
            }

            if (kycModalElement) {
                var kycModal = new bootstrap.Modal(kycModalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                setTimeout(function() {
                    disableScrolling();
                    kycModal.show();
                    modalBackdrop.style.zIndex = '1040';
                }, 3500);

                kycModalElement.addEventListener('hidden.bs.modal', function() {
                    enableScrolling();
                    setTimeout(function() {
                        if (employeeModalElement) {
                            disableScrolling();
                            var employeeModal = new bootstrap.Modal(employeeModalElement, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            employeeModal.show();
                            modalBackdrop.style.zIndex = '1040';
                        }
                    }, 1000);
                });
            } else if (employeeModalElement) {
                setTimeout(function() {
                    disableScrolling();
                    var employeeModal = new bootstrap.Modal(employeeModalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    employeeModal.show();
                    modalBackdrop.style.zIndex = '1040';
                }, 3500);
            }

            if (employeeModalElement) {
                employeeModalElement.addEventListener('hidden.bs.modal', function() {
                    enableScrolling();
                });
            }
        });
    </script> 

    <script>
        let userId = null;
        $(document).ready(function() {
            let currentPage = 1;
            let itemsPerPage = 10;
            let adsCache = [];

            function fetchData(reset = false) {
                if (reset) {
                    $('#adsContainer').empty();
                    adsCache = [];
                    currentPage = 1;
                }

                let selectedSubcategories = [];
                $('.category-checkbox:checked').each(function() {
                    selectedSubcategories.push($(this).val());
                });

                let searchTerm = $('#searchInput').val() || '';

                $('#adsContainer').html(
                    '<div id="loading-indicator" class="py-3 text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>'
                );

                $.ajax({
                    url: "{{ route('user.advertisement.filter') }}",
                    method: "GET",
                    data: {
                        subcategory_ids: selectedSubcategories.join(','),
                        search: searchTerm,
                    },
                    success: function(response) {
                        $('#loading-indicator').remove();

                        if (response.advertisements && response.advertisements.length > 0) {
                            
                            adsCache = response.advertisements;
                            showPage(1);
                            updatePagination();
                        } else {
                            $('#adsContainer').html(
                                '<div class="no-results">No advertisements found.</div>');
                            $('.pagination-wrapper').remove();
                        }
                    },
                    error: function() {
                        $('#loading-indicator').remove();
                        $('#adsContainer').html(
                            '<div class="no-results">Error loading advertisements. Please try again.</div>'
                        );
                    }
                });
            }

            function showPage(page) {
                $('#adsContainer').empty();
                currentPage = page;

                let startIndex = (page - 1) * itemsPerPage;
                let endIndex = startIndex + itemsPerPage;

                let adsToShow = adsCache.slice(startIndex, endIndex);

                if (adsToShow.length > 0) {
                    adsToShow.forEach(ad => {
                        let cardClass = ad.highlighted_color == 1 ? 'highlighted-card' : '';
                        let boostTag = '';
                        if (ad.boost_package && ad.boost_package.type && ad.status != 2) {
                            let boostLabel = '';
                            let boostClass = '';
                            switch (ad.boost_package.type) {
                                case 1:
                                    boostLabel = 'TOP';
                                    boostClass = 'boost-badge-top';
                                    break;
                                case 2:
                                    boostLabel = 'FEATURED';
                                    boostClass = 'boost-badge-featured';
                                    break;
                                case 3:
                                    boostLabel = 'URGENT';
                                    boostClass = 'boost-badge-urgent';
                                    break;
                            }
                            boostTag = `<div class="boost-tag ${boostClass}">${boostLabel}</div>`;
                        }
                        let priceValue = parseFloat(ad.price_formatted.replace(/,/g, ''));
                            let adHtml = `
                                <a href="/user/advertisement/preview/${ad.id}/${ad.account_type}" class="main-anchor">
                                    <div class="mb-3 ad-item col-12" data-name="${ad.title.toLowerCase()}">
                                        <div class="ad-card ${cardClass}">
                                            <div class="ad-image-container">
                                                ${boostTag}
                                                <img src="${ad.image_url}" class="card-img-top ad-image" alt="${ad.title}">
                                                ${ad.status == 2 ? '<div class="sold-badge">SOLD</div>' : ''}
                                            </div>
                                            <div class="card-container">
                                                <div class="card-details">
                                                    <p class="card-title">${ad.title}</p>
                                                    <p class="card-location text-muted">
                                                        ${ad.city_name}, ${ad.district_name}
                                                    </p>
                                                    <div class="flex-row d-flex">
                                                        ${priceValue > 0 ? `<p class="card-text ad-price">LKR ${ad.price_formatted}</p>` : ''}
                                                    </div>
                                                    <div class="updated-time">
                                                        <p>${ad.posted_date}</p>
                                                    </div>
                                                    <div class="card-fav">
                                                        <i class="fa fa-arrow-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>`;
                                
                        $('#adsContainer').append(adHtml);
                    });
                } else {
                    $('#adsContainer').html('<div class="no-results">No advertisements found.</div>');
                }
            }

            function updatePagination() {
                $('.pagination-wrapper').remove();

                let totalAds = adsCache.length;
                let totalPages = Math.ceil(totalAds / itemsPerPage);

                if (totalPages <= 1) return;

                let paginationHtml = `
                    <div class="mt-4 mb-4 pagination-wrapper">
                        <div class="d-flex justify-content-center">
                            <ul class="pagination">
                                <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="javascript:void(0)" data-page="${currentPage - 1}">«</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="javascript:void(0)" data-page="${currentPage}">${currentPage}</a>
                                </li>
                                ${currentPage < totalPages ? `
                                    <li class="page-item">
                                        <a class="page-link" href="javascript:void(0)" data-page="${currentPage + 1}">${currentPage + 1}</a>
                                    </li>` : ''}
                                <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                                    <a class="page-link" href="javascript:void(0)" data-page="${currentPage + 1}">»</a>
                                </li>
                            </ul>
                        </div>
                    </div>`;

                $('#adsContainer').after(paginationHtml);

                $('.pagination .page-link').on('click', function(e) {
                    e.preventDefault();
                    let page = parseInt($(this).data('page'));

                    if (!isNaN(page) && page >= 1 && page <= totalPages) {
                        showPage(page);
                        updatePagination();
                        $('html, body').animate({
                            scrollTop: $('#adsContainer').offset().top - 100
                        }, 200);
                    }
                });
            }

            $(document).on('change', '.category-checkbox', function() {
                fetchData(true);
            });

            $('#resetFilters').on('click', function() {
                $('.category-checkbox').prop('checked', false);
                $('#searchInput').val('');
                fetchData(true);
            });

            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    fetchData(true);
                }, 500);
            });

            $('.accordion-button').off('click').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('bs-target');
                const isOpen = $(target).hasClass('show');

                $('.accordion-collapse').removeClass('show');
                $('.accordion-button').addClass('collapsed').attr('aria-expanded', 'false');
                $('.category-checkbox').prop('checked', false);

                if (!isOpen) {
                    $(target).addClass('show');
                    $(this).removeClass('collapsed').attr('aria-expanded', 'true');
                }
            });

            $('[data-bs-toggle="collapse"]').attr('data-bs-toggle', 'manual');

            fetchData(true);

            $.ajax({
                url: '/slideshow/fetchs',
                method: "GET",
                success: function(response) {
                    if (response.success) {
                        let carouselInner = $('.carousel-inner');
                        carouselInner.empty();

                        response.images.forEach((image, index) => {
                            let activeClass = index === 0 ? 'active' : '';
                            let imageItem = `
                                <div class="carousel-item ${activeClass}">
                                    <img src="${image}" class="rounded-sm d-block w-100" alt="Banner Image">
                                </div>`;
                            carouselInner.append(imageItem);
                        });
                    } else {
                        console.log("No images found.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching images:", error);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const indicators = document.querySelectorAll('.indicator');
            const carousel = document.querySelector('#imageCarousel');

            carousel.addEventListener('slid.bs.carousel', function(event) {
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index === event.to);
                });
            });
        });
    </script>

<script>
    $(document).ready(function() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const currentUserId = '{{ auth()->id() }}';
        const currentUserEmail = '{{ auth()->user()->email }}';
        const liteApiUrl = '{{ env('ADD_CITI_LITE_API') }}';

        // Handle Lite Dashboard button click
        $('#linkedLiteDashboardBtn').on('click', function() {
            const $btn = $(this);

            // Prevent multiple clicks
            if ($btn.prop('disabled')) {
                return false;
            }

            $btn.prop('disabled', true);
            $btn.find('.loading-spinner').show();

            directLiteAccountSwitch($btn);
        });

        function directLiteAccountSwitch($button = null) {
            $.ajax({
                url: '/api/direct-lite-switch',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    user_id: currentUserId,
                    email: currentUserEmail
                },
                timeout: 15000,
                success: function(response) {
                    if (response.success && response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        showErrorMessage(response.message || 'Unable to access Lite dashboard');
                        resetLiteDashboardButton($button);
                    }
                },
                error: function(xhr, status, error) {
                    if (status === 'timeout') {
                        showErrorMessage('Connection timeout. Please try again.');
                    } else {
                        showErrorMessage(
                            'Unable to access Lite dashboard. Please try again later.');
                    }
                    resetLiteDashboardButton($button);
                }
            });
        }

     

        // Helper function to reset button state
        function resetLiteDashboardButton($button) {
            if ($button) {
                $button.prop('disabled', false);
                $button.find('.loading-spinner').hide();
            }
        }

        // Show error message
        function showErrorMessage(message) {
            if (typeof iziToast !== 'undefined') {
                iziToast.error({
                    title: "Error",
                    message: message,
                    position: "topRight",
                    timeout: 5000,
                });
            } else {
                alert('Error: ' + message);
            }
        }
    });
</script>

<style>  
    .switch-to-lite-section {
        position: sticky;
        top: 68px;
        z-index: 100;
    }

    .switch-to-pro-container {
        background: linear-gradient(135deg, #353839 0%, #b2b0b4ff 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .switch-to-pro-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .switch-to-pro-container:hover::before {
        right: -40%;
    }

    .pro-upgrade-content {
        position: relative;
        z-index: 2;
    }

    .pro-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #fff;
        font-weight: bold;
        padding: 8px 16px;
        border-radius: 25px;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden; 
        transition: transform 0.2s ease-in-out;
    }

    .pro-badge i {
        color: #fff;
        animation: shine-icon 2s infinite linear;
    }

     .pro-badge1 {
       
        align-items: center;
        gap: 6px;
        color: #fff;
        font-weight: bold;
        padding: 8px 16px;
        position: relative;
        overflow: hidden; 
        transition: transform 0.2s ease-in-out;
    }

    .pro-badge1 i {
        color: #fff;
        animation: shine-icon 2s infinite linear;
    }

    @keyframes shine-icon {
        0% {
            text-shadow: 0 0 5px #fff200, 0 0 10px #fff200;
        }

        50% {
            text-shadow: 0 0 15px #fff200, 0 0 25px #fff200;
        }

        100% {
            text-shadow: 0 0 5px #fff200, 0 0 10px #fff200;
        }
    }

    .pro-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .pro-description {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 15px;
    }

    .switch-pro-btn {
        background: rgba(255, 255, 255, 0.95);
        color: #353839;
        border: none;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .switch-pro-btn:hover {
        background: white;
        color: #616266ff;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

     #linkedLiteDashboardBtn.btn.btn-primary.btn-sm {
            padding: 5px 10px;
            font-size: 15px;
            border-radius: 25px;
        }

        @media (max-width: 768px) {
            #liteLinkedAlert.lite-linked-alert p {
                font-size: 13px;
            }
        }
        @media (max-width: 576px) {
            #liteLinkedAlert.lite-linked-alert p {
                font-size: 10px;
            }
        }

        @media (max-width: 350px) {
            #liteLinkedAlert.lite-linked-alert p {
                font-size: 10px;
            }
        }

    .pro-features {
        display: flex;
        gap: 15px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        opacity: 0.9;
    }

    .feature-item i {
        color: #4ade80;
    }

    .existing-pro-alert {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 15px;
        padding: 15px 20px;
        margin-bottom: 20px;
        color: white;
    }

    .loading-spinner {
        display: none;
        margin-left: 10px;
    }

    @media (max-width: 768px) {
        .switch-to-pro-container {
            padding: 15px;
        }

        .pro-title {
            font-size: 16px;
        }

        .pro-description {
            font-size: 13px;
        }

        .pro-features {
            gap: 10px;
        }

        .feature-item {
            font-size: 11px;
        }
    }
</style>
    
@endpush

@push('style')
    <style>
        /* Package Activation Success Alert */
        .package-activation-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 350px;
            z-index: 99999;
            margin: 0;
        }

        .package-success-content {
            display: flex;
            align-items: flex-start;
        }

        .package-icon {
            color: #027c68;
            font-size: 18px;
            padding-top: 2px;
        }

        .success-icon {
            margin-right: 15px;
            color: #198754;
        }

        .success-details {
            flex: 1;
        }

        .success-details h5 {
            color: #198754;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .success-details p {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .package-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .feature-item {
            background-color: #e8f5e9;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .feature-item i {
            color: #198754;
        }

        .package-activation-alert .alert {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.5s ease-out forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .package-activation-alert {
                width: 90%;
                right: 5%;
            }

            .package-success-content {
                flex-direction: column;
            }

            .success-icon {
                margin-bottom: 10px;
                margin-right: 0;
            }
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        .form-control {
            padding: 10px 12px;
            border-radius: 4px;
            width: 100%;
            font-size: 14px;
            background-color: white;
        }

        .categories-container {
            border: 1px solid #eaeaea;
            padding-top: 10px;
            margin-top: 70px;
        }

        .accordion-item {
            border: 1px solid #eaeaea;
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .accordion-button {
            padding: 12px 15px;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            background-color: #f8f9fa;
        }

        .accordion-button:not(.collapsed) {
            color: #027c68;
            background-color: #e9f5f2;
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: #eaeaea;
        }

        .accordion-body {
            padding: 10px 15px;
        }

        .clickable-item {
            margin-left: 20px;
            padding: 8px 0;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .clickable-item:hover {
            background-color: rgba(2, 124, 104, 0.05);
        }

        .clickable-item input[type="checkbox"] {
            margin-right: 10px;
            cursor: pointer;
            accent-color: #027c68;
        }

        .clickable-item label {
            cursor: pointer;
            margin-bottom: 0;
            flex-grow: 1;
            font-size: 14px;
        }

        .reset-button {
            background: none;
            border: none;
            color: #027c68;
            font-size: 14px;
        }

        .reset-button:hover {
            color: #025e4f;
        }

        #loading-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
            color: #027c68;
        }

        .no-results {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            color: #666;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin: 20px 0;
        }

        .no-results i {
            font-size: 40px;
            color: #ccc;
            margin-bottom: 15px;
        }

        #adsContainer {
            display: flex;
            flex-direction: column;
        }

        .favorite-icon {
            margin: 0;
        }

        .card-container {
            width: 70%;
            margin-left: 10px;
            min-height: 90px;
        }

        .ad-image-container {
            width: 30%;
            position: relative;
        }

        .card-details {
            position: relative;
            width: 100%;
        }

        .updated-time {
            position: absolute;
            bottom: -10px;
            right: -2px;
        }

        .card-title {
            color: #000;
            font-size: 16px;
            font-weight: 800;
        }

        .card-location {
            font-size: 16px;
        }

        .card-fav {
            position: absolute;
            top: 4px;
            right: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            color: #027c68;
            transition: background-color 0.3s;
        }

        .card-fav:hover {
            cursor: pointer;
            background-color: #027c68;
            color: white;
        }

        .updated-time {
            font-size: 16px;
            display: flex;
            justify-content: flex-end;
        }

        .ad-card {
            display: flex;
            flex-direction: row;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
            background-color: #fff;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            transition: box-shadow 0.3s ease-in-out, transform 0.1s;
            height: 130px;
        }

        .ad-price {
            font-size: 14px;
            font-weight: 600;
            color: #027c68;
        }

        .ad-card:hover {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        .ad-image {
            height: 105px;
            width: 100%;
            border-radius: 3px;
            object-fit: cover;
        }

        .card-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .card-details {
            display: flex;
            flex-direction: column;
        }

        .main-anchor {
            cursor: pointer;
        }

        .main-anchor:hover {
            text-decoration: none;
            color: inherit;
        }

        .sold-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 0 0 0 6px;
            z-index: 2;
        }

        .highlighted-card {
            background: linear-gradient(135deg, #4dff9783, #45f9ffbe, #40ffa085, #ffec3d);
            border-radius: 5px;
            background-size: 300% 300%;
            animation: gradientMove 5s ease infinite;
        }

        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .boost-tag {
            position: absolute;
            top: 0;
            left: 0;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            z-index: 2;
            text-transform: uppercase;
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0 100%);
        }

        .boost-badge-top {
            background-color: #FFD700;
            color: #333;
        }

        .boost-badge-featured {
            background-color: #1E90FF;
            color: white;
        }

        .boost-badge-urgent {
            background-color: #FF4500;
            color: white;
        }

        .pagination .page-item.disabled a,
        .pagination .page-item.disabled span {
            color: #595959 !important;
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .carousel {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 0;
            padding-bottom: 0;
            margin-top: 25px;
        }

        .carousel-inner {
            height: 180px;
            width: 100%;
            border-radius: 4px;
            object-fit: cover;
        }

        .carousel-inner .carousel-item img {
            height: 180px;
            object-fit: cover;
        }

        .carousel-indicators-custom {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .indicator {
            width: 6px;
            height: 6px;
            margin: 0 5px;
            border-radius: 50%;
            background-color: gray;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .indicator.active {
            background-color: black;
        }

        @media (max-width: 768px) {
            .carousel-inner,
            .carousel-inner .carousel-item img {
                height: 140px;
                object-fit: cover;
            }
        }


        .ads-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 0 4px;
            margin-bottom: 10px;
        }

        .ads-container p {
            font-size: 18px;
            font-weight: bold;
        }

        .top-header {
            position: relative;
        }

        .header-center {
            text-align: center;
        }

        .header-center p {
            font-size: 25px;
            font-weight: 600;
            color: #17433c;
            margin: 0;
        }

        .main-container {
            margin-top: 5px;
        }

        .middle-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            width: 100%;
            margin-top: 15px;
            justify-content: space-between;
        }

        .small-cards-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 10px;
            margin-bottom: 10px;
        }

        .small-card {
            flex: 1;
            min-width: 0;
            height: 99px;
            /* background: linear-gradient(to bottom, #0a815d, #b0e892); */
            background: linear-gradient(to bottom, #17433c, #ffffff);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #000;
            text-decoration: none;
            position: relative;
            font-size: 15px;
        }

        .small-card-text{
            color: #000;
        }

        .card-icon img {
            height: 35px;
            width: 35px;
        }


        @media (max-width: 768px) {
            .small-card {
                height: 80px;
                font-size: 12px;
            }

            .card-icon img {
                height: 28px;
                width: 28px;
            }

            .header-center p {
                font-size: 20px;
            }

            .updated-time,
            .card-title,
            .card-location {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .small-card {
                height: 70px;
                font-size: 11px;
            }

            .card-icon img {
                height: 24px;
                width: 24px;
            }
        }

        .badge-custom {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #000000;
            padding: 0.5em 1em;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .badge-custom:hover {
            background-color: #f6f6f6;
        }

        .badge-selected {
            background-color: #ffffff;
            color: #000000;
        }

        .badge-remove {
            margin-left: 10px;
            cursor: pointer;
            color: red;
            font-weight: bold;
        }

        .tag-label {
            background-color: #fff;
            color: #000;
            padding: 5px 10px;
            border: 1px solid black;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .tag-label:hover {
            background-color: #f6f6f6;
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 15px;
            color: #6c757d;
            font-weight: 500;
        }

        @media (max-width: 767.98px) {
            .col-lg-9,
            .col-md-8 {
                width: 100%;
            }
        }

        .modal-backdrop {
            opacity: 0.5 !important;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #e0e0e0;
            padding: 16px 20px;
        }

        .modal-header .modal-title {
            font-weight: 600;
            font-size: 18px;
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            border-top: 1px solid #e0e0e0;
            padding: 16px 20px;
        }

        .confirmation-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .btn-primary {
            background-color: #027c68;
            border-color: #027c68;
            padding: 8px 16px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #016353;
            border-color: #016353;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 8px 16px;
            font-weight: 500;
        }

        .text--base {
            color: #027c68;
            text-decoration: none;
        }

        .text--base:hover {
            text-decoration: underline;
        }

        #employeeNotificationsModal .modal-body p {
            margin-bottom: 15px;
        }

        #employeeNotificationsModal .confirmation-details {
            margin-bottom: 0;
        }
    </style>
@endpush --}}
