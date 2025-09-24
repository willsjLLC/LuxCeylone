@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="container">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>{{ $pageTitle }}
                        </h4>

                    </div>

                    <div class="card-body">
                        <!-- Current Status -->

                        <div class="dd text-center mt-4 mb-3">
                            <p class="text">
                                <i class="fas fa-info-circle me-1"></i>
                                Rank will automatically update after complete task within 20 minutes
                                {{-- Rank will automatically update after complete task within 20 hours --}}
                            </p>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="status-card">
                                    <h6 class="text-white mb-2">Current Status</h6>
                                    <div class="d-flex align-items-center">
                                        
                                        <div>
                                            <h3 class=" mb-0" style="color: orange;">
                                                {{ $currentRank ? $currentRank->name : 'No Rank' }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-end">
                                    <button class="btn btn-outline-primary" onclick="showAllRanks()">
                                        <i class="fas fa-list me-1"></i>All Ranks
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div class="progress-section">
                            <h5 class="mb-3">Progress to Next Rank</h5>

                            <!-- Progress Bar Container -->
                            <div class="rank-progress-container">
                                <div class="progress-track">
                                    <div class="progress-ranks-row d-flex justify-content-between align-items-center">
                                        <!-- Current Rank Circle -->
                                        <div class="rank-circle current-rank">
                                            <div class="rank-icon">
                                                @if ($currentRank)
                                                    <img alt="rank-image"
                                                        src="{{ getImage('assets/admin/images/rank/' . @$currentRank->image, '400x400') }}">
                                                @else
                                                    @auth
                                                        @if (Auth::user()->image)
                                                            <img src="{{ asset('assets/images/user/profile/' . Auth::user()->image) }}"
                                                                alt="User Image"
                                                                style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                                        @else
                                                            <i class="fas fa-user"></i>
                                                        @endif
                                                    @endauth
                                                @endif
                                            </div>
                                            <div class="rank-label">
                                                {{ $currentRank ? $currentRank->name : 'Start' }}
                                            </div>
                                        </div>

                                        <!-- Progress Line (visible on both desktop and mobile in different layouts) -->
                                        <div class="progress-line d-none d-md-block">
                                            <div class="progress-fill" style="width: {{ $progressData['progress'] }}%"></div>
                                            <div class="progress-percentage">{{ number_format($progressData['progress'], 1) }}%</div>
                                        </div>

                                        <!-- Next Rank Circle -->
                                        @foreach ($ranks as $rank)
                                            @if ($rank->rank > $currentRankLevel)
                                                <div class="rank-circle {{ $rank->rank == $currentRankLevel + 1 ? 'next-rank' : 'future-rank' }}">
                                                    <div class="rank-icon">
                                                        <img alt="rank-image"
                                                            src="{{ getImage('assets/admin/images/rank/' . @$rank->image, '400x400') }}">
                                                    </div>
                                                    <div class="rank-label">{{ $rank->name }}</div>
                                                </div>
                                                @if ($rank->rank == $currentRankLevel + 1)
                                                    @break
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>

                                    <!-- Mobile Progress Line Row (only visible on mobile) -->
                                    <div class="progress-line-row d-md-none px-3">
                                        <div class="progress-line ">
                                            <div class="progress-fill" style="width: {{ $progressData['progress'] }}%"></div>
                                            <div class="progress-percentage">{{ number_format($progressData['progress'], 1) }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements Details -->
                            @if ($nextRank)
                                <div class="requirements-section mt-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-tasks me-2"></i>Requirements for {{ $nextRank->name }}
                                        <span
                                            class="badge bg-primary ms-2">{{ number_format($progressData['progress'], 1) }}%
                                            Complete</span>
                                    </h6>

                                    <div class="row">
                                        @foreach ($progressData['requirements'] as $key => $requirement)
                                            <div class="col-md-6 mb-3">
                                                <div
                                                    class="requirement-card {{ $requirement['completed'] ? 'completed' : 'pending' }}">
                                                    <div class="req-icon">
                                                        @if ($requirement['completed'])
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @else
                                                            <i class="fas fa-clock text-warning"></i>
                                                        @endif
                                                    </div>
                                                    <div class="req-content">
                                                        <h6>
                                                            {{ $requirement['label'] }}
                                                            @if ($requirement['completed'])
                                                                <i class="fas fa-check text-success ms-1"></i>
                                                            @endif
                                                        </h6>
                                                        <p class="text-muted mb-0">
                                                            {{ $requirement['current'] }} / {{ $requirement['required'] }}
                                                            @if ($requirement['completed'])
                                                                <span class="text-success ms-2">✓ Completed</span>
                                                            @else
                                                                <span class="text-warning ms-2">In Progress</span>
                                                            @endif
                                                        </p>
                                                        @if (!$requirement['completed'])
                                                            <div class="mini-progress">
                                                                <div class="mini-progress-bar"
                                                                    style="width: {{ min(100, ($requirement['current'] / $requirement['required']) * 100) }}%">
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $requirement['required'] - $requirement['current'] }}
                                                                more needed
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>


                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade mt-5" id="allRanksModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="background: rgba(10, 93, 61, 0.9);">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-trophy me-2"></i>All Ranks Overview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="ranks-overview">
                        @foreach ($ranks as $rank)
                            <div class="rank-item {{ $currentRank && $rank->id == $currentRank->id ? 'current' : '' }}">
                                <div class="rank-info-container">
                                    <div class="rank-info"
                                        onclick="window.location.href='{{ route('user.rank.detail', $rank->id) }}'"
                                        style="cursor: pointer;">
                                        <div class="rank-star">
                                            @for ($i = 1; $i <= $rank->rank; $i++)
                                                <i class="fas fa-star text-warning"></i>
                                            @endfor
                                        </div>
                                        <div class="rank-details">
                                            <h6>{{ $rank->name }}</h6>
                                            <p class="text-muted mb-0">Rank {{ $rank->rank }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="rank-actions">
                                        @if (isset($rank->claim_status) && $rank->claim_status['is_achieved'])
                                            @if ($rank->claim_status['can_claim'])
                                                <button
                                                    class="btn btn-sm {{ $rank->claim_status['button_class'] }} claim-btn"
                                                    data-rank-number="{{ $rank->rank }}"
                                                    onclick="claimRankReward({{ $rank->rank }})">
                                                    {{ $rank->claim_status['button_text'] }}
                                                </button>
                                            @else
                                                <span
                                                    class="btn btn-sm {{ $rank->claim_status['button_class'] }} claim-btn">
                                                    {{ $rank->claim_status['button_text'] }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Not Achieved</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
<div class="modal fade mt-5" id="allRanksModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="background: #17433c;">
            <div class="modal-header">
                <h5 class="modal-title text-white">
                    <i class="fas fa-trophy me-2"></i>All Ranks Overview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="ranks-overview">
                    @foreach ($ranks as $rank)
                        <div class="rank-item {{ $currentRank && $rank->id == $currentRank->id ? 'current' : '' }}">
                            <div class="rank-info-container">
                                <div class="rank-info"
                                    onclick="window.location.href='{{ route('user.rank.detail', $rank->id) }}'"
                                    style="cursor: pointer;">
                                    <div class="rank-star">
                                        @for ($i = 1; $i <= $rank->rank; $i++)
                                            <i class="fas fa-star text-warning"></i>
                                        @endfor
                                    </div>
                                    <div class="rank-details">
                                        <h5 class="text-white">{{ $rank->name }}</h5>
                                        <p class="text-muted mb-0">Rank {{ $rank->rank }}</p>
                                    </div>
                                </div>
                                
                                <div class="rank-actions">
                                    @if (isset($rank->claim_status) && $rank->claim_status['is_achieved'])
                                        @if ($rank->claim_status['can_claim'])
                                            <button
                                                class="btn btn-sm {{ $rank->claim_status['button_class'] }} claim-btn"
                                                data-rank-number="{{ $rank->rank }}"
                                                onclick="claimRankReward({{ $rank->rank }})">
                                                {{ $rank->claim_status['button_text'] }}
                                            </button>
                                        @else
                                            <span
                                                class="btn btn-sm {{ $rank->claim_status['button_class'] }} claim-btn">
                                                {{ $rank->claim_status['button_text'] }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Not Achieved</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
    <style>

        .text-muted {
            --bs-text-opacity: 1;
            color: #e9ebed !important;
        }
        .status-card {
            /* background: linear-gradient(135deg, rgb(135, 178, 219) 0%, rgb(216, 230, 13) 100%); */
            background: linear-gradient(135deg, #17433c 0%, #0c6b0dff 100%);
            padding: 20px;
            border-radius: 15px;
            color: white;
            border: 1px solid rgb(230, 176, 15);
        }


       
        .dd {
            background: linear-gradient(135deg, rgba(25, 132, 54, 1) 0%, rgba(4, 69, 21, 1) 100%);
            padding: 20px;
            border-radius: 15px;
            color: white;
        }

        .rank-progress-container {
            background: #f8f9fa;
            padding: 3px;
            border-radius: 20px;
            margin: 20px 0;
        }

      

        .rank-circle {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        
        .rank-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #12e235ff 0%, #194e05ff 100%);
        }

        .rank-icon img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .current-rank .rank-icon {
            background: linear-gradient(135deg, rgb(237, 237, 240) 0%, rgb(239, 237, 241) 100%);
            color: white;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(16, 159, 241, 0.7);
        }

        .next-rank .rank-icon {
            background: linear-gradient(135deg, rgb(234, 241, 241) 0%, rgb(237, 236, 241) 100%);
            color: #333;
            border: 4px solid #fff;
            animation: pulse 2s infinite;
            box-shadow: 0 4px 15px green;
        }

        .future-rank .rank-icon {
            background: #e9ecef;
            color: #6c757d;
            border: 4px solid #fff;
        }

        .rank-number {
            position: absolute;
            bottom: -5px;
            right: -5px;
            background: #fff;
            color: #333;
            font-size: 0.8rem;
            font-weight: bold;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #333;
        }

        .rank-label {
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            color: #333;
            min-width: 80px;
        }

        /* Default (desktop) styles remain the same */
        .progress-track {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            min-height: 120px;
        }

        .progress-ranks-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .progress-line-row {
            display: none; /* Hidden on desktop */
        }

        .progress-line {
            flex: 1;
            height: 12px;
            background: #e9ecef;
            border-radius: 6px;
            margin: 0 30px;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2d7705ff 0%, #094003ff 50%, #05ae2dff 100%);
            border-radius: 4px;
            transition: width 0.8s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                    animation: shimmer 2s infinite;
                }

        .progress-percentage {
            position: absolute;
            top: -35px;
            left:50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #09a926ff 0%, #033a02ff 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            z-index: 3;
            display: block;
            opacity: 1;
            white-space: nowrap;
        }

        .progress-percentage::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: #0db074ff;
        }

        /* Mobile-specific layout */
        @media (max-width: 768px ) {
            .progress-track {
                display: block;
                min-height: auto;
                padding: 15px 0;
            }

            .progress-ranks-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding: 0 10px;
            }

            .progress-line-row {
                display: block;
                width: 100%;
                padding: 0 10px;
                margin-top: 10px;
                position: relative;

            }

            .progress-line-row .progress-line {
                width: 100%;
                height: 12px;
                margin: 0;
                background: #e9ecef;
                border-radius: 6px;
                position: relative;
                overflow: visible;
            }

            .progress-line-row .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #2d7705ff 0%, #094003ff 50%, #05ae2dff 100%);
                border-radius: 6px;
                transition: width 0.8s ease;
                position: relative;
                overflow: hidden;
            }

            .progress-line-row .progress-percentage {
                position: absolute;
                top: -25px;
                left: 50%;
                transform: translateX(-50%);
                background: linear-gradient(135deg, #09a926ff 0%, #033a02ff 100%);
                color: white;
                padding: 2px 5px;
                border-radius: 15px;
                font-size: 0.65rem;
                font-weight: bold;
                white-space: nowrap;
                z-index: 3;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }

            .progress-line-row .progress-percentage::after {
                content: '';
                position: absolute;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                border: 4px solid transparent;
                border-top-color: #0db074ff;
            }

            /* Adjust rank circles for mobile */
            .rank-circle {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
            }

            .rank-icon {
                width: 50px;
                height: 50px;
                margin-bottom: 8px;
            }

            .rank-icon img {
                width: 40px;
                height: 40px;
            }

            .rank-label {
                font-size: 0.8rem;
                font-weight: 600;
                text-align: center;
                color: #333;
                min-width: 60px;
            }

            /* Hide desktop progress line on mobile */
            .progress-ranks-row .progress-line {
                display: none;
            }
        }

        /* Extra small devices */
        @media (max-width: 480px) {
            .progress-ranks-row {
                padding: 0 5px;
            }

            .progress-line-row {
                padding: 0 5px;
            }

            .rank-icon {
                width: 45px;
                height: 45px;
            }

            .rank-icon img {
                width: 35px;
                height: 35px;
            }

            .rank-label {
                font-size: 0.75rem;
                min-width: 50px;
            }

            .progress-line-row .progress-percentage {
                font-size: 0.6rem;
                padding: 1px 4px;
                top: -20px;
            }
            .progress-line-row .progress-percentage::after {
                border-width: 3px;
            }
        }


        .requirements-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #e9ecef;
        }

        .requirement-card {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .req-icon {
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .req-content h6 {
            margin-bottom: 5px;
            color: #333;
        }

        .mini-progress {
            width: 100%;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 5px;
        }

        .mini-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
            transition: width 0.5s ease;
        }

        /* Status-specific button styles */
        .btn-success.claim-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 6px 12px;
            font-size: 0.9rem;
            border-radius: 20px;
            color: white;
        }

        .btn-warning.disabled {
            background: linear-gradient(135deg, rgb(155, 132, 4) 0%, rgb(221, 172, 11) 100%);
            padding: 6px 12px;
            font-size: 0.9rem;
            font-weight: bold;
            border-radius: 20px;
            color: white;
        }

        .btn-primary.disabled {
            background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);
            padding: 6px 12px;
            font-size: 0.9rem;
            border-radius: 20px;
            color: white;
        }

        .btn-danger.disabled {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            padding: 6px 12px;
            font-size: 0.9rem;
            border-radius: 20px;
            color: white;
        }

        .btn-secondary.disabled {
            background: #6c757d;
            padding: 6px 12px;
            font-size: 0.9rem;
            border-radius: 20px;
            color: white;
            opacity: 0.7;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }
  
    </style>

    <style>
        .requirement-card {
            display: flex;
            height: 100%;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .requirement-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .requirement-card.completed {
            background: linear-gradient(135deg, #1b4925ff 0%, #08d839ff 100%);
            border-color: #0dd63cff;
            
        }

        .requirement-card.pending {
            background: linear-gradient(135deg, #1f90b6ff 0%, #0b51d4ff 100%);
            border-color: #057194ff;
        }

        .text-success {
            --bs-text-opacity
        1
        : 1;
            color: #e8de2aff !important;
        }

        .req-icon {
            margin-right: 15px;
            font-size: 1.8rem;
            min-width: 40px;
            display: flex;
            justify-content: center;
        }

        .req-content {
            flex: 1;
        }

        .req-content h6 {
            margin-bottom: 8px;
            color: #edf4f4ff;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .mini-progress {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin: 8px 0 5px 0;
        }

        .mini-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
            transition: width 0.5s ease;
            position: relative;
        }

        .mini-progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shimmer 2s infinite;
        }

        .progress-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .overall-progress-bar {
            width: 100%;
            height: 12px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }

        .overall-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            border-radius: 6px;
            transition: width 0.8s ease;
            position: relative;
        }

        .overall-progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shimmer 2s infinite;
        }
    </style>

    <style>
        /* Updated Ranks Modal Styles */
        .ranks-overview {
            max-height: 70vh;
            overflow-y: auto;
            padding: 0;
        }

        .rank-item {
            margin-bottom: 15px;
            padding: 0;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .rank-item:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .rank-item.current {
            background: linear-gradient(135deg, rgba(162, 12, 221, 1) 0%, rgb(25, 206, 219) 100%);
            color: white;
            border: none;
        }

        .rank-info-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            min-height: 80px;
        }

        .rank-info {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
            /* Allows text to truncate */
        }

        .rank-star {
            margin-right: 15px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .rank-details {
            flex: 1;
            min-width: 0;
        }

        .rank-details h6 {
            margin-bottom: 5px;
            font-size: 1rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rank-details p {
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .rank-actions {
            flex-shrink: 0;
            margin-left: 15px;
        }

        .claim-btn {
            padding: 8px 16px;
            font-size: 0.875rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            white-space: nowrap;
            min-width: 100px;
            text-align: center;
        }

        .claim-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .claim-btn:active {
            transform: translateY(0);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .modal-content {
                border-radius: 15px;
            }

            .modal-header {
                padding: 15px;
                border-bottom: 1px solid #e9ecef;
            }

            .modal-title {
                font-size: 1.1rem;
            }

            .modal-body {
                padding: 15px;
            }

            .ranks-overview {
                max-height: 60vh;
            }

            .rank-item {
                margin-bottom: 12px;
                border-radius: 10px;
            }

            .rank-info-container {
                flex-direction: column;
                align-items: stretch;
                padding: 15px;
                gap: 15px;
            }

            /* .rank-info {
                        justify-content: center;
                        text-align: center;
                    } */

            .rank-star {
                margin-right: 10px;
                font-size: 1.1rem;
            }

            .rank-details h6 {
                font-size: 1rem;
                white-space: normal;
                text-align: left;
            }

            .rank-details p {
                font-size: 0.875rem;
            }

            .rank-actions {
                margin-left: 0;
                text-align: center;
            }

            .claim-btn {
                padding: 10px 20px;
                font-size: 0.875rem;
                min-width: 120px;
                width: 100%;
            }

            .badge {
                padding: 8px 16px;
                font-size: 0.875rem;
            }
        }

        /* Extra Small Devices (320px and up) */
        @media (max-width: 320px) {
            .modal-dialog {
                margin: 5px;
                max-width: calc(100% - 10px);
            }

            .modal-header {
                padding: 12px;
            }

            .modal-title {
                font-size: 1rem;
            }

            .modal-body {
                padding: 12px;
            }

            .rank-info-container {
                padding: 12px;
                gap: 12px;
            }

            .rank-details h6 {
                font-size: 0.95rem;
            }

            .rank-details p {
                font-size: 0.8rem;
            }

            .rank-star {
                font-size: 1rem;
            }

            .claim-btn {
                padding: 8px 16px;
                font-size: 0.8rem;
                min-width: 100px;
            }
        }

        /* Medium Devices (768px and up) */
        @media (min-width: 768px) and (max-width: 991px) {
            .modal-dialog {
                max-width: 600px;
            }

            .rank-info-container {
                padding: 18px;
            }

            .rank-details h6 {
                font-size: 1.1rem;
            }

            .claim-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
                min-width: 110px;
            }
        }

        /* Large Devices (992px and up) */
        @media (min-width: 992px) {
            .modal-dialog {
                max-width: 800px;
            }

            .rank-info-container {
                padding: 20px;
            }

            .rank-details h6 {
                font-size: 1.2rem;
            }

            .claim-btn {
                padding: 10px 20px;
                font-size: 0.95rem;
                min-width: 120px;
            }
        }

        /* Scrollbar Styling for Modal */
        .ranks-overview::-webkit-scrollbar {
            width: 6px;
        }

        .ranks-overview::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .ranks-overview::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .ranks-overview::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Current rank item special styling for mobile */
        @media (max-width: 576px) {

            .rank-item.current .rank-details h6,
            .rank-item.current .rank-details p {
                color: white;
            }

            .rank-item.current .rank-star .fas {
                color: #ffd700 !important;
            }
        }
    </style>
@endpush


@push('script')
    <script>
        function showAllRanks() {
            $('#allRanksModal').modal('show');
        }


        // function claimRankReward(rankNumber) {
        //         const button = document.querySelector(`button[data-rank-number="${rankNumber}"]`);
        //         if (!button) {
        //             console.error('Button not found for rank number:', rankNumber);
        //             toastr.error('Button not found');
        //             return;
        //         }

        //         console.log('Initiating claim for rank number:', rankNumber);
        //         const originalText = button.textContent;
        //         const originalClass = button.className;

        //         // Update button to processing state
        //         button.textContent = 'Processing';
        //         button.className = 'btn btn-sm btn-warning disabled';
        //         button.disabled = true;

        //         fetch('{{ route('user.rank.claim') }}', {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/json',
        //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                 },
        //                 body: JSON.stringify({
        //                     rank_number: rankNumber  // Changed from rank_id to rank_number
        //                 })
        //             })
        //             .then(response => {
        //                 if (!response.ok) {
        //                     throw new Error(`HTTP error! Status: ${response.status}`);
        //                 }
        //                 return response.json();
        //             })
        //             .then(data => {
        //                 console.log('Response data:', data);
        //                 if (data.success) {
        //                     toastr.success(data.message);

        //                     // Update button with new status
        //                     const rankItem = button.closest('.rank-item');
        //                     const actionDiv = rankItem.querySelector('.rank-actions');

        //                     // Update button HTML based on response
        //                     actionDiv.innerHTML = `<span class="btn btn-sm ${data.claim_status.button_class}">
        //                                     ${data.claim_status.button_text}
        //                                 </span>`;
        //                 } else {
        //                     toastr.error(data.message || 'Failed to process claim');
        //                     // Restore original button state
        //                     button.textContent = originalText;
        //                     button.className = originalClass;
        //                     button.disabled = false;
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error('AJAX error:', error.message);
        //                 toastr.error('Network error: ' + error.message);
        //                 // Restore original button state
        //                 button.textContent = originalText;
        //                 button.className = originalClass;
        //                 button.disabled = false;
        //             });
        // }

        function claimRankReward(rankNumber) {
            const button = document.querySelector(`button[data-rank-number="${rankNumber}"]`);
            if (!button) {
                console.error('Button not found for rank number:', rankNumber);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Button not found');
                } else {
                    alert('Button not found');
                }
                return;
            }

            console.log('Initiating claim for rank number:', rankNumber);
            const originalText = button.textContent;
            const originalClass = button.className;

            // Update button to processing state
            button.textContent = 'Processing';
            button.className = 'btn btn-sm btn-warning disabled';
            button.disabled = true;

            fetch('{{ route('user.rank.claim') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rank_number: rankNumber
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || `HTTP error! Status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    } else {
                        alert(data.message);
                    }

                    // Update button with new status
                    const rankItem = button.closest('.rank-item');
                    const actionDiv = rankItem.querySelector('.rank-actions');

                    // Update button HTML based on response
                    actionDiv.innerHTML = `<span class="btn btn-sm ${data.claim_status.button_class}">
                        ${data.claim_status.button_text}
                    </span>`;
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to process claim');
                    } else {
                        alert(data.message || 'Failed to process claim');
                    }
                    // Restore original button state
                    button.textContent = originalText;
                    button.className = originalClass;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('AJAX error:', error.message);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Network error: ' + error.message);
                } else {
                    alert('Network error: ' + error.message);
                }
                // Restore original button state
                button.textContent = originalText;
                button.className = originalClass;
                button.disabled = false;
            });
        }

            
           function updateProgressPosition() {
                const progressFill = document.querySelector('.progress-fill');
                const progressPercentage = document.querySelector('.progress-percentage');
                const progressLine = document.querySelector('.progress-line');
                
                const percentage = {{ $progressData['progress'] }};
                
                if (progressFill && progressPercentage && progressLine) {
                    if (window.innerWidth <= 768) {
                        // Mobile layout - target the mobile progress line specifically
                        const mobileProgressLine = document.querySelector('.progress-line-row .progress-line');
                        const mobileProgressPercentage = document.querySelector('.progress-line-row .progress-percentage');
                        
                        if (mobileProgressLine && mobileProgressPercentage) {
                            // Wait for the element to be rendered
                            setTimeout(() => {
                                const progressLineWidth = mobileProgressLine.offsetWidth;
                                if (progressLineWidth > 0) {
                                    const position = (percentage / 100) * progressLineWidth;
                                    const positionPercentage = (position / progressLineWidth) * 100;
                                    
                                    // Ensure the percentage doesn't go beyond the container bounds
                                    const minPosition = 0; // Minimum 10% to avoid edge cutoff
                                    const maxPosition = 100; // Maximum 90% to avoid edge cutoff
                                    const clampedPosition = Math.max(minPosition, Math.min(maxPosition, positionPercentage));
                                    
                                    // Apply the position using a CSS custom property
                                    mobileProgressPercentage.style.left = clampedPosition + '%';
                                    
                                    // Also ensure the element is visible
                                    mobileProgressPercentage.style.display = 'block';
                                    mobileProgressPercentage.style.opacity = '1';
                                }
                            }, 100);
                        }
                    } else {
                        // Desktop layout
                        const desktopProgressLine = document.querySelector('.progress-ranks-row .progress-line');
                        const desktopProgressPercentage = document.querySelector('.progress-ranks-row .progress-percentage');
                        
                        if (desktopProgressLine && desktopProgressPercentage) {
                            const progressLineWidth = desktopProgressLine.offsetWidth;
                            if (progressLineWidth > 0) {
                                const position = (percentage / 100) * progressLineWidth;
                                const positionPercentage = (position / progressLineWidth) * 100;
                                
                                // For desktop, clamp between 5% and 95% to avoid edge cutoff
                                const minPosition = 0;
                                const maxPosition = 100;
                                const clampedPosition = Math.max(minPosition, Math.min(maxPosition, positionPercentage));
                                
                                desktopProgressPercentage.style.left = clampedPosition + '%';
                                desktopProgressPercentage.style.display = 'block';
                                desktopProgressPercentage.style.opacity = '1';
                            }
                        }
                    }
                }
            }

            // Initialize and handle window resize
            document.addEventListener('DOMContentLoaded', function() {
                // Wait for DOM to be fully loaded
                setTimeout(updateProgressPosition, 200);
                
                // Update progress position when window is resized
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(updateProgressPosition, 150);
                });
            });

            // Also trigger on modal show if needed
            document.addEventListener('shown.bs.modal', function() {
                setTimeout(updateProgressPosition, 100);
            });

            // Auto-update notification
            @if (session('rank_updated'))
                setTimeout(() => {
                    toastr.success('Congratulations! Your rank has been updated!', 'Rank Updated');
                }, 1000);
            @endif
    </script>
@endpush