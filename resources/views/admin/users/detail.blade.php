@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4">

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="7" link="{{ route('admin.report.transaction', $user->id) }}" title="Balance"
                        icon="las la-money-bill-wave-alt" value="{{ showAmount($user->balance) }}" bg="indigo"
                        type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="7" link="{{ route('admin.deposit.list', $user->id) }}" title="Deposits"
                        icon="las la-wallet" value="{{ showAmount($totalDeposit) }}" bg="8" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="7" link="{{ route('admin.withdraw.data.all', $user->id) }}" title="Withdrawals"
                        icon="la la-bank" value="{{ showAmount($totalWithdrawals) }}" bg="6" type="2" />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget style="7" link="{{ route('admin.report.transaction', $user->id) }}" title="Transactions"
                        icon="las la-exchange-alt" value="{{ $totalTransaction }}" bg="17" type="2" />
                </div>

            </div>

            {{-- balance --}}
            <div class="d-flex flex-wrap gap-3 mt-4">
                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal"
                        class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                        <i class="las la-plus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal"
                        class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                        <i class="las la-minus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.report.login.history') }}?search={{ $user->username }}"
                        class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.users.notification.log', $user->id) }}"
                        class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>

                @if ($user->kyc_data)
                    <div class="flex-fill">
                        <a href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank"
                            class="btn btn--dark btn--shadow w-100 btn-lg">
                            <i class="las la-user-check"></i>@lang('KYC Data')
                        </a>
                    </div>
                @endif

                <div class="flex-fill">
                    @if ($user->status == Status::USER_ACTIVE)
                        <button type="button" class="btn btn--warning btn--shadow w-100 btn-lg userStatus"
                            data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="las la-ban"></i>@lang('Ban User')
                        </button>
                    @else
                        <button type="button" class="btn btn--success btn--shadow w-100 btn-lg userStatus"
                            data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="las la-undo"></i>@lang('Unban User')
                        </button>
                    @endif
                </div>
            </div>

            {{-- bonuses --}}
            @if (isset($user->customerBonuses) ||
                    isset($user->leaderBonuses) ||
                    isset($user->topLeaderBonuses) ||
                    isset($companySaving))
                <div class="card mt-30">
                    <div class="card-body">

                        @if (isset($companySaving) && $user->username == 'luxceylone')
                            <div class="row">
                                <div class="col-xl-6 col-12">
                                    <h5 class="card-title mb-0">
                                        @lang('Company Savings')

                                    </h5>
                                </div>

                                <hr>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.company.saving.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Company Saving')</label>
                                            <input class="form-control" type="text" name="balance"
                                                value="{{ $companySaving }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if (isset($user->customerBonuses))
                            <div class="row">
                                <div class="col-xl-6 col-12">
                                    <h5 class="card-title mb-0">
                                        @lang('Customer Bonus & Commissions')

                                    </h5>
                                </div>

                                <hr>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.customer.voucher.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Voucher')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->customerBonuses->voucher_balance }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.customer.festival.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">

                                            <label>@lang('Festival Bonus')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->customerBonuses->festival_bonus_balance }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.customer.saving.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Saving')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->customerBonuses->saving }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        @endif

                        @if (isset($user->leaderBonuses))
                            <div class="row">
                                <div class="col-xl-6 col-12">
                                    <h5 class="card-title mb-0">
                                        @lang('Leader Bonus & Commissions')

                                    </h5>
                                </div>

                                <hr>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.leader.leasing.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Vehicle')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->leaderBonuses->leasing_amount }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.leader.petrol.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Petrol')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->leaderBonuses->petrol_allowance }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.leader.referral.calculate', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Total Users')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->leaderBonuses->total_users }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Re Calculate')
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if (isset($user->topLeaderBonuses))
                            <div class="row">
                                <div class="col-xl-6 col-12">
                                    <h5 class="card-title mb-0">
                                        @lang('Top Leader Bonus & Commissions')
                                    </h5>
                                </div>

                                <hr>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.topLeader.car.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Car')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->topLeaderBonuses->for_car }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.topLeader.house.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('House')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->topLeaderBonuses->for_house }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-4">
                                    <form action="{{ route('admin.users.topLeader.expenses.transfer', [$user->id]) }}"
                                        method="get">
                                        <div class="form-group">
                                            <label>@lang('Expenses')</label>
                                            <input class="form-control" type="text" name="firstname"
                                                value="{{ $user->topLeaderBonuses->for_expenses }}" readonly>

                                            <button type="submit"
                                                class="btn btn--primary w-100 h-45 mt-2">@lang('Transfer')
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        @endif
                        {{-- </form> --}}
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="card mt-30">

                    <div class="card-body">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <h5 class="card-title mb-0">@lang('Information of') {{ $user->fullname }}</h5>
                            </div>

                            @if ($user->username != 'luxceylone')
                                @if ($user->role == Status::LEADER || ($user->is_top_leader == Status::TOP_LEADER && $user->role != Status::CUSTOMER))
                                    <div class="col-xl-3 col-12 mb-2">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <label class="ms-2 mb-0 pe-2">@lang('Roles')</label>

                                            <input type="checkbox" data-width="70%" data-height="50"
                                                data-onstyle="-warning" data-offstyle="-primary" data-bs-toggle="toggle"
                                                data-on="@lang('Leader')" data-off="@lang('Customer')" name="role"
                                                class="me-1" style="padding: 10px"
                                                @if ($user->role == Status::LEADER) checked @endif>

                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-12 mb-2">
                                        <div class="d-flex justify-content-end align-items-center">
                                            {{-- <label class="ms-2 mb-0 pe-2">@lang('Roles')</label> --}}

                                            <input type="checkbox" data-width="100%" data-height="50"
                                                data-onstyle="-warning" data-offstyle="-primary" data-bs-toggle="toggle"
                                                data-on="@lang('Top Leader')" data-off="@lang('Normal Leader')"
                                                name="is_top_leader" class="ms-1"
                                                @if ($user->is_top_leader == Status::TOP_LEADER) checked @endif>
                                        </div>
                                    </div>
                                @elseif($user->role != Status::LEADER || ($user->is_top_leader != Status::TOP_LEADER && $user->role == Status::CUSTOMER))
                                    <div class="col-xl-6 col-12 mb-2">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <label class="ms-2 mb-0 pe-2">@lang('Role')</label>

                                            <input type="checkbox" data-width="25%" data-height="50"
                                                data-onstyle="-warning" data-offstyle="-primary" data-bs-toggle="toggle"
                                                data-on="@lang('Leader')" data-off="@lang('Customer')" name="role"
                                                class="me-1" style="padding: 10px"
                                                @if ($user->role == Status::LEADER) checked @endif>

                                        </div>
                                    </div>
                                @endif
                            @endif
                            <hr>
                            @php
                                $kyc = getContent('kyc.content', true);
                                $userKycStatus = $user->kv;
                                $kycCollection = collect($user->kyc_data);
                                $kycByName = $kycCollection->keyBy('name');
                                $address = $kycByName->get('Address')->value ?? '';
                                $state = $kycByName->get('State')->value ?? '';
                            @endphp
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required
                                        value="{{ $user->firstname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required
                                        value="{{ $user->lastname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') </label>
                                    <input class="form-control" type="email" name="email"
                                        value="{{ $user->email }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+{{ $user->dial_code }}</span>
                                        <input type="number" name="mobile" value="{{ $user->mobile }}" id="mobile"
                                            class="form-control checkUser" >
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    @if ($userKycStatus == Status::KYC_VERIFIED)
                                        <input class="form-control" type="text" name="address"
                                            value="{{ $address }}">
                                    @else
                                        <input class="form-control" type="text" name="address"
                                            value="{{ @$address }}">
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city"
                                        value="{{ @$user->city }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    @if ($userKycStatus == Status::KYC_VERIFIED)
                                        <input class="form-control" type="text" name="state"
                                            value="{{ $state }}">
                                    @else
                                        <input class="form-control" type="text" name="state"
                                            value="{{ @$state }}">
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip"
                                        value="{{ @$user->zip }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country') <span class="text--danger">*</span></label>
                                    <select name="country" class="form-control select2">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}"
                                                value="{{ $key }}" @selected($user->country_code == $key)>
                                                {{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Email Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                        data-off="@lang('Unverified')" name="ev"
                                        @if ($user->ev) checked @endif>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Mobile Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                        data-off="@lang('Unverified')" name="sv"
                                        @if ($user->sv) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('2FA Verification') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')"
                                        data-off="@lang('Disable')" name="ts"
                                        @if ($user->ts) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('KYC') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                        data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                        data-off="@lang('Unverified')" name="kv"
                                        @if ($user->kv == Status::KYC_VERIFIED) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                        {{-- </form> --}}
                    </div>
                </div>

            </form>
        </div>
    </div>



    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.add.sub.balance', $user->id) }}"
                    class="balanceAddSub disableSubmission" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control"
                                    placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($user->status == Status::USER_ACTIVE)
                            @lang('Ban User')
                        @else
                            @lang('Unban User')
                        @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($user->status == Status::USER_ACTIVE)
                            <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</h6>
                            <div class="form-group">
                                <label>@lang('Reason')</label>
                                <textarea class="form-control" name="reason" rows="4" required></textarea>
                            </div>
                        @else
                            <p><span>@lang('Ban reason was'):</span></p>
                            <p>{{ $user->ban_reason }}</p>
                            <h4 class="text-center mt-3">@lang('Are you sure to unban this user?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($user->status == Status::USER_ACTIVE)
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        @else
                            <button type="button" class="btn btn--dark"
                                data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transfer Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">@lang('Confirm Transfer')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Would you like to transfer') <strong id="transferAmount"></strong> @lang('from') <strong
                            id="transferType"></strong> @lang('to user\'s main account balance?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="button" class="btn btn--primary" id="confirmTransferBtn">@lang('Confirm Transfer')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recalculation Confirmation Modal -->
    <div class="modal fade" id="recalculateModal" tabindex="-1" aria-labelledby="recalculateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recalculateModalLabel">@lang('Confirm Recalculation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Do you want to recalculate leader referral users?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="button" class="btn btn--primary"
                        id="confirmRecalculateBtn">@lang('Confirm')</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.users.secondOwner', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--success"><i
            class="las la-user-tie"></i>@lang('Second Owner Details')</a>

    <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--primary"><i
            class="las la-sign-in-alt"></i>@lang('Login as User')</a>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict"

            $('.bal-btn').on('click', function() {
                $('.balanceAddSub')[0].reset();
                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });

            let mobileElement = $('.mobile-code');
            $('select[name=country]').on('change', function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            // Store the current form to submit
            let currentForm = null;

            // Handle transfer buttons
            $('form[action*="transfer"] [type="submit"]').on('click', function(e) {
                e.preventDefault();
                currentForm = $(this).closest('form');

                // Get the amount and type from the form
                const amount = currentForm.find('input[readonly]').val();
                let type = '';

                // Determine transfer type based on URL
                if (currentForm.attr('action').includes('voucher')) {
                    type = 'Voucher';
                } else if (currentForm.attr('action').includes('festival')) {
                    type = 'Festival Bonus';
                } else if (currentForm.attr('action').includes('saving')) {
                    type = 'Saving';
                } else if (currentForm.attr('action').includes('leasing')) {
                    type = 'Vehicle Allowance';
                } else if (currentForm.attr('action').includes('petrol')) {
                    type = 'Petrol Allowance';
                } else if (currentForm.attr('action').includes('car')) {
                    type = 'Car Bonus';
                } else if (currentForm.attr('action').includes('house')) {
                    type = 'House Bonus';
                } else if (currentForm.attr('action').includes('expenses')) {
                    type = 'Expenses Allowance';
                }

                // Update modal content
                $('#transferAmount').text(amount);
                $('#transferType').text(type);

                // Show the transfer confirmation modal
                $('#confirmationModal').modal('show');
            });

            // Handle recalculate button separately
            $('form[action*="referral.calculate"] [type="submit"]').on('click', function(e) {
                e.preventDefault();
                currentForm = $(this).closest('form');
                $('#recalculateModal').modal('show');
            });

            // Handle transfer confirmation
            $('#confirmTransferBtn').on('click', function() {
                if (currentForm) {
                    $('#confirmationModal').modal('hide');
                    currentForm.submit();
                }
            });

            // Handle recalculation confirmation
            $('#confirmRecalculateBtn').on('click', function() {
                if (currentForm) {
                    $('#recalculateModal').modal('hide');
                    currentForm.submit();
                }
            });
        })(jQuery);
    </script>
@endpush
