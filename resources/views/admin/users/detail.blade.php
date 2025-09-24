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

    {{-- Referral Hierarchy Modal --}}
    <div id="hierarchyModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @lang('Referral Hierarchy') - <span id="hierarchyUserName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="hierarchyLoader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                        <p class="mt-3">@lang('Loading hierarchy...')</p>
                    </div>
                    <div id="hierarchyContent" style="display: none;">
                        <div class="hierarchy-stats mb-3">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <h4 id="totalUsers" class="text-primary">0</h4>
                                        <small>@lang('Total Users')</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <h4 id="totalLevels" class="text-success">0</h4>
                                        <small>@lang('Total Levels')</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stats-card">
                                        <h4 id="activeUsers" class="text-info">0</h4>
                                        <small>@lang('Active Users')</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="hierarchy-container">
                            <div id="hierarchyTree"></div>
                        </div>
                    </div>
                    <div id="hierarchyError" class="alert alert-danger" style="display: none;">
                        <i class="las la-exclamation-triangle"></i>
                        <strong>@lang('Error!')</strong> @lang('Failed to load referral hierarchy. Please try again.')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="button" class="btn btn-primary" id="expandAllBtn">@lang('Expand All')</button>
                    <button type="button" class="btn btn-warning" id="collapseAllBtn">@lang('Collapse All')</button>
                </div>
            </div>
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
    <div class="d-flex flex-wrap gap-3 ">

        <div class="flex-fill">
            <button type="button" class="btn btn-sm btn-outline--primary" id="viewHierarchyBtn" data-user-id="{{ $user->id }}">
                <i class="las la-sitemap"></i>@lang('View Hierarchy')
            </button>
        </div>

    </div>
    <a href="{{ route('admin.users.secondOwner', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--success"><i
            class="las la-user-tie"></i>@lang('Second Owner Details')</a>

    <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--primary"><i
            class="las la-sign-in-alt"></i>@lang('Login as User')</a>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";

             let hierarchyData = null;

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

            // Updated Hierarchy modal functionality
        $('#viewHierarchyBtn').on('click', function() {
            const userId = $(this).data('user-id');
            console.log('Loading hierarchy for user ID:', userId); // Debug log
            $('#hierarchyModal').modal('show');
            loadHierarchy(userId);
        });

        function loadHierarchy(userId) {
            console.log('Starting to load hierarchy...'); // Debug log
            $('#hierarchyLoader').show();
            $('#hierarchyContent').hide();
            $('#hierarchyError').hide();

            // Show loading state
            $('#hierarchyUserName').text('Loading...');

            $.ajax({
                url: `{{ route('admin.users.referral.hierarchy', '') }}/${userId}`
                , method: 'GET'
                , timeout: 30000, // 30 seconds timeout
                beforeSend: function() {
                    console.log('Sending AJAX request...'); // Debug log
                }
                , success: function(response) {
                    console.log('AJAX Success:', response); // Debug log
                    $('#hierarchyLoader').hide();

                    if (response && response.success) {
                        hierarchyData = response;
                        $('#hierarchyUserName').text(response.user.name + ' (' + response.user.username + ')');
                        renderHierarchy(response.user, response.hierarchy);
                        calculateStats(response.hierarchy);
                        $('#hierarchyContent').show();
                    } else {
                        console.error('Invalid response:', response); // Debug log
                        showError(response.message || 'Invalid response from server');
                    }
                }
                , error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr, status, error); // Debug log
                    $('#hierarchyLoader').hide();

                    let errorMessage = 'Failed to load referral hierarchy';

                    if (xhr.status === 404) {
                        errorMessage = 'Hierarchy endpoint not found. Please check the route configuration.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please check the server logs.';
                    } else if (status === 'timeout') {
                        errorMessage = 'Request timeout. Please try again.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (error) {
                        errorMessage += ': ' + error;
                    }

                    showError(errorMessage);
                }
            });
        }

        function showError(message) {
            $('#hierarchyError').show();
            $('#hierarchyError').find('p').text(message);
            console.error('Hierarchy Error:', message); // Debug log
        }

        function calculateStats(hierarchy) {
            let totalUsers = 0;
            let maxLevel = 0;
            let activeUsers = 0;
            let bannedUsers = 0;

            function countUsers(nodes, level) {
                if (!nodes || !Array.isArray(nodes)) return;

                nodes.forEach(node => {
                    totalUsers++;
                    maxLevel = Math.max(maxLevel, level);
                    if (node.status == 1) {
                        activeUsers++;
                    } else {
                        bannedUsers++;
                    }

                    if (node.children && Array.isArray(node.children) && node.children.length > 0) {
                        countUsers(node.children, level + 1);
                    }
                });
            }

            if (hierarchy && Array.isArray(hierarchy) && hierarchy.length > 0) {
                countUsers(hierarchy, 1);
            }

            // Update stats display
            $('#totalUsers').text(totalUsers);
            $('#totalLevels').text(maxLevel);
            $('#activeUsers').text(activeUsers);
            $('#bannedUsers').text(bannedUsers);
        }

        function renderHierarchy(rootUser, hierarchy) {
            if (!rootUser) {
                $('#hierarchyTree').html('<div class="no-referrals">Invalid user data</div>');
                return;
            }

            let html = `
            <div class="hierarchy-node root">
                <div class="level-badge">ROOT</div>
                <div class="user-info">
                    <div class="user-details">
                        <h6>${rootUser.name || 'Unknown'}</h6>
                        <small>@${rootUser.username || 'unknown'} | ${rootUser.email || 'no-email'}</small>
                    </div>
                    <div class="status-badge status-active">Root User</div>
                </div>
                <div class="user-stats">
                    <span><strong>Direct Referrals:</strong> ${hierarchy ? hierarchy.length : 0}</span>
                </div>
            </div>
        `;

            if (hierarchy && Array.isArray(hierarchy) && hierarchy.length > 0) {
                html += renderChildren(hierarchy);
            } else {
                html += `
                <div class="no-referrals">
                    <i class="las la-users la-3x mb-3 d-block"></i>
                    <strong sty>No referrals found</strong>
                    <p>This user hasn't referred anyone yet.</p>
                </div>
            `;
            }

            $('#hierarchyTree').html(html);
            attachEventHandlers();
        }

        function renderChildren(children, level = 1) {
            if (!children || !Array.isArray(children) || children.length === 0) {
                return '';
            }

            let html = '';

            children.forEach(function(child, index) {
                if (!child) return;

                const statusClass = child.status == 1 ? 'status-active' : 'status-inactive';
                const statusText = child.status == 1 ? 'Active' : 'Banned';
                const hasChildren = child.children && Array.isArray(child.children) && child.children.length > 0;
                const nodeId = `node-${child.id || index}-${level}`;

                html += `
                <div class="hierarchy-node level-${level > 5 ? 5 : level}" data-node-id="${nodeId}">
                    <div class="level-badge">L${level}</div>
                    <div class="user-info">
                        <div class="user-details">
                            <h5 style="font-weight: bold; color: #080808ff;">${child.name || 'Unknown User'}</h5>
                            <small style="font-weight: bold; color: #05078dff;">@${child.username || 'unknown'} | ${child.email || 'no-email'}</small>
                        </div>
                        <div class="status-badge ${statusClass}">${statusText}</div>
                    </div>
                    <div class="user-stats">
                        <span><strong>Joined:</strong> ${child.joined_date || 'Unknown'}</span>
                        <span><strong>Balance:</strong> ${child.balance || '0.00'}</span>
                        <span><strong>Direct Referrals:</strong> ${child.direct_referrals || 0}</span>
                        <span><strong>Level:</strong> ${level}</span>
                    </div>
            `;

                if (hasChildren) {
                    html += `
                    <button class="toggle-children" data-target="${nodeId}">
                        <i class="las la-plus"></i> Show ${child.children.length} Referral${child.children.length !== 1 ? 's' : ''}
                    </button>
                    <div class="children-container" id="children-${nodeId}" style="display: none;">
                        ${renderChildren(child.children, level + 1)}
                    </div>
                `;
                } else if ((child.direct_referrals || 0) > 0) {
                    html += '<div style="margin-top: 8px; color: #ed0f08ff; font-size: 11px; "><i class="las la-info-circle"></i> Has referrals (not displayed due to depth limit)</div>';
                } else {
                    html += '<div style="margin-top: 8px; color: #e61c05ff; font-size: 11px; "><i class="las la-user-times"></i> No referrals</div>';
                }

                html += '</div>';
            });

            return html;
        }

        function attachEventHandlers() {
            $('.toggle-children').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const targetId = $(this).data('target');
                const container = $(`#children-${targetId}`);
                const isVisible = container.is(':visible');

                if (isVisible) {
                    container.slideUp(300);
                    $(this).html('<i class="las la-plus"></i> Show Referrals');
                } else {
                    container.slideDown(300);
                    $(this).html('<i class="las la-minus"></i> Hide Referrals');
                }
            });
        }

        // Expand All functionality
        $('#expandAllBtn').on('click', function() {
            $('.children-container').slideDown(300);
            $('.toggle-children').html('<i class="las la-minus"></i> Hide Referrals');
        });

        // Collapse All functionality
        $('#collapseAllBtn').on('click', function() {
            $('.children-container').slideUp(300);
            $('.toggle-children').html('<i class="las la-plus"></i> Show Referrals');
        });

        // Reset modal on close
        $('#hierarchyModal').on('hidden.bs.modal', function() {
            hierarchyData = null;
            $('#hierarchyTree').empty();
            $('#hierarchyLoader').show();
            $('#hierarchyContent').hide();
            $('#hierarchyError').hide();
            $('#hierarchyUserName').text('');

            // Reset stats
            $('#totalUsers, #totalLevels, #activeUsers, #bannedUsers').text('0');
        });

        // Add error handling for missing route
        if (typeof window.hierarchyRoute === 'undefined') {
            console.warn('Hierarchy route not defined. Make sure the route is properly configured.');
        }

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


@push('style')
<style>
    .hierarchy-container {
        max-height: 60vh;
        overflow-y: auto;
        overflow-x: auto;
    }

    .stats-card {
        padding: 15px;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 10px;
    }

    .hierarchy-tree {
        font-family: Arial, sans-serif;
    }

    .hierarchy-node {
        margin: 8px 0;
        padding: 12px 15px;
        border: 1px solid #e3e6f0;
        border-radius: 6px;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
        transition: all 0.3s ease;
    }

    .hierarchy-node:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
    }

    .hierarchy-node.root {
        border: 2px solid #4e73df;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }

    .hierarchy-node.root .user-details h6,
    .hierarchy-node.root .user-details small {
        color: white;
    }

    .hierarchy-node.level-1 {
        border-left: 4px solid #1cc88a;
        border-right: 1px solid #1cc88a;
        border-bottom: 1px solid #1cc88a;
        border-top: 1px solid #1cc88a;
        background-color: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    }

    .hierarchy-node.level-2 {
        border-left: 4px solid #36b9cc;
        border-right: 1px solid #36b9cc;
        border-bottom: 1px solid #36b9cc;
        border-top: 1px solid #36b9cc;
        background-color:linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
    }

    .hierarchy-node.level-3 {
        border-left: 4px solid #f6c23e;
        border-right: 1px solid #f6c23e;
        border-bottom: 1px solid #f6c23e;
        border-top: 1px solid #f6c23e;
        background-color: linear-gradient(135deg, #f6c23e 0%, #d4ac0d 100%);
    }

    .hierarchy-node.level-4 {
        border-left: 4px solid #e74a3b;
        border-right: 1px solid #e74a3b;
        border-bottom: 1px solid #e74a3b;
        border-top: 1px solid #e74a3b;
        background-color:linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
    }

    .hierarchy-node.level-5 {
        border-left: 4px solid #6f42c1;
        border-right: 1px solid #6f42c1;
        border-bottom: 1px solid #6f42c1;
        border-top: 1px solid #6f42c1;
        background-color: linear-gradient(135deg, #6f42c1 0%, #5e35b1 100%);
    }

     .hierarchy-node.level-6 {
        border-left: 4px solid #abc42cff;
        border-right: 1px solid #abc42cff;
        border-bottom: 1px solid #abc42cff;
        border-top: 1px solid #abc42cff;
        background-color: linear-gradient(135deg, #abc42cff 0%, #9a34b1 100%);
    }

     .hierarchy-node.level-7 {
        border-left: 4px solid #4642c1ff;
        border-right: 1px solid #4642c1ff;
        border-bottom: 1px solid #4642c1ff;
        border-top: 1px solid #4642c1ff;
        background-color: linear-gradient(135deg, #4642c1ff 0%, #3535b1 100%);
    }


    .user-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .user-details h6 {
        margin: 0;
        color: #5a5c69;
        font-weight: 600;
        font-size: 14px;
    }

    .user-details small {
        color: #858796;
        font-size: 12px;
    }

    .user-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 11px;
        color: #858796;
        margin-top: 5px;
    }

    .user-stats span {
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .level-badge {
        position: absolute;
        top: -6px;
        right: 8px;
        background: #4e73df;
        color: white;
        padding: 2px 6px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 600;
    }

    .children-container {
        margin-left: 15px;
        margin-top: 10px;
        border-left: 2px dashed #dee2e6;
        padding-left: 15px;
    }

    .toggle-children {
        background: #4e73df;
        color: white;
        border: none;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 11px;
        cursor: pointer;
        margin-top: 8px;
        transition: all 0.3s ease;
    }

    .toggle-children:hover {
        background: #2e59d9;
        transform: scale(1.05);
    }

    .no-referrals {
        text-align: center;
        color: #858796;
        padding: 40px 20px;
        font-style: italic;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }

    .balance-info {
        color: #28a745;
        font-weight: 600;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .user-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .user-stats {
            font-size: 10px;
            gap: 8px;
        }

        .children-container {
            margin-left: 8px;
            padding-left: 8px;
        }
    }

</style>
@endpush
