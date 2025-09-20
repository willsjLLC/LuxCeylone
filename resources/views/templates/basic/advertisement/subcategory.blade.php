@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')

@section('panel')

<div class="container subcategory-container">
    <div class="mb-4 row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.advertisement.index') }}">Categories</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.advertisement.category', $subCategory->category->name) }}">{{ $subCategory->category->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subCategory->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="mb-4 row">
        <div class="col-12">
            <div class="subcategory-header">
                <h2>{{ $pageTitle }}</h2>
            </div>
        </div>
    </div>

    <!-- Search bar -->
    <div class="mb-4 row search-row">
        <div class="align-items-center">
            <div class="position-relative">
                <input type="text" id="searchInput" class="form-control" placeholder="Search in {{ $subCategory->name }}..."
                    style="border-radius: 12px; padding:7px">
            </div>
        </div>
    </div>

    <!-- Advertisements Container -->
    <div id="adsContainer" class="row">
        @forelse ($advertisements as $advertisement)
        <a href="{{ route('user.advertisement.preview', $advertisement->id) }}" class="main-anchor ad-item-container">
            <div class="mb-3 ad-item col-12" data-name="{{ strtolower($advertisement->title) }}">
                <div class="ad-card">
                    <div class="ad-image-container">
                        @if ($advertisement->file_name)
                            <img src="{{ asset('assets/admin/images/advertisementImages/' . $advertisement->file_name) }}"
                                class="card-img-top ad-image" alt="{{ $advertisement->title }}">
                        @else
                            <img src="{{ asset('assets/images/default-ad-image.jpg') }}"
                                class="card-img-top ad-image" alt="Default Ad Image">
                        @endif
                    </div>
                    <div class="card-container">
                        <div class="card-details">
                            <p class="card-title">{{ $advertisement->title }}</p>
                            <p class="card-location text-muted">
                                {{ $advertisement->city->name ?? 'Unknown' }}, {{ $advertisement->district->name ?? 'Unknown' }}
                            </p>
                            <div class="flex-row d-flex">
                                @if(number_format($advertisement->price, 2)>0)
                                    <p class="card-text ad-price">LKR {{ number_format($advertisement->price, 2) }}</p>
                                @endif
                            </div>

                            <div class="card-fav">
                                    <i class="fa fa-arrow-right"></i>
                            </div>
                        </div>
                        <div class="updated-time">
                            <p>{{ $advertisement->posted_date->diffForHumans() }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </a>
        @empty
            <div class="no-results">No advertisements found in this subcategory.</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <nav aria-label="Advertisement pagination">
            <ul id="pagination" class="pagination justify-content-center">
                <!-- Pagination buttons will be inserted here by JavaScript -->
            </ul>
        </nav>
    </div>

    <!-- Back navigation -->
    <div class="mt-4 row">
        <div class="col-12">
            <a href="{{ route('user.advertisement.category', $subCategory->category->name) }}" class="btn-back">
                <i class="fa fa-arrow-left me-2"></i> Back to {{ $subCategory->category->name }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .subcategory-container{
        max-width:720px;
    }
    /* Breadcrumb styles */
    .breadcrumb {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .breadcrumb-item a {
        color: #1e3774;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .subcategory-header h2 {
        color: #333;
        margin-bottom: 10px;
    }

    /* Back button */
    .btn-back {
        display: inline-block;
        padding: 4px 8px;
        background-color: #1e3774;
        color: white;
        border-radius: 20px;
        font-weight: bold;
        margin-top: 20px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background-color: #e9ecef;
    }

    /* Search Bar */
    .search-row {
        margin-bottom: 15px;
    }

    #searchInput {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    #searchInput:focus {
        outline: none;
        border-color: #009933;
        box-shadow: 0 0 0 2px rgba(0, 153, 51, 0.2);
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
        height: 150px;
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
        height: 125px;
        width: 100%;
        border-radius: 3px;
        object-fit: cover;
    }

    .card-container {
        position:relative;
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
        color: #1e3774;
        margin-bottom: 2px;
    }

    .updated-time {
        position: absolute;
        bottom: -10px;
        right: -2px;
        font-size: 16px;
    }

    .card-fav {
        position: absolute;
        top: 4px;
        right: 4px;
    }

    .card-fav{
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        color: #1e3774;
        transition: background-color 0.3s;
    }

    .card-fav:hover {
        background-color: #1e3774;
        color: white;
    }

    /* No results message */
    .no-results {
        text-align: center;
        padding: 20px;
        color: #666;
        font-size: 16px;
    }
    .main-anchor{
        cursor:pointer;
    }
    .main-anchor:hover{
        text-decoration: none;
        color: inherit;
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

        .updated-time {
            font-size: 10px;
        }

        .ad-price {
            font-size: 12px;
        }
        .updated-time{
            font-size:14px;
        }
    }

    @media (max-width: 576px) {
        .ad-card {
            height:130px;
            flex-direction: row;
        }

        .ad-image-container {
            width: 30%;
        }

        .card-container {
            width: 70%;
            margin-left: 10px;
        }

        .ad-image {
            height: 105px;
        }

        .card-fav {
            top: 4px;
            right: 4px;
        }
        .updated-time{
            font-size:12px;
        }
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

        // Filter advertisements based on search input
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".ad-item").filter(function() {
                    $(this).toggle($(this).data("name").indexOf(value) > -1);
                });
            });
        });
    })(jQuery);

    (function($) {
        "use strict";

        // Pagination configuration
        const itemsPerPage = 15;
        let currentPage = 1;
        let filteredItems = [];
        
        // Initialize advertisements and pagination on document ready
        $(document).ready(function() {
            // Store all ad items initially
            filteredItems = $('.ad-item-container').toArray();
            
            // Initialize pagination
            updatePagination();
            displayCurrentPageItems();
            
            // Filter advertisements based on search input
            $("#searchInput").on("keyup", function() {
                const searchValue = $(this).val().toLowerCase();
                
                // Reset to first page when searching
                currentPage = 1;
                
                // Filter items based on search value
                filteredItems = $('.ad-item-container').filter(function() {
                    const nameMatch = $(this).find('.ad-item').data("name").indexOf(searchValue) > -1;
                    return nameMatch;
                }).toArray();
                
                // Update pagination and display
                updatePagination();
                displayCurrentPageItems();
                
                // Show "no results" message if needed
                if (filteredItems.length === 0) {
                    if ($('#no-results-message').length === 0) {
                        $('#adsContainer').append('<div id="no-results-message" class="col-12"><div class="alert alert-info">No advertisements match your search.</div></div>');
                    }
                } else {
                    $('#no-results-message').remove();
                }
            });
        });
        
        // Update pagination buttons
        function updatePagination() {
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            const $pagination = $('#pagination');
            $pagination.empty();
            
            // Don't show pagination if there's only one page or no items
            if (totalPages <= 1) {
                return;
            }
            
            // Previous button
            const $prevButton = $('<li class="page-item"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>');
            if (currentPage === 1) {
                $prevButton.addClass('disabled');
            } else {
                $prevButton.find('a').click(function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                        displayCurrentPageItems();
                    }
                });
            }
            $pagination.append($prevButton);
            
            // Previous page number (if not on first page)
            if (currentPage > 1) {
                const $prevPageNum = $('<li class="page-item"><a class="page-link" href="#">' + (currentPage - 1) + '</a></li>');
                $prevPageNum.find('a').click(function(e) {
                    e.preventDefault();
                    currentPage--;
                    updatePagination();
                    displayCurrentPageItems();
                });
                $pagination.append($prevPageNum);
            }
            
            // Current page
            const $currentPageNum = $('<li class="page-item active"><span class="page-link">' + currentPage + '</span></li>');
            $pagination.append($currentPageNum);
            
            
            // Next button
            const $nextButton = $('<li class="page-item"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>');
            if (currentPage === totalPages) {
                $nextButton.addClass('disabled');
            } else {
                $nextButton.find('a').click(function(e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                        displayCurrentPageItems();
                    }
                });
            }
            $pagination.append($nextButton);
        }
        
        // Display current page items
        function displayCurrentPageItems() {
            // Hide all ad items first
            $('.ad-item-container').hide();
            
            // Show only items for current page
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredItems.length);
            
            for (let i = startIndex; i < endIndex; i++) {
                $(filteredItems[i]).show();
            }
        }
    })(jQuery);

    
</script>
@endpush
