@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
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
                                <h6 class="accordion-header" id="heading-{{ $category->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#category-{{ $category->id }}" aria-expanded="false"
                                        aria-controls="category-{{ $category->id }}">
                                        <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                            alt="category image"
                                            style="width:25px; height:25px; border-radius:4px; margin-right:8px;" />
                                        {{ $category->name }}
                                    </button>
                                </h6>
                                <div id="category-{{ $category->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#categoryAccordion" aria-labelledby="heading-{{ $category->id }}">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled">
                                            @foreach ($category->subcategories as $subCategory)
                                                <li class="clickable-item">
                                                    <input type="checkbox" class="category-checkbox" name="subcategory_id[]"
                                                        value="{{ $subCategory->id }}"
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

                <!-- Right Side: Advertisements -->
                <div class="order-1 col-lg-8 col-md-8 col-12 order-md-2">


                    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner"></div>
                        <div class="carousel-indicators-custom">
                            <span class="indicator active" data-bs-target="#imageCarousel" data-bs-slide-to="0"></span>
                            <span class="indicator" data-bs-target="#imageCarousel" data-bs-slide-to="1"></span>
                            <span class="indicator" data-bs-target="#imageCarousel" data-bs-slide-to="2"></span>
                        </div>
                    </div>
<br>
                    <!-- Desktop View: Search and Categories -->
                    <div class="mb-4 row d-none d-md-flex">
                        <div class="col-12">
                            <div class="search-category-bar ">

                                <div class="search-container">
                                    <div class="position-relative pt-0 !important">
                                        <input type="text" id="searchInput" class="form-control"
                                            placeholder="Search ads">
                                        <div class="search-icon">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile: Search bar -->
                    <div class="mb-4 d-md-none">
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

                    <div class="row">
                        <!-- Advertisements -->
                        <div class="order-1 col-12 order-md-2">
                            <!-- Category Icons Section (Mobile) -->
                            <div class="mb-4 mobile-categories d-md-none">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Browse Advertisements by category</h6>
                                    <a href="javascript:void(0)" id="seeAllCategoriesBtn" class="text-primary">See all</a>
                                </div>

                                <div class="category-icons-container">
                                    <div class="category-scroll-container" id="initialCategories">
                                        @php $visibleCategories = $categories->take(5); @endphp
                                        @foreach ($visibleCategories as $category)
                                            <div class="category-item visible-category">
                                                <a href="{{ route('user.login') }}"
                                                    class="text-decoration-none">
                                                    <div class="text-center category-icon-box">
                                                        @if ($category->image)
                                                            <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                                                alt="{{ $category->name }}" class="category-icon">
                                                        @else
                                                            <div class="placeholder-icon">
                                                                <i class="fa fa-box"></i>
                                                            </div>
                                                        @endif
                                                        <p class="mt-2 mb-0 category-name">{{ $category->name }}</p>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Category Overlay Header -->
                                    <div class="category-overlay-header" id="categoryOverlayHeader"
                                        style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Select a Category</h5>
                                            <button type="button" id="closeCategories" class="btn-close"
                                                aria-label="Close"></button>
                                        </div>
                                    </div>

                                    <!-- All categories in list view -->
                                    <div class="all-categories-list" id="allCategoriesContainer" style="display: none;">
                                        <div class="category-full-list">
                                            <a href="{{ route('user.login') }}" class="text-decoration-none">
                                                <div class="category-list-row">
                                                    <div class="category-list-content">
                                                        <span class="category-list-name">All Categories</span>
                                                        <button class="category-btn">X</button>
                                                    </div>
                                                </div>
                                            </a>
                                            @foreach ($categories as $category)
                                                <div class="category-parent">
                                                    <div class="category-list-row category-parent-row"
                                                        data-category-id="{{ $category->id }}">
                                                        <div class="category-list-content">
                                                            <div class="category-list-icon-container">
                                                                @if ($category->image)
                                                                    <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                                                        alt="{{ $category->name }}"
                                                                        class="category-list-icon">
                                                                @else
                                                                    <div class="category-list-placeholder-icon">
                                                                        <i class="fa fa-box"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <span class="category-list-name">{{ $category->name }}</span>
                                                            @if ($category->subCategories && $category->subCategories->count() > 0)
                                                                <div class="category-chevron">
                                                                    <i class="fa fa-chevron-right chevron-icon"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if ($category->subCategories && $category->subCategories->count() > 0)
                                                        <div class="subcategory-container"
                                                            id="subcategory-{{ $category->id }}" style="display: none;">
                                                            <a href="{{ route('user.login') }}"
                                                                class="text-decoration-none">
                                                                <div class="subcategory-list-row">
                                                                    <div class="subcategory-list-content">
                                                                        <span class="subcategory-list-name">All
                                                                            {{ $category->name }}</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            @foreach ($category->subCategories as $subCategory)
                                                                <a href="{{ route('user.login') }}"
                                                                    class="text-decoration-none">
                                                                    <div class="subcategory-list-row">
                                                                        <div class="subcategory-list-content">
                                                                            <div class="subcategory-list-icon-container">
                                                                                @if ($subCategory->image)
                                                                                    <img src="{{ asset('assets/images/subcategory/' . $subCategory->image) }}"
                                                                                        alt="{{ $subCategory->name }}"
                                                                                        class="subcategory-list-icon">
                                                                                @else
                                                                                    <div
                                                                                        class="subcategory-list-placeholder-icon">
                                                                                        <i class="fa fa-tag"></i>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <span
                                                                                class="subcategory-list-name">{{ $subCategory->name }}</span>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advertisements Container -->
                            <div id="adsContainer" class="row">

                            </div>

                        </div>
                    </div>
                </div>

                <!-- Category Selection Modal -->
                <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="categoryModalLabel">Select Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Left side - Category List -->
                                    <div class="col-md-4 border-end">
                                        <div class="category-sidebar">
                                            <a href="{{ route('user.login') }}" class="text-decoration-none">
                                                <div class="category-sidebar-item">
                                                    <span>All Categories</span>
                                                </div>
                                            </a>
                                            @foreach ($categories as $category)
                                                <div class="category-sidebar-item"
                                                    data-category-id="{{ $category->id }}">
                                                    @if ($category->image)
                                                        <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                                            alt="{{ $category->name }}" class="category-icon-sm me-2">
                                                    @else
                                                        <div class="category-icon-placeholder me-2">
                                                            <i class="fa fa-box"></i>
                                                        </div>
                                                    @endif
                                                    <span>{{ $category->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Right side - Subcategories -->
                                    <div class="col-md-8">
                                        <div class="subcategory-area">
                                            <h6 class="subcategory-title" id="selectedCategoryName">Select a category</h6>
                                            <div class="subcategory-container">
                                                @foreach ($categories as $category)
                                                    <div class="subcategory-list" id="subcategories-{{ $category->id }}"
                                                        style="display: none;">
                                                        @if ($category->subCategories && $category->subCategories->count() > 0)
                                                            @foreach ($category->subCategories as $subCategory)
                                                                <a href="{{ route('user.login') }}"
                                                                    class="subcategory-item">
                                                                    <div class="d-flex align-items-center">
                                                                        @if ($subCategory->image)
                                                                            <img src="{{ asset('assets/images/subcategory/' . $subCategory->image) }}"
                                                                                alt="{{ $subCategory->name }}"
                                                                                class="subcategory-icon-sm me-2">
                                                                        @else
                                                                            <div class="subcategory-icon-placeholder me-2">
                                                                                <i class="fa fa-tag"></i>
                                                                            </div>
                                                                        @endif
                                                                        <span>{{ $subCategory->name }}</span>
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        @else
                                                            <div class="no-subcategories">
                                                                No subcategories available
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (isset($promotion_banner) && $promotion_banner && $promotion_banner->status == 1)
                    <!-- Popup Box -->
                    <div id="popupBox" class="popup">
                        <div class="popup-content">
                            <div class="pop-head">
                                <p class="popup-message">{{ $promotion_banner->title }}</p>
                                <button class="close-btn" onclick="closePopup()">×</button>
                                <div class="image-container">
                                    <div class="image-box">
                                        @if ($promotion_banner->image)
                                            <img src="{{ getImage(getFilePath('promotionalBanner') . '/' . $promotion_banner->image, getFileSize('promotionalBanner')) }}"
                                                class="b-radius--10 withdraw-detailImage">
                                        @else
                                            <img src="{{ getImage('assets/templates/basic/images/product/offer.png') }}"
                                                alt="offer" class="b-radius--10 withdraw-detailImage">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <h5 class="popup-message">{{ $promotion_banner->description }}</h5>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>

@endsection

@push('script')
    <style>
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
            margin-top: 38px;
        }

        /* Refinements for filter checkbox styles */
        .clickable-item {
            margin-left: 25px;
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
            accent-color: #1e3774;
        }

        .clickable-item label {
            cursor: pointer;
            margin-bottom: 0;
            flex-grow: 1;
            font-size: 14px;
        }

        /* Badge styles for selected filters */
        .selected-filters {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            background-color: #e9f5f2;
            color: #1e3774;
            border-radius: 4px;
            padding: 5px 12px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .filter-badge .remove-filter {
            margin-left: 5px;
            cursor: pointer;
            font-size: 10px;
            color: #025e4f;
        }

        /* Loading indicator */
        #loading-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
            color: #1e3774;
        }

        /* No results message */
        .no-results {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            color: #666;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin: 20px 0;
        }

        .no-results i {
            font-size: 40px;
            color: #ccc;
            margin-bottom: 15px;
        }
    </style>
    <style>
        #adsContainer {
            display: flex;
            flex-direction: column;
        }

        /* Filter sidebar styles */
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
            color: #1e3774;
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
            padding: 8px 0;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .clickable-item input {
            margin-right: 8px;
        }

        .reset-button {
            background: none;
            border: none;
            color: #1e3774;
            font-size: 14px;
        }

        .reset-button:hover {
            color: #025e4f;
        }

        #loading-indicator {
            color: #1e3774;
        }

        /* General styles */
        .favorite-icon {
            margin: 0;
        }

        /* Balance card styles */
        .balance-card {
            background: linear-gradient(to bottom, #019B35, #CFFFDF);
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .balance-image {
            width: 140px;
            text-align: center;
            padding-right: 15px;
        }

        .person-img {
            max-width: 100%;
            height: auto;
        }

        .post-ad-btn {
            display: flex;
            background-color: #1e3774;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-right: 10px;
        }

        .my-ad-btn {
            display: inline-block;
            background-color: #f8f9fa;
            color: #1e3774;
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .my-ad-btn:hover {
            background-color: #e2e6ea;
            color: #1e3774;
        }

        .post-ad-btn:hover {
            background-color: #025e4f;
            color: white;
        }

        .my-ads-link {
            color: #1e3774;
            text-decoration: underline;
            margin-left: 10px;
        }

        /* Search and category styles */
        .search-category-bar {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
        }

        /* Mobile categories */
        .category-icons-container {
            white-space: nowrap;
            padding: 10px 0;
        }

        .category-scroll-container {
            display: flex;
            padding-bottom: 5px;
            overflow-x: auto;
        }

        .category-item {
            flex: 0 0 auto;
            margin-right: 15px;
        }

        .category-icon-box {
            background-color: #f1f1f1;
            border-radius: 10px;
            padding: 10px;
            width: 80px;
            transition: transform 0.2s;
        }

        .category-icon-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .category-icon {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .placeholder-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin: 0 auto;
        }

        .category-name {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 80px;
        }

        /* Category overlay header styles */
        .category-overlay-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            z-index: 1001;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Category list view for mobile */
        .all-categories-list {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .category-full-list {
            display: flex;
            flex-direction: column;
        }

        /* Parent category styles */
        .category-parent {
            border-bottom: 1px solid #f0f0f0;
        }

        .category-parent:last-child {
            border-bottom: none;
        }

        .category-list-row {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
        }

        .category-parent-row {
            cursor: pointer;
        }

        .category-list-row:hover,
        .category-parent-row:hover {
            background-color: #f9f9f9;
        }

        .category-list-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .category-list-name {
            text-align: left;
            font-size: 14px;
            color: #333;
            flex-grow: 1;
        }

        .category-btn {
            margin-left: auto;
            padding: 5px 10px;
            background-color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .category-btn:hover {
            background-color: #e9ecef;
        }

        .category-list-icon-container {
            width: 28px;
            height: 28px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-list-icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .category-list-placeholder-icon {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
        }

        .category-chevron {
            margin-left: auto;
            color: #aaa;
            font-size: 12px;
        }

        /* Subcategory styles */
        .subcategory-container {
            background-color: #f5f9ff;
            border-top: 1px solid #eaeaea;
            display: flex;
            flex-direction: column;
        }

        .subcategory-list-row {
            padding: 12px 15px 12px 40px;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            border-bottom: 1px solid #eaeaea;
            width: 100%;
        }

        .subcategory-list-row:last-child {
            border-bottom: none;
        }

        .subcategory-list-row:hover {
            background-color: #e9f0ff;
        }

        .subcategory-list-content {
            width: 100%;
            display: flex;
            align-items: center;
            position: relative;
        }

        .subcategory-list-icon-container {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .subcategory-list-icon {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .subcategory-list-placeholder-icon {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            font-size: 10px;
        }

        .subcategory-list-name {
            font-size: 14px;
            color: #333;
            flex-grow: 1;
        }

        /* Animation for chevron */
        .chevron-icon {
            transition: transform 0.3s ease;
        }

        .chevron-icon.rotate {
            transform: rotate(90deg);
        }

        /* Category overlay for mobile */
        .category-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1000;
            background-color: white;
            overflow-y: auto;
            padding-top: 65px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 15px;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .hide-when-overlay {
            transition: opacity 0.3s, visibility 0.3s;
        }

        .hide-when-overlay.hidden {
            opacity: 0;
            visibility: hidden;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
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
            height: 130px;
            transition: box-shadow 0.3s ease-in-out, transform 0.1s;
        }

        .ad-card:hover {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        .ad-image-container {
            width: 30%;
            overflow: hidden;
            position: relative;
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
            color: #1e3774;
            margin-bottom: 2px;
        }

        .updated-time {
            position: absolute;
            bottom: -35px;
            right: -2px;
            font-size: 16px;
        }

        .card-fav {
            position: absolute;
            top: 4px;
            right: 4px;
        }

        .card-fav {
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

        .main-anchor {
            cursor: pointer;
        }

        .main-anchor:hover {
            text-decoration: none;
            color: inherit;
        }

        /* Category Modal Styles */
        .category-sidebar {
            max-height: 400px;
            overflow-y: auto;
        }

        .category-sidebar-item {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .category-sidebar-item:hover {
            background-color: #f5f5f5;
        }

        .category-sidebar-item.active {
            background-color: #e9f5f2;
            color: #1e3774;
            font-weight: 500;
        }

        .category-icon-sm {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .category-icon-placeholder {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e0e0;
            border-radius: 4px;
            color: #666;
            font-size: 12px;
        }

        .subcategory-area {
            padding: 10px 0;
        }

        .subcategory-title {
            padding: 0 15px 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            color: #1e3774;
        }

        .subcategory-container {
            max-height: 350px;
            overflow-y: auto;
        }

        .subcategory-item {
            display: block;
            padding: 8px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: background-color 0.2s;
        }

        .subcategory-item:hover {
            background-color: #f5f5f5;
            color: #1e3774;
        }

        .subcategory-icon-sm {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .subcategory-icon-placeholder {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e0e0e0;
            border-radius: 4px;
            color: #666;
            font-size: 10px;
        }

        .no-subcategories {
            padding: 15px;
            color: #888;
            text-align: center;
            font-style: italic;
        }

        /* Category selection in modal */
        .category-selection-item {
            display: block;
            color: #333;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .category-selection-item:hover {
            transform: translateY(-2px);
        }

        /* Popup styles */
        .popup {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.3s;
        }

        .popup.show {
            display: block;
        }

        .popup-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideIn 0.4s;
        }

        .close-btnn {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 24px;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .balance-image {
                width: 90px;
                padding-right: 10px;
            }

            .balance-text {
                font-size: 14px;
            }

            .balance-text h4 {
                font-size: 18px;
            }

            .ad-card {
                margin-bottom: 15px;
            }

            .card-title,
            .card-location {
                font-size: 14px;
            }

            .updated-time {
                bottom: -40px;
            }

            .updated-time p {
                font-size: 12px !important;
            }

            .ad-price {
                font-size: 12px;
            }

            .popup-content {
                margin: 20% auto;
                max-width: 90%;
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

            .ad-image {
                height: 105px;
            }

            .card-fav {
                top: 4px;
                right: 4px;
            }

            .my-ad-btn {
                padding: 3px 5px;
            }
        }

        /* No results message */
        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 16px;
        }

        /* Boost tag styles */
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

        /* Sold badge */
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

        .highlighted-card-inner {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Animation for gradient movement */
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
    </style>

    <script>
        $(document).ready(function() {
            // Initialize the modal
            var categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

            // Show modal when clicking the category button
            $('#selectCategoryBtn').on('click', function() {
                categoryModal.show();
            });

            // Search functionality
            $('#searchInput, #searchInputMobile').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                filterAds(searchTerm);
            });

            function filterAds(searchTerm) {
                $('.ad-item').each(function() {
                    var adName = $(this).data('name');
                    if (adName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Category sidebar item click handling
            $('.category-sidebar-item').on('click', function() {
                var categoryId = $(this).data('category-id');
                if (!categoryId) return; // Skip if it's "All Categories"

                // Update active state
                $('.category-sidebar-item').removeClass('active');
                $(this).addClass('active');

                // Show the corresponding subcategories
                $('.subcategory-list').hide();
                $('#subcategories-' + categoryId).show();

                // Update the title with category name
                $('#selectedCategoryName').text($(this).find('span').text());
            });

            // Mobile category handling
            $('#seeAllCategoriesBtn').on('click', function() {
                toggleCategoryOverlay(true);
            });

            $('#closeCategories').on('click', function() {
                toggleCategoryOverlay(false);
            });

            $('.category-parent-row').on('click', function() {
                var categoryId = $(this).data('category-id');
                var subCategoryContainer = $('#subcategory-' + categoryId);
                var chevronIcon = $(this).find('.chevron-icon');

                if (subCategoryContainer.is(':visible')) {
                    subCategoryContainer.slideUp(200);
                    chevronIcon.removeClass('rotate');
                } else {
                    $('.subcategory-container').slideUp(200);
                    $('.chevron-icon').removeClass('rotate');
                    subCategoryContainer.slideDown(200);
                    chevronIcon.addClass('rotate');
                }
            });

            function toggleCategoryOverlay(show) {
                if (show) {
                    $('#initialCategories').hide();
                    $('#allCategoriesContainer').fadeIn(300);
                    $('#allCategoriesContainer').addClass('category-overlay');
                    $('#categoryOverlayHeader').fadeIn(200);
                    $('body').addClass('overflow-hidden');
                    $('.advertiesment-header').addClass('hidden hide-when-overlay');
                    $('.balance-card').addClass('hidden hide-when-overlay');
                    $('.search-container').addClass('hidden hide-when-overlay');
                    $('.mobile-categories > .d-flex').addClass('hidden hide-when-overlay');
                    $('#adsContainer').addClass('hidden hide-when-overlay');
                } else {
                    $('#allCategoriesContainer').hide();
                    $('#categoryOverlayHeader').hide();
                    $('#initialCategories').fadeIn(300);
                    $('#allCategoriesContainer').removeClass('category-overlay');
                    $('body').removeClass('overflow-hidden');
                    $('.advertiesment-header').removeClass('hidden hide-when-overlay');
                    $('.balance-card').removeClass('hidden hide-when-overlay');
                    $('.search-container').removeClass('hidden hide-when-overlay');
                    $('.mobile-categories > .d-flex').removeClass('hidden hide-when-overlay');
                    $('#adsContainer').removeClass('hidden hide-when-overlay');
                }
            }

            // Popup handling for promotions
            if ($('#popupBox').length) {
                setTimeout(function() {
                    $('#popupBox').addClass('show').css('display', 'block');
                }, 1000);
            }
        });

        window.closePopup = function() {
            const popup = document.getElementById('popupBox');
            if (popup) {
                popup.classList.remove('show');
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 500);
            }
        };
    </script>

    <script>
        $(document).ready(function() {
            let isUserLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            let currentPage = 1;
            let itemsPerPage = 10; // << you can change this anytime (ex: 5, 10, etc.)
            let adsCache = [];

            // Fetch all advertisements once
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

                let searchTerm = $('#searchInput').val() || $('#searchInputMobile').val() || '';

                // Show loading indicator
                $('#adsContainer').html(
                    '<div id="loading-indicator" class="py-3 text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>'
                );

                // AJAX to get ALL ads
                $.ajax({
                    url: "{{ route('ads.filter') }}",
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

            // Display advertisements for current page

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
                        let adLinkStart = isUserLoggedIn
                                            ? `<a href="/user/advertisement/preview/${ad.id}" class="main-anchor">`
                                            : `<a href="/preview/${ad.id}" class="main-anchor">`;
                        let adHtml = `
                                ${adLinkStart}
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

            // Create pagination controls
            function updatePagination() {
                $('.pagination-wrapper').remove();

                let totalAds = adsCache.length;
                let totalPages = Math.ceil(totalAds / itemsPerPage);

                if (totalPages <= 1) return; // No need for pagination

                let paginationHtml = `
                        <div class="mt-4 mb-4 pagination-wrapper">
                            <div class="d-flex justify-content-center">
                                <ul class="pagination">
                                    <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                                        <a class="page-link" href="javascript:void(0)" data-page="${currentPage - 1}">«</a>
                                    </li>`;

                                // Only display two page numbers: current and next (if exists)
                                paginationHtml += `
                        <li class="page-item active">
                            <a class="page-link" href="javascript:void(0)" data-page="${currentPage}">${currentPage}</a>
                        </li>`;

                if (currentPage < totalPages) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" data-page="${currentPage + 1}">${currentPage + 1}</a>
                        </li>`;
                }

                paginationHtml += `
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

                        // Update pagination after clicking (IMPORTANT!)
                        updatePagination();

                        $('html, body').animate({
                            scrollTop: $('#adsContainer').offset().top - 100
                        }, 200);
                    }
                });
            }

            // Filter events
            $(document).on('change', '.category-checkbox', function() {
                fetchData(true);
            });

            $('#resetFilters').on('click', function() {
                $('.category-checkbox').prop('checked', false);
                $('#searchInput, #searchInputMobile').val('');
                fetchData(true);
            });

            let searchTimeout;
            $('#searchInput, #searchInputMobile').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    fetchData(true);
                }, 500);
            });

            // Accordion behavior
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

            // Initial load
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

<style>
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
    }

    .carousel-inner .carousel-item img {
        height: 180px;
        object-fit: fill;
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

    @media (max-width: 1440px) {
        .carousel-inner,
        .carousel-inner .carousel-item img {
            height: 250px;
        }
    }

    @media (max-width: 1024px) {
        .carousel-inner,
        .carousel-inner .carousel-item img {
            height: 180px;
        }
    }

    @media (max-width: 768px) {
        .carousel-inner,
        .carousel-inner .carousel-item img {
            height: 170px;
            object-fit: fill;
        }
    }

    @media (max-width: 470px) {
        .carousel-inner,
        .carousel-inner .carousel-item img {
            height: 140px;
            object-fit: fill;
        }
    }
</style>
@endpush
