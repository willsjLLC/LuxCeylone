@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
<div class="container">
    <div class="row">
        <div >
            <a href="{{ route('user.wallet') }}" class="text-dark me-3">
                <i class="mt-10 mb-4 fa-solid fa-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mobile-view-margin">Transactions History</h3>
        </div>
      
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
            <div class="mb-3 d-flex justify-content-end table-form">
                <div class="input-group table_data_search">
                    <input type="text" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
                    <button class="text-white input-group-text btn btn--base ">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>
        </form>


        <table class="table transaction__table">
            <thead>
                <tr>
                    <th>@lang('Gateway | Transaction')</th>
                    <th class="text-center">@lang('Initiated')</th>
                    <th class="text-center">@lang('Amount')</th>
                    <th class="text-center">@lang('Conversion')</th>
                    <th class="text-center">@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>

                @forelse($withdraws as $withdraw)
                    <tr>
                        <td>
                            <div class="text--end">
                                <span class="fw-bold"><span class="text--primary">
                                        {{ __(@$withdraw->method->name) }}</span></span>
                                <br>
                                <small>{{ $withdraw->trx }}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text--end">
                                {{ showDateTime($withdraw->created_at) }} <br>
                                {{ diffForHumans($withdraw->created_at) }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text--end">
                                {{ showAmount($withdraw->amount) }} - <span class="text--danger" {{ showAmount($withdraw->amount) }} - <span class="text--danger" title="@lang('charge')">{{ showAmount($withdraw->charge) }}
                                </span>
                                <br>
                                <strong title="@lang('Amount after charge')">
                                    {{ showAmount($withdraw->amount - $withdraw->charge) }}

                                </strong>
                            </div>

                        </td>
                        <td class="text-center">
                            <div class="text--end">
                                1 {{ __(gs("cur_text")) }} = {{ showAmount($withdraw->rate) }}
                                {{ __($withdraw->currency) }}
                                <br>
                                <strong>{{ showAmount($withdraw->final_amount) }}
                                    {{ __($withdraw->currency) }}</strong>
                            </div>
                        </td>
                        <td class="text-center">
                            @php echo $withdraw->statusBadge @endphp
                        </td>
                        <td>
                            <button class="btn btn-sm btn--base btn--sm detailBtn" data-user_data="{{ json_encode($withdraw->withdraw_information) }}" @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                                <i class="la la-desktop"></i>
                            </button>
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
        @if ($withdraws->hasPages())
            <div class="justify-content-center">
                {{ paginateLinks($withdraws) }}
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
                    <ul class="list-group userData list-group-flush">

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
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    }
                });
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
