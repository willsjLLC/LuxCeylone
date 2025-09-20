@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Advertisement')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('price')</th>
                                    <th>@lang('Is Boosted')</th>
                                    <th>@lang('Posted Date')</th>
                                    <th>@lang('Expiry Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advertisements as $advertisement)
                                    <tr>
                                        <td> <strong>{{ __($advertisement->advertisement_code) }}</strong> <br>
                                            {{ strLimit($advertisement->title, 50) }}
                                        </td>
                                        <td>
                                            @if ($advertisement->account_type == Status::PRO_ACCOUNT)
                                                <span class="fw-bold">{{ @$advertisement->user->username }}</span>
                                            @else
                                                <span class="fw-bold">{{ @$advertisement->user['username'] }}</span>
                                            @endif
                                            <br>
                                                    @if ($advertisement->account_type == Status::LITE_ACCOUNT)
                                                        <span class="badge badge--warning">Lite Account</span>
                                                    @else
                                                        <span class="badge badge--primary">Pro Account</span>
                                                    @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge
                                                        @if ($advertisement->status == Status::AD_PENDING) badge--dark
                                                        @elseif($advertisement->status == Status::AD_APPROVED)
                                                            badge--success
                                                        @elseif($advertisement->status == Status::AD_COMPLETED)
                                                            badge--success
                                                        @elseif($advertisement->status == Status::AD_PAUSE)
                                                            badge--warning
                                                        @elseif($advertisement->status == Status::AD_ONGOING)
                                                            badge--info
                                                        @elseif($advertisement->status == Status::AD_REJECTED)
                                                            badge--danger
                                                        @elseif($advertisement->status == Status::AD_EXPIRED)
                                                            badge--danger
                                                        @elseif($advertisement->status == Status::AD_CANCELED)
                                                            badge--danger
                                                        @else
                                                            badge--secondary @endif
                                                    ">
                                                @if ($advertisement->status == Status::AD_PENDING)
                                                    @lang('Pending')
                                                @elseif($advertisement->status == Status::AD_APPROVED)
                                                    @lang('Approved')
                                                @elseif($advertisement->status == Status::AD_COMPLETED)
                                                    @lang('Completed')
                                                @elseif($advertisement->status == Status::AD_PAUSE)
                                                    @lang('Paused')
                                                @elseif($advertisement->status == Status::AD_ONGOING)
                                                    @lang('Ongoing')
                                                @elseif($advertisement->status == Status::AD_REJECTED)
                                                    @lang('Rejected')
                                                @elseif($advertisement->status == Status::AD_EXPIRED)
                                                    @lang('Expired')
                                                @elseif($advertisement->status == Status::AD_CANCELED)
                                                    @lang('Canceled')
                                                @else
                                                    @lang('Unknown')
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ showAmount($advertisement->price) }}</td>
                                        <td>
                                            @if ($advertisement->is_boosted == Status::ADVERTISEMENT_BOOSTED)
                                                <span class="badge badge--success">@lang('Yes')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="d-block"> {{ showDateTime($advertisement->posted_date) }}</span>
                                            {{ diffForHumans($advertisement->posted_date) }}
                                        </td>
                                        <td>
                                            <span class="d-block"> {{ showDateTime($advertisement->expiry_date) }}</span>
                                            {{ diffForHumans($advertisement->expiry_date) }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap justify-content-end">
                                                <a href="{{ route('admin.ads.view', $advertisement->id) }}"
                                                    class="btn btn-sm btn-outline--success">
                                                    <i class="las la-eye "></i>@lang('view')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($advertisements->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($advertisements) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Seach here..." />
@endpush
