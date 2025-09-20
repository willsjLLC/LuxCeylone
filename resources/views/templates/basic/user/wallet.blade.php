@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div class="mt-3 wallet-container">
        <div class="wallet-header d-flex align-items-center">
            <a href="{{ route('user.product.index') }}" class="text-dark me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h3 class="mb-0">Wallet</h3>
        </div>
        {{-- mobile view --}}
        <div class=" mt-3 wallet-content d-md-none d-block">
            <!-- Main Content -->
            <div class="wallet-main-content">
                <!-- Balance Card -->
                <h5>Main Balance</h5>
                <div class="wallet-card balance-card position-relative">
                    {{-- card header --}}
                    <div class="balance-header d-flex justify-content-between align-items-center position-relative">

                        <div class="balance-header d-flex justify-content-between align-items-start">
                            <div>
                                <div style="color:#303841; margin:0; line-height:1;font-size: 15px;">My Balance
                                    (LKR)</div>
                                <div class="lite-balance"
                                    style="color:#303841; font-weight:bold; font-size:40px; margin:0; line-height:0.8;">
                                    {{ number_format($user->balance, 2) }}
                                </div>
                            </div>
                        </div>


                        <div>

                            <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                data-bs-title="Transfer your balance to Lite account">
                                <button class="px-4 btn transfer-btn-to-pro rounded-pill" data-bs-toggle="modal"
                                    data-bs-target="#transferModal">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                            </span>
                            {{-- @endif --}}

                            <img src="{{ asset('assets/image/purse.png') }}" alt="purse"
                                style="width: 50px; height: auto;" />
                        </div>
                    </div>

                    <!-- Card content -->
                    <div class="mt-3 d-flex">
                        <div class="gap-3 flex-grow-1 d-flex flex-column">

                            <div class="d-flex justify-content-start" style="line-height:1; gap: 1rem;">
                                <div>
                                    <div class="text-label">Total Deposit (LKR)</div>
                                    <div class="amount">{{ number_format($totalDeposit, 2) }}</div>
                                </div>
                                <div>
                                    <div class="text-label">Total Withdrawal (LKR)</div>
                                    <div class="amount">{{ number_format($totalWithdrawals, 2) }}</div>
                                </div>
                            </div>


                            <div class="gap-2 d-flex">
                                <button class="btn deposit-btn rounded-pill">
                                    <a href="{{ route('user.deposit.index') }}"
                                        class="text-white text-decoration-none">Deposit</a>
                                </button>

                                @if (!$withdrawalEnabled)
                                    <button class="px-4 btn withdraw-btn rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#withdrawalModal">

                                        <a href="#" class="text-white text-decoration-none">Withdraw</a>
                                    </button>
                                @else
                                    <button class="px-4 btn withdraw-btn rounded-pill">

                                        <a href= "{{ route('user.withdraw') }}"
                                            class="text-white text-decoration-none">Withdraw</a>
                                    </button>
                                @endif

                                <button class="btn withdraw-btn rounded-pill">
                                    <a href="{{ route('user.withdraw.history') }}"
                                        class="text-white text-decoration-none">Transactions</a>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Content -->
            <div class="wallet-secondary-content">
                <!-- Voucher Card -->
                <h5>Voucher</h5>

                <div class="wallet-card voucher-card d-flex align-items-stretch">
                    <!-- Ribbon icon flush to the left border -->
                    <div class="ribbon-container d-flex ">
                        <img src="{{ asset('assets/image/red-ribbon.png') }}" alt="Ribbon" class="ribbon-icon" />
                    </div>

                    <!-- Voucher Content centered vertically -->
                    <div class="voucher-content d-flex flex-column align-items-left justify-content-center">
                        <div class="voucher-amount">
                            @if (isset($user->customerBonuses->voucher_balance))
                                {{ number_format($user->customerBonuses->voucher_balance, 2) }} LKR
                            @else
                                0.00 LKR
                            @endif
                        </div>
                        @if (
                            (isset($user->customerBonuses->voucher_balance) && $user->customerBonuses->voucher_balance > 0) ||
                                (isset($user->customerBonuses->is_voucher_open) && $user->customerBonuses->is_voucher_open == 1))
                            @if (isset($user->customerBonuses->is_voucher_open) && $user->customerBonuses->is_voucher_open == 1)
                                <div class="redeem-text">
                                    @php
                                        // Get cart count for the current user
                                        $cartCount = \App\Models\CartItem::where('customer_id', $user->id)->sum(
                                            'quantity',
                                        );
                                    @endphp
                                    @if (isset($user->customerBonuses->voucher_balance) && $user->customerBonuses->voucher_balance > 0)
                                        <button>
                                            <a
                                                href="{{ $cartCount > 0 ? route('user.cart.index') : route('user.product.index') }}">
                                                Redeem Now!
                                            </a>
                                        </button>
                                    @endif
                                </div>
                            @elseif (isset($user->customerBonuses->voucher_remaining_to_open))
                                <div class="calendar-display">
                                    <div class="calendar-icon">
                                        <div class="calendar-number">
                                            {{ $user->customerBonuses->voucher_remaining_to_open }}</div>
                                    </div>
                                    <div class="days-text">DAYS LEFT</div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Savings Card -->
                <h5>Savings</h5>
                <div class="mb-3 savings-card">
                    <div class="card-info">
                        {{-- <div class="card-number">{{ $user->unique_id }}</div> --}}
                        <div class="card-number">{{ implode(' ', str_split($user->unique_id, 4)) }}</div>
                        <div class="card-holder">{{ $user->firstname }} {{ $user->lastname }}</div>
                        <div class="mb-1 card-balance">
                            @if (isset($user->customerBonuses))
                                {{ number_format($user->customerBonuses->saving, 2) }} LKR
                            @else
                                0.00 LKR
                            @endif
                        </div>
                    </div>
                </div>
            </div>



            <!-- Bottom Content - Contains both Bonus and Points card -->
            <div class="wallet-bottom-content">
                <!-- Bonus Card -->

                <!-- Bonus Card -->
                <h5>Festival Bonus</h5>
                <div class="bonus-card d-flex justify-content-between align-items-center"
                    style="line-height:0.8; border:gray 1px solid; border-radius: 16px; padding: 10px;">
                    <div>
                        <div class="card-label bonus">Festival Bonus (LKR)</div>
                        <div class="bonus-amount">
                            @if (isset($user->customerBonuses))
                                {{ number_format($user->customerBonuses->festival_bonus_balance, 2) }} LKR
                            @else
                                0.00 LKR
                            @endif
                        </div>
                        @if (isset($user->customerBonuses) &&
                                isset($user->customerBonuses->festival_bonus_balance) &&
                                $user->customerBonuses->festival_bonus_balance > 0)
                            <div class="festival-message-container">
                                <div class="festival-message">
                                    @if ($user->kv == 0)
                                        <!-- If KYC not verified, show as text -->

                                        <button class="btn transfer-btn rounded-pill transfer-festival">
                                            <a href="{{ route('user.kyc.form') }}">
                                                {{ $festivalBonusMessage }}
                                            </a>
                                        </button>
                                    @elseif (isset($fromMonth) && isset($toMonth) && $currentMonth >= $fromMonth && $currentMonth < $toMonth)
                                        <button>

                                            {{ $festivalBonusMessage }}

                                        </button>
                                    @else
                                        <!-- If outside festival period, show as text -->
                                        {{ $festivalBonusMessage }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <img src="{{ asset('assets/image/gift_box.png') }}" alt="Gift" class="gift-box-icon">
                </div>

                <!-- Points Card - Only show for Leaders -->

                @if ($user->role == \App\Constants\Status::LEADER && $user->is_top_leader != Status::TOP_LEADER)
                    <h5>Leader Bonus</h5>
                    <div class="wallet-card points-card" style="line-height:1;">
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="card-label">Bonus (LKR)</span>
                                    <h3 class="mt-1 bonus-amount">
                                        @if ($user->leaderBonuses)
                                            {{ number_format($user->leaderBonuses->bonus, 2) }}
                                        @else
                                            0.00
                                        @endif
                                    </h3>
                                </div>
                                <button class="btn transfer-btn rounded-pill transfer-bonus">
                                    Transfer
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="card-label">Petrol Allowance (LKR)</span>
                                    <h3 class="mt-1 points-amount">
                                        @if ($user->leaderBonuses)
                                            {{ number_format($user->leaderBonuses->petrol_allowance, 2) }}
                                        @else
                                            0.00
                                        @endif
                                    </h3>
                                </div>
                                <button class="btn transfer-btn rounded-pill transfer-petrol">
                                    Transfer
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="card-label">Leasing Amount (LKR)</span>
                                    <h3 class="mt-1 leasing-amount">
                                        @if ($user->leaderBonuses)
                                            {{ number_format($user->leaderBonuses->leasing_amount, 2) }}
                                        @else
                                            0.00
                                        @endif
                                    </h3>
                                </div>
                                <button class="btn transfer-btn rounded-pill transfer-leasing">
                                    Transfer
                                </button>
                            </div>

                            <div class="mt-3 gap-2 d-flex">
                                <button class="btn withdraw1-btn rounded-pill">
                                    <a href="{{ route('user.withdraw.history') }}"
                                        class="text-white text-decoration-none">Bonus Transactions</a>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            <div class="wallet-card bonus-stats-card">
                <div class="bonus-stats-header">
                    <div class="bonus-stats-title">
                        @php
                            // Calculate total available bonuses based on user role
                            $availableBonuses = 0;
                            $unavailableBonuses = 0;

                            // Customer bonuses (for all users)
                            if (isset($user->customerBonuses)) {
                                $availableBonuses += $user->customerBonuses->commission_balance ?? 0;
                                $availableBonuses += $user->customerBonuses->voucher_balance ?? 0;
                                $availableBonuses += $user->customerBonuses->festival_bonus_balance ?? 0;
                                $availableBonuses += $user->customerBonuses->saving ?? 0;

                                $unavailableBonuses += $user->customerBonuses->temp_total ?? 0;
                            }

                            // Add leader bonuses if user is a leader or top leader
                            if (
                                ($user->role == \App\Constants\Status::LEADER || $user->is_top_leader) &&
                                isset($user->leaderBonuses)
                            ) {
                                $availableBonuses += $user->leaderBonuses->bonus ?? 0;
                                $availableBonuses += $user->leaderBonuses->leasing_amount ?? 0;
                                $availableBonuses += $user->leaderBonuses->petrol_allowance ?? 0;

                                $unavailableBonuses += $user->leaderBonuses->temp_total ?? 0;
                            }

                            // Add top leader bonuses if user is a top leader
                            if ($user->is_top_leader) {
                                $topLeader = \App\Models\TopLeader::where('user_id', $user->id)->first();
                                if ($topLeader) {
                                    $availableBonuses += $topLeader->for_car ?? 0;
                                    $availableBonuses += $topLeader->for_house ?? 0;
                                    $availableBonuses += $topLeader->for_expenses ?? 0;

                                    $unavailableBonuses += $topLeader->temp_total ?? 0;
                                }
                            }
                        @endphp

                        <i class="fa-solid fa-chart-line me-2"></i>
                        Total Available Bonuses: {{ number_format($availableBonuses, 2) }} LKR
                    </div>

                </div>

                <div class="bonus-stats-details">
                    <div class="mt-3 row">
                        @if (
                            $user->role == \App\Constants\Status::CUSTOMER ||
                                $user->role == \App\Constants\Status::LEADER ||
                                $user->is_top_leader)
                            <div class="mb-2 col-12">
                                <div class="bonus-type-label">Customer Bonuses</div>
                                @if (isset($user->customerBonuses))
                                    <div class="d-flex justify-content-between">
                                        <span>Commission Balance:</span>
                                        <span>{{ number_format($user->customerBonuses->commission_balance ?? 0, 2) }}
                                            LKR</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Voucher Balance:</span>
                                        <span>{{ number_format($user->customerBonuses->voucher_balance ?? 0, 2) }}
                                            LKR</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Festival Bonus:</span>
                                        <span>{{ number_format($user->customerBonuses->festival_bonus_balance ?? 0, 2) }}
                                            LKR</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Savings:</span>
                                        <span>{{ number_format($user->customerBonuses->saving ?? 0, 2) }} LKR</span>
                                    </div>
                                @else
                                    <div class="no-data-message">No customer bonuses data available</div>
                                @endif
                            </div>
                        @endif

                        @if ($user->role == \App\Constants\Status::LEADER && $user->is_top_leader != Status::TOP_LEADER)
                            <div class="mb-2 col-12">
                                <div class="bonus-type-label">Leader Bonuses</div>
                                @if (isset($user->leaderBonuses))
                                    <div class="d-flex justify-content-between">
                                        <span>Bonus:</span>
                                        <span>{{ number_format($user->leaderBonuses->bonus ?? 0, 2) }} LKR</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Leasing Amount:</span>
                                        <span>{{ number_format($user->leaderBonuses->leasing_amount ?? 0, 2) }} LKR</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Petrol Allowance:</span>
                                        <span>{{ number_format($user->leaderBonuses->petrol_allowance ?? 0, 2) }}
                                            LKR</span>
                                    </div>
                                @else
                                    <div class="no-data-message">No leader bonuses data available</div>
                                @endif
                            </div>
                        @endif

                        @if ($user->is_top_leader)
                            <div class="mb-2 col-12">
                                <div class="bonus-type-label">Top Leader Bonuses</div>
                                @php
                                    $topLeader = \App\Models\TopLeader::where('user_id', $user->id)->first();
                                @endphp
                                @if ($topLeader)
                                    
                                    <div class="d-flex justify-content-between">
                                        <span>Expenses Allowance:</span>
                                        <span>{{ number_format($topLeader->for_expenses ?? 0, 2) }} LKR</span>
                                    </div>
                                @else
                                    <div class="no-data-message">No top leader bonuses data available</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if ($unavailableBonuses > 0 && !$user->employee_package_activated)
                    <div class="unavailable-bonus-alert">
                        <div class="alert-content">
                            <div class="alert-title"> <i class="fa-solid fa-circle-exclamation"></i> Unavailable Bonuses:
                                {{ number_format($unavailableBonuses, 2) }} LKR</div>
                            <div class="alert-message">Please activate your package to claim these unavailable bonuses.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- desktop view --}}
        <div class="wallet-content d-none d-md-block">
            <!-- Main Content -->

            <div class="wallet-main-content">
                <!-- First Row: Balance and Voucher Cards -->
                <div class="balance-voucher">
                    <div class="row">
                        <!-- Balance Card Column -->
                        <div class="col-md-6">
                            <h5>Main Balance</h5>
                            <!-- Balance Card -->
                            <div class="wallet-card balance-card position-relative">
                                {{-- card header --}}
                                <div
                                    class="balance-header d-flex justify-content-between align-items-center position-relative">

                                    <div class="balance-header d-flex justify-content-between align-items-start">
                                        <div>
                                            <div style="color:#303841; margin:0; line-height:1;font-size: 15px;">My Balance
                                                (LKR)</div>
                                            <div class="lite-balance"
                                                style="color:#303841; font-weight:bold; font-size:40px; margin:0; line-height:0.8;">
                                                {{ number_format($user->balance, 2) }}
                                            </div>
                                        </div>
                                    </div>


                                    <div>
                                       
                                        {{-- <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Transfer your balance to Lite account">
                                            <button class="px-4 btn transfer-btn-to-pro rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#transferModal">
                                                <i class="fas fa-exchange-alt"></i> Transfer
                                            </button>
                                        </span> --}}
                                        {{-- @endif --}}

                                        <img src="{{ asset('assets/image/purse.png') }}" alt="purse"
                                            style="width: 50px; height: auto;" />
                                    </div>
                                </div>

                                <!-- Card content -->
                                <div class="mt-3 d-flex">
                                    <div class="gap-3 flex-grow-1 d-flex flex-column">

                                        <div class="d-flex justify-content-start" style="line-height:1; gap: 1rem;">
                                            <div>
                                                <div class="text-label">Total Deposit (LKR)</div>
                                                <div class="amount">{{ number_format($totalDeposit, 2) }}</div>
                                            </div>
                                            <div>
                                                <div class="text-label">Total Withdrawal (LKR)</div>
                                                <div class="amount">{{ number_format($totalWithdrawals, 2) }}</div>
                                            </div>
                                        </div>


                                        <div class="gap-2 d-flex">
                                            <button class="px-4 btn deposit-btn rounded-pill">
                                                <a href="{{ route('user.deposit.index') }}"
                                                    class="text-white text-decoration-none">Deposit</a>
                                            </button>

                                            @if (!$withdrawalEnabled)
                                                <button class="px-4 btn withdraw-btn rounded-pill" data-bs-toggle="modal"
                                                    data-bs-target="#withdrawalModal">

                                                    <a href="#" class="text-white text-decoration-none">Withdraw</a>
                                                </button>
                                            @else
                                                <button class="px-4 btn withdraw-btn rounded-pill">

                                                    <a href= "{{ route('user.withdraw') }}"
                                                        class="text-white text-decoration-none">Withdraw</a>
                                                </button>
                                            @endif

                                            <button class="px-4 btn withdraw-btn rounded-pill">
                                                <a href="{{ route('user.withdraw.history') }}"
                                                    class="text-white text-decoration-none">Transactions</a>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Voucher Card Column -->
                        <div class="col-md-6">
                            <h5>Voucher</h5>
                            <div class="wallet-card voucher-card d-flex align-items-stretch" style="height: 160px">
                                {{-- card header --}}
                                <!-- Ribbon icon flush to the left border -->
                                <div class="ribbon-container d-flex ">
                                    <img src="{{ asset('assets/image/red-ribbon.png') }}" alt="Ribbon"
                                        class="ribbon-icon" />
                                </div>

                                <!-- Voucher Content centered vertically -->
                                <div class="voucher-content d-flex flex-column align-items-left justify-content-center">
                                    <div class="voucher-amount">
                                        @if (isset($user->customerBonuses->voucher_balance))
                                            {{ number_format($user->customerBonuses->voucher_balance, 2) }} LKR
                                        @else
                                            0.00 LKR
                                        @endif
                                    </div>
                                    @if (
                                        (isset($user->customerBonuses->voucher_balance) && $user->customerBonuses->voucher_balance > 0) ||
                                            (isset($user->customerBonuses->is_voucher_open) && $user->customerBonuses->is_voucher_open == 1))
                                        @if (isset($user->customerBonuses->is_voucher_open) && $user->customerBonuses->is_voucher_open == 1)
                                            <div class="redeem-text">
                                                @php
                                                    // Get cart count for the current user
                                                    $cartCount = \App\Models\CartItem::where(
                                                        'customer_id',
                                                        $user->id,
                                                    )->sum('quantity');
                                                @endphp
                                                @if (isset($user->customerBonuses->voucher_balance) && $user->customerBonuses->voucher_balance > 0)
                                                    <button>
                                                        <a
                                                            href="{{ $cartCount > 0 ? route('user.cart.index') : route('user.product.index') }}">
                                                            Redeem Now!
                                                        </a>
                                                    </button>
                                                @endif
                                            </div>
                                        @elseif (isset($user->customerBonuses->voucher_remaining_to_open))
                                            <div class="calendar-display">
                                                <div class="calendar-icon">
                                                    <div class="calendar-number">
                                                        {{ $user->customerBonuses->voucher_remaining_to_open }}</div>
                                                </div>
                                                <div class="days-text">DAYS LEFT</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row: Festival Bonus and Savings Cards -->
                <div class="bonus-savings">
                    <div class="row">
                        <!-- Festival Bonus Column -->
                        <div class="col-md-6">
                            <h5>Festival Bonus</h5>
                            <!-- Bonus Card -->
                            <div class="wallet-card bonus-card d-flex justify-content-between align-items-center"
                                style="height: 260px">
                                <div>
                                    <div class="card-label">Festival Bonus (LKR)</div>
                                    <div class="bonus-amount">
                                        @if (isset($user->customerBonuses))
                                            {{ number_format($user->customerBonuses->festival_bonus_balance, 2) }} LKR
                                        @else
                                            0.00 LKR
                                        @endif
                                    </div>
                                    @if (isset($user->customerBonuses) &&
                                            isset($user->customerBonuses->festival_bonus_balance) &&
                                            $user->customerBonuses->festival_bonus_balance > 0)
                                        <div class="festival-message-container">
                                            <div class="festival-message">
                                                @if ($user->kv == 0)
                                                    <!-- If KYC not verified, show as text -->
                                                    <button>
                                                        <a href="{{ route('user.kyc.form') }}">
                                                            {{ $festivalBonusMessage }}
                                                        </a>
                                                    </button>
                                                @elseif (isset($fromMonth) && isset($toMonth) && $currentMonth >= $fromMonth && $currentMonth < $toMonth)
                                                    <!-- If within festival period, show as button -->

                                                    <button class="btn transfer-btn rounded-pill transfer-festival">
                                                        {{ $festivalBonusMessage }}
                                                    </button>
                                                @else
                                                    <!-- If outside festival period, show as text -->
                                                    {{ $festivalBonusMessage }}
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <img src="{{ asset('assets/image/gift_box.png') }}" alt="Gift"
                                    class="gift-box-icon">
                            </div>
                        </div>

                        <!-- Savings Column -->
                        <div class="col-md-6">
                            <h5>Savings</h5>
                            <div class="savings-card">
                                <div class="card-info ">
                                    <div class="mb-2 card-number">{{ implode(' ', str_split($user->unique_id, 4)) }}</div>
                                    <div class="mb-2 card-holder">{{ $user->firstname }} {{ $user->lastname }}</div>
                                    <p class="mb-2 card-balance">
                                        @if (isset($user->customerBonuses))
                                            {{ number_format($user->customerBonuses->saving, 2) }} LKR
                                        @else
                                            0.00 LKR
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leader Bonus Section -->
                <div class="wallet-bottom-content">

                    @if ($user->role == \App\Constants\Status::LEADER && $user->is_top_leader != Status::TOP_LEADER)
                        <h5>Leader Bonus</h5>
                        <div class="wallet-card points-card" style="line-height:1;">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Bonus (LKR)</span>
                                        <h3 class="mt-1 bonus-amount">
                                            @if ($user->leaderBonuses)
                                                {{ number_format($user->leaderBonuses->bonus, 2) }}
                                            @else
                                                0.00
                                            @endif
                                        </h3>
                                    </div>
                                    <button class="btn transfer-btn rounded-pill transfer-bonus">
                                        Transfer
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Petrol Allowance (LKR)</span>
                                        <h3 class="mt-1 points-amount">
                                            @if ($user->leaderBonuses)
                                                {{ number_format($user->leaderBonuses->petrol_allowance, 2) }}
                                            @else
                                                0.00
                                            @endif
                                        </h3>
                                    </div>
                                    <button class="btn transfer-btn rounded-pill transfer-petrol">
                                        Transfer
                                    </button>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="card-label">Leasing Amount (LKR)</span>
                                        <h3 class="mt-1 leasing-amount">
                                            @if ($user->leaderBonuses)
                                                {{ number_format($user->leaderBonuses->leasing_amount, 2) }}
                                            @else
                                                0.00
                                            @endif
                                        </h3>
                                    </div>
                                    <button class="btn transfer-btn rounded-pill transfer-leasing">
                                        Transfer
                                    </button>
                                </div>
                                <div class="mt-3 gap-2 d-flex">
                                    <button class="btn withdraw1-btn rounded-pill">
                                        <a href="{{ route('user.withdraw.history') }}"
                                            class="text-white text-decoration-none">Bonus Transactions</a>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>


                <!-- Bonus Stats Section -->
                <div class="wallet-bonus-stats">
                    <div class="wallet-card bonus-stats-card">
                        <div class="bonus-stats-header">
                            <div class="bonus-stats-title">
                                @php
                                    // Calculate total available bonuses based on user role
                                    $availableBonuses = 0;
                                    $unavailableBonuses = 0;

                                    // Customer bonuses (for all users)
                                    if (isset($user->customerBonuses)) {
                                        $availableBonuses += $user->customerBonuses->commission_balance ?? 0;
                                        $availableBonuses += $user->customerBonuses->voucher_balance ?? 0;
                                        $availableBonuses += $user->customerBonuses->festival_bonus_balance ?? 0;
                                        $availableBonuses += $user->customerBonuses->saving ?? 0;

                                        $unavailableBonuses += $user->customerBonuses->temp_total ?? 0;
                                    }

                                    // Add leader bonuses if user is a leader or top leader
                                    if (
                                        ($user->role == \App\Constants\Status::LEADER || $user->is_top_leader) &&
                                        isset($user->leaderBonuses)
                                    ) {
                                        $availableBonuses += $user->leaderBonuses->bonus ?? 0;
                                        $availableBonuses += $user->leaderBonuses->leasing_amount ?? 0;
                                        $availableBonuses += $user->leaderBonuses->petrol_allowance ?? 0;

                                        $unavailableBonuses += $user->leaderBonuses->temp_total ?? 0;
                                    }

                                    // Add top leader bonuses if user is a top leader
                                    if ($user->is_top_leader) {
                                        $topLeader = \App\Models\TopLeader::where('user_id', $user->id)->first();
                                        if ($topLeader) {
                                            $availableBonuses += $topLeader->for_car ?? 0;
                                            $availableBonuses += $topLeader->for_house ?? 0;
                                            $availableBonuses += $topLeader->for_expenses ?? 0;

                                            $unavailableBonuses += $topLeader->temp_total ?? 0;
                                        }
                                    }
                                @endphp

                                <i class="fa-solid fa-chart-line me-2"></i>
                                Total Available Bonuses: {{ number_format($availableBonuses, 2) }} LKR
                            </div>
                            <div class="bonus-stats-amount">

                            </div>
                        </div>

                        <div class="bonus-stats-details">
                            <div class="mt-3 row">
                                @if (
                                    $user->role == \App\Constants\Status::CUSTOMER ||
                                        $user->role == \App\Constants\Status::LEADER ||
                                        $user->is_top_leader)
                                    <div class="mb-2 col-12">
                                        <div class="bonus-type-label">Customer Bonuses</div>
                                        @if (isset($user->customerBonuses))
                                            <div class="d-flex justify-content-between">
                                                <span class="text-">Commission Balance:</span>
                                                <span>{{ number_format($user->customerBonuses->commission_balance ?? 0, 2) }}
                                                    LKR</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Voucher Balance:</span>
                                                <span>{{ number_format($user->customerBonuses->voucher_balance ?? 0, 2) }}
                                                    LKR</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Festival Bonus:</span>
                                                <span>{{ number_format($user->customerBonuses->festival_bonus_balance ?? 0, 2) }}
                                                    LKR</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Savings:</span>
                                                <span>{{ number_format($user->customerBonuses->saving ?? 0, 2) }}
                                                    LKR</span>
                                            </div>
                                        @else
                                            <div class="no-data-message">No customer bonuses data available</div>
                                        @endif
                                    </div>
                                @endif

                                @if ($user->role == \App\Constants\Status::LEADER && $user->is_top_leader != Status::TOP_LEADER)
                                    <div class="mb-2 col-12">
                                        <div class="bonus-type-label">Leader Bonuses</div>
                                        @if (isset($user->leaderBonuses))
                                            <div class="d-flex justify-content-between">
                                                <span>Bonus:</span>
                                                <span>{{ number_format($user->leaderBonuses->bonus ?? 0, 2) }} LKR</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Leasing Amount:</span>
                                                <span>{{ number_format($user->leaderBonuses->leasing_amount ?? 0, 2) }}
                                                    LKR</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Petrol Allowance:</span>
                                                <span>{{ number_format($user->leaderBonuses->petrol_allowance ?? 0, 2) }}
                                                    LKR</span>
                                            </div>
                                        @else
                                            <div class="no-data-message">No leader bonuses data available</div>
                                        @endif
                                    </div>
                                @endif

                                @if ($user->is_top_leader)
                                    <div class="mb-2 col-12">
                                        <div class="bonus-type-label">Top Leader Bonuses</div>
                                        @php
                                            $topLeader = \App\Models\TopLeader::where('user_id', $user->id)->first();
                                        @endphp
                                        @if ($topLeader)
                                           
                                            <div class="d-flex justify-content-between">
                                                <span>Expenses Allowance:</span>
                                                <span>{{ number_format($topLeader->for_expenses ?? 0, 2) }} LKR</span>
                                            </div>
                                        @else
                                            <div class="no-data-message">No top leader bonuses data available</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($unavailableBonuses > 0 && !$user->employee_package_activated)
                            <div class="unavailable-bonus-alert">
                                <div class="alert-content">
                                    <div class="alert-title"> <i class="fa-solid fa-circle-exclamation"></i> Unavailable
                                        Bonuses: {{ number_format($unavailableBonuses, 2) }} LKR</div>
                                    <div class="alert-message text danger">Please activate your package to claim these
                                        unavailable bonuses.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>



    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="text-center modal-header">
                    <h5 class="modal-title w-100" id="confirmationModalLabel">Confirm Your Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        Would you like to transfer your <strong id="transferType"></strong> to your main account balance?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn confirm-payment-btn">Confirm Transfer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade transfer-modal" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content transfer-modal-content">
                <div class="modal-header transfer-modal-header">
                    <h5 class="modal-title transfer-modal-title" id="transferModalLabel"> <i
                            class="fas fa-exchange-alt"></i> Transfer to Lite Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>



                <div class="modal-body transfer-modal-body">
                    <form id="transferForm" method="post">
                        @csrf
                        <div class="mb-4">
                            <label for="transferAmount" class="form-label transfer-modal-label">Amount to Transfer
                                Rs.</label>
                            <input type="number" class="form-control transfer-modal-input" id="transferAmount"
                                name="amount" min="1.00" step="0.01" required placeholder="Enter amount">

                            <div class="d-flex justify-content-end mt-1">

                                <button type="button" class="px-4 btn max-amount rounded-pill"
                                    id="maxTransferAmountButton">
                                    Max Amount
                                </button>

                            </div>
                        </div>

                    </form>
                </div>


                <div class="modal-footer transfer-modal-footer">
                    <button type="button" class="btn transfer-modal-btn-secondary"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn transfer-modal-btn-primary" id="confirmTransferBtn">Confirm
                        Transfer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="withdrawalModal" tabindex="-1" aria-labelledby="withdrawalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning" id="withdrawalModalLabel"> <i
                            class="las la-exclamation-triangle"></i>
                        Attention</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($withdrawalEnabled)
                        <p>You are eligible to withdraw funds. Click the button below to proceed.</p>
                        <a href="{{ route('user.withdraw') }}" class="btn btn-primary">Proceed to Withdrawal</a>
                    @else
                        <p class="fw-bold fs-6 text-danger">{{ $withdrawalMessage }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($reason == 1)
                        <button type="button" class="btn btn-success"> <a class="text-white"
                                href="{{ route('user.training.index') }}">Tickets</a></button>
                    @endif
                    @if ($reason == 2 || $reason == 3)
                        <button type="button" class="btn btn-success"> <a class="text-white"
                                href="{{ route('user.deposit.employee.package.active') }}">Packages</a></button>
                    @endif

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('style')
    <style>
        .bonus-stats-card {
            background: linear-gradient(to bottom right, #ffffff, #f8f9fa);
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .bonus-stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .bonus-stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .bonus-stats-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0a815d;
            display: flex;
            align-items: center;
        }

        .bonus-stats-title i {
            font-size: 1.1rem;
            margin-right: 8px;
            color: #25BBA2;
        }

        .bonus-stats-details {
            margin-top: 15px;
            font-size: 0.95rem;
            color: #303841;
        }

        .bonus-type-label {
            font-size: 1rem;
            font-weight: 600;
            color: #0a815d;
            margin-bottom: 10px;
            padding-left: 8px;
            border-left: 3px solid #25BBA2;
        }

        .bonus-stats-details .d-flex {
            padding: 8px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .bonus-stats-details .d-flex span:first-child {
            flex: 1;
            color: #555;
        }

        .bonus-stats-details .d-flex span:last-child {
            font-weight: 600;
            color: #303841;
        }

        .unavailable-bonus-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-size: 1rem;
            font-weight: 600;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-title i {
            font-size: 1.2rem;
            color: #f0ad4e;
        }

        .alert-message {
            font-size: 0.9rem;
            color: #856404;
            margin-top: 5px;
        }

        .no-data-message {
            font-size: 0.9rem;
            color: #6c757d;
            
            padding: 10px 0;
        }

        /* Responsive Design */

        /* Desktop View */
        @media (min-width: 769px) {
            .bonus-stats-card {
                padding: 25px;
                margin-bottom: 30px;
            }

            .bonus-stats-title {
                font-size: 1.3rem;
            }

            .bonus-stats-details {
                font-size: 1rem;
            }

            .bonus-type-label {
                font-size: 1.1rem;
            }

            .unavailable-bonus-alert {
                padding: 20px;
            }

            .alert-title {
                font-size: 1.1rem;
            }

            .alert-message {
                font-size: 0.95rem;
            }
        }


        @media (max-width: 768px) {
            .bonus-stats-card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .bonus-stats-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .bonus-stats-title {
                font-size: 1.1rem;
            }

            .bonus-stats-details {
                font-size: 0.9rem;
            }

            .bonus-type-label {
                font-size: 0.95rem;
            }

            .unavailable-bonus-alert {
                flex-direction: column;
                text-align: center;
                padding: 12px;
            }

            .alert-title {
                font-size: 0.95rem;
            }

            .alert-message {
                font-size: 0.85rem;
            }
        }


        @media (max-width: 576px) {
            .bonus-stats-card {
                padding: 12px;
            }

            .bonus-stats-title {
                font-size: 1rem;
            }

            .bonus-stats-details {
                font-size: 0.85rem;
            }

            .bonus-type-label {
                font-size: 0.9rem;
            }

            .bonus-stats-details .d-flex {
                flex-direction: column;
                gap: 5px;
            }

            .unavailable-bonus-alert {
                padding: 10px;
            }

            .alert-title {
                font-size: 0.9rem;
            }

            .alert-message {
                font-size: 0.8rem;
            }
        }
    </style>

    <style>
        .bonus {
            margin-bottom: 5px;
            font-size: 1.2rem !important;
        }

        .card-information {
            margin-top: 175px;
            margin-left: 40px;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .wallet-container {
            padding: 15px;
            margin: 0 auto;
            max-width: 1200px;
        }

        .wallet-content {
            display: flex;
            flex-direction: column;
        }

        .wallet-card-desktop {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
            width: 100%;
            height: auto;
            background-image: url('{{ asset('assets/image/card.png') }}');
            background-size: cover;
            background-position: center;
            color: white;
            overflow: hidden;
            padding: 20px;
        }

        .wallet-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            background-color: #ffffff;
            margin-bottom: 20px;
            padding: 15px;
        }

        .card-label {
            color: #003333;
            font-size: 1.5rem;
        }

        .balance-card {
            /* background: linear-gradient(to bottom, #0a815d, #CFFFDF); */
            /* background: linear-gradient(to bottom, rgb(1, 45, 45), #17433c); */
            background: linear-gradient(to bottom, #17433c, #ffffff);
            color: white;
        }

        .text-label {
            font-size: 0.7rem;
            color: #303841;
        }

        .amount {
            font-size: 1.1rem;
            font-weight: bold;
            color: #303841;
        }

        .deposit-btn,
        .withdraw-btn {
            font-weight: bold;
            border: none;
            padding: 0px 8px;
            border-radius: 8px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .withdraw1-btn {
            font-weight: bold;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .deposit-btn {
            background-color: #25BBA2;
        }

        .deposit-btn:hover {
            background-color: #00b347;
        }

        .withdraw-btn {
            background-color: #25BBA2;
        }

        .withdraw-btn:hover {
            background-color: #00b347;
        }

        .withdraw1-btn {
            background-color: #25BBA2;
        }

        .withdraw1-btn:hover {
            background-color: #00b347;
        }

        .withdraw1-btn {
            background-color: #25BBA2;
        }

        .withdraw1-btn:hover {
            background-color: #00b347;
        }

        .voucher-card {
            /* background: linear-gradient(to bottom, #0a815d, #CFFFDF); */
            background: linear-gradient(to bottom, #17433c, #ffffff);
            color: #003333;
            padding: 0px;
        }

        .voucher-amount {
            font-size: 30px;
            font-weight: bold;
            color: white;
        }

        .redeem-text {
            font-size: 28px;
            font-weight: 700;
            color: #003333;
        }

        .ribbon-icon {
            width: 120px;
            height: auto;
        }

        .savings-card {
            width: 100%;
            background-image: url('{{ asset('assets/image/card.png') }}');
            background-size: cover;
            background-position: center;
            color: white;
            position: relative;
            padding: 20px;
            border-radius: 16px;
            overflow: hidden;
        }

        .card-info {
            position: absolute;
            bottom: 5px;
            left: 20px;
            z-index: 1;
            font-family: 'Courier New', monospace;
        }

        .card-number,
        .card-balance,
        .card-holder {
            font-size: 1.4rem;
            font-weight: bold;
        }

        .card-balance {
            font-size: 1.9rem;
            color: #b0e892;
        }

        .card-details {
            font-family: 'Courier New', monospace;
        }

        .card-number {
            font-size: 1.3rem;
            letter-spacing: 2px;
        }

        .card-holder {
            font-size: 1.3rem;
            text-transform: uppercase;
        }

        .bonus-card {
            margin: 10px 0;
            color: #003333;
        }

        .bonus-amount {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .gift-box-icon {
            width: 80px;
        }

        .points-card {
            background: linear-gradient(to bottom, #0a815d, #CFFFDF);
        }

        .points-amount,
        .leasing-amount,
        .bonus-amount {
            font-size: 1.4rem;
            font-weight: bold;
            color: #303841;
        }

        .transfer-btn {
            background-color: #024b14;
            color: white;
            font-weight: 600;
            border: none;
            padding: 5px 15px;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }

        .transfer-btn:hover {
            background-color: #00b347;
            color: white;
        }

        .transfer-btn-to-pro {
            background-color: #024b14;
            color: white;
            font-weight: 600;
            border: none;
            padding: 5px 15px;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }

        .transfer-btn-to-pro:hover {
            background-color: rgba(247, 247, 247, 1);
            color: black;
            box-shadow: 1px white;
        }


        .max-amount {
            background-color: #f0ad4e;
            color: black;
            font-weight: 600;
            border: none;
            padding: 5px 15px;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }

        .max-amount:hover {
            background-color: #da9434ff;
            ;
            color: black;
            box-shadow: 1px white;
        }

        /* Confirmation Modal Styles */
        .modal-title {
            color: #024b14;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .confirm-payment-btn {
            background-color: #024b14;
            color: white;
            font-weight: 600;
            border: none;
            transition: background-color 0.3s ease;
        }

        .confirm-payment-btn:hover {
            background-color: #00b347;
            color: white;
        }

        /* Mobile View */
        @media (max-width: 768px) {
            .wallet-container {
                padding: 10px;
                max-width: 480px;
            }

            .wallet-content,
            .balance-voucher-row,
            .bonus-savings-row,
            .wallet-bottom-content {
                flex-direction: column;
                gap: 20px;
            }

            .savings-card {
                aspect-ratio: 85 / 54;
            }
        }

        /* Small Mobile Devices */
        @media (max-width: 576px) {
            .wallet-container {
                padding: 0 10px;
            }

            .card-balance {
                font-size: 1.2rem;
            }

            .card-info {
                bottom: 0;
                left: 16px;
            }

            .card-number,
            .card-balance,
            .card-holder {
                font-size: 1.2rem;
            }

            .card-balance {
                font-size: 1.6rem;
            }

            .gift-box-icon {
                width: 80px;
            }

            .voucher-amount {
                font-size: 24px;
            }

            .redeem-text {
                font-size: 22px;
            }

            .transfer-btn {
                padding: 3px 10px;
                font-size: 0.8rem;
            }
        }

        @media (max-width:425px) {

            .card-number,
            .card-holder {
                font-size: 0.9rem;
            }

            .card-balance {
                font-size: 1.2rem;
            }
        }

        /* Desktop View */
        @media (min-width: 769px) {
            .wallet-content {
                gap: 30px;
            }

            .wallet-main-content,
            .wallet-bottom-content {
                padding: 20px;
                border-radius: 16px;
                background-color: #ffffff;
            }

            .balance-voucher-row {
                display: grid;
                grid-template-columns: 1.5fr 1fr;
                gap: 20px;
            }

            .bonus-savings-row {
                display: grid;
                grid-template-columns: 1fr 1.5fr;
                gap: 20px;
            }

            .card-info {
                left: 32px;
                line-height: 1.2;
            }

            .card-number,
            .card-holder {
                font-size: 1.2rem;
                font-weight: bold;
            }

            .card-balance {
                font-size: 1.6rem;
            }
        }

        @media (min-width: 991px) {
            .savings-card {
                aspect-ratio: 85 / 54;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            let transferType = ''; // Track current type

            // Handle the click event for the bonus transfer button
            $('.transfer-bonus').on('click', function() {
                let amount = $(this).closest('.d-flex').find('.bonus-amount').text().trim();

                $('#transferType').text('Bonus');
                $('#transferAmount').text(amount);

                transferType = 'bonus'; // Set current transfer type

                $('#confirmationModal').modal('show');
            });

            // Handle the click event for the petrol transfer button
            $('.transfer-petrol').on('click', function() {
                let amount = $(this).closest('.d-flex').find('.points-amount').text().trim();

                $('#transferType').text('Petrol Allowance');
                $('#transferAmount').text(amount);

                transferType = 'petrol'; // Set current transfer type

                $('#confirmationModal').modal('show');
            });

            // Handle the click event for the leasing transfer button
            $('.transfer-leasing').on('click', function() {
                let amount = $(this).closest('.d-flex').find('.leasing-amount').text().trim();

                $('#transferType').text('Leasing Amount');
                $('#transferAmount').text(amount);

                transferType = 'leasing'; // Set current transfer type

                $('#confirmationModal').modal('show');
            });

            // Handle the click event for the festival bonus transfer button
            $('.transfer-festival').on('click', function() {
                $('#transferType').text('Festival Bonus');
                transferType = 'festival'; // Set current transfer type
                $('#confirmationModal').modal('show');
            });

            // Handle the confirmation button click
            $('.confirm-payment-btn').on('click', function() {
                $('#confirmationModal').modal('hide');

                if (transferType === 'petrol') {
                    window.location.href = "{{ route('user.petrol.transfer') }}";
                } else if (transferType === 'leasing') {
                    window.location.href = "{{ route('user.leasing.transfer') }}";
                } else if (transferType === 'bonus') {
                    window.location.href = "{{ route('user.bonus.transfer') }}";
                } else if (transferType === 'festival') {
                    window.location.href = "{{ route('user.festival.transfer') }}";
                }
            });
        });
    </script>
@endpush

@push('script')
    <script>
        $('#transferModal').on('show.bs.modal', function() {
            $('#transferAmount').val('');
        });

        const userBalance = parseFloat("{{ $user->balance }}");

        $('#maxTransferAmountButton').on('click', function() {
            $('#transferAmount').val(userBalance);
        });


        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $('#confirmTransferBtn').on('click', function() {
            const transferAmount = $('#transferAmount').val();
            if (transferAmount > 0) {
                $.ajax({
                    url: '/user/wallet/transfer-to-lite',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        amount: transferAmount
                    },
                    success: function(res) {
                        $('#transferModal').modal('hide');
                        iziToast.success({
                            title: "success",
                            message: res.message,
                            position: "topRight",
                            timeout: 3000,
                        });
                        $('.lite-balance').text(Number(res.lite_balance).toFixed(2));
                        $('.pro-balance').text(res.pro_balance);
                        $('#transferAmount').val('');
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        iziToast.error({
                            title: "error",
                            message: errorMessage,
                            position: "topRight",
                            timeout: 3000,
                        });
                    }
                });
            } else {
                iziToast.error({
                    title: "error",
                    message: 'Please enter a valid amount to transfer.',
                    position: "topRight",
                    timeout: 3000,
                });

            }
        });
    </script>
@endpush

@if (!$withdrawalEnabled)
    <script>
        document.querySelector('.withdraw-btn').addEventListener('click', function(event) {
            event.preventDefault();
            var myModal = new bootstrap.Modal(document.getElementById('withdrawalModal'));
            myModal.show();
        });
    </script>
@else
    <script>
        document.querySelector('.withdraw-btn').addEventListener('click', function(event) {
            event.preventDefault();
            window.location.href = "{{ route('user.withdraw') }}";
        });
    </script>
@endif

@push('style')
    <style>
        .festival-message-container {
            background-color: #e9f5f2;
            padding: 15px;
            border-radius: 8px;
            text-align: left;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .festival-message {
            margin: 0;
            font-size: 16px !important;
            font-weight: bold !important;
            color: #009933 !important;
            line-height: 1.4;
        }

        /* Festival message button styling */
        .festival-message button {
            background-color: #009933;
            border: none;
            border-radius: 30px;
            padding: 6px 10px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .festival-message button:hover {
            background-color: #00b347;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        .festival-message button a {
            color: white !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            display: block;
            width: 100%;
        }

        .redeem-text button {
            background-color: #009933;
            border: none;
            border-radius: 30px;
            padding: 4px 16px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .redeem-text button:hover {
            background-color: #00b347;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        .redeem-text button a {
            color: white !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            display: block;
            width: 100%;
        }

        /* Mobile adjustments */
        @media (max-width: 576px) {
            .festival-message-container {
                padding: 10px;
            }

            .festival-message {
                font-size: 14px !important;
            }

            .festival-message button {
                padding: 6px 15px;
            }

            .festival-message button a {
                font-size: 1rem;
            }


            .redeem-text button a {
                font-size: 1rem;
            }
        }
    </style>

    <style>
        /* CSS for the calendar style days remaining display with text outside */
        .calendar-display {
            display: flex;
            align-items: center;
            margin-top: 5px;
            gap: 10px;
        }

        .calendar-icon {
            width: 60px;
            height: 70px;
            background-image: url('{{ asset('assets/image/calendar.png') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }



        .calendar-number {
            font-size: 36px;
            font-weight: bold;
            color: black;
            margin-top: 6px;
            /* Push down a bit to account for the header */
        }

        .days-text {
            font-size: 20px;
            font-weight: bold;
            color: black;
            letter-spacing: 0.5px;
        }

        /* Mobile view adjustments */
        @media (max-width: 576px) {
            .calendar-icon {
                width: 50px;
                height: 60px;
            }

            .calendar-number {
                font-size: 30px;
            }

            .days-text {
                font-size: 16px;
            }
        }
    </style>

    <style>
        .transfer-modal .transfer-modal-content {
            border-radius: 12px;
            border: 1px solid #d4f4e2;
            background-color: #f8fcf9;
            box-shadow: 0 4px 20px rgba(0, 128, 64, 0.1);
        }

        .transfer-modal .transfer-modal-header {
            background-color: #e6f4ec;
            border-bottom: 1px solid #d4f4e2;
            padding: 1.25rem;
        }

        .transfer-modal .transfer-modal-title {
            color: #2e7d32;
            font-weight: 600;
        }

        .transfer-modal .transfer-modal-body {
            padding: 1.5rem;
        }

        .transfer-modal .transfer-modal-label {
            color: #388e3c;
            font-weight: 500;
        }

        .transfer-modal .transfer-modal-input {
            border: 1px solid #a5d6a7;
            background-color: #ffffff;
            transition: border-color 0.3s ease;
        }

        .transfer-modal .transfer-modal-input:focus {
            border-color: #4caf50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .transfer-modal .transfer-modal-footer {
            border-top: 1px solid #d4f4e2;
            padding: 1rem;
            background-color: #f8fcf9;
        }

        .transfer-modal .transfer-modal-btn-primary {
            background-color: #4caf50;
            border-color: #4caf50;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }

        .transfer-modal .transfer-modal-btn-primary:hover {
            background-color: #388e3c;
            border-color: #388e3c;
        }

        .transfer-modal .transfer-modal-btn-secondary {
            background-color: #e0e0e0;
            border-color: #e0e0e0;
            color: #424242;
            transition: background-color 0.3s ease;
        }

        .transfer-modal .transfer-modal-btn-secondary:hover {
            background-color: #d5d5d5;
            border-color: #d5d5d5;
        }
    </style>
@endpush
