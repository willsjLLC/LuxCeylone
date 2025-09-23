@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div class="product-detail-container">
        <div class="container my-4">
            <div class="row">
                <div class="col-md-6 position-relative">
                    <!-- Main Product Image Gallery -->
                    <div class="mb-3 main-product-image">
                        @if ($product->images && $product->images->count() > 0)
                            @foreach ($product->images as $index => $image)
                                <img src="{{ asset('assets/admin/images/product/' . $image->image) }}"
                                    class="product-image {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}"
                                    alt="{{ $product->name }} - Image {{ $index + 1 }}">
                            @endforeach
                        @elseif ($product->image_url)
                            <img src="{{ asset('assets/admin/images/product/' . $product->image_url) }}"
                                class="product-image active" data-index="0" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('assets/admin/images/empty.png') }}" class="product-image active"
                                data-index="0" alt="product image">
                        @endif

                        <!-- Navigation arrows -->
                        <div class="image-navigation">
                            <button class="arrow-btn arrow-prev"><i class="fa fa-chevron-left"></i></button>
                            <button class="arrow-btn arrow-next"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Thumbnail gallery -->
                    <div class="product-thumbnails">
                        @if ($product->images && $product->images->count() > 0)
                            @foreach ($product->images as $index => $image)
                                <div class="product-thumbnail {{ $index == 0 ? 'active' : '' }}"
                                    data-index="{{ $index }}">
                                    <img src="{{ asset('assets/admin/images/product/' . $image->image) }}"
                                        alt="{{ $product->name }} - Thumbnail {{ $index + 1 }}">
                                </div>
                            @endforeach
                        @elseif ($product->image_url)
                            <div class="product-thumbnail active" data-index="0">
                                <img src="{{ asset('assets/admin/images/product/' . $product->image_url) }}"
                                    alt="{{ $product->name }}">
                            </div>
                        @else
                            <div class="product-thumbnail active" data-index="0">
                                <img src="{{ asset('assets/admin/images/empty.png') }}" alt="product image">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-2 product-category">{{ $product->category->name }}</div>

                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <h1 class="mb-0 product-title">{{ $product->name }}</h1>
                        <div class="card-fav">
                            <i class="far text-dark fa-heart favorite-icon fa-lg"
                                data-product-id="{{ $product->id }}"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div
                            class="availability {{ $product->quantity > 0 ? 'in-stock' : 'out-of-stock' }} d-flex align-items-center ">
                            Availability:
                            @if ($product->quantity > 0)
                                <span class="p-2 stock-count fw-bold" style="color: #28a745;"> In stock</span>
                            @else
                                <span class="p-2 out-of-stock fw-bold" style="color: #dc3545;"> Out of stock</span>
                            @endif
                        </div>
                    </div>

                    <div class="add-to-cart-section">
                        @if ($product->quantity >= 1)
                            <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="cart-form">
                                @csrf
                                <button type="submit" class="p-2 add-to-cart">Add to Cart <i
                                        class="fa-solid fa-cart-plus ms-1"></i></button>
                            </form>
                        @else
                            <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="cart-form">
                                @csrf
                                <button type="submit" class="p-2 disabled-add-to-cart" disabled>Add to Cart <i
                                        class="fa-solid fa-cart-plus"></i></button>
                            </form>
                        @endif
                    </div>

                    <div class="mt-4 mb-4 d-flex flex-column product-price">
                        @if ($product->discount == 0)
                            <h3 class="p-0 detail-text text-success">LKR {{ number_format($product->original_price, 2) }}
                            </h3>
                        @else
                            <div>
                                <h3 class="p-0 detail-text text-danger"><del>LKR
                                        {{ number_format($product->original_price, 2) }}</del>
                                </h3>
                            </div>
                            <div>
                                <h3 class="p-0 detail-text text-price text-muted text-success fw-bold">LKR
                                    {{ number_format($product->selling_price, 2) }}
                                </h3>
                            </div>
                        @endif
                        <small class="text-muted d-block">
                            Delivery: LKR {{ $productDeliveryChargers }}
                        </small>
                    </div>

                    <div class="mb-4 product-description">
                        <div>{!! $product->description !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewModalLabel">{{ $product->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="" id="modalImage" class="img-fluid" alt="{{ $product->name }}">
                    </div>
                    <div class="modal-footer">
                        <div class="modal-nav-buttons">
                            <button class="btn btn-outline-secondary modal-prev-btn">
                                <i class="fa fa-chevron-left me-2"></i>Previous
                            </button>
                            <span class="modal-counter">1 of 1</span>
                            <button class="btn btn-outline-secondary modal-next-btn">
                                Next<i class="fa fa-chevron-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br><br><br>
        <div id="productContainer">
            <h4 class="mb-3">Similar Products</h4>
            <div class="mb-5 category-section">
                <div class="scroll-container">
                    @forelse($smilerProducts as $product)
                        <div class="product-item" data-name="{{ strtolower($product->name) }}">
                            <div class="product-card h-100 d-flex flex-column">
                                <a href="{{ route('user.product.details', $product->id) }}" class="no-style">
                                    <div class="product-image-container">
                                        @if ($product->image_url)
                                            <img src=" {{ asset('assets/admin/images/product/' . $product->image_url) }}"
                                                class="card-img-top product-image object-fit-contain"
                                                alt="{{ $product->name }}">
                                        @else
                                            <img src="{{ asset('assets/templates/basic/product/offer.png') }}"
                                                class="card-img-top product-image object-fit-contain"
                                                alt="No Image Available">
                                        @endif
                                    </div>

                                    <div class="p-2 card-container d-flex flex-column justify-content-between flex-grow-1">
                                        <div class="mb-2 d-flex justify-content-between align-items-start">
                                            <h6 class="card-title text-truncate" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </h6>
                                            <i class="far fa-heart favorite-icon text-dark"
                                                data-product-id="{{ $product->id }}"></i>
                                        </div>

                                        <h6 class="mb-1 card-text small">{{ intval($product->weight) }}
                                            {{ $product->unit }}</h6>

                                        <div class="product-price">
                                            @if ($product->discount > 0 && $product->original_price > $product->selling_price)
                                                <p class="mb-0 text-danger"><del>LKR
                                                        {{ $product->original_price }}</del></p>
                                                <p class="mb-1 text-success fw-bold">LKR
                                                    {{ $product->selling_price }}
                                                </p>
                                            @else
                                                <p class="mb-1 text-success fw-bold">LKR
                                                    {{ $product->original_price }}
                                                </p>
                                            @endif
                                        </div>

                                        <small class="text-muted d-block">
                                            Delivery: LKR {{ $productDeliveryChargers }}
                                        </small>
                                    </div>
                                </a>

                                <form action="{{ route('user.cart.add', $product->id) }}" method="POST"
                                    class="mt-auto cart-form">
                                    @csrf
                                    <button type="submit" class="add-to-cart btn btn-sm btn-primary w-100">
                                        Add to Cart <i class="fa-solid fa-cart-plus ms-1"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>No similar products available</p>
                    @endforelse
                </div>
            </div>
        </div>


    </div>
@endsection

@push('style')
    <style>
        /* Main product image gallery */
        .main-product-image {
            position: relative;
            width: 100%;
            height: 450px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .main-product-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: opacity 0.3s ease;
            opacity: 0;
            display: none;
        }

        .main-product-image img.active {
            opacity: 1;
            display: block;
        }

        /* Image navigation arrows */
        .image-navigation {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            z-index: 10;
        }

        .arrow-btn {
            background-color: rgba(255, 255, 255, 0.7);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #333;
        }

        .arrow-btn:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: scale(1.1);
        }

        /* Thumbnail gallery */
        .product-thumbnails {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: thin;
        }

        .product-thumbnails::-webkit-scrollbar {
            height: 5px;
        }

        .product-thumbnails::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .product-thumbnails::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        .product-thumbnail {
            min-width: 80px;
            height: 80px;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .product-thumbnail:hover {
            transform: scale(1.05);
        }

        .product-thumbnail.active {
            border-color: #00b300;
        }

        .product-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Modal styling */
        #imagePreviewModal .modal-body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
            padding: 0;
            background-color: #f9f9f9;
        }


        @media(min-width:768px) {
            #modalImage {
                padding: 40px 10px;
                max-height: 60vh;
                object-fit: contain;
            }

            #imagePreviewModal .modal-dialog {
                max-height: 500px;
                height: 500px;
            }

            #imagePreviewModal .modal-content {
                height: 80%;
                display: flex;
                flex-direction: column;
            }
        }

        @media(min-width:567px) {
            .modal-nav-buttons {
                flex-direction: row;
            }
        }

        @media(max-width:566px) {
            .modal-nav-buttons {
                flex-direction: column;
                gap: 5px;
            }
        }

        @media(max-width:767px) {
            #modalImage {
                padding: 0 10px;
                max-height: 60vh;
                object-fit: contain;
            }

            #imagePreviewModal .modal-dialog {
                max-height: 400px;
                height: 400px;
            }

            #imagePreviewModal .modal-content {
                display: flex;
                flex-direction: column;
            }
        }

        .modal-nav-buttons {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-counter {
            font-size: 14px;
            color: #666;
        }

        /* Additional existing styles */
        .add-to-cart-section {
            justify-content: left;
        }

        .disabled-add-to-cart {
            align-self: flex-end;
            padding: 5px 10px;
            background-color: rgb(221, 220, 220);
            border: 1px solid rgb(255, 255, 255);
            color: #929292;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .add-to-cart {
            align-self: flex-end;
            padding: 5px 10px;
            background-color: #00b300;
            border: 1px solid #e0e0e0;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            flex-shrink: 0;
        }

        .add-to-cart:hover {
            color: white;
            background-color: #218b44;
            transform: scale(1.05);
            border: 1px solid rgb(255, 255, 255);
        }

        .add-to-cart:active {
            transform: scale(0.98);
        }

        .detail-text,
        .detail-text-price {
            font-size: 30px;
            padding-left: 8px;
        }

        .card-fav {
            cursor: pointer;
        }

        @media(max-width:768px) {
            .add-to-cart-section {
                justify-content: right;
            }

            .add-to-cart {
                padding: 2px 8px;
                font-size: 12px;
            }

            .product-title {
                font-size: 20px;
            }

            .current-price {
                font-size: 30px;
            }

            .main-product-image {
                height: 300px;
            }

            .product-thumbnail {
                min-width: 60px;
                height: 60px;
            }
        }

        @media(max-width:480px) {
            .main-product-image {
                height: 250px;
            }

            .product-thumbnail {
                min-width: 50px;
                height: 50px;
            }
        }

        /* Rest of your existing styles */
        /* ... */
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Variables to track current image
            let currentIndex = 0;
            const $mainImages = $('.main-product-image img');
            const $thumbnails = $('.product-thumbnail');
            const totalImages = $mainImages.length;

            // Initialize counters
            updateModalCounter();

            // Handle thumbnail click
            $('.product-thumbnail').on('click', function() {
                const index = $(this).data('index');
                showImage(index);
            });

            // Handle arrow navigation
            $('.arrow-prev').on('click', function() {
                navigateImage(-1);
            });

            $('.arrow-next').on('click', function() {
                navigateImage(1);
            });

            // Handle modal navigation
            $('.modal-prev-btn').on('click', function() {
                navigateImage(-1);
            });

            $('.modal-next-btn').on('click', function() {
                navigateImage(1);
            });

            // Open modal when clicking on main image
            $('.main-product-image').on('click', function() {
                const imgSrc = $('.main-product-image img.active').attr('src');
                $('#modalImage').attr('src', imgSrc);
                $('#imagePreviewModal').modal('show');
            });

            // Function to show image at given index
            function showImage(index) {
                if (index < 0 || index >= totalImages) return;

                // Update main image
                $mainImages.removeClass('active');
                $mainImages.eq(index).addClass('active');

                // Update thumbnails
                $thumbnails.removeClass('active');
                $thumbnails.eq(index).addClass('active');

                // Update modal image if modal is open
                if ($('#imagePreviewModal').is(':visible')) {
                    $('#modalImage').attr('src', $mainImages.eq(index).attr('src'));
                }

                // Update current index
                currentIndex = index;

                // Update counter in modal
                updateModalCounter();
            }

            // Function to navigate images
            function navigateImage(direction) {
                let newIndex = currentIndex + direction;

                // Handle wrapping
                if (newIndex < 0) newIndex = totalImages - 1;
                if (newIndex >= totalImages) newIndex = 0;

                showImage(newIndex);
            }

            // Update counter in modal
            function updateModalCounter() {
                $('.modal-counter').text(`${currentIndex + 1} of ${totalImages}`);
            }

            // Handle keyboard navigation in modal
            $(document).on('keydown', function(e) {
                if (!$('#imagePreviewModal').is(':visible')) return;

                if (e.keyCode === 37) { // Left arrow
                    navigateImage(-1);
                } else if (e.keyCode === 39) { // Right arrow
                    navigateImage(1);
                } else if (e.keyCode === 27) { // Escape key
                    $('#imagePreviewModal').modal('hide');
                }
            });

            // Handle favorite icon click
            $(document).on('click', '.favorite-icon', function() {
                var heartIcon = $(this);
                var productId = heartIcon.data('product-id');
                var userId = {{ auth()->id() }};
                var isFavorite = heartIcon.hasClass('fas');

                if (isFavorite) {
                    // Remove from favorites
                    heartIcon.removeClass('fas text-danger').addClass('far text-dark');
                } else {
                    // Add to favorites
                    heartIcon.removeClass('far text-dark').addClass('fas text-danger');
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
                        if (response.status === 'added') {} else if (response.status ===
                            'removed') {}
                    },
                    error: function(xhr) {
                        console.error('Error occurred while toggling the favorite status');
                    }
                });
            });

            // Load favorite products
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

                    $('.favorite-icon').each(function() {
                        var productId = $(this).data('product-id');
                        if (favoriteProductIds.includes(productId)) {
                            $(this).removeClass('far text-dark').addClass('fas text-danger');
                        } else {
                            $(this).removeClass('fas text-danger').addClass('far text-dark');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching favorite products:", error);
                }
            });
        });
    </script>


    <style>
        .add-to-cart-section {
            justify-content: left;
        }

        .disabled-add-to-cart {
            align-self: flex-end;
            padding: 5px 10px;
            background-color: rgb(221, 220, 220);
            border: 1px solid rgb(255, 255, 255);
            color: #929292;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .add-to-cart {
            align-self: flex-end;
            padding: 5px 10px;
            background-color: #00b300;
            border: 1px solid #e0e0e0;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            flex-shrink: 0;
        }

        .add-to-cart:hover {
            color: white;
            background-color: #218b44;
            transform: scale(1.05);
            border: 1px solid rgb(255, 255, 255);
        }

        .add-to-cart:active {
            transform: scale(0.98);
        }

        .detail-text,
        .detail-text-price {
            font-size: 30px;
            padding-left: 8px;
        }

        .card-fav {
            cursor: pointer;
        }

        @media(max-width:768px) {
            .add-to-cart-section {
                justify-content: right;
            }

            .add-to-cart {
                padding: 2px 8px;
                font-size: 12px;
            }

            .product-title {
                font-size: 20px;
            }

            ,
            .current-price {
                font-size: 30px;
            }
        }

        .product-image {
            border-radius: 10px;
        }

        .text-success {
            color: #28a745 !important;
        }

        .product-link {
            display: block;
            text-decoration: none;
            margin-right: 5px;
        }

        .card-text-price {
            padding-left: 15px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: left;
            align-items: left;
        }

        .cart-form {
            margin: 0;
        }

        .card-container {
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .card-details {
            width: 90%;
            display: flex;
            flex-direction: column;
        }

        .card-fav {
            padding-top: 10px;
            display: flex;
            justify-content: flex-end;
            width: 10%;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px white;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .product-title {
            font-size: 25px;
        }

        .product-image-container {
            height: 200px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 375px) {
            .row {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }
        }

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 220px;
            min-width: 220px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }

        .product-card:hover {
            cursor: pointer;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        }




        .card-container {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .favorite-icon {
            font-size: 1.2rem;
            cursor: pointer;
        }

        .product-price p {
            font-size: 0.9rem;
        }

        .add-to-cart {
            font-size: 0.85rem;
            padding: 5px 0;
        }

        .text-success {
            color: #28a745 !important;
        }

        .product-link {
            display: block;
            text-decoration: none;
        }

        .card-details>div:first-child {
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .card-title {
            margin-top: 0 !important;
            margin-bottom: 5px;
            max-width: calc(100% - 25px);
        }

        .card-fav {
            padding-top: 0;
            margin-left: 5px;
            flex-shrink: 0;
        }

        .product-name-hover {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-text-price {
            padding-left: 15px;
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .popup.show {
            display: flex;
            opacity: 1;
        }

        .popup-content {
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            position: relative;
            height: 300px;
            width: 400px;
        }

        .close-btn {
            position: absolute;
            top: 1px;
            right: 1px;
            color: #3a4750;
            border: none;
            font-size: 18px;
            border-radius: 50%;
            cursor: pointer;
            background: none;
        }

        .image-container {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .image-box {
            width: 340px;
            height: 180px;
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: fit;
            border-radius: 10px;
        }

        .popup-message {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .category-section {
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-header a h4:hover {
            color: #0056b3;
            opacity: 0.9;
            transition: opacity 0.5s ease-in-out;
        }

        .see-all-btn {
            font-size: 14px;
            color: #0056b3;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .see-all-btn:hover {
            color: #003d7a;
            transform: translateX(3px);
        }

        .see-all-btn i {
            margin-left: 5px;
            font-size: 12px;
        }

        .scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 10px;
            scrollbar-width: 2px;
            position: relative;
        }

        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }
    </style>
@endpush
