@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="container">
        <div class="row mt-5">
            <div class="col-12">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ route('user.rank.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Rank Progress
                    </a>
                </div>

                <!-- Rank Detail Card -->
                <div class="card">
                    <div class="card-header bg-gradient-green text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-star me-2"></i>{{ $pageTitle }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Rank Header -->
                        <div class="rank-header mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="rank-display">
                                        <div class="nav-rank-icon1">
                                            @if ($rank->image)
                                                <img alt="rank-image"
                                                    src="{{ getImage('assets/admin/images/rank/' . @$rank->image, '400x400') }}">
                                            @else
                                                <div class="default-rank-icon">
                                                    @for ($i = 1; $i <= $rank->rank; $i++)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @endfor
                                                </div>
                                            @endif
                                        </div>
                                        <div class="rank-info">
                                            <h2 class="rank-name">{{ $rank->name }}</h2>
                                            <p class="rank-level">{{ $rank->rank }} Star Rank</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="rank-status">
                                        @if ($currentRank && $currentRank->id == $rank->id)
                                            <div class="status-badge current-rank-badge">
                                                <i class="fas fa-check-circle me-2"></i>Current Rank
                                            </div>
                                        @elseif($currentRankLevel >= $rank->rank)
                                            <div class="status-badge achieved-badge">
                                                <i class="fas fa-trophy me-2"></i>Achieved
                                            </div>
                                        @elseif($canAchieve)
                                            <div class="status-badge available-badge">
                                                <i class="fas fa-target me-2"></i>Available
                                            </div>
                                        @else
                                            <div class="status-badge locked-badge">
                                                <i class="fas fa-lock me-2"></i>Locked
                                            </div>
                                        @endif

                                        
                                        <!-- Claim Button -->
                                        
                                        @if ($claimStatus['is_achieved'])
                                            <form action="{{ route('user.rank.claim') }}" method="POST" class="rank-actions claim-form" id="claim-form-{{ $rank->id }}">
                                                @csrf
                                                <input type="hidden" name="rank_number" value="{{ $rank->rank }}">
                                                <div class="claim-action mt-3">
                                                    @if ($claimStatus['can_claim'])
                                                        <button type="submit" class="btn {{ $claimStatus['button_class'] }}"
                                                                data-rank-number="{{ $rank->rank }}"
                                                                oncli>
                                                            {{ $claimStatus['button_text'] }}
                                                        </button>
                                                    @else
                                                        <span class="btn {{ $claimStatus['button_class'] }}">
                                                            {{ $claimStatus['button_text'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </form>
                                        @endif

                                        @if ($canAchieve && $currentRankLevel < $rank->rank)
                                            <div class="progress-display mt-3">
                                                <div class="progress-circle">
                                                    <div class="progress-text">
                                                        <span
                                                            class="percentage">{{ number_format($progressData['progress'], 1) }}%</span>
                                                        <span class="label">Complete</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements Section -->
                        @if ($canAchieve)
                            <div class="requirements-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-tasks me-2"></i>Requirements to Achieve
                                </h5>

                                @if ($currentRankLevel < $rank->rank)
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
                                @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-trophy me-2"></i>
                                        Congratulations! You have already achieved this rank.
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="requirements-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-lock me-2"></i>Requirements
                                </h5>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    You need to achieve Rank {{ $rank->rank - 1 }} before you can work towards this rank.
                                </div>
                            </div>
                        @endif

                        <!-- Rewards Section -->
                        @if ($rankRewards->count() > 0)
                            <div class="rewards-section mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-gift me-2"></i>Rank Rewards
                                </h5>
                                <div class="row">
                                    @foreach ($rankRewards as $reward)
                                        <div class="col-md-4 mb-3">
                                            <div class="reward-card">
                                                @if ($reward->image)
                                                    <div class="reward-image">
                                                        <img alt="Reward"
                                                            src="{{ getImage('assets/admin/images/rank/reward/' . @$reward->image, '400x400') }}">
                                                    </div>
                                                @endif
                                                <div class="reward-content">
                                                    <h6>{{ $reward->reward }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Navigation -->
                        <div class="rank-navigation">
                            <h5 class="section-title">
                                <i class="fas fa-map me-2"></i>All Ranks
                            </h5>
                            <div class="ranks-grid">
                                @foreach ($allRanks as $navRank)
                                    <div class="rank-nav-item {{ $navRank->id == $rank->id ? 'active' : '' }} {{ $currentRankLevel >= $navRank->rank ? 'achieved' : '' }}"
                                        onclick="window.location.href='{{ route('user.rank.detail', $navRank->id) }}'">
                                        <div class="nav-rank-icon">
                                            @if ($navRank->image)
                                                <img src="{{ getImage(getFilePath('rank') . '/' . $navRank->image) }}"
                                                    alt="{{ $navRank->name }}">
                                            @else
                                                @for ($i = 1; $i <= $navRank->rank; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                            @endif
                                        </div>
                                        <div class="nav-rank-info">
                                            <h6>{{ $navRank->name }}</h6>
                                            <p>Rank {{ $navRank->rank }}</p>
                                        </div>
                                        @if ($currentRank && $currentRank->id == $navRank->id)
                                            <div class="nav-badge current">Current</div>
                                        @elseif($currentRankLevel >= $navRank->rank)
                                            <div class="nav-badge achieved">✓</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .rank-header {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .rank-display {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-rank-icon1 img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
        }

        .rank-info h2 {
            color: #1b5e20;
            margin-bottom: 5px;
        }

        .rank-level {
            color: #2e7d32;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .btn {
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .rank-status {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        .current-rank-badge {
            background: linear-gradient(135deg, #388e3c 0%, #4caf50 100%);
            color: white;
        }

        .achieved-badge {
            background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
            color: white;
        }

        .available-badge {
            background: linear-gradient(135deg, #66bb6a 0%, #81c784 100%);
            color: white;
        }

        .locked-badge {
            background: linear-gradient(135deg, #757575 0%, #9e9e9e 100%);
            color: white;
        }

        .claim-action {
            margin-top: 15px;
        }

        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, #2e7d32 0deg, #2e7d32 {{ $progressData['progress'] * 3.6 }}deg, #e0e0e0 {{ $progressData['progress'] * 3.6 }}deg, #e0e0e0 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .progress-text .percentage {
            display: block;
            font-size: 1.2rem;
            font-weight: bold;
            color: #1b5e20;
        }

        .progress-text .label {
            font-size: 0.8rem;
            color: #2e7d32;
        }

        .section-title {
            color: #1b5e20;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #c8e6c9;
        }

        .requirement-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f1f8e9;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .requirement-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.2);
        }

        .requirement-card.completed {
            background: linear-gradient(135deg, #c8e6c9 0%, #a5d6a7 100%);
            border-color: #388e3c;
        }

        .requirement-card.pending {
            background: linear-gradient(135deg, #f9fbe7 0%, #f0f4c3 100%);
            border-color: #afb42b;
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
            color: #1b5e20;
            font-weight: 600;
        }

        .mini-progress {
            width: 100%;
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin: 8px 0 5px 0;
        }

        .mini-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #2e7d32 0%, #388e3c 100%);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .reward-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: left;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.15);
            transition: transform 0.3s ease;
        }

        .reward-card:hover {
            transform: translateY(-5px);
        }

        .reward-image {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            overflow: hidden;
        }

        .reward-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .reward-content h6 {
            text-align: center;
            margin-top: 10px;
            color: #1b5e20;
        }

        .ranks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .rank-nav-item {
            background: white;
            border: 2px solid #c8e6c9;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
        }

        .rank-nav-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.25);
        }

        .rank-nav-item.active {
            border-color: #2e7d32;
            background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
        }

        .rank-nav-item.achieved {
            border-color: #388e3c;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }

        .nav-rank-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
        }

        .nav-rank-icon img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-rank-icon i {
            color: white;
            font-size: 1.2rem;
        }

        .nav-rank-info h6 {
            margin-bottom: 5px;
            color: #1b5e20;
        }

        .nav-rank-info p {
            color: #2e7d32;
            font-size: 0.9rem;
            margin: 0;
        }

        .nav-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .nav-badge.current {
            background: #2e7d32;
            color: white;
        }

        .nav-badge.achieved {
            background: #388e3c;
            color: white;
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
        }

        @media (max-width: 768px) {
            .rank-display {
                flex-direction: column;
                text-align: center;
            }

            .nav-rank-icon1 img {
                width: 100px;
                height: 100px;
            }

            .ranks-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .requirement-card {
                flex-direction: column;
                text-align: center;
            }

            .req-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@push('script')
    <script>
    // Auto-redirect notification
    @if (session('rank_updated'))
        setTimeout(() => {
            toastr.success('Congratulations! Your rank has been updated!', 'Rank Updated');
        }, 1000);
    @endif

    // Smooth scroll for navigation
    document.querySelectorAll('.rank-nav-item').forEach(item => {
        item.addEventListener('click', function() {
            // Add loading effect
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 200);
        });
    });

    // Claim Form Submission - UPDATED TO USE RANK NUMBER
    document.querySelectorAll('.claim-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const rankNumber = button.getAttribute('data-rank-number');
            const originalText = button.textContent;
            const originalClass = button.className;

            // Update button to processing state
            button.textContent = 'Reward Processing';
            button.className = 'btn btn-warning disabled';
            button.disabled = true;

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    toastr.success(data.message);

                    // Update button with new status
                    const actionDiv = button.closest('.claim-action');
                    actionDiv.innerHTML = `<span class="btn ${data.claim_status.button_class}">
                                            ${data.claim_status.button_text}
                                          </span>`;
                } else {
                    toastr.error(data.message || 'Failed to process claim');
                    // Restore original button state
                    button.textContent = originalText;
                    button.className = originalClass;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('AJAX error:', error.message);
                toastr.error('Network error: ' + error.message);
                // Restore original button state
                button.textContent = originalText;
                button.className = originalClass;
                button.disabled = false;
            });
        });
    });
 </script>
@endpush