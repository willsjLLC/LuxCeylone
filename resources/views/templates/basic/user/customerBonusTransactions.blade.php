@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div class="container">
        <div class="row">
            <a href="{{ route('user.transactions') }}" class="text-dark me-3">
                <i class="mt-10 mb-4 fa-solid fa-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mobile-view-margin">Customer Bonus Transactions</h3>
            <!-- Sidebar (Left) -->
            <div class="col-lg-3 col-md-4">
                <div class="mb-4 accordion" id="sidebarAccordion">

                    <!-- Transactions Accordion Item -->
                    <div class="accordion-item">
                        <h6 class="accordion-header">
                            <button
                                class="bg-transparent accordion--button d-flex justify-content-between align-items-center w-100"
                                type="button" data-bs-toggle="collapse" data-bs-target="#transactionsAccordion"
                                aria-expanded="false">
                                @lang('Transactions') <i class="la la-chevron-down"></i>
                            </button>
                        </h6>
                        <div id="transactionsAccordion" class="accordion-collapse collapse"
                            data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body">
                                <a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }}"><i
                                        class="la la-arrow-right"></i> @lang('Transactions')</a>
                            </div>
                        </div>
                    </div>

                    <!-- Deposit Accordion Item -->
                    <div class="accordion-item">
                        <h6 class="accordion-header">
                            <button
                                class="bg-transparent accordion--button d-flex justify-content-between align-items-center w-100"
                                type="button" data-bs-toggle="collapse" data-bs-target="#depositAccordion"
                                aria-expanded="false">
                                <span>@lang('Deposit')</span>
                                <i class="la la-chevron-down"></i>
                            </button>
                        </h6>
                        <div id="depositAccordion" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    <li><a href="{{ route('user.deposit.index') }}"
                                            class="{{ menuActive(['user.deposit.index', 'user.deposit.confirm']) }}"><i
                                                class="la la-arrow-right"></i> @lang('Deposit Now')</a></li>
                                    <li><a href="{{ route('user.deposit.history') }}"
                                            class="{{ menuActive('user.deposit.history') }}"><i
                                                class="la la-arrow-right"></i> @lang('Deposit History')</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Withdraw Accordion Item -->
                    <div class="accordion-item">
                        <h6 class="accordion-header">
                            <button
                                class="bg-transparent accordion--button d-flex justify-content-between align-items-center w-100"
                                type="button" data-bs-toggle="collapse" data-bs-target="#withdrawAccordion"
                                aria-expanded="false">
                                <span>@lang('Withdraw')</span>
                                <i class="la la-chevron-down"></i>
                            </button>
                        </h6>
                        <div id="withdrawAccordion" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="la la-arrow-right"></i> <a href="{{ route('user.withdraw') }}"
                                            class="{{ menuActive(['user.withdraw', 'user.withdraw.preview']) }}">
                                            @lang('Withdraw Now')
                                        </a>
                                    </li>
                                    <li>
                                        <i class="la la-arrow-right"></i> <a href="{{ route('user.withdraw.history') }}"
                                            class="{{ menuActive(['user.withdraw.history']) }}">
                                            @lang('Withdraw History')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Bonus Accordion Item -->
                    <div class="accordion-item">
                        <h6 class="accordion-header">
                            <button
                                class="bg-transparent accordion--button d-flex justify-content-between align-items-center w-100"
                                type="button" data-bs-toggle="collapse" data-bs-target="#bonusAccordion"
                                aria-expanded="false">
                                <span>@lang('Bonus')</span>
                                <i class="la la-chevron-down"></i>
                            </button>
                        </h6>
                        <div id="bonusAccordion" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    
                                    <li><a href="{{ route('user.customerBonusTransactions') }}"
                                            class="{{ menuActive('user.customerBonusTransactions') }}"><i
                                                class="la la-arrow-right"></i> @lang('Customer Bonuses')</a></li>
                                     @if(auth()->user()->role == 2)   
                                    <li><a href="{{ route('user.leaderBonusTransactions') }}"
                                            class="{{ menuActive('user.leaderBonusTransactions') }}"><i
                                                class="la la-arrow-right"></i> @lang('Leader Bonuses')</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Wallet Content (Right) -->
            <div class="col-lg-9 col-md-8">

                <div class="row">
                    <div class="dashboard__content ">
                        <form action="" class="mb-3">
                            <div class="flex-wrap gap-4 d-flex">
                                <div class="flex-grow-1">
                                    <label>@lang('Transaction Number')</label>
                                    <input type="text" name="search" value="{{ request()->search }}"
                                        class="form-control form--control">
                                </div>
                               
                                <div class="flex-grow-1 align-self-end">
                                    <button class="btn btn--base w-100"><i class="las la-filter"></i>
                                        @lang('Filter')
                                    </button>
                                </div>
                            </div>
                        </form>

                        <table class="table transaction__table">
                            <thead>
                                <tr>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Customer Voucher')</th>
                                    <th>@lang('Customer Festival')</th>
                                    <th>@lang('Customer Saving')</th>
                                    <th>@lang('Post Balance')</th>
                                    <th>@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td>
                                            <strong>{{ $trx->trx }}</strong>
                                        </td>

                                        <td class="transacted">
                                            <div class="text--end">
                                                <span class="d-block">
                                                    {{ showDateTime($trx->created_at) }}
                                                </span>
                                                {{ diffForHumans($trx->created_at) }}
                                            </div>
                                        </td>

                                        <td class="budget">
                                            <span class="fw-bold text--success">
                                                
                                                {{ showAmount($trx->customers_voucher) }}
                                            </span>
                                        </td>

                                        <td class="budget">
                                            <span class="fw-bold text--success">
                                                {{ showAmount($trx->customers_festival) }}
                                            </span>
                                        </td>

                                        <td class="budget">
                                            <span class="fw-bold text--success">
                                                {{ showAmount($trx->customers_saving) }}
                                            </span>
                                        </td>

                                        <td class="budget">
                                            {{ showAmount($trx->post_bonus_balance) }}
                                        </td>
                                        <td>
                                            <div class="text--end">
                                                {{ __($trx->details) }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted" colspan="100%">{{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($transactions->hasPages())
                            <div class="justify-content-center">
                                {{ $transactions->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }
    </style>
@endpush