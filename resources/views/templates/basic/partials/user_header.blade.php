<header class="fixed-top">
    <div class="container-fluid">
        <div class="px-3 py-2 row">
            <!-- First Row (Menu, Logo, Switch Role, Profile) -->
            <div class="col-12 d-flex justify-content-between align-items-center" style="height:51px">
                <div class="flex-row d-flex flex-start align-items-center">
                    @if (!Route::is('user.data'))
                        <i class="las la-bars fs-3 " id="sidebarToggle"
                            style="cursor: pointer; color:white; padding-right:5px;"></i>
                    @endif
                    <!-- Logo -->
                    <div class="logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ siteLogo() }}" alt="@lang('logo')" class="img-fluid"
                                style="height: 55px;">
                        </a>
                    </div>
                </div>

                <div class="flex-row d-flex align-items-center">
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

                                        <img src="{{ asset('assets/images/user/icon/crown.png') }}" alt="Crown Image"
                                             class="golden-crown-icon">
                                        <!-- Package status indicator with dynamic color -->
                                        @php
                                            $user = auth()->user();
                                            $isPackageActive = isUserEmployeePackageActivated($user);
                                            $activationCount = 0;
                                            $dotColor = '#dc3545'; // Red (default - no package)
                                            $statusText = 'No Package Active';

                                            if ($isPackageActive) {
                                                // Get activation count from employee_package_activation_histories table
                                                $activationCount = \App\Models\EmployeePackageActivationHistory::where(
                                                    'user_id',
                                                    $user->id,
                                                )->count();

                                                if ($activationCount == 1) {
                                                    $dotColor = '#28a745'; // Green (first time)
                                                    $statusText = 'First Package Activation';
                                                } else {
                                                    $dotColor = '#ffc107'; // Yellow (multiple activations)
                                                    $statusText = 'Multiple Package Activations';
                                                }
                                            }
                                        @endphp

                                        <span
                                            class="bottom-0 p-1 border position-absolute start-0 translate-middle border-light rounded-circle"
                                            style="width: 12px; height: 12px; background-color: {{ $dotColor }}"
                                            title="{{ $statusText }}"></span>
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
        </div>
    </div>
</header>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <button id="sidebarClose" class="btn-outline-light btn-sm close-btn">
        <i class="las la-times"></i>
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


        <li><a href="{{ route('user.training.index') }}" class="text-light"><i class="las la-chalkboard-teacher"></i>
                Training</a>
        </li>

        <a href="{{ route('user.referral.index') }}" class="text-light"><i class="la la-users"></i> Affiliates</a>
        </li>
        <li>
            <a href="{{ route('user.deposit.employee.package.active') }}" class="text-light position-relative">
                <i class="la la-box"></i> Packages
                @php
                    $user = auth()->user();
                    $isPackageActive = isUserEmployeePackageActivated($user);
                    $sidebarDotColor = '#dc3545'; // Red (default - no package)
                    $sidebarStatusText = 'No Package Active';

                    if ($isPackageActive) {
                        $activationCount = \App\Models\EmployeePackageActivationHistory::where(
                            'user_id',
                            $user->id,
                        )->count();

                        if ($activationCount == 1) {
                            $sidebarDotColor = '#28a745'; // Green (first time)
                            $sidebarStatusText = 'First Package Activation';
                        } else {
                            $sidebarDotColor = '#ffc107'; // Yellow (multiple activations)
                            $sidebarStatusText = 'Multiple Package Activations';
                        }
                    }
                @endphp

                <span class="position-absolute top-50 end-0 translate-middle-y" style="right: 10px;">
                    <span class="badge rounded-circle"
                        style="width: 8px; height: 8px; background-color: {{ $sidebarDotColor }}"
                        title="{{ $sidebarStatusText }}"></span>
                </span>
            </a>
        </li>
        <li><a href="{{ route('user.favorite') }}" class="text-light"><i class="la la-heart"></i> Favourites</a></li>
        <li><a href="{{ route('ticket.index') }}" class="text-light"><i class="las la-question-circle"></i> Support</a>
        </li>
        <li><a href="{{ route('user.logout') }}" class="text-light"><i class="la la-sign-out"></i> Sign Out</a></li>
    </ul>
</div>

<style>
    .golden-crown-icon {
        position: absolute;
        width:22px;
        height:21px;
        top: -4px;
        right: -6px;
        font-size: 14px;
        color: gold;
        transform: rotate(20deg);
        background: transparent;
        padding: 2px;
        /* box-shadow: 0 0 3px rgba(0, 0, 0, 0.3); */
    }
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

    :root {
        --header-height: 60px;
        --sidebar-bg-color: rgb(1, 45, 45);
        --sidebar-width: 200px;
    }

    body {
        padding-top: var(--header-height);
        padding-left: 0;
    }

    .fixed-top {
        height: var(--header-height);
    }

    /* Sidebar styles */
    .sidebar {
        position: fixed;
        margin-top: 67px;
        left: -250px;
        top: 0;
        height: 100%;
        transition: left 0.3s ease;
        z-index: 1020;
        width: var(--sidebar-width);
        background-color: rgb(1, 45, 45);
        overflow-y: auto;
    }

    .sidebar a {
        display: block;
        padding: 8px 10px;
        text-decoration: none;
        transition: background 0.3s;
        position: relative;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar.active {
        left: 0;
    }

    .header-lef {
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    /* Main content adjustments */
    .main-content {
        transition: margin-left 0.3s ease;
        margin-left: 0;
    }

    /* Active indicator style */
    .active-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #00ff00;
        border-radius: 50%;
        margin-left: 5px;
        vertical-align: middle;
    }

    /* Package status color classes */
    .package-status-green {
        background-color: #28a745 !important;
        /* Green for first activation */
    }

    .package-status-yellow {
        background-color: #ffc107 !important;
        /* Yellow for multiple activations */
    }

    .package-status-red {
        background-color: #dc3545 !important;
        /* Red for no package */
    }

    /* Improved badge styles */
    .badge.rounded-pill {
        font-size: 0.7rem;
        padding: 3px 8px;
    }

    /* Tooltip improvements */
    .tooltip {
        font-size: 0.875rem;
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
