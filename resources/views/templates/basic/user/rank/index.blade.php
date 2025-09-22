@extends($activeTemplate . 'layouts.master')
@section('panel')
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5">
                <div class="main-card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>{{ $pageTitle }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Current Status -->
                        <div class="status-notification text-center mt-4 mb-3">
                            <p class="text-white mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Rank will automatically update after complete task within 20 minutes
                            </p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="status-card">
                                    <h6 class="status-title mb-2">Current Status</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="status-icon me-3">
                                            <i class="fas fa-star text-warning fa-2x"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 status-rank-name">
                                                {{ $currentRank ? $currentRank->name : 'No Rank' }}
                                            </h5>
                                            <p class="status-rank-level mb-0">
                                                {{ $currentRank ? 'Rank ' . $currentRank->rank : 'Starting Level' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="text-end">
                                    <button class="btn btn-outline-primary" onclick="showAllRanks()">
                                        <i class="fas fa-list me-1"></i>All Ranks
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div class="progress-section">
                            <h5 class="section-title mb-3">Progress to Next Rank</h5>

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
                                                    <i class="fas fa-user"></i>
                                                @endif
                                            </div>
                                            <div class="rank-label">
                                                {{ $currentRank ? $currentRank->name : 'Start' }}
                                            </div>
                                        </div>

                                        <!-- Progress Line (visible on desktop only) -->
                                        <div class="progress-line d-none d-md-block">
                                            <div class="progress-fill" style="width: {{ $progressData['progress'] }}%"></div>
                                            <div class="progress-percentage">{{ number_format($progressData['progress'], 1) }}%</div>
                                        </div>

                                        <!-- Next Rank Circle -->
                                        @if ($nextRank)
                                            <div class="rank-circle next-rank">
                                                <div class="rank-icon">
                                                    <img alt="rank-image"
                                                        src="{{ getImage('assets/admin/images/rank/' . @$nextRank->image, '400x400') }}">
                                                </div>
                                                <div class="rank-label">{{ $nextRank->name }}</div>
                                            </div>
                                        @else
                                            <div class="rank-circle future-rank">
                                                <div class="rank-icon">
                                                    <i class="fas fa-crown"></i>
                                                </div>
                                                <div class="rank-label">Max Rank</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Mobile Progress Line Row (only visible on mobile) -->
                                    <div class="progress-line-row d-md-none px-3">
                                        <div class="progress-line">
                                            <div class="progress-fill" style="width: {{ $progressData['progress'] }}%"></div>
                                            <div class="progress-percentage ">{{ number_format($progressData['progress'], 1) }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements Details -->
                            @if ($nextRank && count($progressData['requirements']) > 0)
                                <div class="requirements-section mt-4">
                                    <h6 class="requirements-title mb-3">
                                        <i class="fas fa-tasks me-2"></i>Requirements for {{ $nextRank->name }}
                                        <span class="badge bg-primary ms-2">{{ number_format($progressData['progress'], 1) }}% Complete</span>
                                        @if (isset($progressData['completed_requirements']) && isset($progressData['total_requirements']))
                                            <span class="badge bg-info ms-2">
                                                {{ $progressData['completed_requirements'] }}/{{ $progressData['total_requirements'] }} Requirements Met
                                            </span>
                                        @endif
                                    </h6>

                                    <div class="row">
                                        @foreach ($progressData['requirements'] as $key => $requirement)
                                            <div class="col-md-6 mb-3">
                                                <div class="requirement-card {{ $requirement['completed'] ? 'completed' : 'pending' }}">
                                                    <div class="req-icon">
                                                        @if ($requirement['completed'])
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        @else
                                                            <i class="fas fa-clock text-warning"></i>
                                                        @endif
                                                    </div>
                                                    <div class="req-content">
                                                        <h6 class="req-title">
                                                            {{ $requirement['label'] }}
                                                            @if ($requirement['completed'])
                                                                <i class="fas fa-check text-success ms-1"></i>
                                                            @endif
                                                        </h6>
                                                        <p class="req-progress mb-0">
                                                            {{ number_format($requirement['current']) }} / {{ number_format($requirement['required']) }}
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
                                                            <small class="req-remaining">
                                                                {{ number_format($requirement['required'] - $requirement['current']) }} more needed
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif ($nextRank)
                                <div class="requirements-section mt-4">
                                    <div class="text-center py-4">
                                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                                        <h5 class="congratulations-title">Congratulations!</h5>
                                        <p class="congratulations-text">You have met all requirements for {{ $nextRank->name }}. Your rank will be updated automatically within 20 minutes.</p>
                                    </div>
                                </div>
                            @else
                                <div class="requirements-section mt-4">
                                    <div class="text-center py-4">
                                        <i class="fas fa-trophy fa-3x text-gold mb-3"></i>
                                        <h5 class="max-rank-title">Maximum Rank Achieved!</h5>
                                        <p class="max-rank-text">You have reached the highest rank available. Congratulations on your achievement!</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Ranks Modal -->
    <div class="modal fade mt-10" id="allRanksModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
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
                                            <h6 class="rank-name">{{ $rank->name }}</h6>
                                            <p class="rank-level mb-0">Rank {{ $rank->rank }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="rank-actions">
                                        @if ($currentRank && $rank->rank <= $currentRank->rank)
                                            <span class="badge bg-success">Achieved</span>
                                        @elseif ($rank->rank == $currentRankLevel + 1)
                                            <span class="badge bg-warning">Next Rank</span>
                                        @else
                                            <span class="badge bg-secondary">Locked</span>
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
        :root {
            --primary-color: #17433c;
            --primary-light: #25BBA2;
            --accent-color: #CFFFDF;
            --accent-light: #f8fcf9;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(37, 187, 162, 0.2);
            --shadow-light: 0 4px 12px rgba(37, 187, 162, 0.08);
            --shadow-medium: 0 8px 20px rgba(37, 187, 162, 0.12);
            --shadow-heavy: 0 12px 30px rgba(37, 187, 162, 0.16);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        .main-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--glass-border);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 20px;
            border-bottom: none;
        }

        .card-header h4 {
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 30px;
            background: transparent;
        }

        .status-notification {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 15px 20px;
            border-radius: var(--border-radius);
            color: white;
            box-shadow: var(--shadow-light);
        }

        .status-card {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            padding: 25px;
            border-radius: var(--border-radius);
            border: 2px solid var(--glass-border);
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .status-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .status-rank-name {
            color: var(--primary-color);
            font-weight: bold;
        }

        .status-rank-level {
            color: var(--primary-light);
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 10px 20px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
        }

        .rank-progress-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: var(--border-radius);
            margin: 20px 0;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-light);
        }

        .rank-circle {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .rank-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            border: 3px solid var(--glass-border);
            transition: var(--transition);
        }

        .rank-icon img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .current-rank .rank-icon {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: 4px solid white;
            box-shadow: 0 8px 25px rgba(37, 187, 162, 0.4);
            transform: scale(1.1);
        }

        .next-rank .rank-icon {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--primary-light) 100%);
            color: var(--primary-color);
            border: 4px solid var(--primary-light);
            animation: pulse 2s infinite;
            box-shadow: 0 8px 25px rgba(37, 187, 162, 0.3);
        }

        .future-rank .rank-icon {
            background: #f8f9fa;
            color: #6c757d;
            border: 4px solid #e9ecef;
        }

        .rank-label {
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            color: var(--primary-color);
            min-width: 80px;
        }

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
            display: none;
        }

        .progress-line {
            flex: 1;
            height: 12px;
            background: rgba(37, 187, 162, 0.1);
            border-radius: 6px;
            margin: 0 30px;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 50%, #00d4aa 100%);
            border-radius: 6px;
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
            
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            box-shadow: var(--shadow-light);
            z-index: 3;
            white-space: nowrap;
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .progress-percentage::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--primary-color);
        }

        .requirements-section {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-light);
        }

        .requirements-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .requirement-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: var(--glass-bg);
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid transparent;
            transition: var(--transition);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .requirement-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
        }

        .requirement-card.completed {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            border-color: #10B981;
        }

        .requirement-card.pending {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent-color) 100%);
            border-color: var(--primary-light);
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

        .req-title {
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .req-progress {
            color: var(--primary-light);
        }

        .req-remaining {
            color: var(--primary-color);
        }

        .mini-progress {
            width: 100%;
            height: 6px;
            background: rgba(37, 187, 162, 0.1);
            border-radius: 3px;
            overflow: hidden;
            margin: 8px 0 5px 0;
        }

        .mini-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .congratulations-title, .max-rank-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .congratulations-text, .max-rank-text {
            color: var(--primary-light);
        }

        /* Modal styles */
        .modal-content {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border-bottom: none;
        }

        .ranks-overview {
            max-height: 70vh;
            overflow-y: auto;
        }

        .rank-item {
            margin-bottom: 15px;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            transition: var(--transition);
            background: var(--glass-bg);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .rank-item:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
        }

        .rank-item.current {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border-color: var(--primary-color);
        }

        .rank-info-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
        }

        .rank-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .rank-star {
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .rank-name {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .rank-level {
            opacity: 0.8;
        }

        .rank-actions .badge {
            padding: 8px 16px;
            font-size: 0.875rem;
            border-radius: 20px;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-info {
            background-color: var(--primary-light) !important;
        }

        .badge.bg-success {
            background-color: #10B981 !important;
        }

        .badge.bg-warning {
            background-color: #F59E0B !important;
        }

        .badge.bg-secondary {
            background-color: #6B7280 !important;
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
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
                background: rgba(37, 187, 162, 0.1);
                border-radius: 6px;
                position: relative;
                overflow: visible;
            }

            .progress-ranks-row .progress-line {
                display: none;
            }

            .rank-icon {
                width: 60px;
                height: 60px;
            }

            .rank-icon img {
                width: 50px;
                height: 50px;
            }

            .progress-line-row .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 50%, #00d4aa 100%);
                border-radius: 6px;
                transition: width 0.8s ease;
                position: relative;
                overflow: hidden;
            }

            .progress-line-row .progress-percentage {
                position: absolute;
                top: -25px;
                
                transform: translateX(-50%);
                background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 100%);
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
                border-top: 4px solid var(--primary-color);
            }

            .rank-label {
                font-size: 0.8rem;
                min-width: 60px;
            }

            .current-rank .rank-icon {
                transform: scale(1.05);
            }

            .card-body {
                padding: 20px;
            }

            .status-card {
                padding: 20px;
            }

            .rank-progress-container {
                padding: 20px;
            }

            .requirements-section {
                padding: 20px;
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Text color utilities */
        .text-gold {
            color: #F59E0B !important;
        }
    </style>
@endpush

@push('script')
    <script>
        function showAllRanks() {
            $('#allRanksModal').modal('show');
        }

        // Auto-update notification
        @if (session('rank_updated'))
            setTimeout(() => {
                toastr.success('Congratulations! Your rank has been updated!', 'Rank Updated');
            }, 1000);
        @endif

        // Update progress position for responsive design
        function updateProgressPosition() {
            const percentage = {{ $progressData['progress'] }};
            
            // Mobile layout
            if (window.innerWidth <= 768) {
                const mobileProgressPercentage = document.querySelector('.progress-line-row .progress-percentage');
                if (mobileProgressPercentage) {
                    mobileProgressPercentage.style.left = Math.max(10, Math.min(90, percentage)) + '%';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(updateProgressPosition, 200);
        });

        window.addEventListener('resize', function() {
            setTimeout(updateProgressPosition, 100);
        });
    </script>
@endpush