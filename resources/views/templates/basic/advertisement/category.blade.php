@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <div class="container mt-5 category-container">
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('user.advertisement.index') }}" class="text-dark me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h3 class="mb-0">{{ $pageTitle }}</h3>
        </div>
        <!-- Advertisements Section -->

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
                <div class="col-12">
                    <div class="alert alert-info">No advertisements found in this category.</div>
                </div>
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

        <!-- Back to categories link -->
        <div class="mt-4 row">
            <div class="col-12">
                <a href="{{ route('user.advertisement.index') }}" class="btn-back"> <i class="fa fa-arrow-left me-2"></i>Back to Advertisements</a>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            
            // Pagination functionality
            $(document).ready(function() {
                // Configuration
                const itemsPerPage = 15;
                const $items = $('.ad-item-container');
                const totalItems = $items.length;
                const totalPages = Math.ceil(totalItems / itemsPerPage);
                let currentPage = 1;
                
                // Initialize pagination
                function initPagination() {
                    $items.hide();
                    
                    showItemsForPage(currentPage);
                    
                    updatePaginationButtons();
                }
                
                // Show items for the specified page
                function showItemsForPage(page) {
                    const startIndex = (page - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    
                    $items.hide();
                    $items.slice(startIndex, endIndex).show();
                }
                
                // Update pagination buttons
                function updatePaginationButtons() {
                    const $pagination = $('#pagination');
                    $pagination.empty();
                    
                    // Previous button
                    const $prevButton = $('<li class="page-item"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>');
                    if (currentPage === 1) {
                        $prevButton.addClass('disabled');
                    } else {
                        $prevButton.find('a').click(function(e) {
                            e.preventDefault();
                            if (currentPage > 1) {
                                currentPage--;
                                showItemsForPage(currentPage);
                                updatePaginationButtons();
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
                            showItemsForPage(currentPage);
                            updatePaginationButtons();
                        });
                        $pagination.append($prevPageNum);
                    }
                    
                    // Current page
                    const $currentPageNum = $('<li class="page-item active"><span class="page-link">' + currentPage + '</span></li>');
                    $pagination.append($currentPageNum);
                    
                    // Next page number (if not on last page)
                    if (currentPage < totalPages) {
                        const $nextPageNum = $('<li class="page-item"><a class="page-link" href="#">' + (currentPage + 1) + '</a></li>');
                        $nextPageNum.find('a').click(function(e) {
                            e.preventDefault();
                            currentPage++;
                            showItemsForPage(currentPage);
                            updatePaginationButtons();
                        });
                        $pagination.append($nextPageNum);
                    }
                    
                    // Next button
                    const $nextButton = $('<li class="page-item"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>');
                    if (currentPage === totalPages) {
                        $nextButton.addClass('disabled');
                    } else {
                        $nextButton.find('a').click(function(e) {
                            e.preventDefault();
                            if (currentPage < totalPages) {
                                currentPage++;
                                showItemsForPage(currentPage);
                                updatePaginationButtons();
                            }
                        });
                    }
                    $pagination.append($nextButton);
                }
                
                // Initialize pagination if there are items
                if (totalItems > 0) {
                    initPagination();
                }
            });
        })(jQuery);
    </script>
@endpush


@push('style')
<style>
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    .category-container{
        max-width:720px;
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

    .updated-time p {
        margin: 0;
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

    /* Back button */
    .btn-back {
        display: inline-block;
        padding: 4px 8px;
        background-color: #1e3774;
        font-weight: bold;
        color: white;
        border-radius: 20px;
        text-decoration: none;
        margin-top: 20px;
        border: 1px solid #ddd;
        font-size:14px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background-color: #e9ecef;
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
        .updated-time {
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        .ad-card {
            flex-direction: row;
            height: 130px;
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
        .updated-time {
            font-size: 12px;
        }
    }
</style>
@endpush

