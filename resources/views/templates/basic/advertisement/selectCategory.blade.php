@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <div class="container">
        <div class="row justify-content-center">
            <div class="mt-3 col-md-10 col-lg-9">
                <!-- Header with responsive styling -->
                <div class="mb-3 category-header-container">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('user.advertisement.index') }}" class="text-dark me-3">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <h5 class="mb-0">Select a Category</h5>
                    </div>
                </div>

                <!-- Categories section with updated layout -->
                <div class="category-selector">
                    <!-- Mobile view with proper spacing -->
                    <div class="p-3 mobile-category-list d-md-none">
                        <!-- Mobile view code remains unchanged -->
                        <div class="all-categories-list" id="allCategoriesContainer">
                            <div class="category-full-list">
                                <!-- All Categories option -->
                                <a href="{{ route('user.advertisement.index') }}" class="text-decoration-none">
                                    <div class="category-list-row">
                                        <div class="category-list-content">
                                            <span class="category-list-name">All Categories</span>
                                        </div>
                                    </div>
                                </a>

                                <!-- Individual categories with subcategories -->
                                @foreach($categories as $category)
                                <div class="category-parent">
                                    <div class="category-list-row category-parent-row" data-category-id="{{ $category->id }}">
                                        <div class="category-list-content">
                                            <div class="category-list-icon-container">
                                                @if ($category->image)
                                                    <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                                        alt="{{ $category->name }}" class="category-list-icon">
                                                @else
                                                    <div class="category-list-placeholder-icon">
                                                        <i class="fa fa-box"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="category-list-name">{{ $category->name }}</span>
                                            @if($category->subCategories && $category->subCategories->count() > 0)
                                                <div class="category-chevron">
                                                    <i class="fa fa-chevron-right chevron-icon"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($category->subCategories && $category->subCategories->count() > 0)
                                        <div class="subcategory-container" id="subcategory-{{ $category->id }}" style="display: none;">
                                            <!-- "All [Category]" option -->
                                            <a href="{{ route('user.advertisement.category', $category->name) }}" class="text-decoration-none">
                                                <div class="subcategory-list-row">
                                                    <div class="subcategory-list-content">
                                                        <span class="subcategory-list-name">All {{ $category->name }}</span>
                                                    </div>
                                                </div>
                                            </a>

                                            <!-- Individual subcategories -->
                                            @foreach($category->subCategories as $subCategory)
                                                <a href="{{ route('user.advertisement.form', ['category' => $category->name, 'subCategory' => $subCategory->name]) }}" class="text-decoration-none">
                                                    <div class="subcategory-list-row">
                                                        <div class="subcategory-list-content">
                                                            <div class="subcategory-list-icon-container">
                                                                @if ($subCategory->image)
                                                                    <img src="{{ asset('assets/images/subcategory/' . $subCategory->image) }}"
                                                                        alt="{{ $subCategory->name }}" class="subcategory-list-icon">
                                                                @else
                                                                    <div class="subcategory-list-placeholder-icon">
                                                                        <i class="fa fa-tag"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <span class="subcategory-list-name">{{ $subCategory->name }}</span>
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

                    <!-- Desktop view with enhanced styling -->
                    <div class="desktop-category-container d-none d-md-flex">
                        <div class="row w-100 g-0">
                            <!-- Main categories column -->
                            <div class="col-md-6">
                                <div class="category-list">
                                    <!-- All Categories option -->
                                    <div class="category-item">
                                        <a href="{{ route('user.advertisement.index') }}" class="category-main">
                                            <span>All Categories</span>
                                        </a>
                                    </div>

                                    @foreach($categories as $category)
                                        <div class="category-item">
                                            <a href="{{ route('user.advertisement.category', $category->name) }}" class="category-main" data-category="{{ $category->name }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="category-list-icon-container me-2">
                                                        @if ($category->image)
                                                            <img src="{{ asset('assets/images/category/' . $category->image) }}"
                                                                alt="{{ $category->name }}" class="category-list-icon">
                                                        @else
                                                            <div class="category-list-placeholder-icon">
                                                                <i class="fa fa-box"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <span>{{ $category->name }}</span>
                                                </div>
                                                @if($category->subCategories->count() > 0)
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                @endif
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Subcategories column - now 50% width -->
                            <div class="col-md-6">
                                <div class="subcategories-container">
                                    @foreach($categories as $category)
                                        @if($category->subCategories->count() > 0)
                                            <div class="subcategory-list-desktop" id="subcategory-{{ $category->name }}">
                                                <h6 class="subcategory-header">{{ $category->name }}</h6>
                                                <div class="subcategory-list">
                                                    @foreach($category->subCategories as $subCategory)
                                                        <a href="{{ route('user.advertisement.form', ['category' => $category->name, 'subCategory' => $subCategory->name]) }}" class="subcategory-item-desktop">
                                                            @if ($subCategory->image)
                                                                <img src="{{ asset('assets/images/subcategory/' . $subCategory->image) }}"
                                                                    alt="{{ $subCategory->name }}" class="subcategory-list-icon me-2">
                                                            @else
                                                                <div class="subcategory-list-placeholder-icon me-2">
                                                                    <i class="fa fa-tag"></i>
                                                                </div>
                                                            @endif
                                                            {{ $subCategory->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Common styles */
        .category-header-container {
            padding: 15px 0;
        }

        /* Mobile styles */
        .mobile-category-list {
            padding: 10px;
            background-color: none;
            border: none;
        }

        /* Category list view for mobile */
        .all-categories-list {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

        .category-list-row:hover, .category-parent-row:hover {
            background-color: #f9f9f9;
        }

        .category-list-content {
            width: 100%;
            display: flex;
            align-items: center;
            position: relative;
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

        .category-list-name {
            font-size: 14px;
            color: #333;
            flex-grow: 1;
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
            flex-direction: column; /* Make subcategories stack vertically */
        }

        .subcategory-list-row {
            padding: 12px 15px 12px 40px;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
            border-bottom: 1px solid #eaeaea;
            width: 100%; /* Ensure full width */
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
            flex-shrink: 0; /* Prevent icon from shrinking */
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

        /* Desktop styles */
        .desktop-category-container {
            border: 1px solid #eaeaea;
            border-radius: 8px;
            overflow: hidden;
            min-height: 500px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            width: 100%;
        }

        .category-list {
            height: 100%;
            max-height: 75vh;
            overflow-y: auto;
            border-right: 1px solid #eee;
            background-color: #f8f8f8;
        }

        .category-item {
            border-bottom: 1px solid #eee;
        }

        .category-main {
            padding: 15px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .category-main:hover {
            background-color: #f0f0f0;
            text-decoration: none;
            color: #333;
        }

        .category-main.active {
            background-color: #e8e8e8;
            border-left: 3px solid #007bff;
            color: #007bff;
        }

        .subcategories-container {
            height: 100%;
            max-height: 75vh;
            overflow-y: auto;
            padding: 15px;
            background-color: #fff;
        }

        .subcategory-list-desktop {
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .subcategory-list-desktop.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .subcategory-header {
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        /* Replace grid with vertical list */
        .subcategory-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .subcategory-item-desktop {
            padding:4px 4px 4px 20px;
            background-color: #f9f9f9;
            border-radius: 6px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            color: #555;
            text-decoration: none;
            transition: all 0.2s ease;
            width: 100%;
        }

        .subcategory-item-desktop:hover {
            background-color: #f0f0f0;
            border-color: #ddd;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-decoration: none;
            color: #333;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .subcategory-list {
                /* Keep the vertical layout */
                display: flex;
                flex-direction: column;
            }
        }

        /* Tablet specific adjustments */
        @media (min-width: 768px) and (max-width: 991px) {
            .category-list {
                max-height: 60vh;
            }

            .subcategories-container {
                max-height: 60vh;
            }
        }

        /* Small mobile screens */
        @media (max-width: 375px) {
            .category-list-name, .subcategory-list-name {
                font-size: 13px;
            }

            .category-list-row, .subcategory-list-row {
                padding: 10px;
            }

            .subcategory-list-row {
                padding-left: 30px;
            }
        }

        @media (min-width: 992px) {
            .col-md-10.col-lg-9 {
                padding: 0 30px;
            }
        }
    </style>
@endpush

@push('script')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Mobile view category toggling
    const categoryParentRows = document.querySelectorAll('.category-parent-row');
    categoryParentRows.forEach(row => {
        row.addEventListener('click', function(e) {
            e.preventDefault();

            const categoryId = this.getAttribute('data-category-id');
            const subcategoryContainer = document.getElementById('subcategory-' + categoryId);

            if (subcategoryContainer) {
                // Close all other open subcategory lists
                document.querySelectorAll('.subcategory-container').forEach(container => {
                    if (container !== subcategoryContainer && container.style.display !== 'none') {
                        container.style.display = 'none';

                        // Find the parent row and reset its chevron
                        const parentRow = container.previousElementSibling;
                        if (parentRow) {
                            const chevron = parentRow.querySelector('.chevron-icon');
                            if (chevron) {
                                chevron.classList.remove('rotate');
                            }
                        }
                    }
                });

                // Toggle current subcategory visibility
                if (subcategoryContainer.style.display === 'none') {
                    subcategoryContainer.style.display = 'flex';

                    // Rotate chevron icon
                    const chevron = this.querySelector('.chevron-icon');
                    if (chevron) {
                        chevron.classList.add('rotate');
                    }
                } else {
                    subcategoryContainer.style.display = 'none';

                    // Reset chevron icon
                    const chevron = this.querySelector('.chevron-icon');
                    if (chevron) {
                        chevron.classList.remove('rotate');
                    }
                }
            }
        });
    });

    // Desktop view - show subcategories when clicking on main category
    const mainCategories = document.querySelectorAll('.category-main[data-category]');

    // REMOVE the showDefaultCategory function and its calls
    // This function was causing the automatic selection of the first category

    // Show subcategories when clicking on a category
    mainCategories.forEach(category => {
        category.addEventListener('click', function(e) {
            const categoryName = this.getAttribute('data-category');
            const subcategoryContainer = document.getElementById('subcategory-' + categoryName);

            if (subcategoryContainer) {
                e.preventDefault();

                // Deactivate all categories and subcategory containers
                document.querySelectorAll('.category-main').forEach(cat => {
                    cat.classList.remove('active');
                });

                document.querySelectorAll('.subcategory-list-desktop').forEach(subList => {
                    subList.classList.remove('active');
                });

                // Activate current category and its subcategory container
                this.classList.add('active');
                subcategoryContainer.classList.add('active');
            }
        });
    });

    // Handle resize events to manage transitions between mobile and desktop
    let lastWindowWidth = window.innerWidth;
    window.addEventListener('resize', function() {
        // Only trigger if we cross the responsive breakpoint
        if ((lastWindowWidth < 768 && window.innerWidth >= 768) ||
            (lastWindowWidth >= 768 && window.innerWidth < 768)) {

            // Reset mobile categories when switching to desktop
            if (window.innerWidth >= 768) {
                document.querySelectorAll('.subcategory-container').forEach(container => {
                    container.style.display = 'none';
                });

                document.querySelectorAll('.chevron-icon').forEach(icon => {
                    icon.classList.remove('rotate');
                });
            }
        }
        lastWindowWidth = window.innerWidth;
    });
});
    </script>
@endpush
