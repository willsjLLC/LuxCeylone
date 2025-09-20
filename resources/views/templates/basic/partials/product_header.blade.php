@php
    $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', Status::NO)->get();
@endphp
<header class="fixed-top">
    <div class="container-fluid ">
        <div class="px-3 py-2 row">

            <div class="flex-row justify-between d-flex align-items-center w-100" style="height:51px">
                {{-- Header left --}}
                <div class="flex-row header-left d-flex align-items-center">
                    <!-- Toggle Button for Sidebar -->
                    <i class="las la-bars fs-3" id="sidebarToggle"
                        style="cursor: pointer; color:white; padding-right:5px;"></i>

                    <!-- Logo -->
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ siteLogo() }}" alt="@lang('logo')" class="img-fluid"
                                style="height: 55px;">
                        </a>
                    </div>
                </div>
                {{-- Header right --}}
                @if (Route::is('user.product.index') ||
                        Route::is('user.cart.checkout') ||
                        Route::is('user.product.details') ||
                        Route::is('user.product.category') ||
                        Route::is('user.cart.index'))
                    <div class="px-5 ml-auto header-right">
                        <a class="transition rounded-lg switch position-relative" href="{{ route('user.cart.index') }}">
                            <i class="text-white fa-solid fa-cart-plus fs-5"></i>
                            @php
                                $count = auth()->check()
                                    ? App\Models\CartItem::where('customer_id', auth()->id())->sum('quantity')
                                    : 0;
                            @endphp

                            <span id="cart-counter" class="position-absolute"
                                style="
                            font-size: 0.7rem;
                            background-color: red;
                            color: white;
                            font-weight: bold;
                            padding: 4px 7px;
                            border-radius: 50%;
                            display: {{ $count > 0 ? 'flex' : 'none' }};
                            align-items: center;
                            justify-content: center;
                            min-width: 20px;
                            height: 20px;
                            top: -8px;
                            right: -8px;
                            ">
                                {{ $count }}
                            </span>


                        </a>

                    </div>
                @endif
                <!-- User Menu -->
                <div class="dropdown">
                    <div class="header-lef">

                        <button class="icon-bg position-relative" type="button" id="navbarDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="padding: 0; border: none; background: none;">
                            <div class="position-relative" style="width: 48px; height: 48px;">
                                @auth
                                    @if (Auth::user()->image)
                                        <img src="{{ asset('assets/images/user/profile/' . Auth::user()->image) }}"
                                            alt="User Image" class="img-fluid rounded-circle"
                                            style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/image/user.svg') }}" alt="User Image"
                                            class="img-fluid rounded-circle"
                                            style="width: 43px; height: 43px; object-fit: cover;">
                                    @endif

                                    <!-- Package active indicator (bottom left) -->
                                    @if (isUserEmployeePackageActivated(auth()->user()))
                                        <span
                                            class="bottom-0 p-1 border position-absolute start-0 translate-middle border-light rounded-circle"
                                            style="width: 12px; height: 12px; background-color:#00ff00"
                                            title="Package Active"></span>
                                    @endif
                                @endauth
                            </div>
                        </button>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('user.profile.setting') }}">
                                    <i class="la la-user"></i> @lang('Profile Setting')
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('user.advertisement.myAds') }}">
                                    <i class="la la-ad"></i> @lang('My Ads')
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('user.change.password') }}">
                                    <i class="la la-lock"></i> @lang('Change Password')
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('user.twofactor') }}">
                                    <i class="la la-key"></i> @lang('2FA Security')
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('user.secondOwner') }}">
                                    <i class="la la-user-tie"></i> @lang('Second Owner')
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('user.logout') }}">
                                    <i class="la la-sign-out"></i> @lang('Sign Out')
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
</header>


<!-- Sidebar -->
<div id="sidebar" class="sidebar" style="background-color: rgb(0, 88, 77)">
    <button id="sidebarClose" class="btn-outline-light btn-sm close-btn">
        <i class="las la-times" style="color:#fff"></i>
    </button>
    <ul class="mt-3 list-unstyled">
        {{-- <li><a href="{{ route('user.home.view') }}" class="text-light"><i class="las la-home"></i> Home</a></li> --}}
        <li>
            <a href="{{ route('user.product.index') }}" class="text-light">
                <i class="la la-archive"></i> Products
            </a>
        </li>
        <li><a href="{{ route('user.advertisement.index') }}" class="text-light"><i class="la la-sign-out"></i>
                Advertisement</a></li>
        <li><a href="{{ route('user.wallet') }}" class="text-light"><i class="las la-wallet"></i> Wallet</a></li>
        <li><a href="{{ route('user.referral.index') }}" class="text-light"><i class="la la-users"></i> Affiliates</a>
        </li>

        <li><a href="{{ route('user.deposit.employee.package.active') }}" class="text-light"><i
                    class="la la-users"></i> Packages</a></li>
        <li><a href="{{ route('user.favorite') }}" class="text-light"><i class="la la-heart"></i> Favourites</a></li>
        <li><a href="{{ route('ticket.index') }}" class="text-light"><i class="las la-question-circle"></i> Support</a>
        </li>
        <li><a href="{{ route('user.logout') }}" class="text-light"><i class="la la-sign-out"></i> Sign Out</a></li>
    </ul>
</div>

<style>
    .close-btn {
        font-family: "Montserrat", sans-serif;
        padding-top: 10px;
        padding-right: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        border: none;
        outline: none;
        border: 1px solid transparent;
        color: #fff;
        background-color: transparent;
        position: absolute;
        top: 0px;
        right: 0px;
        z-index: 1;
        display: flex;
        justify-content: center;
        align-items: right;
    }

    .header-right {
        margin-left: auto;
    }

    .container-fluid {
        background-color: #009933
    }

    .sidebar {
        position: fixed;
        margin-top: 67px;
        left: -250px;
        top: 0;
        height: 100%;
        transition: left 0.3s ease;
        z-index: 1020;
        width: var(--sidebar-width);
        /* background-color: var(--sidebar-bg-color); */
        overflow-y: auto;
    }

    .sidebar.active {
        left: 0;
    }

    :root {
        --header-height: 60px;
        --header-bg-color: #009933;
        /* --sidebar-bg-color: #009933; */
        --sidebar-width: 200px;
    }

    body {
        padding-top: var(--header-height);
        padding-left: 0;
    }

    /* Header styles */
    .container-fluid {
        background-color: var(--header-bg-color);
    }

    .fixed-top {
        height: var(--header-height);
    }

    .main-content {
        transition: margin-left 0.3s ease;
        margin-left: 0;
    }

    .sidebar a {
        display: block;
        padding: 6px 10px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>

<script>
    // Only attach event listeners if elements exist
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebar = document.getElementById('sidebar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.add('active');
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }

        // Add tooltip initialization for package status indicators
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
