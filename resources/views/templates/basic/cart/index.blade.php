@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div style="margin-bottom: 70px; margin-top:20px;" id="cartContainer">
        <h2>{{ __($pageTitle) }}</h2>
        <div class="order-summary">
            <div class="order-header">
                <div class="order-tabs">
                    <span class="tab active" data-tab="current-order">CURRENT ORDERS</span>
                    <span class="tab" data-tab="order-history">ORDERS HISTORY</span>
                </div>
            </div>

            <div id="current-order" class="order-content active">
                <div class="order-items">
                    @foreach ($cart_items as $item)
                        <div class="order-item position-relative" data-price="{{ $item->selling_price }}"
                            data-discount="{{ $item->discount }}" data-quantity="{{ $item->quantity }}"
                            data-product-id="{{ $item->product_id }}">
                            <div class="item-details">
                                @if ($item->product->image_url)
                                    <img src="{{ asset('assets/admin/images/product/' . $item->product->image_url) }}"
                                        class="item-image" alt="product image">
                                @else
                                    <img src="{{ asset('assets/admin/images/empty.png') }}" class="item-image" alt="product image">
                                @endif
                                <div class="item-info">
                                    <a href="{{ route('user.product.details', $item->product->id) }}" class="product-link">
                                        <h4 class="item-name">{{ $item->product_name }}</h4>
                                    </a>
                                    <p class="card-text text-muted">{{ intval($item->product->weight) }}
                                        {{ $item->product->unit }}
                                    </p>
                                    <p class="item-price">
                                        @if ($item->discount > 0)
                                            <del class="text-danger">LKR {{ $item->original_price }}</del>
                                            <span class="text-success" style="font-weight: bold">LKR
                                                {{ $item->selling_price }}</span>
                                        @else
                                            <span class="text-success" style="font-weight: bold">LKR
                                                {{ number_format($item->original_price, 2) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="item-right">
                                <form action="{{ route('user.cart.item.remove', $item->product_id) }}" method="POST"
                                    class="cart-form" id="remove-cart-item-{{ $item->product_id }}">
                                    @csrf
                                    @method('POST')
                                </form>

                                <div class="top-0 item-close position-absolute end-0">
                                    <a href="javascript:void(0);"
                                        onclick="document.getElementById('remove-cart-item-{{ $item->product_id }}').submit();">
                                        x
                                    </a>
                                </div>

                                <div class="item-quantity">
                                    <div class="quantity-control">
                                        <button class="quantity-btn quantity-decrease">-</button>
                                        <span class="quantity">{{ $item->quantity }}</span>
                                        <button class="quantity-btn quantity-increase">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Total Price & Checkout Button -->
                <div class="total-summary">
                    @if ($cart_items->isNotEmpty())
                        <div class="total-checkout">
                            <a href="{{ route('user.cart.checkout') }}" class="checkout-button">
                                Checkout <i class="las la-play"></i>
                            </a>
                        </div>
                    @endif
                    <div class="total-price">
                        <p style="color: black">Total</p>
                        <div class="total-sub">
                            <p style="color: #28a745">LKR</p>
                            <span id="total-cost" style="color: #28a745">0</span>
                        </div>
                    </div>
                </div>
            </div>


            <div id="order-history" class="order-content">
                <div class="order-items">
                    

                    <!-- <form action="{{ route('user.order.deleteAll') }}" method="POST" class="cart-form"
                        onsubmit="return confirm('Are you sure you want to clear all pending order history?');">
                        @csrf
                        <div class="mb-3 d-flex justify-content-end">
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#clearHistoryModal">
                                <div class="cancel-button-wrapper">
                                    Clear All Pending Order History
                                </div>
                            </button>
                        </div>
                    </form> -->
                    @php
                        $pendingOrderCount = \App\Models\Order::where('customer_id', auth()->id())
                                                            ->where('status',  \App\Constants\Status::ORDER_PENDING)
                                                            ->count();
                    @endphp

                    @if($pendingOrderCount > 0)
                        <form action="{{ route('user.order.deleteAll') }}" method="POST" class="cart-form"
                            onsubmit="return confirm('Are you sure you want to clear all pending order history?');">
                            @csrf
                            <div class="mb-3 d-flex justify-content-end">
                                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#clearHistoryModal">
                                    <div class="cancel-button-wrapper">
                                        Clear All Pending Order History
                                    </div>
                                </button>
                            </div>
                        </form>
                    @endif


                    @foreach ($order_history as $item)
                        @if ($item->status != Status::ORDER_PENDING)
                            <div class="order-item">
                                <div class="order-info">
                                    <div class="info-row">
                                        <span class="info-label">Order ID:</span>
                                        <span class="info-value">{{ $item->code }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Status:</span>
                                        @if ($item->status == Status::ORDER_PENDING)
                                            <span class="info-value text-dark fw-bold">@lang('Pending')</span>
                                        @elseif($item->status == Status::ORDER_COMPLETED)
                                            <span class="info-value text-success fw-bold">@lang('Completed')</span>
                                        @elseif($item->status == Status::ORDER_PROCESSING)
                                            <span class="info-value text-warning fw-bold">@lang('Processing')</span>
                                        @else
                                            <span class="info-value text-danger fw-bold">@lang('Canceled')</span>
                                        @endif
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Date Of Created:</span>
                                        <span class="info-value">{{ showDateTime($item->created_at) }}</span>
                                    </div>
                                    <div class="items-list">
                                        <span class="info-label">Items:</span>
                                        <div class="items">
                                            <table class="order-items-table">
                                                <thead>
                                                    <tr>
                                                        <th class="product-name">Product</th>
                                                        <th class="">Price</th>
                                                        @if ($item->discount)
                                                            <th class="">Discount</th>
                                                        @endif
                                                        <th class="">Qty</th>
                                                        <th class="">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item->orderItems as $order_history_item)
                                                        <tr>
                                                            <td class="product-name">
                                                                {{ $order_history_item->product->name ?? 'Product not found' }}
                                                            </td>
                                                            <td class="">
                                                                {{ number_format($order_history_item->original_price, 2) }}
                                                            </td>
                                                            @if ($order_history_item->discount)
                                                                <td class="">
                                                                    {{ number_format($order_history_item->discount, 2) }}
                                                                </td>
                                                            @endif
                                                            <td class="">{{ $order_history_item->quantity }}
                                                            </td>
                                                            <td class="">
                                                                {{ number_format($order_history_item->net_total, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        @if ($item->discount)
                                                            <td colspan="4" class="text-center fw-bold">
                                                                Net Total
                                                            </td>
                                                        @else
                                                            <td colspan="3" class="text-center fw-bold">
                                                                Net Total
                                                            </td>
                                                        @endif
                                                        <td class="fw-bold">
                                                            {{ $item->net_total }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @csrf
                            <div class="order-item">
                                <div class="order-info">
                                    <div class="info-row">
                                        <span class="info-label">Order ID:</span>
                                        <span class="info-value">{{ $item->code }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Status:</span>
                                        @if ($item->status == Status::ORDER_PENDING)
                                            <span class="info-value text-dark fw-bold">@lang('Pending')</span>
                                        @elseif($item->status == Status::ORDER_COMPLETED)
                                            <span class="info-value text-success fw-bold">@lang('Completed')</span>
                                        @elseif($item->status == Status::ORDER_PROCESSING)
                                            <span class="info-value text-warning fw-bold">@lang('Processing')</span>
                                        @else
                                            <span class="info-value text-danger fw-bold">@lang('Canceled')</span>
                                        @endif
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Date Of Created:</span>
                                        <span class="info-value">{{ showDateTime($item->created_at) }}</span>
                                    </div>
                                    <div class="items-list">
                                        <span class="info-label">Items:</span>
                                        <div class="items">
                                            <table class="order-items-table">
                                                <thead>
                                                    <tr>
                                                        <th class="product-name">Product</th>
                                                        <th class="">Price</th>
                                                        @if ($item->discount)
                                                            <th class="">Discount</th>
                                                        @endif
                                                        <th class="">Qty</th>
                                                        <th class="">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item->orderItems as $order_history_item)
                                                        <tr>
                                                            <td class="product-name">
                                                                {{ $order_history_item->product->name ?? 'Product not found' }}
                                                            </td>
                                                            <td class="">
                                                                {{ number_format($order_history_item->original_price, 2) }}
                                                            </td>
                                                            @if ($order_history_item->discount)
                                                                <td class="">
                                                                    {{ number_format($order_history_item->discount, 2) }}
                                                                </td>
                                                            @endif
                                                            <td class="">{{ $order_history_item->quantity }}
                                                            </td>
                                                            <td class="">
                                                                {{ number_format($order_history_item->net_total, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        @if ($item->discount)
                                                            <td colspan="4" class="text-center fw-bold">
                                                                Net Total
                                                            </td>
                                                        @else
                                                            <td colspan="3" class="text-center fw-bold">
                                                                Net Total
                                                            </td>
                                                        @endif
                                                        <td class="fw-bold">
                                                            {{ $item->net_total }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="gap-2 mt-2 info-row d-flex justify-content-end align-items-center">
                                        <form action="{{ route('user.order.exist.cancel', $item->id) }}" method="POST"
                                            class="cart-form">
                                            @csrf
                                            <button type="submit" class="total-checkout ">
                                                <div class="cancel-button">
                                                    Delete
                                                </div>
                                            </button>
                                        </form>

                                        <form action="{{ route('user.order.exist.checkout', $item->id) }}" method="POST"
                                            class="cart-form">
                                            @csrf
                                            <button type="submit" class="total-checkout border-button">
                                                <div class="checkout-button">
                                                    Checkout <i class="las la-play"></i>
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clearHistoryModal" tabindex="-1" aria-labelledby="clearHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearHistoryModalLabel">Clear Order History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to clear all pending order history? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="clearHistoryForm" action="{{ route('user.order.deleteAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Yes, Clear All</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Total Price & Checkout Button -->
    <!-- <div class="total-summary">
                                        @if ($cart_items->isNotEmpty())
                                            <div class="total-checkout">
                                                <a href="{{ route('user.cart.checkout') }}" class="checkout-button">
                                                    Checkout <i class="las la-play"></i>
                                                </a>
                                            </div>
                                        @endif
                                        <div class="total-price">
                                            <p style="color: black">Total</p>
                                            <div class="total-sub" >
                                                <p style="color: #28a745">LKR</p>
                                                <span id="total-cost" style="color: #28a745">0</span>
                                            </div>
                                        </div>
                                    </div> -->
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalCostElement = document.getElementById('total-cost');

            const tabs = document.querySelectorAll(".tab");
            const contents = document.querySelectorAll(".order-content");

            tabs.forEach(tab => {
                tab.addEventListener("click", () => {
                    tabs.forEach(t => t.classList.remove("active"));
                    tab.classList.add("active");

                    const targetId = tab.getAttribute("data-tab");

                    contents.forEach(content => {
                        content.classList.remove("active");
                    });

                    document.getElementById(targetId).classList.add("active");
                });
            });

            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('#current-order .order-item').forEach(item => {
                    const price = parseFloat(item.dataset.price) || 0;
                    const quantity = parseInt(item.querySelector('.quantity').textContent) || 1;
                    total += price * quantity;
                });

                totalCostElement.textContent = total.toFixed(2);
            }

            function updateCart(productId, quantity) {
                fetch(`/user/cart/update`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert("Failed to update cart.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }

            document.querySelectorAll('.quantity-decrease').forEach(button => {
                button.addEventListener('click', function () {
                    const quantitySpan = this.nextElementSibling;
                    let quantity = parseInt(quantitySpan.textContent);
                    if (quantity > 1) {
                        quantitySpan.textContent = quantity - 1;
                        calculateTotal();
                        const productId = this.closest('.order-item').getAttribute(
                            'data-product-id');
                        updateCart(productId, quantity - 1);
                    }
                });
            });

            document.querySelectorAll('.quantity-increase').forEach(button => {
                button.addEventListener('click', function () {
                    const quantitySpan = this.previousElementSibling;
                    let quantity = parseInt(quantitySpan.textContent);
                    quantitySpan.textContent = quantity + 1;
                    calculateTotal();
                    const productId = this.closest('.order-item').getAttribute('data-product-id');
                    updateCart(productId, quantity + 1);
                });
            });

            calculateTotal();
        });
    </script>

    <style>
        .item-name {
            transition: color 0.5s ease-in-out;
        }

        .item-name:hover {
            color: blue;
        }

        .order-summary {
            width: 100%;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            padding: 0 15px 3px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-tabs span {
            margin-right: 15px;
            cursor: pointer;
            padding-bottom: 8px;
            position: relative;
        }

        .order-tabs span.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -3px;
            width: 100%;
            height: 3px;
            background-color: #28a745;
        }

        .edit-link {
            text-decoration: none;
        }

        .order-content {
            display: none;
        }

        .order-content.active {
            display: block;
        }

        .order-item {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 5px 8px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin: 10px 0;
            background-color: #ffffff;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .item-right {
            display: flex;
            margin-bottom: 15px;
            flex-direction: column;
            justify-content: space-between;
            align-items: right;
            width: 20%;
        }

        .item-details {
            display: flex;
            align-items: center;
            width: 80%;
        }

        .item-image {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            border-radius: 5px;
        }

        .item-quantity {
            display: flex;
            justify-content: flex-end;
            align-items: center;

        }

        .item-close {
            display: flex;
            padding-bottom: 5px;
            flex-direction: column;
            justify-content: flex-start;
            text-align: right;

        }

        .item-close a {
            font-size: 16px;
            padding-right: 4px;
            text-decoration: none;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 90px;
            height: 20px;
            background-color: #f0f2f5;
            border-radius: 5px;
            overflow: hidden;
        }

        .quantity-btn {
            width: 30px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #8896a9;
            color: white;
            border: none;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .quantity-btn:hover {
            background-color: #7a8699;
        }

        .quantity {
            flex: 1;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }





        .order-info {
            width: 100%;
            padding: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .info-label {
            font-weight: bold;
            text-align: left;
            flex: 1;
            color: #000;
        }

        .info-value {
            text-align: right;
            flex: 1;
            color: #2e2d2d;
        }

        .items-list {
            display: flex;
            flex-direction: row;
            justify-content: center;
            justify-content: space-between;
            padding: 5px 0;
        }

        .items {
            display: flex;
            flex-direction: column;
            text-align: right;
            color: #2e2d2d;
        }

        .info-value ol {
            list-style: decimal;
            padding-left: 20px;
            margin: 0;
            text-align: right;
        }

        .info-value ol li {
            text-align: right;
        }

        .total-summary {
            width: 100%;
            background: #fff;
            padding: 15px;
            display: flex;
            background-color: rgb(218, 213, 213);
            justify-content: space-between;
            /* Changed to space-between for left/right alignment */
            align-items: center;
            flex-direction: row;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;

        }

        .total-price {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            order: -1;
            /* Moves total to left */
        }

        .total-price p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #7f7f7f;
        }

        .total-sub {
            display: flex;
            flex-direction: row;
            align-items: center;
            font-size: 28px;
        }

        .total-sub p {
            margin: 0;
            color: #333;
            margin-right: 5px;
            font-size: 28px;
        }

        .total-sub span {
            font-weight: bold;

        }

        .total-price {
            font-weight: bold;

            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .total-checkout {
            background: none;
            display: flex;
            flex-direction: row;
            border: none;
            order: 1;
            /* Moves checkout to right */
        }

        .checkout-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 2px 3px 5px rgba(0, 0, 0, 0.2);
        }

        .checkout-button:hover {
            background-color: #218838;
            color: #fff;
        }

        /* .cancel-button-wrapper{
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 10px;
        } */
        .cancel-button-wrapper
        {
            background-color: #d61e1e;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 2px 3px 5px rgba(0, 0, 0, 0.2);
            margin-top: 10px; 
        }
              
        

       
        .cancel-button {
            background-color: #d61e1e;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            box-shadow: 2px 3px 5px rgba(0, 0, 0, 0.2);
        }

        .cancel-button:hover {
            background-color: #d63e3e;
            color: #fff;
        }

        .las.la-play {
            color: white;
            font-size: 14px;
        }

        .order-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .order-items-table th,
        .order-items-table td {
            padding: 8px 12px;
            text-align: right;
        }

        .order-items-table .product-name {
            text-align: left;
        }

        .order-items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .order-items-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .item-price del {
            color: #dc3545;
        }

        .item-price {
            color: #28a745;
        }

        @media(max-width: 575px) {

            .order-items-table th,
            .order-items-table td {
                font-size: 10px;
                padding: 4px 7px;
            }

            .order-header {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .total-summary {
                flex-direction: row;
                text-align: center;
            }

            .order-info {
                font-size: 12px;
            }

            .order-tabs span {
                font-size: 13px;
            }

            .item-name {
                font-size: 14px;
            }

            .checkout-button,
            .cancel-button {
                padding: 4px 9px;
                font-size: 14px;
            }

            .las.la-play {
                color: white;
                font-size: 11px;
            }

            .quantity {
                padding: 8px;
            }

            .cancel-button-wrapper {
                position: relative;
                top: 5px;
                left: 0px;

            }





        }
    </style>

    <script>
        // Modal handling for clear all pending order history
const clearHistoryModal = document.getElementById('clearHistoryModal');
if (clearHistoryModal) {
    clearHistoryModal.addEventListener('show.bs.modal', function (event) {
        
    });
}
    </script>


<style>
    /* Additional styling for right-aligned button */
    .d-flex.justify-content-end {
        width: 100%;
        padding-right: 15px;
        
    }
    
    /* Make sure the button doesn't have unwanted margins/padding */
    .d-flex.justify-content-end .btn {
        padding: 0;
        background: none;
        border: none;
    }
</style>
@endpush