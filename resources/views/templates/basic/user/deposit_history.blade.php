@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
<div class="container">
    <div class="row">
        <a href="{{ route('user.withdraw.history') }}" class="text-dark me-3">
            <i class="mt-10 mb-4 fa-solid fa-arrow-left"></i>
        </a>
        <h3 class="mt-10 mb-4 fw-bold text-dark mobile-view-margin">Deposit History</h3>
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
        <form action="" class="float-end">
            <div class="mb-3 d-flex justify-content-end table-form ">
                <div class="input-group table_data_search">
                    <input type="text" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
                    <button class="text-white input-group-text btn btn--base">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <table class="table custom--table">
            <thead>
                <tr>
                    <th>@lang('Gateway | Transaction')</th>
                    <th class="text-center">@lang('Initiated')</th>
                    <th class="text-center">@lang('Amount')</th>
                    <th class="text-center">@lang('Conversion')</th>
                    <th class="text-center">@lang('Status')</th>
                    <th>@lang('Details')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold"> <span class="text--primary">{{ __($deposit->gateway?->name) }}</span>
                                </span>
                                <br>
                                <small> {{ $deposit->trx }} </small>
                            </div>
                        </td>

                        <td class="text-center">
                            <div>
                                <span class="d-block">{{ showDateTime($deposit->created_at) }}</span>
                                <span>{{ diffForHumans($deposit->created_at) }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div>

                                {{ showAmount($deposit->amount) }} + <span class="text--danger" title="@lang('charge')">{{ showAmount($deposit->charge) }} </span>
                                <br>
                                <strong title="@lang('Amount with charge')">
                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                </strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <div>

                                1 {{ __(gs("cur_text")) }} = {{ showAmount($deposit->rate, currencyFormat:false) }}
                                {{ __($deposit->method_currency) }}
                                <br>
                                <strong>{{ showAmount($deposit->final_amount, currencyFormat:false) }}
                                    {{ __($deposit->method_currency) }}</strong>
                            </div>

                        </td>
                        <td class="text-center">
                            @php echo $deposit->statusBadge @endphp
                        </td>
                        @php
                            $details = $deposit->detail != null ? json_encode($deposit->detail) : null;
                        @endphp

                        <td>
                            <a href="javascript:void(0)" class="btn btn--base btn--sm @if ($deposit->method_code >= 1000) detailBtn @else disabled @endif" @if ($deposit->method_code >= 1000) data-info="{{ $details }}" @endif @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                <i class="fa fa-desktop"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($deposits->hasPages())
            <div class="card-footer">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>


    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="mb-2 list-group userData list-group-flush">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
            </div></div></div></div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";

            let width = $(window).width()
            $('.detailBtn').on('click', function() {

                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
