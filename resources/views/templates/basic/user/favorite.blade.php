@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')

    <div class="mt-5">
        <div style="margin-top:5px;" id="favContainer" class="text-center ">
            <h3>Favourite Products</h3>
        </div>

        <div class="order-summary mt-4">
            <div class="order-header text-center">
                <!-- Removed tabs section -->
            </div>
        </div>

        <!-- Removed jobs section -->

        <div id="fav-products" class="order-content active">
            <div class="order-items mt-5">
                @if ($favorite_products->isNotEmpty())
                    @foreach ($favorite_products as $favorite_product)
                        <div class="card mb-3" style="max-width: 640px;" data-job-id="{{ $favorite_product->id }}">
                            <div class="card-body">
                                <div class="image-container text-center">
                                    @if ($favorite_product->product->image_url)
                                        <img src="{{ asset('assets/admin/images/product/' . $favorite_product->product->image_url) }}"
                                            class="product-image img-fluid p-2" alt="Product Image">
                                    @else
                                        <img src="{{ asset('assets/admin/images/empty.png') }}"
                                            class="card-img-top product-image p-2" alt="product image">
                                    @endif
                                </div>
                                <div class="text-container">
                                    <div class="text-main-sub-container">
                                        <div class="text-details">
                                            <a href="{{ route('user.product.details', $favorite_product->product->id) }}"
                                                class="product-link">
                                                <p class="product-title">{{ $favorite_product->product->name }}</p>
                                            </a>
                                            <div class="d-flex">
                                                <p class="product-weight text-muted">
                                                    {{ intval($favorite_product->product->weight) }}{{ $favorite_product->product->unit }}
                                                </p>
                                            </div>

                                            <div class="test-sub-container">
                                                <div class="d-flex flex-row">
                                                    @if ($favorite_product->product->discount == 0)
                                                    <p class="card-text text-muted">LKR {{ $favorite_product->product->original_price }}</p>
                                                    @else
                                                    <p class="card-text-delete text-muted"><del>LKR {{ $favorite_product->product->original_price }}</del></p>
                                                    <p class="card-text-price text-muted pl-1">LKR {{ $favorite_product->product->selling_price }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cart-header">
                                            <form action="{{ route('user.cart.add', $favorite_product->product->id) }}"
                                                method="POST" class="cart-form">
                                                @csrf
                                                <button type="submit"
                                                    class="add-to-cart position-absolute">Add to Cart <i
                                                        class="fa-solid fa-cart-plus"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('product.favorite.remove', $favorite_product->id) }}" method="POST"
                                    class="cart-form">
                                    @csrf
                                    <button type="submit" class="favorite"><i
                                            class="heart-icon fas text-danger fa-heart position-absolute top-0 end-0 p-1"></i></button>
                                </form>

                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="empty-fav">No favorite products found.</p>
                @endif

            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalCostElement = document.getElementById('total-cost');
            // Removed tab switching code since we only display products now
        });
    </script>
    <style>
        .card-text-price{
            padding-left:15px;
        }
        .empty-fav {
            text-align: center;
        }

        .cart-form {
            display: flex;
            margin: 0;
            justify-content: flex-end;
        }

        .add-to-cart {
            bottom:4px;
            right:4px;
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
        .card {
            max-width: 540px;
            margin: auto;
            border-radius: 10px;
        }

        .card-body {
            display: flex;
            flex-direction: row;
            width: 100%;
            padding: 0px;
        }

        .image-container {
            width: 23%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            border-radius: 10px 0 0 10px;
        }

        .text-container {
            width: 77%;
            flex: 1;
            padding-left: 10px;
        }

        .text-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .test-sub-container {
            display: flex;
            flex-direction: column;
        }

        .product-image,
        .job-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border-radius: 20px;
        }

        .heart-icon {
            color: #000;
        }

        .favorite {
            all: unset;
            cursor: pointer;
        }

        .order-content {
            display: block; /* Changed from none to block to always show */
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 8px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin: 10px 0;
            background-color: #ececec;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.2);
        }

        .total-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgb(208, 208, 208);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .checkout-button,
        .cancel-button {
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            box-shadow: 2px 3px 5px rgba(0, 0, 0, 0.2);
        }

        .checkout-button {
            background-color: #3a4750;
            color: white;
        }

        .checkout-button:hover {
            background-color: #5e5e5e;
        }

        .cancel-button {
            background-color: #d61e1e;
            color: white;
        }

        .cancel-button:hover {
            background-color: #d63e3e;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .product-price {
            font-size: 1rem;
        }

        .product-weight {
            font-size: 0.8rem;
            ;
        }

        @media (max-width: 768px) {
            .text-details {}
        }

        @media (max-width: 530px) {
            .card {
                height: 130px;
            }

            .card-body,
            .image-container,
            .text-container {
                height: 120px;
            }

            .image-container {
                width: 30%;
            }

            .text-container {
                width: 70%;
                padding-left: 5px;
            }

            .product-image,
            .job-image {
                margin-left: 5px;
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 430px) {
            .add-to-cart {
                padding: 0px 4px;
                font-size: 12px;
            }

            .cart-form {
                justify-content: flex-end;
            }

            .product-title {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 425px) {
            .total-summary {
                flex-direction: row;
                text-align: center;
            }

            .checkout-button {
                padding: 4px 9px;
                font-size: 14px;
            }

            .image-container {
                width: 40%;
            }

            .text-container {
                padding-top: 8px;
                padding-right: 2px;
                width: 60%;
            }

            .product-title {
                font-size: 1rem;
            }

            .product-weight {
                font-weight: 0.7rem;
            }
            .card-text-price , .card-text, .card-text-delete{
                font-size:13px;
             }
        }
    </style>
@endpush
