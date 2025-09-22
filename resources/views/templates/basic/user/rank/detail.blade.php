@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="container">
        <div class="row">
            <div class="col-12 ">
                <!-- Back Button -->
                <div class="mb-4 mt-5">
                    <a href="{{ route('user.rank.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Rank Progress
                    </a>
                </div>

                <!-- Rank Detail Card -->
                <div class="card shadow-lg border-0 overflow-hidden">
                    <div class="card-header bg-primary text-white py-4">
                        <h4 class="mb-0 text-center">
                            <i class="fas fa-star me-2"></i>{{ $pageTitle }}
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        <!-- Rank Header -->
                        <div class="rank-header-section mb-5">
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="rank-image-wrapper">
                                            @if ($rank->image)
                                                <img class="rank-main-image" 
                                                     alt="rank-image"
                                                     src="{{ getImage('assets/admin/images/rank/' . @$rank->image, '400x400') }}">
                                            @else
                                                <div class="default-rank-icon-large">
                                                    @for ($i = 1; $i <= $rank->rank; $i++)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @endfor
                                                </div>
                                            @endif
                                        </div>
                                        <div class="rank-info-section">
                                            <h2 class="rank-name mb-2">{{ $rank->name }}</h2>
                                            {{-- <p class="rank-level text-muted mb-1">{{ $rank->rank }} Star Rank</p>
                                            @if($rank->alias)
                                                <p class="rank-alias text-secondary mb-0">{{ $rank->alias }}</p>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-center">
                                        @if ($currentRank && $currentRank->id == $rank->id)
                                            <div class="status-badge current-rank mb-3">
                                                <i class="fas fa-check-circle me-2"></i>Current Rank
                                            </div>
                                        @elseif($currentRankLevel >= $rank->rank)
                                            <div class="status-badge achieved mb-3">
                                                <i class="fas fa-trophy me-2"></i>Achieved
                                            </div>
                                        @elseif($canAchieve)
                                            <div class="status-badge available mb-3">
                                                <i class="fas fa-target me-2"></i>Available
                                            </div>
                                        @else
                                            <div class="status-badge locked mb-3">
                                                <i class="fas fa-lock me-2"></i>Locked
                                            </div>
                                        @endif

                                        @if ($canAchieve && $currentRankLevel < $rank->rank)
                                            <div class="progress-circle-wrapper">
                                                <div class="progress-circle" data-progress="{{ $progressData['progress'] ?? 0 }}">
                                                    <div class="progress-inner">
                                                        <span class="progress-percent">{{ number_format($progressData['progress'] ?? 0, 1) }}%</span>
                                                        <span class="progress-label">Complete</span>
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
                            <div class="requirements-section mb-5">
                                <h5 class="section-title mb-4">
                                    <i class="fas fa-tasks me-2"></i>Requirements to Achieve {{ $rank->name }}
                                </h5>

                                @if ($currentRankLevel < $rank->rank)
                                    @if(count($progressData['requirements']) > 0)
                                        <!-- Progress Summary -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <div class="summary-card text-center p-3">
                                                    <h3 class="text-primary mb-1">{{ number_format($progressData['progress'], 1) }}%</h3>
                                                    <p class="text-muted small mb-0">Overall Progress</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="summary-card text-center p-3">
                                                    <h3 class="text-success mb-1">{{ $progressData['completed_requirements'] ?? 0 }}</h3>
                                                    <p class="text-muted small mb-0">Requirements Met</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="summary-card text-center p-3">
                                                    <h3 class="text-warning mb-1">{{ $progressData['total_requirements'] ?? 0 }}</h3>
                                                    <p class="text-muted small mb-0">Total Requirements</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Requirements List -->
                                        <div class="row g-3">
                                            @foreach ($progressData['requirements'] as $key => $requirement)
                                                <div class="col-lg-6">
                                                    <div class="requirement-card h-100 {{ $requirement['completed'] ? 'completed' : 'pending' }}">
                                                        <div class="d-flex align-items-start">
                                                            <div class="requirement-icon me-3">
                                                                @if ($requirement['completed'])
                                                                    <i class="fas fa-check-circle text-success fs-4"></i>
                                                                @else
                                                                    <i class="fas fa-clock text-warning fs-4"></i>
                                                                @endif
                                                            </div>
                                                            <div class="requirement-content flex-grow-1">
                                                                <h6 class="requirement-title mb-2">
                                                                    {{ $requirement['label'] }}
                                                                </h6>
                                                                <div class="requirement-progress mb-2">
                                                                    <span class="fw-medium">
                                                                        {{ number_format($requirement['current']) }} / {{ number_format($requirement['required']) }}
                                                                    </span>
                                                                    @if ($requirement['completed'])
                                                                        <span class="badge bg-success ms-2">Completed</span>
                                                                    @else
                                                                        <span class="badge bg-warning ms-2">In Progress</span>
                                                                    @endif
                                                                </div>
                                                                @if (!$requirement['completed'])
                                                                    <div class="progress mb-2" style="height: 6px;">
                                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                                             style="width: {{ min(100, ($requirement['current'] / $requirement['required']) * 100) }}%">
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        {{ number_format($requirement['required'] - $requirement['current']) }} more needed
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No specific requirements found for this rank.
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-success text-center py-4">
                                        <i class="fas fa-trophy fa-2x text-success mb-3"></i>
                                        <h5 class="mb-2">Congratulations!</h5>
                                        <p class="mb-0">You have already achieved this rank and met all requirements.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="requirements-section mb-5">
                                <h5 class="section-title mb-4">
                                    <i class="fas fa-lock me-2"></i>Requirements
                                </h5>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    @if($rankRequirements && $rankRequirements->min_rank_id)
                                        @php
                                            $minRank = \App\Models\Rank::find($rankRequirements->min_rank_id);
                                        @endphp
                                        You need to achieve <strong>{{ $minRank ? $minRank->name : 'a higher rank' }}</strong> (Rank {{ $minRank ? $minRank->rank : $rank->rank - 1 }}) before you can work towards this rank.
                                    @else
                                        You need to achieve the previous rank before you can work towards this rank.
                                    @endif
                                </div>
                                
                                @if($rankRequirements)
                                    <div class="locked-requirements p-4">
                                        <h6 class="text-muted mb-3">Future Requirements (Locked)</h6>
                                        <div class="row g-2">
                                            @if($rankRequirements->level_one_user_count > 0)
                                                <div class="col-md-6">
                                                    <div class="locked-req-item">
                                                        <i class="fas fa-lock me-2 text-muted"></i>
                                                        Level 1 Users: {{ number_format($rankRequirements->level_one_user_count) }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($rankRequirements->level_two_user_count > 0)
                                                <div class="col-md-6">
                                                    <div class="locked-req-item">
                                                        <i class="fas fa-lock me-2 text-muted"></i>
                                                        Level 2 Users: {{ number_format($rankRequirements->level_two_user_count) }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($rankRequirements->level_three_user_count > 0)
                                                <div class="col-md-6">
                                                    <div class="locked-req-item">
                                                        <i class="fas fa-lock me-2 text-muted"></i>
                                                        Level 3 Users: {{ number_format($rankRequirements->level_three_user_count) }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($rankRequirements->level_four_user_count > 0)
                                                <div class="col-md-6">
                                                    <div class="locked-req-item">
                                                        <i class="fas fa-lock me-2 text-muted"></i>
                                                        Level 4 Users: {{ number_format($rankRequirements->level_four_user_count) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Navigation Section -->
                        <div class="navigation-section">
                            <h5 class="section-title mb-4">
                                <i class="fas fa-map me-2"></i>All Ranks
                            </h5>
                            <div class="ranks-grid">
                                @foreach ($allRanks as $navRank)
                                    <div class="rank-nav-item {{ $navRank->id == $rank->id ? 'active' : '' }} {{ $currentRankLevel >= $navRank->rank ? 'achieved' : '' }}"
                                         data-href="{{ route('user.rank.detail', $navRank->id) }}">
                                        <div class="nav-rank-icon mb-2">
                                            @if ($navRank->image)
                                                <img src="{{ getImage('assets/admin/images/rank/' . $navRank->image, '400x400') }}"
                                                     alt="{{ $navRank->name }}" class="nav-rank-image">
                                            @else
                                                <div class="nav-rank-stars">
                                                    @for ($i = 1; $i <= $navRank->rank; $i++)
                                                        <i class="fas fa-star"></i>
                                                    @endfor
                                                </div>
                                            @endif
                                        </div>
                                        <div class="nav-rank-info">
                                            <h6 class="nav-rank-name mb-1">{{ $navRank->name }}</h6>
                                            <p class="nav-rank-level mb-0">Rank {{ $navRank->rank }}</p>
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

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize animations
            initializeAnimations();
            
            // Handle rank navigation clicks
            $('.rank-nav-item[data-href]').on('click', function() {
                const href = $(this).data('href');
                if (href) {
                    $(this).addClass('loading');
                    window.location.href = href;
                }
            });

            // Auto-redirect notification
            @if (session('rank_updated'))
                setTimeout(() => {
                    toastr.success('Congratulations! Your rank has been updated!', 'Rank Updated');
                }, 1000);
            @endif

            // Check if user just completed all requirements
            @if(isset($progressData['progress']) && $progressData['progress'] >= 100 && $currentRankLevel < $rank->rank)
                setTimeout(() => {
                    toastr.success('All requirements completed! Your rank will be updated automatically within 20 minutes.', 'Requirements Met');
                }, 1500);
            @endif
        });

        function initializeAnimations() {
            // Animate progress circles
            $('.progress-circle').each(function() {
                const $circle = $(this);
                const progress = parseFloat($circle.data('progress')) || 0;
                const circumference = 2 * Math.PI * 40; // radius = 40
                const offset = circumference - (progress / 100) * circumference;
                
                $circle.find('.progress-ring-bar').css({
                    'stroke-dasharray': circumference,
                    'stroke-dashoffset': offset
                });
            });

            // Animate requirement cards with stagger
            $('.requirement-card').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(index * 100).animate({
                    'opacity': 1
                }, {
                    duration: 600,
                    step: function(now) {
                        $(this).css('transform', `translateY(${20 - (20 * now)}px)`);
                    }
                });
            });

            // Animate summary cards
            $('.summary-card').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'scale(0.9)'
                }).delay(index * 150).animate({
                    'opacity': 1
                }, {
                    duration: 500,
                    step: function(now) {
                        $(this).css('transform', `scale(${0.9 + (0.1 * now)})`);
                    }
                });
            });
        }
    </script>
@endpush

@push('style')
    <style>
        :root {
            --primary-color: #17433c;
            --primary-light: #25BBA2;
            --accent-color: #CFFFDF;
            --accent-light: #f8fcf9;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(37, 187, 162, 0.2);
            --shadow-light: 0 8px 32px rgba(37, 187, 162, 0.1);
            --shadow-medium: 0 12px 40px rgba(37, 187, 162, 0.15);
            --shadow-heavy: 0 20px 60px rgba(37, 187, 162, 0.2);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: #f5f5f5;
            min-height: 100vh;
        }

        /* Card Styling */
        .card {
            border-radius: var(--border-radius);
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--glass-border);
        }

        .card-header.bg-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%) !important;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            color: white;
        }

        /* Rank Header Section */
        .rank-header-section {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            padding: 2rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .rank-image-wrapper {
            flex-shrink: 0;
        }

        .rank-main-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 8px 25px rgba(37, 187, 162, 0.2);
            border: 4px solid white;
        }

        .default-rank-icon-large {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(37, 187, 162, 0.3);
        }

        .default-rank-icon-large i {
            color: #ffd700;
            font-size: 1.2rem;
            margin: 0 1px;
        }

        .rank-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .rank-level {
            font-size: 1.1rem;
            color: var(--primary-light);
        }

        .rank-alias {
            font-style: italic;
            color: var(--primary-color);
            opacity: 0.8;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            min-width: 140px;
            box-shadow: var(--shadow-light);
        }

        .status-badge.current-rank {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            animation: pulse 2s infinite;
        }

        .status-badge.achieved {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
        }

        .status-badge.available {
            background: linear-gradient(135deg, var(--primary-light) 0%, #1BA48A 100%);
            color: white;
            animation: pulse 2s infinite;
        }

        .status-badge.locked {
            background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%);
            color: white;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Progress Circle */
        .progress-circle-wrapper {
            margin-top: 1rem;
        }

        .progress-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--glass-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto;
            border: 8px solid rgba(37, 187, 162, 0.1);
        }

        .progress-circle::before {
            content: '';
            position: absolute;
            top: -8px;
            left: -8px;
            right: -8px;
            bottom: -8px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, var(--primary-color) 0deg, var(--primary-color) var(--progress, 0deg), transparent var(--progress, 0deg));
            z-index: 1;
        }

        .progress-inner {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .progress-percent {
            display: block;
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
            line-height: 1;
        }

        .progress-label {
            font-size: 0.75rem;
            color: var(--primary-light);
        }

        /* Section Titles */
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--glass-border);
        }

        /* Summary Cards */
        .summary-card {
            background: var(--glass-bg);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .summary-card .text-primary {
            color: var(--primary-color) !important;
        }

        .summary-card .text-success {
            color: #10B981 !important;
        }

        .summary-card .text-warning {
            color: var(--primary-light) !important;
        }

        /* Requirement Cards */
        .requirement-card {
            background: var(--glass-bg);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 1.25rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .requirement-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .requirement-card.completed {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            border-color: #10B981;
        }

        .requirement-card.pending {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            border-color: var(--primary-light);
        }

        .requirement-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .requirement-progress {
            color: var(--primary-light);
        }

        /* Requirements Section */
        .requirements-section {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-light);
        }

        /* Locked Requirements */
        .locked-requirements {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            border-radius: 12px;
            border: 2px dashed var(--glass-border);
        }

        .locked-req-item {
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: var(--primary-color);
            border: 1px solid var(--glass-border);
            margin-bottom: 0.5rem;
        }

        /* Navigation Grid */
        .ranks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
        }

        .rank-nav-item {
            background: var(--glass-bg);
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            position: relative;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .rank-nav-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        .rank-nav-item.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .rank-nav-item.active {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
        }

        .rank-nav-item.achieved {
            border-color: #10B981;
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
        }

        .nav-rank-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        }

        .nav-rank-image {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-rank-stars i {
            color: white;
            font-size: 0.8rem;
            margin: 0 1px;
        }

        .nav-rank-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .nav-rank-level {
            font-size: 0.8rem;
            color: var(--primary-light);
        }

        .nav-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 10;
        }

        .nav-badge.current {
            background: var(--primary-color);
            color: white;
        }

        .nav-badge.achieved {
            background: #10B981;
            color: white;
        }

        /* Button Styles */
        .btn-outline-secondary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: var(--transition);
        }

        .btn-outline-secondary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Alert Styles */
        .alert {
            border-radius: 10px;
            border: none;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .alert-info {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            color: var(--primary-color);
            border: 1px solid var(--glass-border);
        }

        .alert-warning {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #D97706;
            border: 1px solid #F59E0B;
        }

        .alert-success {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #059669;
            border: 1px solid #10B981;
        }

        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
            border-radius: 20px;
            padding: 0.5rem 0.75rem;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-success {
            background-color: #10B981 !important;
        }

        .badge.bg-warning {
            background-color: var(--primary-light) !important;
        }

        .badge.bg-info {
            background-color: #0EA5E9 !important;
        }

        /* Progress Bar */
        .progress {
            border-radius: 10px;
            background-color: rgba(37, 187, 162, 0.1);
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .progress-bar.bg-primary {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 100%) !important;
        }

        /* Text Utilities */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-muted {
            color: var(--primary-light) !important;
            opacity: 0.7;
        }

        .text-secondary {
            color: var(--primary-color) !important;
            opacity: 0.8;
        }

        .text-success {
            color: #10B981 !important;
        }

        .text-warning {
            color: var(--primary-light) !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .rank-header-section {
                padding: 1.5rem;
            }
            
            .rank-header-section .d-flex {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .rank-main-image,
            .default-rank-icon-large {
                width: 100px;
                height: 100px;
            }

            .rank-name {
                font-size: 1.5rem;
            }

            .ranks-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }

            .status-badge {
                min-width: auto;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .requirements-section,
            .navigation-section {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }
            
            .rank-header-section {
                padding: 1rem;
            }

            .summary-card {
                margin-bottom: 0.75rem;
            }

            .requirement-card {
                padding: 1rem;
            }

            .requirements-section,
            .navigation-section {
                padding: 1rem;
            }
        }

        /* Animation Keyframes */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Hover Effects */
        .requirement-card:hover .requirement-title {
            color: var(--primary-light);
        }

        .rank-nav-item:hover .nav-rank-name {
            color: var(--primary-light);
        }

        /* Focus States */
        .btn:focus,
        .rank-nav-item:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--accent-light);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }
    </style>
@endpush