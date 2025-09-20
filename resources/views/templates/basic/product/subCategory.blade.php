@extends($activeTemplate . 'layouts.master')

@section('panel')
    @include('partials.preloader')
    <div class="container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mt-3 mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('user.product.index') }}">All Products</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('user.product.category', $category->name) }}">{{ $category->name }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $subCategory->name }}</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="page-header mb-4">
            <h2 class="mb-2">{{ $pageTitle }}</h2>
            <p class="text-muted mb-0">
                <i class="fa-solid fa-tag me-1"></i>
                {{ $products->count() }} product{{ $products->count() !== 1 ? 's' : '' }} found in {{ $subCategory->name }}
            </p>
        </div>

        <!-- Products Grid -->
        <div class="row g-3">
            @forelse($products as $product)
                <div class="mb-4 col-6 col-md-3">
                    <div class="product-card h-100">
                        <a href="{{ route('user.product.details', $product->id) }}" class="product-link">
                            <div class="product-image-container">
                                @if ($product->image_url)
                                    <img src="{{ asset('assets/admin/images/product/' . $product->image_url) }}"
                                        class="card-img-top product-image" alt="{{ $product->name }}">
                                @else
                                    <img src="{{ asset('assets/admin/images/empty.png') }}"
                                        class="card-img-top product-image" alt="No image available">
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
                                    <p class="card-text text-muted">
                                        <i class="fa-solid fa-weight-hanging me-1"></i>
                                        {{ intval($product->weight) }} {{ $product->unit }}
                                    </p>
                                    <div class="product-price">
                                        @if ($product->discount > 0 && $product->original_price > $product->selling_price)
                                            <p class="mb-0 text-danger">
                                                <del>LKR {{ number_format($product->original_price, 2) }}</del>
                                                <span class="discount-badge ms-2">-{{ $product->discount }}%</span>
                                            </p>
                                            <p class="mb-1 text-success fw-bold">
                                                LKR {{ number_format($product->selling_price, 2) }}
                                            </p>
                                        @else
                                            <p class="mb-1 text-success fw-bold">
                                                LKR {{ number_format($product->original_price, 2) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div class="mt-auto">
                            <form action="{{ route('user.cart.add', $product->id) }}" method="POST" class="mt-2 cart-form">
                                @csrf
                                <button type="submit" class="add-to-cart btn btn-sm btn-primary w-100">
                                    Add to Cart <i class="fa-solid fa-cart-plus ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fa-solid fa-info-circle mb-2"></i>
                        <h5 class="mb-2">No products found</h5>
                        <p class="mb-3">There are currently no products available in the "{{ $subCategory->name }}" subcategory.</p>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="{{ route('user.product.category', $category->name) }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Browse {{ $category->name }}
                            </a>
                            <a href="{{ route('user.product.index') }}" class="btn btn-primary">
                                <i class="fa-solid fa-home me-1"></i>
                                All Products
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Navigation Links -->
        <div class="mt-5 pt-3 border-top">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('user.product.category', $category->name) }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Back to {{ $category->name }}
                    </a>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <a href="{{ route('user.product.index') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-grid-2 me-1"></i>
                        View All Products
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <style>
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #6c757d;
        }

        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .page-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
        }

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
            border-radius: 8px;
            background: #fff;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.08);
            width: 220px;
            min-width: 220px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .product-image-container {
            height: 200px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 6px;
            background-color: #f8f9fa;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .card-container {
            margin-top: 12px;
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
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 1.3;
        }

        .card-text {
            font-size: 0.8rem;
            margin-bottom: 8px;
            color: #6c757d;
        }

        .product-price p {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .discount-badge {
            background-color: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 12px;
            font-weight: bold;
        }

        .favorite-icon {
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .favorite-icon:hover {
            transform: scale(1.1);
        }

        .favorite-icon.fas {
            color: #dc3545 !important;
        }

        .add-to-cart {
            align-self: flex-end;
            padding: 8px 12px;
            background-color: #28a745;
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .add-to-cart:hover {
            color: white;
            background-color: #218838;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .add-to-cart:active {
            transform: translateY(0);
        }

        /* Alert styling */
        .alert-info {
            border: none;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 12px;
            padding: 2rem;
        }

        /* Media queries */
        @media (max-width: 767px) {
            .product-image-container {
                height: 150px;
            }

            .card-title {
                font-size: 0.9rem;
            }

            .card-text, .product-price p {
                font-size: 0.8rem;
            }

            .add-to-cart {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }

        @media (max-width: 575px) {
            .product-image-container {
                height: 130px;
            }

            .product-card {
                width: 100%;
                min-width: auto;
            }
        }
    </style>

    <script>
        $(document).on('click', '.favorite-icon', function(e) {
            e.preventDefault();
            var heartIcon = $(this);
            var productId = heartIcon.data('product-id');
            var userId = {{ auth()->id() ?? 'null' }};
            
            if (!userId) {
                alert('Please login to add favorites');
                return;
            }
            
            var isFavorite = heartIcon.hasClass('fas');

            if (isFavorite) {
                heartIcon.removeClass('fas text-danger').addClass('far text-dark');
            } else {
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
                    if (response.status === 'added') {
                        // Optional: Show success message
                    } else if (response.status === 'removed') {
                        // Optional: Show removal message
                    }
                },
                error: function(xhr) {
                    console.error('Error occurred while toggling the favorite status');
                    // Revert the icon state on error
                    if (isFavorite) {
                        heartIcon.removeClass('far text-dark').addClass('fas text-danger');
                    } else {
                        heartIcon.removeClass('fas text-danger').addClass('far text-dark');
                    }
                }
            });
        });

        $(document).ready(function() {
            var userId = {{ auth()->id() ?? 'null' }};
            
            if (!userId) return;
            
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

        // Add to cart form handling
        $('.cart-form').on('submit', function(e) {
            var button = $(this).find('.add-to-cart');
            button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Adding...');
            
            setTimeout(function() {
                button.prop('disabled', false).html('Add to Cart <i class="fa-solid fa-cart-plus ms-1"></i>');
            }, 2000);
        });
    </script>
@endpush