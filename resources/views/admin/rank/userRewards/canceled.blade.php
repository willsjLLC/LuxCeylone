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
                                    <th class="text-start">@lang('User')</th>
                                    <th class="text-start">@lang('Current Rank')</th>

                                    <th class="text-start">@lang('Rank 1 Status')</th>
                                    <th class="text-start">@lang('Rank 1 Claimed Status')</th>

                                    <th class="text-start">@lang('Rank 2 Status')</th>
                                    <th class="text-start">@lang('Rank 2 Claimed Status')</th>

                                    <th class="text-start">@lang('Rank 3 Status')</th>
                                    <th class="text-start">@lang('Rank 3 Claimed Status')</th>

                                    <th class="text-start">@lang('Rank 4 Status')</th>
                                    <th class="text-start">@lang('Rank 4 Claimed Status')</th>

                                    <th class="text-center">@lang('View')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rankRewards as $rankReward)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $rankReward->user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $rankReward->user->id) }}"><span>@</span>{{ $rankReward->user->username }}</a>
                                            </span>
                                        </td>
                                        <td class="text-start">
                                            ({{ $rankReward->rank?->rank }})
                                            {{ $rankReward->rank?->name }}
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_one_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_one_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_one_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_one_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_one_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_one_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_two_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_two_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_two_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_two_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_two_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_two_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_three_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_three_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_three_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_three_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_three_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_three_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_four_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_four_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_four_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_four_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_four_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_four_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_five_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_five_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_five_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_five_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_five_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_five_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_six_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_six_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_six_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_six_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_six_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_six_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_seven_status == Status::RANK_ACHIEVED)
                                                <span class="badge badge--success">@lang('Achieved')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rankReward->rank_seven_claimed_status == Status::RANK_NOT_SATISFIED)
                                                <span class="badge badge--dark">@lang('Not Satisfied')</span>
                                            @elseif($rankReward->rank_seven_claimed_status == Status::RANK_CLAIM_PENDING)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($rankReward->rank_seven_claimed_status == Status::RANK_CLAIM_PROCESSING)
                                                <span class="badge badge--primary">@lang('Processing')</span>
                                            @elseif($rankReward->rank_seven_claimed_status == Status::RANK_CLAIM_COMPLETED)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @elseif($rankReward->rank_seven_claimed_status == Status::RANK_CLAIM_CANCELED)
                                                <span class="badge badge--danger">@lang('Canceled')</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2 justify-content-end align-items-center">
                                                <a href="{{ route('admin.ranks.view.user.rewards', $rankReward->id) }}"
                                                    class="btn btn-sm btn-outline--success">
                                                    <i class="las la-eye"></i> @lang('View')
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
                @if ($rankRewards->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($rankRewards) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <div class="modal categoryModal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Category Description')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
@endpush


@push('style')
    <style>
        .product {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .product-image {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
@endpush
