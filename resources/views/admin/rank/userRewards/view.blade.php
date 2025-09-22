@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">@lang('User Rank Reward Details')</h5>
                        {{-- <div class="badge badge--primary">@lang('User ID: ') {{ $rankReward->user_id }}</div> --}}
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light border-0 text-dark">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">@lang('General Information')</h6>
                                    <div class="row">

                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Username')</small>
                                            <div class="fw-bold">{{ $rankReward->user->username ?? 'N/A' }}</div>
                                        </div>

                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Fullname')</small>
                                            <div class="fw-bold">{{ $rankReward->user->fullname ?? 'N/A' }}</div>
                                        </div>

                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Mobile')</small>
                                            <div class="fw-bold">{{ $rankReward->user->mobile ?? 'N/A' }}</div>
                                        </div>

                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Address')</small>
                                            <div class="fw-bold">{{ $rankReward->user->address->address ?? 'N/A' }}</div>
                                        </div>

                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Email')</small>
                                            <div class="fw-bold">{{ $rankReward->user->email ?? 'N/A' }}</div>
                                        </div>


                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Current Rank')</small>
                                            <div class="fw-bold">
                                                @if ($rankReward->rank)
                                                    ({{ $rankReward->rank->rank }}) {{ $rankReward->rank->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Stars')</small>

                                            <div class="fw-bold">
                                                @if ($rankReward->rank)
                                                    @for ($i = 0; $i < $rankReward->rank->rank; $i++)
                                                        <i class="fas fa-star text-warning me-2"></i>
                                                    @endfor
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-3 mb-3">
                                            <small class="text-muted">@lang('Last Updated')</small>
                                            <div class="fw-bold">{{ showDateTime($rankReward->updated_at) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="rankStatusForm" action="{{ route('admin.ranks.user.rewards.update', $rankReward->id) }}"
                        method="POST">
                        @csrf
                        @method('post')

                        <input type="hidden" name="id" value="{{ $rankReward->id }}">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">@lang('Rank Status Management')</h5>
                        </div>

                        <div class="row">
                            @php
                                // Constants for rank claim statuses (using variables to avoid const conflicts)
                                $RANK_CLAIM_NOT_SATISFIED = 0;
                                $RANK_CLAIM_PENDING = 1;
                                $RANK_CLAIM_PROCESSING = 2;
                                $RANK_CLAIM_COMPLETED = 3;
                                $RANK_CLAIM_CANCELED = 4;

                                // Mapping for rank numbers to their string representations
                                $rankLevelMap = [
                                    1 => 'one',
                                    2 => 'two',
                                    3 => 'three',
                                    4 => 'four'
                                ];

                                // Status options for the select dropdown
                                $claimStatusOptions = [
                                    $RANK_CLAIM_NOT_SATISFIED => 'Not Satisfied',
                                    $RANK_CLAIM_PENDING => 'Pending (Satisfied)',
                                    $RANK_CLAIM_PROCESSING => 'Processing',
                                    $RANK_CLAIM_COMPLETED => 'Completed',
                                    $RANK_CLAIM_CANCELED => 'Canceled',
                                ];

                                // Badge arrays to replace functions (fixes redeclaration error)
                                $rankStatusBadges = [
                                    0 => '<span class="badge badge--warning"><i class="las la-clock"></i> Pending</span>',
                                    1 => '<span class="badge badge--success"><i class="las la-check"></i> Achieved</span>',
                                ];

                                $claimStatusBadges = [
                                    $RANK_CLAIM_NOT_SATISFIED =>
                                        '<span class="badge badge--danger"><i class="las la-times"></i> Not Satisfied</span>',
                                    $RANK_CLAIM_PENDING =>
                                        '<span class="badge badge--info"><i class="las la-hourglass-half"></i> Pending</span>',
                                    $RANK_CLAIM_PROCESSING =>
                                        '<span class="badge badge--primary"><i class="las la-spinner"></i> Processing</span>',
                                    $RANK_CLAIM_COMPLETED =>
                                        '<span class="badge badge--success"><i class="las la-check-circle"></i> Completed</span>',
                                    $RANK_CLAIM_CANCELED =>
                                        '<span class="badge badge--dark"><i class="las la-ban"></i> Canceled</span>',
                                ];
                            @endphp

                            @for ($i = 1; $i <= 7; $i++)
                                @php
                                    $rankName = $rankLevelMap[$i];
                                    $statusField = 'rank_' . $rankName . '_status';
                                    $claimedStatusField = 'rank_' . $rankName . '_claimed_status';
                                    $rankStatus = $rankReward->$statusField;
                                    $rankClaimedStatus = $rankReward->$claimedStatusField;
                                @endphp
                                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card custom--card b-radius--10 box--shadow1 h-100 rank-card"
                                        data-rank="{{ $i }}">
                                        <div class="card-header bg-gradient-primary text-white text-center py-3">
                                            <h6 class="card-title mb-0 text-light">
                                                <i class="las la-trophy text-light"></i> @lang('Rank ')
                                                {{ $i }}
                                            </h6>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small">@lang('Achievement Status')</label>
                                                <div>{!! $rankStatusBadges[$rankStatus] ??
                                                    '<span class="badge badge--dark"><i class="las la-question"></i> Unknown</span>' !!}</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label text-muted small">@lang('Current Claim Status')</label>
                                                <div class="current-status-display mb-2">
                                                    {!! $claimStatusBadges[$rankClaimedStatus] ??
                                                        '<span class="badge badge--dark"><i class="las la-question"></i> Unknown</span>' !!}
                                                </div>
                                            </div>

                                            <div class="mt-auto">
                                                <label for="rank_{{ $rankName }}_claimed_status"
                                                    class="form-label small fw-bold">
                                                    @lang('Update Claim Status')
                                                </label>
                                                <select name="rank_{{ $rankName }}_claimed_status"
                                                    id="rank_{{ $rankName }}_claimed_status"
                                                    class="form-select form-select-sm rank-status-select"
                                                    data-original-value="{{ $rankClaimedStatus }}"
                                                    data-rank="{{ $i }}">
                                                    @foreach ($claimStatusOptions as $value => $label)
                                                        <option value="{{ $value }}"
                                                            {{ $rankClaimedStatus == $value ? 'selected' : '' }}
                                                            data-badge-class="{{ $value == $RANK_CLAIM_NOT_SATISFIED
                                                                ? 'badge--danger'
                                                                : ($value == $RANK_CLAIM_PENDING
                                                                    ? 'badge--info'
                                                                    : ($value == $RANK_CLAIM_PROCESSING
                                                                        ? 'badge--primary'
                                                                        : ($value == $RANK_CLAIM_COMPLETED
                                                                            ? 'badge--success'
                                                                            : 'badge--dark'))) }}">
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">@lang('Bulk Actions')</h6>
                                                <small class="text-muted">@lang('Apply status changes to multiple ranks at once')</small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn--primary" id="saveButton">
                                                    <i class="las la-save"></i> @lang('Save Changes')
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .rank-card {
            transition: all 0.3s ease;
        }

        .rank-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .rank-card.changed {
            border-left: 4px solid #007bff;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .current-status-display {
            min-height: 28px;
        }

        .form-select-sm {
            font-size: 0.875rem;
        }

        .badge i {
            margin-right: 2px;
        }

        #saveButton {
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .changed-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .changed-indicator.show {
            opacity: 1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('rankStatusForm');
            const saveButton = document.getElementById('saveButton');
            const selects = document.querySelectorAll('.rank-status-select');
            let hasChanges = false; // Initialize to false

            // Function to update the card appearance (border and indicator)
            function updateCardAppearance(select) {
                const card = select.closest('.rank-card');
                const originalValue = select.dataset.originalValue;
                const currentValue = select.value;

                if (originalValue !== currentValue) {
                    card.classList.add('changed');
                    let indicator = card.querySelector('.changed-indicator');
                    if (!indicator) {
                        indicator = document.createElement('div');
                        indicator.className = 'changed-indicator';
                        indicator.innerHTML = '<i class="las la-edit"></i>';
                        card.style.position = 'relative'; // Ensure card is positioned for absolute indicator
                        card.appendChild(indicator);
                    }
                    indicator.classList.add('show');
                } else {
                    card.classList.remove('changed');
                    const indicator = card.querySelector('.changed-indicator');
                    if (indicator) {
                        indicator.classList.remove('show');
                        // Optionally remove the element after transition for cleaner DOM
                        setTimeout(() => {
                            if (!indicator.classList.contains(
                                    'show')) { // Check again in case it changed back
                                indicator.remove();
                            }
                        }, 300); // Match CSS transition duration
                    }
                }
            }

            function checkForChanges() {
                hasChanges = Array.from(selects).some(select =>
                    select.dataset.originalValue !== select.value
                );

                saveButton.disabled = !hasChanges;
                const changedCount = Array.from(selects).filter(s => s.dataset.originalValue !== s.value).length;
                saveButton.innerHTML =
                    `<i class="las la-save"></i> @lang('Save Changes') ${changedCount > 0 ? `<span class="badge badge-light ms-1">${changedCount}</span>` : ''}`;
            }

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    updateCardAppearance(this);
                    checkForChanges();
                });
            });

            saveButton.addEventListener('click', function() {
                if (hasChanges) {
                    hasChanges = false;
                    form.submit();
                }
            });

            selects.forEach(select => updateCardAppearance(select));
            checkForChanges();

            window.addEventListener('beforeunload', function(e) {
                if (hasChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>

    <x-confirmation-modal />
@endsection
