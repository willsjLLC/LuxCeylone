@extends($activeTemplate . 'layouts.master')

@section('panel')
    @include('partials.preloader')
    <div class="container">
        <div class="mb-2 align-items-center" style="padding-top:1.5rem!important">
            <div class="p-3 header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('user.advertisement.index') }}" class="text-dark me-3">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h3 class="mb-0">My Ads</h3>
                </div>
                <div>
                    {{-- @if($remainingAdCount>=0)
                        @if ($remainingAdCount == 0)
                        <a href="#" class="post-ad-btn expired disabled" onclick="event.preventDefault();">
                            + Post New Ad
                        </a>
                        @else
                        <a href="{{ route('user.advertisement.selectCategory') }}" class="post-ad-btn">+ Post New Ad</a>
                        @endif
                    @else --}}
                        <a href="{{ route('user.advertisement.selectCategory') }}" class="post-ad-btn">+ Post New Ad</a>
                    {{-- @endif --}}
                </div>
            </div>
        </div>

        <div class="mt-5 row">
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
                            <h6 class="accordion-header">
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
                                                <input type="checkbox" class="category-checkbox" name="subcategory_id[]"
                                                    value="{{ $subCategory->id }}" id="subcategory_{{ $subCategory->id }}"
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

            <!-- Right Side: Search and Ads -->
            <div class="col-lg-8 col-md-8 col-12">
                <!-- Mobile Post Ad Button -->


                <!-- Search bar -->
                <div class="mb-4 search-container search-row">
                    <div class="position-relative pt-0 !important">
                        <input type="text" id="searchInput" class="form-control" placeholder="What are you looking for?"
                            style="font-size:15px;">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>

                <div class="ads-container">

                </div>

                <div id="adsContainer" class="row">
                    <!-- Ads will be loaded here via AJAX -->
                    <div class="text-center col-12" id="initialLoading">
                        <i class="fa fa-spinner fa-spin"></i> Loading your ads...
                    </div>
                </div>

                <!-- Loading more indicator -->
                <div id="loadingMore" class="mb-4 text-center" style="display: none;">
                    <i class="fa fa-spinner fa-spin"></i> Loading more ads...
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Post Ad Button Style */
        .post-ad-btn {
            display: inline-block;
            background-color: #027c68;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .post-ad-btn:hover {
            background-color: #025e4f;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* For mobile devices */
        @media (max-width: 767px) {
            .post-ad-btn {
                display: block;
                text-align: center;
                padding: 4px 8px;
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
        }

        /* Filter sidebar styles */
        .accordion-item {
            border: 1px solid #eaeaea;
            border-radius: 8px;
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
            margin-left: 22px;
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
            color: #027c68;
        }
    </style>

    <style>
        .pagination .page-item.disabled a,
        .pagination .page-item.disabled span {
            color: #595959 !important;
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Search Bar */
        .search-row {
            margin-bottom: 15px;
        }

        #searchInput {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            outline: none;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        /* Ads Header */
        .ads-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .ads-container p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        /* Ad card styles */
        .ad-card {
            display: flex;
            flex-direction: row;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
            background-color: #fff;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            height: 130px;
            transition: box-shadow 0.3s ease-in-out, transform 0.1s;
        }

        .ad-card:hover {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        .ad-image-container {
            width: 30%;
            overflow: hidden;
        }

        .ad-image {
            height: 105px;
            width: 100%;
            border-radius: 3px;
            object-fit: cover;
        }

        .card-container {
            position: relative;
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

        /* Status styles */
        .ad-status {
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #FFF3CD;
            /* light yellow */
            color: #856404;
            border: 1px solid #FFE69C;
        }

        .status-approved {
            background-color: #D1E7DD;
            /* soft green */
            color: #0F5132;
            border: 1px solid #BADBCC;
        }

        .status-completed {
            background-color: #CFF4FC;
            /* cyan blue */
            color: #055160;
            border: 1px solid #B6EFFB;
        }

        .status-paused {
            background-color: #E2E3E5;
            /* gray */
            color: #41464B;
            border: 1px solid #D3D6D8;
        }

        .status-ongoing {
            background-color: #CCE5FF;
            /* soft blue */
            color: #004085;
            border: 1px solid #B8DAFF;
        }

        .status-rejected {
            background-color: #FADBD8;
            /* salmon pink */
            color: #922B21;
            border: 1px solid #F5B7B1;
        }

        .status-expired {
            background-color: #FDEBD0;
            /* light orange */
            color: #7E5109;
            border: 1px solid #FAD7A0;
        }

        .status-cancelled {
            background-color: #EAD1DC;
            /* dusty rose */
            color: #7B1E5D;
            border: 1px solid #D6A8BD;
        }

        .updated-time {
            position: absolute;
            bottom: -10px;
            right: -2px;
            font-size: 16px;
        }

        .updated-time p {
            margin: 0;
        }

        .card-fav {
            position: absolute;
            top: 4px;
            right: 4px;
        }

        .card-fav a {
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

        .card-fav a:hover {
            background-color: #027c68;
            color: white;
        }

        /* No results message */
        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 16px;
            font-weight: bold;
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

            .updated-time p {
                font-size: 12px;
            }

            .ad-price {
                font-size: 12px;
            }

            .ad-status {
                font-size: 10px;
                padding: 1px 6px;
            }
        }

        @media (max-width: 576px) {
            .ad-card {
                flex-direction: row;
            }

            .ad-image-container {
                width: 30%;
            }

            .card-container {
                width: 70%;
                margin-left: 10px;
            }

            .card-fav {
                top: 4px;
                right: 4px;
            }

            /* Boosted status style */
            .status-boosted {

                display: inline-block;
                /* ensure proper display */
                border-radius: 10px;
                /* match other status tags */
                padding: 0px 10px;
                /* match other status tags */
                font-size: 10px;
                font-weight: bold;
                text-transform: uppercase;
            }

            /* Status container structure for vertical alignment */
            .ad-status-container {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
            }

            /* Ensure that the status container works on all screen sizes */
            @media (min-width: 768px) {
                .ad-status-container {
                    flex-direction: column !important;
                    align-items: flex-end !important;
                }

                .ad-status-container>div {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: flex-end !important;
                }

                .mt-1 {
                    margin-top: 0.25rem !important;
                }
            }
        }
    </style>
@endpush

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const packageIcon = document.querySelector('.expired');
                if (packageIcon) {
                    new bootstrap.Tooltip(packageIcon, {
                        title: 'Advertisement limit reached. Please activate new package to continue!',
                        placement: 'bottom',
                        trigger: 'hover'
                    });
                }
        });
    </script>
    <script>
        (function($) {
            "use strict";

            let page = 1;
            let adsPerPage = 10;
            let isLoading = false;
            let adsCache = []; // Store fetched ads in the cache to handle pagination
            let hasMorePages = true;

            // Function to fetch data with filters
            function fetchData(reset = false) {
                if (isLoading) return;
                isLoading = true;

                if (reset) {
                    page = 1;
                    hasMorePages = true;
                    $('#adsContainer').empty().append(
                        '<div class="text-center col-12" id="loading-indicator"><i class="fa fa-spinner fa-spin"></i> Loading...</div>'
                    );
                } else {
                    // Don't fetch more if there are no more pages
                    if (!hasMorePages) {
                        isLoading = false;
                        return;
                    }
                    $('#loadingMore').show();
                }

                // Get selected categories
                let selectedCategories = [];
                $('.category-checkbox:checked').each(function() {
                    selectedCategories.push($(this).val());
                });

                // Get selected statuses
                let selectedStatuses = [];
                $('.status-checkbox:checked').each(function() {
                    if ($(this).val() !== 'all') {
                        selectedStatuses.push($(this).val());
                    }
                });

                // If "All" is checked, don't filter by status
                let filterByStatus = !$('#status_all').is(':checked');

                let data = {
                    subcategory_ids: selectedCategories.join(','),
                    statuses: selectedStatuses.join(','),
                    filter_by_status: filterByStatus,
                    search: $('#searchInput').val(),
                    page: page,
                    user_ads_only: true
                };

                // Make AJAX request
                $.ajax({
                    url: "{{ route('user.advertisement.myAds.filter') }}",
                    method: "GET",
                    data: data,
                    success: function(response) {
                        isLoading = false;
                        $('#loading-indicator').remove();
                        $('#initialLoading').remove();
                        $('#loadingMore').hide();

                        if (reset) {
                            $('#adsContainer').empty();
                        }

                        // Cache ads to handle pagination
                        adsCache = response.advertisements;
                        hasMorePages = response.has_more;

                        if (adsCache.length > 0) {
                            showPage(page); // Show the ads for the current page
                            updatePagination(); // Update pagination controls
                        } else if (page === 1) {
                            $('#adsContainer').html(
                                '<div class="no-results">No advertisements found.</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        isLoading = false;
                        $('#loading-indicator').remove();
                        $('#initialLoading').remove();
                        $('#loadingMore').hide();
                        console.error("Error fetching advertisements:", error);
                        $('#adsContainer').append(
                            '<div class="alert alert-danger">Failed to load advertisements. Please try again.</div>'
                        );
                    }
                });
            }

            // Display ads for the current page
            function showPage(page) {
                $('#adsContainer').empty();
                let startIndex = (page - 1) * adsPerPage;
                let endIndex = startIndex + adsPerPage;

                let adsToDisplay = adsCache.slice(startIndex, endIndex);

                $.each(adsToDisplay, function(index, ad) {
                    let statusClass = '';
                    let statusText = 'Unknown';

                    switch (parseInt(ad.status)) {
                        case 0:
                            statusClass = 'status-pending';
                            statusText = 'Pending';
                            break;
                        case 1:
                            statusClass = 'status-approved';
                            statusText = 'Approved';
                            break;
                        case 2:
                            statusClass = 'status-completed';
                            statusText = 'Completed';
                            break;
                        case 3:
                            statusClass = 'status-paused';
                            statusText = 'Paused';
                            break;
                        case 4:
                            statusClass = 'status-ongoing';
                            statusText = 'Ongoing';
                            break;
                        case 5:
                            statusClass = 'status-expired';
                            statusText = 'Expired';
                            break;
                        case 6:
                            statusClass = 'status-cancelled';
                            statusText = 'Cancelled';
                            break;
                        case 9:
                            statusClass = 'status-rejected';
                            statusText = 'Rejected';
                            break;
                        default:
                            statusClass = '';
                            statusText = 'Unknown';
                    }

                    const adId = `ad-${ad.id}`;
                    const isBoosted = ad.boost_package !== null;

                    if ($('#' + adId).length === 0) {
                        let priceValue = parseFloat(ad.price_formatted.replace(/,/g, ''));
                        let adHtml = `
                    <a href="/user/advertisement/preview/${ad.id}/${ad.account_type}">
                    <div id="${adId}" class="mb-3 ad-item col-12" data-name="${ad.title.toLowerCase()}">
                        <div class="ad-card">
                            <div class="ad-image-container">
                                <img src="${ad.image_url}" class="card-img-top ad-image" alt="${ad.title}">
                            </div>
                            <div class="card-container">
                                <div class="card-details">
                                    <p class="card-title">${ad.title}</p>
                                    <div class="flex-row d-flex justify-content-between">
                                        <p class="card-location text-muted">
                                            ${ad.city_name}, ${ad.district_name}
                                        </p>
                                        <div class="ad-status-container">
                                            <div class="text-end">
                                                <div><span class="ad-status ${statusClass}">${statusText}</span></div>
                                                ${isBoosted ? '<div class="mt-1"><span class="ad-status status-boosted" style=" background-color: #FFEB3B ;color: #856404 ;border: 1px solid #FFC107;">Boosted</span></div>' : ''}
                                            </div>
                                        </div>
                                    </div>
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
                    </div> </a>`;
                        $('#adsContainer').append(adHtml);
                    }
                });
            }

            // Update pagination controls
            function updatePagination() {
                let totalPages = Math.ceil(adsCache.length / adsPerPage);
                if (totalPages <= 1) return; // No pagination needed if only one page

                // Remove existing pagination controls to avoid duplication
                $('.pagination-wrapper').remove();

                let paginationHtml = `
                        <div class="mt-4 mb-4 pagination-wrapper">
                            <div class="d-flex justify-content-center">
                                <ul class="pagination">
                                    <li class="page-item ${page <= 1 ? 'disabled' : ''}">
                                        <a class="page-link" href="javascript:void(0)" data-page="${page - 1}">&laquo;</a>
                                    </li>`;

                                // Display current page
                                paginationHtml += `
                        <li class="page-item active">
                            <a class="page-link" href="javascript:void(0)" data-page="${page}">${page}</a>
                        </li>`;

                                // Display next page if it exists
                                if (page < totalPages) {
                                    paginationHtml += `
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" data-page="${page + 1}">${page + 1}</a>
                            </li>`;
                                }

                                paginationHtml += `
                                    <li class="page-item ${page >= totalPages ? 'disabled' : ''}">
                                        <a class="page-link" href="javascript:void(0)" data-page="${page + 1}">&raquo;</a>
                                    </li>
                                </ul>
                            </div>
                        </div>`;

                // Append the new pagination controls
                $('#adsContainer').after(paginationHtml);

                // Add event listener to page links
                $('.pagination .page-link').on('click', function(e) {
                    e.preventDefault();
                    let newPage = parseInt($(this).data('page'));

                    if (!isNaN(newPage) && newPage >= 1 && newPage <= totalPages) {
                        page = newPage;
                        showPage(page); // Show ads for the new page
                        updatePagination(); // Update pagination controls
                    }
                });
            }


            $(document).ready(function() {
                // Event listener for category checkboxes
                $('.category-checkbox').on('change', function() {
                    fetchData(true);
                });

                // Event listener for status checkboxes
                $('.status-checkbox').on('change', function() {
                    fetchData(true);
                });

                // Search input handling with debounce
                let searchTimeout;
                $('#searchInput').on('keyup', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        fetchData(true);
                    }, 500);
                });

                // Reset filters button
                $('#resetFilters').on('click', function() {
                    $('.category-checkbox').prop('checked', false);
                    $('.status-checkbox:not(#status_all)').prop('checked', false);
                    $('#status_all').prop('checked', true);
                    $('#searchInput').val('');
                    fetchData(true);
                });

                // Initial load
                fetchData(true);
            });
        })(jQuery);
    </script>
@endpush
