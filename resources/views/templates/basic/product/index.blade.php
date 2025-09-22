@extends($activeTemplate . 'layouts.master')

@section('panel')
    @include('partials.preloader')

    <div class="container">
        <!-- Enhanced Header Section -->
        <div class="mb-4 product align-items-center" style="padding-top:1.5rem!important">
            @php
                $user = auth()->user() 
            @endphp

            <div class="top-header">
                <div class="header-center">
                    <p class="user-name text-center">👋 Welcome {{ $user->username }}</p>
                </div>
            </div>

        </div>

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
                                            <button type="button" class="btn" style="background-color: #cc321bff; color: white;"
                                                data-bs-dismiss="modal">Later</button>
                                            <a href="{{ route('user.kyc.form') }}"
                                                class="btn" style="background-color: #024017ff; color: white;">Submit Documents</a>
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
                                        <button type="button" class="btn" style="background-color: #cc321bff; color: white;"
                                            data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('user.deposit.employee.package.active') }}"
                                            class="btn" style="background-color: #024017ff; color: white;">Activate Package</a>
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

        <!-- Enhanced Carousel -->
        <div id="imageCarousel" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner rounded-lg shadow">
                <div class="carousel-item ${activeClass}">
                    <img src="${image}" class="d-block w-100" alt="Banner Image">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        
            <!-- Navigation Cards -->
            <div class="middle-container mb-4">
                <div class="small-cards-row">
                    <a href="{{ route('user.advertisement.index') }}" class="small-card">
                        <div class="card-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="small-card-text">Ads</div>
                    </a>
                    <a href="{{ route('user.wallet') }}" class="small-card">
                        <div class="card-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="small-card-text">Wallet</div>
                    </a>
                    <a href="{{ route('user.deposit.employee.package.active') }}" class="small-card">
                        <div class="card-icon">
                             <i class="fas fa-box"></i> 
                        </div>
                        <div class="small-card-text">Packages</div>
                    </a>
                    <a href="{{ route('user.favorite') }}" class="small-card">
                        <div class="card-icon">
                           <i class="fas fa-heart"></i>
                        </div>
                        <div class="small-card-text">Favorites</div>
                    </a>
                </div>
            </div>

        <!-- Enhanced Search Bar -->
        <div class="mb-5 search-container">
            <div class="position-relative">
                <input type="text" id="searchInput" class="form-control form-control-lg rounded-pill shadow-sm" placeholder="Search for products...">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>

        <!-- Enhanced Category Section -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Shop by Category</h4>
                <a href="javascript:void(0)" id="seeAllCategoriesBtn" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            <div class="category-icons-container">
                <div class="category-scroll-container">
                    @php $visibleCategories = $categories->take(7); @endphp

                    @foreach ($visibleCategories as $category)
                        <div class="category-item visible-category">
                            <a href="{{ route('user.product.category', $category->name) }}" class="text-decoration-none">
                                <div class="category-icon-box">
                                    @if ($category->image_url)
                                        <img src="{{ asset('assets/admin/images/product_category/' . $category->image_url) }}" alt="{{ $category->name }}" class="category-icon">
                                    @else
                                        <div class="placeholder-icon">
                                            <i class="fa fa-box"></i>
                                        </div>
                                    @endif
                                    <p class="category-name">{{ $category->name }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    @foreach ($categories->skip(5) as $category)
                        <div class="category-item hidden-category" style="display: none;">
                            <a href="{{ route('user.product.category', $category->name) }}" class="text-decoration-none">
                                <div class="category-icon-box">
                                    @if ($category->image_url)
                                        <img src="{{ asset('assets/admin/images/product_category/' . $category->image_url) }}" alt="{{ $category->name }}" class="category-icon">
                                    @else
                                        <div class="placeholder-icon">
                                            <i class="fa fa-box"></i>
                                        </div>
                                    @endif
                                    <p class="category-name">{{ $category->name }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Enhanced Product Sections -->
        <div id="productContainer">
            @forelse($categories as $category)
                <div class="mb-5 category-section" data-category="{{ $category->name }}">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="section-title">{{ $category->name }}</h3>
                        <a href="{{ route('user.product.category', $category->name) }}" >
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    @forelse($category->productSubcategories as $subcategory)
                        <div class="mb-5 subcategory-section" data-subcategory="{{ $subcategory->name }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="subsection-title">{{ $subcategory->name }}</h4>
                                <a href="{{ route('user.product.subcategory', [$category->name, $subcategory->name]) }}" class="btn btn-sm btn-link">
                                    See All <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>

                            <div class="scroll-container">
                                @php
                                    $latestProducts = $subcategory->products->sortByDesc('created_at')->take(10);
                                @endphp

                                @forelse($latestProducts as $product)
                                    <div class="product-item" data-name="{{ strtolower($product->name) }}" data-category="{{ $category->name }}" data-subcategory="{{ $subcategory->name }}">
                                        <div class="product-card h-100">
                                            <div class="product-image-container">
                                                <a href="{{ route('user.product.details', $product->id) }}">
                                                    @if ($product->image_url)
                                                        <img src="{{ asset('assets/admin/images/product/' . $product->image_url) }}" class="product-image" alt="{{ $product->name }}">
                                                    @else
                                                        <img src="{{ asset('assets/templates/basic/product/offer.png') }}" class="product-image" alt="No Image Available">
                                                    @endif
                                                </a>
                                                <button class="favorite-btn" data-product-id="{{ $product->id }}">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                                @if ($product->discount > 0)
                                                    <div class="discount-badge">-LKR {{ $product->discount }}</div>
                                                @endif
                                            </div>

                                            <div class="product-details">
                                                <a href="{{ route('user.product.details', $product->id) }}">
                                                    <h5 class="product-title text-bold mb-0">{{ $product->name }}</h5>
                                                </a>
                                                <p class="product-weight mb-0">{{ intval($product->weight) }} {{ $product->unit }}</p>

                                                <div class="product-price">
                                                    @if ($product->discount > 0 && $product->original_price > $product->selling_price)
                                                        <p class="original-price text-danger">LKR {{ $product->original_price }}</p>
                                                        <p class="current-price">LKR {{ $product->selling_price }}</p>
                                                    @else
                                                        <p class="current-price">LKR {{ $product->original_price }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="cart-form">
                                                @csrf
                                                <button type="submit" class="add-to-cart">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info">No products found in this subcategory.</div>
                                    </div>
                                @endforelse

                                @if ($subcategory->products->count() > 10)
                                    <div class="view-all-container">
                                        <a href="{{ route('user.product.subcategory', [$category->name, $subcategory->name]) }}" class="view-all-btn">
                                            View All Products <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">No subcategories found in this category.</div>
                        </div>
                    @endforelse
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-danger">No categories or products found!</div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Enhanced Popup -->
    @if ($promotion_banner && $promotion_banner->status == 1)
        <div id="popupBox" class="popup">
            <div class="popup-content">
                <button class="close-btn" onclick="closePopup()">×</button>
                <div class="popup-header">
                    <h3>{{ $promotion_banner->title }}</h3>
                </div>
                <div class="popup-body">
                    @if ($promotion_banner->image)
                        <img src="{{ getImage(getFilePath('promotionalBanner') . '/' . $promotion_banner->image, getFileSize('promotionalBanner')) }}" class="popup-image">
                    @else
                        <img src="{{ getImage('assets/templates/basic/images/product/offer.png') }}" alt="offer" class="popup-image">
                    @endif
                    <p>{{ $promotion_banner->description }}</p>
                </div>
                <div class="popup-footer">
                    <button class="btn btn-primary" onclick="closePopup()">Shop Now</button>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('style')
    <style>
        :root {
            --primary-color: #17433c;
            --secondary-color: #0a815d;
            --accent-color: #b0e892;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-color);
            background-color: #f5f7fa;
        }

        /* Header Styles */
        .top-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
        }

        .header-center p {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Navigation Cards */
        .middle-container {
            margin-bottom: 2rem;
        }

        .small-cards-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .small-card {
            background: white;
            border-radius: var(--border-radius);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            text-decoration: none;
            color: var(--dark-color);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            height: 120px;
        }

        .small-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        }

        .small-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card-icon {
            font-size: 2rem;
            color: var(--secondary-color);
            margin-bottom: 0.75rem;
        }

        .small-card-text {
            font-weight: 600;
            font-size: 1rem;
        }

        /* Carousel Styles */
        .carousel {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .carousel-item img {
            height: 350px;
            object-fit: cover;
        }

        /* Search Bar */
        .search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .form-control-lg {
            padding: 1rem 3rem 1rem 1.5rem;
            font-size: 1rem;
            border: none;
            box-shadow: var(--box-shadow);
        }

        .search-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        /* Category Section */
       .category-icons-container {
            margin-bottom: 32px;
            position: relative;
        }

        .category-scroll-container {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 8px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .category-scroll-container::-webkit-scrollbar {
            display: none;
        }

        .category-icon-box {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9ff 100%);
            border: 1px solid rgba(102, 126, 234, 0.1);
            border-radius: 20px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 120px;
            width: 100px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .category-icon-box::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .category-icon-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.15);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .category-icon-box:hover::before {
            opacity: 1;
        }

        .category-icon {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .category-icon-box:hover .category-icon {
            transform: scale(1.1);
        }

        .category-name {
            font-size: 12px;
            color: #4a5568;
            font-weight: 600;
            margin-top: 12px;
            text-align: center;
            line-height: 1.2;
        }

        .category-section {
            background: linear-gradient(145deg, #e8e3e3ff 0%, #f1f1f8ff 100%);
            border: 1px solid rgba(102, 126, 234, 0.08);
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 40px;
        
            
        }


        /* Product Sections */
        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--secondary-color);
        }

        .subsection-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .subcategory-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
        }

        .scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 1.5rem;
            padding-bottom: 1rem;
            scrollbar-width: thin;
            scrollbar-color: var(--secondary-color) var(--light-color);
        }

        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background: var(--light-color);
            border-radius: 4px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 4px;
        }

        /* Product Cards */
        .product-card {
            background: linear-gradient(145deg, #fefafaff, #dfdff2ff);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            width: 220px;
            min-width: 220px;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }

        .product-image-container {
            height: 200px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6px 12px;
        }

        .product-image {
            padding: 2px;
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }



        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.8);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            z-index: 2;
        }

        .favorite-btn:hover {
            background: white;
            transform: scale(1.1);
        }

        .favorite-btn i {
            color: var(--danger-color);
            font-size: 1.1rem;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger-color);
            color: white;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            z-index: 2;
        }

        .product-details {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            
            color: var(--dark-color);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 1.5rem;
        }

        .product-weight {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }

        .product-price {
            margin-top: auto;
        }

        .original-price {
            font-size: 0.85rem;
            color: #6c757d;
            text-decoration: line-through;
            margin-bottom: 0.25rem;
        }

        .current-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--success-color);
            margin: 0;
        }

        .cart-form {
            padding: 0 1rem 1rem;
        }
        .add-to-cart {
            padding: 6px 24px;
            background: linear-gradient(135deg, #48bb78, #38a169);
            border: none;
            color: white;
            font-size: 14px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(72, 187, 120, 0.3);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .add-to-cart::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(72, 187, 120, 0.4);
            background: linear-gradient(135deg, #38a169, #2f855a);
        }

        .add-to-cart:hover::before {
            opacity: 1;
        }

        .add-to-cart:active {
            transform: translateY(0);
        }

        .view-all-container {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        .view-all-btn {
            display: inline-flex;
            align-items: center;
            background: var(--light-color);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .view-all-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Popup Styles */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .popup.show {
            display: flex;
        }

        .popup-content {
            background: white;
            border-radius: var(--border-radius);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: popupFadeIn 0.5s ease;
        }

        @keyframes popupFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .popup-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            position: relative;
        }

        .popup-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
        }

        .close-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        .popup-body {
            padding: 1.5rem;
            text-align: center;
        }

        .popup-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .popup-body p {
            margin: 0;
            font-size: 1rem;
            color: var(--dark-color);
        }

        .popup-footer {
            padding: 1rem 1.5rem 1.5rem;
            text-align: center;
        }

        .popup-footer .btn {
            padding: 0.75rem 2rem;
            border-radius: 30px;
            font-weight: 600;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .carousel-item img {
                height: 250px;
            }
        }

        @media (max-width: 768px) {
            .small-cards-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .category-icon-box {
                width: 100px;
                height: 100px;
            }
            
            .category-icon {
                width: 50px;
                height: 50px;
            }
            
            .product-card {
                width: 180px;
                min-width: 180px;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .header-center p {
                font-size: 1.5rem;
            }
            
            .small-card {
                height: 100px;
                padding: 1rem 0.5rem;
            }
            
            .card-icon {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            .small-card-text {
                font-size: 0.9rem;
            }
            
            .product-card {
                width: 160px;
                min-width: 160px;
            }
            
            .product-image-container {
                height: 150px;
            }
        }
    </style>
@endpush

@push('script')
    <!-- All existing scripts remain unchanged -->
    <script>
        $(document).on('click', '.favorite-icon, .favorite-btn', function() {
            var heartIcon = $(this).find('i');
            var productId = $(this).data('product-id');
            var userId = {{ auth()->id() }};
            var isFavorite = heartIcon.hasClass('fas');

            if (isFavorite) {
                heartIcon.removeClass('fas text-danger').addClass('far');
            } else {
                heartIcon.removeClass('far').addClass('fas text-danger');
            }

            $.ajax({
                url: '{{ route('toggle.favorite.product') }}',
                method: 'POST',
                data: {
                    user_id: userId,
                    product_id: productId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'added') {} else if (response.status === 'removed') {}
                },
                error: function(xhr) {
                    console.error('Error occurred while toggling the favorite status');
                }
            });
        });

        $(document).ready(function() {
            var userId = {{ auth()->id() }};
            $.ajax({
                url: '{{ route('get.favorite.product') }}',
                method: 'POST',
                data: {
                    user_id: userId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'error') {
                        console.log(response.message);
                        return;
                    }
                    var favoriteProductIds = response;

                    $('.favorite-icon, .favorite-btn').each(function() {
                        var productId = $(this).data('product-id');
                        var heartIcon = $(this).find('i');
                        if (favoriteProductIds.includes(productId)) {
                            heartIcon.removeClass('far').addClass('fas text-danger');
                        } else {
                            heartIcon.removeClass('fas text-danger').addClass('far');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching favorite products:", error);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // See All Categories functionality
            document.getElementById('seeAllCategoriesBtn').addEventListener('click', function() {
                const hiddenCategories = document.querySelectorAll('.hidden-category');
                const seeAllBtn = document.getElementById('seeAllCategoriesBtn');

                hiddenCategories.forEach(category => {
                    if (category.style.display === 'none') {
                        category.style.display = 'block';
                        seeAllBtn.textContent = 'Show Less';
                    } else {
                        category.style.display = 'none';
                        seeAllBtn.textContent = 'View All';
                    }
                });
            });

            const searchInput = document.getElementById('searchInput');
            const productContainer = document.getElementById('productContainer');
            const originalContent = productContainer.innerHTML;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();

                if (query.length === 0) {
                    productContainer.innerHTML = originalContent;
                    return;
                }

                const categorySections = document.querySelectorAll('.category-section');
                let hasResults = false;
                let filteredContent = '';

                categorySections.forEach(section => {
                    const categoryName = section.getAttribute('data-category');
                    const products = section.querySelectorAll('.product-item');
                    let matchingProducts = [];

                    products.forEach(product => {
                        const productTitle = product.getAttribute('data-name');
                        if (productTitle.includes(query)) {
                            matchingProducts.push(product.outerHTML);
                        }
                    });

                    if (matchingProducts.length > 0) {
                        hasResults = true;

                        filteredContent += `
                    <div class="mb-5 category-section" data-category="${categoryName}">
                        <a href="${section.querySelector('a').href}">
                            <h3 class="section-title">${categoryName}</h3>
                        </a>
                        <div class="scroll-container">
                            ${matchingProducts.join('')}
                        </div>
                    </div>`;
                    }
                });

                if (hasResults) {
                    productContainer.innerHTML = filteredContent;
                } else {
                    productContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info">No matching products found.</div>
                    </div>`;
                }
            });

            const popup = document.getElementById('popupBox');
            if (popup) {
                setTimeout(() => {
                    popup.style.display = 'flex';
                    setTimeout(() => {
                        popup.classList.add('show');
                    }, 10);
                }, 3000);
            }

            window.closePopup = function() {
                const popup = document.getElementById('popupBox');
                popup.classList.remove('show');

                setTimeout(() => {
                    popup.style.display = 'none';
                }, 500);
            };
        });

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
                                <img src="${image}" class="d-block w-100" alt="Banner Image">
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

        // Add to cart AJAX functionality
        document.addEventListener('DOMContentLoaded', function() {
            const cartForms = document.querySelectorAll('.cart-form');

            cartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const url = this.getAttribute('action');
                    const formData = new FormData(this);

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateCartCounter(data.cartCount);
                                showNotification(data.message, 'success');
                                animateCartIcon();
                            } else {
                                showNotification('Error adding product to cart', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Product added to cart', 'success');
                        });
                });
            });

            // Function to update the cart counter dynamically
            function updateCartCounter(count) {
                let cartCounter = document.getElementById('cart-counter');

                if (!cartCounter) {
                    // If the counter doesn't exist, create it dynamically
                    cartCounter = document.createElement('span');
                    cartCounter.id = 'cart-counter';
                    cartCounter.className = 'position-absolute';
                    cartCounter.style.cssText = `
                    font-size: 0.7rem;
                    background-color: red;
                    color: white;
                    font-weight: bold;
                    padding: 4px 7px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 20px;
                    height: 20px;
                    top: -5px;
                    right: -8px;
                `;
                    document.querySelector('.cart-icon-container').appendChild(cartCounter);
                }

                // Update the cart count
                cartCounter.textContent = count;

                // Show or hide based on count
                cartCounter.style.display = count > 0 ? 'flex' : 'none';
            }

            // Notification function
            function showNotification(message, type) {
                let notificationContainer = document.getElementById('notification-container');
                if (!notificationContainer) {
                    notificationContainer = document.createElement('div');
                    notificationContainer.id = 'notification-container';
                    notificationContainer.style.position = 'fixed';
                    notificationContainer.style.top = '20px';
                    notificationContainer.style.right = '20px';
                    notificationContainer.style.zIndex = '9999';
                    document.body.appendChild(notificationContainer);
                }

                const notification = document.createElement('div');
                notification.className =
                    `alert alert-${type === 'success' ? 'success' : 'danger'} notification-popup`;
                notification.innerHTML = message;
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(20px)';
                notification.style.transition = 'all 0.3s ease-in-out';
                notification.style.marginBottom = '10px';
                notification.style.minWidth = '250px';
                notification.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';

                notificationContainer.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateX(0)';
                }, 10);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        notificationContainer.removeChild(notification);
                        if (notificationContainer.children.length === 0) {
                            document.body.removeChild(notificationContainer);
                        }
                    }, 300);
                }, 1000);
            }

            // Cart icon animation
            function animateCartIcon() {
                const cartIcon = document.querySelector('.fa-shopping-cart');
                if (cartIcon) {
                    cartIcon.classList.add('cart-animation');
                    setTimeout(() => {
                        cartIcon.classList.remove('cart-animation');
                    }, 500);
                }
            }
        });

        @if ($promotion_banner && $promotion_banner->status == 1)
            <script src="{{ asset('assets/admin/js/confetti.min.js') }}"></script>

            <!-- Confetti  JS-->
            <script>
                const start = () => {
                    setTimeout(function() {
                        confetti.start()
                    }, 3000);
                };

                //  Stop
                const stop = () => {
                    setTimeout(function() {
                        confetti.stop()
                    }, 5000);
                };

                start();
                stop();
            </script>
        @endif
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
@endpush