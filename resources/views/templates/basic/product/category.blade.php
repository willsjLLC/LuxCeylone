@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div class="container">
        <h2 class="mt-10 mb-4">{{ $pageTitle }}</h2>
        
        <!-- Subcategory Filter Section -->
        @if($subCategories->count() > 0)
            <div class="subcategory-filter mb-4">
                <div class="filter-header mb-3">
                    <h5>Filter by Subcategory:</h5>
                </div>
                <div class="filter-options">
                    <a href="{{ route('user.product.category', $category->name) }}" 
                       class="filter-btn {{ !$selectedSubCategory || $selectedSubCategory === 'all' ? 'active' : '' }}">
                        All Products
                    </a>
                    @foreach($subCategories as $subCategory)
                        <a href="{{ route('user.product.category', [$category->name, 'subcategory' => $subCategory->name]) }}" 
                           class="filter-btn {{ $selectedSubCategory === $subCategory->name ? 'active' : '' }}">
                            {{ $subCategory->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Products Display Section -->
        <div class="row g-3">
            @forelse($products as $product)
                <div class="mb-4 col-6 col-md-3">
                    <div class="product-card h-100">
                        <a href="{{ route('user.product.details', $product->id) }}" class="product-link">
                            <div class="product-image-container">
                                @if ($product->image_url)
                                    <img src=" {{ asset('assets/admin/images/product/' . $product->image_url) }}"
                                        class="card-img-top product-image" alt="product image">
                                @else
                                    <img src="{{ asset('assets/admin/images/empty.png') }}"
                                        class="card-img-top product-image" alt="product image">
                                @endif
                            </div>
                            <div class="card-container">
                                <div class="card-details">
                                    <div class="mb-1 d-flex justify-content-between align-items-start">
                                        <h6 class="mt-1 card-title text-truncate" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </h6>
                                        <div class="card-fav">
                                            <i class="far text-dark fa-heart favorite-icon"
                                                data-product-id="{{ $product->id }}"></i>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted">{{ intval($product->weight) }} {{ $product->unit }}</p>
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
                                </div>
                            </div>
                        </a>
                        <div class="mt-auto">
                            <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="mt-2 cart-form">
                                @csrf
                                <button type="submit" class="add-to-cart btn btn-sm btn-primary w-100">Add to Cart <i
                                        class="fa-solid fa-cart-plus ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        @if($selectedSubCategory && $selectedSubCategory !== 'all')
                            No products found in the "{{ $selectedSubCategory }}" subcategory.
                        @else
                            No products found in this category.
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Back to products link -->
        <a href="{{ route('user.product.index') }}"><i class="mt-4 fa-solid fa-arrow-left"></i> Back to All Products</a>
    </div>
@endsection

@push('script')
    <style>
        /* Subcategory Filter Styles */
        .subcategory-filter {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .filter-header h5 {
            margin-bottom: 0;
            color: #495057;
            font-weight: 600;
        }

        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            text-decoration: none;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .filter-btn:hover {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
        }

        .filter-btn.active {
            background: #00b300;
            border-color: #00b300;
            color: white;
        }

        .filter-btn.active:hover {
            background: #218b44;
            border-color: #218b44;
            color: white;
        }

        /* Existing Product Card Styles */
        .product-link {
            display: block;
            text-decoration: none;
            color: inherit;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
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
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
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

        .card-container {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-details {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-fav {
            padding-top: 5px;
            display: flex;
            justify-content: flex-end;
        }

        .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-text {
            font-size: 0.8rem;
            margin-bottom: 5px;
        }

        .product-price p {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .favorite-icon {
            font-size: 1.1rem;
            cursor: pointer;
        }

        .add-to-cart {
            align-self: flex-end;
            padding: 5px 10px;
            background-color: #00b300;
            border: 1px solid #e0e0e0;
            color:white;
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

        /* Media queries */
        @media (max-width: 767px) {
            .subcategory-filter {
                padding: 15px;
            }
            
            .filter-options {
                gap: 8px;
            }
            
            .filter-btn {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
            
            .product-image-container {
                height: 130px;
            }

            .card-title {
                font-size: 0.85rem;
            }

            .card-text, .product-price p {
                font-size: 0.8rem;
            }

            .add-to-cart {
                font-size: 0.75rem;
                padding: 4px 8px;
            }
        }

        @media (max-width: 575px) {
            .subcategory-filter {
                padding: 12px;
            }
            
            .filter-btn {
                padding: 5px 10px;
                font-size: 0.85rem;
            }
            
            .product-image-container {
                height: 120px;
            }
        }
    </style>

    <script>
        $(document).on('click', '.favorite-icon', function(e) {
            e.preventDefault(); // Prevent the link from navigating
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
@endpush