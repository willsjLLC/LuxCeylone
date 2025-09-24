@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')
    <div class="container mt-5">
        <!-- Back button and title -->
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('user.product.index') }}" class="text-dark me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h3 class="mb-0">Affiliates</h3>
        </div>

        <!-- Mobile Order Section - Always at top regardless of screen size -->
        <div class="row">
            <div class="col-12">
                <!-- My Link Section - Only show if user can refer more people -->

               
                <div class="mb-4 border-0 card1">
                    <div class="p-3 card-body p-md-4">
                        <h6 class="mb-3 fw-bold text-white">My Affiliate Link</h6>
                        <div class="input-group">
                            <input type="text" class="bg-white border-0 form-control rounded-start" id="referralLink"
                                value="{{ env('APP_URL') }}/user/register?affiliated_by={{ bin2hex(auth()->user()->username) }}"
                                readonly>
                            <button class="btn copy-btn" onclick="copyReferralLink()">
                                Copy
                            </button>
                        </div>
                    </div>
                    {{-- <div class="p-3 card-body p-md-4">
                        <h6 class="mb-3 fw-bold text-white">My Affiliate Code</h6>
                        <div class="input-group affiliate-code-group">
                            <input type="text" class="bg-white border-0 form-control rounded-start" id="referralCode"
                                value="{{ auth()->user()->four_digit_unique_id }}"
                                readonly>
                            <button class="btn copy-btn" onclick="copyReferralCode()">
                                Copy
                            </button>
                        </div>
                    </div> --}}
                </div>

                @if (auth()->user()->role == \App\Constants\Status::LEADER || auth()->user()->is_top_leader==1)
                    <!-- Progress Bar Section -->
                    <div class="mb-4 bg-white border-0 card">
                        <div class="p-3 card-body p-md-4">
                            <h6 class="mb-3 fw-bold">Affiliate Progress</h6>
                            <div class="affiliate-progress-container">
                                <div class="mb-2 progress-info d-flex justify-content-between align-items-center">
                                    <div class="progress-stats">
                                        <span class="current-count">{{ $totalActiveUsersCount }}</span>
                                        <span class="text-muted"> / 8190</span>
                                    </div>
                                    <div class="progress-percentage">
                                        {{ number_format(($totalActiveUsersCount / 8190) * 100, 2) }}%
                                    </div>
                                </div>
                                <div class="progress-track position-relative">
                                    @php
                                        $progressPercent = min(100, ($totalActiveUsersCount / 8190) * 100);
                                    @endphp
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                    <div class="progress-icons">
                                        <div class="icon-start">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        @if ($progressPercent >= 100)
                                            <div class="icon-end active">
                                                <img src="{{ asset('assets/image/Car.png') }}" alt="car"
                                                    class="car">
                                            </div>
                                        @else
                                            <div class="icon-end">
                                                <img src="{{ asset('assets/image/Car.png') }}" alt="car"
                                                    class="car">
                                            </div>
                                        @endif
                                        <div class="icon-current" style="left: {{ $progressPercent }}%">
                                            <div class="current-marker"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 progress-milestones d-flex justify-content-between">
                                    <small class="text-muted">Start</small>
                                    <small class="text-muted">Win a Car at 8190</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Affiliate Stats Section (Full width on desktop, proper mobile order) -->
            <div class="order-1 col-12 order-lg-1">
                <div class="bg-white border-0 card">
                    <div class="p-3 card-body p-md-4">
                        <h6 class="mb-3 fw-bold">Affiliate Stats</h6>
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="text-center rounded stat-card bg-light">
                                    <div class="stat-value fw-bold">{{ $totalAffiliatesCount }}</div>
                                    <div class="stat-label">Total Affiliates</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 text-center rounded stat-card bg-light">
                                    <div class="stat-value fw-bold">{{ $activeLevelsCount }}</div>
                                    <div class="stat-label">Active Levels</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 text-center rounded stat-card bg-light">
                                    <div class="stat-value fw-bold text-success">{{ $currentActiveUsersCount }}</div>
                                    <div class="stat-label">Current Active</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 text-center rounded stat-card bg-light">
                                    <div class="stat-value fw-bold text-success">{{ $totalActiveUsersCount }}</div>
                                    <div class="stat-label">Total Active</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this button above the My Levels section -->
        <div class="row">
            <div class="order-3 col-6">
                @php
                    $hasLevelsWithUsers = false;
                    foreach ($allLevels as $level => $levelUsers) {
                        if (count($levelUsers) > 0) {
                            $hasLevelsWithUsers = true;
                            break;
                        }
                    }
                @endphp

                @if ($hasLevelsWithUsers)
                    <div class="text-center mb-3">
                        <button id="toggleLevelsBtn" class="btn view-referral-btn" onclick="toggleLevelsSection()">
                            <i class="fa-solid fa-users me-2"></i> Show My Levels
                        </button>
                    </div>
                @endif
            </div>
            <div class="order-3 col-6">
                <div class="text-center mb-3">
                    <button class="btn view-referral-btn">
                       <a href="{{ route('user.training.index') }}" class="text-light"><i class="las la-chalkboard-teacher"></i> Training</a>
                </button>
            </div>
        </div>

        <!-- My Levels Section - Only show if there are levels with users -->
        {{-- <div class="row">
            <div class="order-3 col-12">
                @php
                    $hasLevelsWithUsers = false;
                    foreach ($allLevels as $level => $levelUsers) {
                        if (count($levelUsers) > 0) {
                            $hasLevelsWithUsers = true;
                            break;
                        }
                    }
                @endphp

                @if ($hasLevelsWithUsers)
                    <div class="bg-white border-0 card">
                        <div class="p-3 card-body p-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold">My Levels</h6>
                                <div class="view-referral">
                                    <button onclick="showHierarchyModal()">
                                        <i class="fa-solid fa-sitemap me-2"></i>View Referral Hierarchy
                                    </button>
                                </div>
                            </div>

                            <!-- Only Active Levels using foreach loop -->
                            <div class="levels-container">
                                @forelse($allLevels as $level => $levelUsers)
                                    @if (count($levelUsers) > 0)
                                        <div class="mb-3 level-item">
                                            <div class="p-2 d-flex justify-content-between align-items-center"
                                                data-bs-toggle="collapse" href="#level{{ $level }}Content"
                                                role="button">
                                                <span class="fw-bold">
                                                    Level {{ str_pad($level, 2, '0', STR_PAD_LEFT) }}

                                                     @if ((auth()->user()->role == \App\Constants\Status::LEADER || auth()->user()->is_top_leader==1) && $level > 4)
                                                        <span class="badge bg-warning text-dark ms-2"
                                                            style="font-size: 10px;">Leader Only</span>
                                                     @endif
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="level-count me-3">{{ count($levelUsers) }}</span>
                                                    <i class="fa-solid fa-plus"></i>
                                                </div>
                                            </div>
                                            <div class="collapse" id="level{{ $level }}Content">
                                                <!-- Level content -->
                                                <div class="mt-2 level-users">
                                                    @foreach ($levelUsers as $levelUser)
                                                        <div
                                                            class="py-2 d-flex justify-content-between align-items-center level-user-item">
                                                            <div class="user-name d-flex align-items-center">
                                                                <span class="affiliate-name" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-html="true"
                                                                    title="<div class='tooltip-content'>
                                                                            <strong>{{ $levelUser['firstname'] }} {{ $levelUser['lastname'] }}</strong><br>
                                                                            <small>Mobile No: {{ $levelUser['mobile'] ?? 'N/A' }}</small><br>
                                                                            <small>Package Activation: Rs. {{ $levelUser['package_activation_commission'] }}</small><br>
                                                                            <small>Product Purchase: Rs. {{ $levelUser['product_purchase_commission'] }}</small><br>
                                                                            <small>Bonus Commission: Rs. {{ $levelUser['bonus_commission'] }}</small><br>
                                                                            <strong>Total Commission: Rs. {{ $levelUser['total_commission'] }}</strong>
                                                                            </div>">
                                                                    {{ $levelUser['firstname'] }}
                                                                    {{ $levelUser['lastname'] }}
                                                                </span>
                                                                @if (isset($levelUser['employee_package_activated']) && $levelUser['employee_package_activated'])
                                                                    <span class="active-icon ms-2" title="Package Active">
                                                                        <i class="fa-solid fa-circle text-success"></i>
                                                                        <span class="visually-hidden">Package Active</span>
                                                                    </span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="py-3 text-center text-muted">
                                        <i class="fa-solid fa-users fa-2x mb-2"></i>
                                        <p>No affiliate levels found yet.</p>
                                        <small>Start referring users to build your network!</small>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div> --}}

        <!-- My Levels Section - Now hidden initially -->
        <div class="row">
            <div class="order-3 col-12">
                @if ($hasLevelsWithUsers)
                    <div id="levelsSection" class="bg-white border-0 card" style="display: none;">
                        <div class="p-3 card-body p-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold">My Levels</h6>
                                <div class="view-referral">
                                    <button onclick="showHierarchyModal()">
                                        <i class="fa-solid fa-sitemap me-2"></i>View Referral Hierarchy
                                    </button>
                                </div>
                            </div>

                            <!-- Only Active Levels using foreach loop -->
                            <div class="levels-container">
                                @forelse($allLevels as $level => $levelUsers)
                                    @if (count($levelUsers) > 0)
                                        <div class="mb-3 level-item">
                                            <div class="p-2 d-flex justify-content-between align-items-center"
                                                data-bs-toggle="collapse" href="#level{{ $level }}Content"
                                                role="button">
                                                <span class="fw-bold">
                                                    Level {{ str_pad($level, 2, '0', STR_PAD_LEFT) }}

                                                    @if ((auth()->user()->role == \App\Constants\Status::LEADER || auth()->user()->is_top_leader==1) && $level > 4)
                                                        <span class="badge bg-warning text-dark ms-2"
                                                            style="font-size: 10px;">Leader Only</span>
                                                    @endif
                                                </span>
                                                <div class="d-flex align-items-center">
                                                    <span class="level-count me-3">{{ count($levelUsers) }}</span>
                                                    <i class="fa-solid fa-plus"></i>
                                                </div>
                                            </div>
                                            <div class="collapse" id="level{{ $level }}Content">
                                                <!-- Level content -->
                                                <div class="mt-2 level-users">
                                                    @foreach ($levelUsers as $levelUser)
                                                        <div
                                                            class="py-2 d-flex justify-content-between align-items-center level-user-item">
                                                            <div class="user-name d-flex align-items-center">
                                                                <span class="affiliate-name" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-html="true"
                                                                    title="<div class='tooltip-content'>
                                                                            <strong>{{ $levelUser['firstname'] }} {{ $levelUser['lastname'] }}</strong><br>
                                                                            <small>Mobile No: {{ $levelUser['mobile'] ?? 'N/A' }}</small><br>
                                                                            <small>Package Activation: Rs. {{ $levelUser['package_activation_commission'] }}</small><br>
                                                                            <small>Product Purchase: Rs. {{ $levelUser['product_purchase_commission'] }}</small><br>
                                                                            <small>Bonus Commission: Rs. {{ $levelUser['bonus_commission'] }}</small><br>
                                                                            <strong>Total Commission: Rs. {{ $levelUser['total_commission'] }}</strong>
                                                                            </div>">
                                                                        {{ $levelUser['firstname'] }}
                                                                        {{ $levelUser['lastname'] }}
                                                                    </span>
                                                                @if (isset($levelUser['employee_package_activated']) && $levelUser['employee_package_activated'])
                                                                    <span class="active-icon ms-2" title="Package Active">
                                                                        <i class="fa-solid fa-circle text-success"></i>
                                                                        <span class="visually-hidden">Package Active</span>
                                                                    </span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="py-3 text-center text-muted">
                                        <i class="fa-solid fa-users fa-2x mb-2"></i>
                                        <p>No affiliate levels found yet.</p>
                                        <small>Start referring users to build your network!</small>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
   

    <div class="top-0 p-3 position-fixed end-0" style="z-index: 11">
        <div id="copyToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto text-success">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="copyToastBody">
                Affiliate link copied to clipboard!
            </div>
        </div>
    </div>

    <!-- Referral Hierarchy Modal -->
    <div class="modal fade" id="hierarchyModal" tabindex="-1" aria-labelledby="hierarchyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hierarchyModalLabel">
                        <i class="fa-solid fa-sitemap me-2"></i>My Referral Hierarchy
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="hierarchyTree" class="hierarchy-container">
                        <div class="text-center">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading hierarchy...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

        function toggleLevelsSection() {
            const levelsSection = document.getElementById('levelsSection');
            const toggleBtn = document.getElementById('toggleLevelsBtn');
            
            if (levelsSection.style.display === 'none') {
                levelsSection.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fa-solid fa-users-slash me-2"></i> Hide My Levels';
            } else {
                levelsSection.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fa-solid fa-users me-2"></i> Show My Levels';
            }
        }
        // Function to copy referral link to clipboard
        function copyReferralLink() {
            const referralInput = document.getElementById('referralLink');
            referralInput.select();
            referralInput.setSelectionRange(0, 99999); // For mobile devices

            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(referralInput.value).then(function() {
                    showCopyToast('Affiliate link copied to clipboard!');
                }).catch(function() {
                    // Fallback to execCommand
                    document.execCommand('copy');
                    showCopyToast('Affiliate link copied to clipboard!');
                });
            } else {
                // Fallback for older browsers
                document.execCommand('copy');
                showCopyToast('Affiliate link copied to clipboard!');
            }
        }

        function copyReferralCode() {
            const referralCodeInput = document.getElementById('referralCode');
            referralCodeInput.select();
            referralCodeInput.setSelectionRange(0, 99999); // For mobile devices

            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(referralCodeInput.value).then(function() {
                    showCopyToast('Affiliate code copied to clipboard!');
                }).catch(function() {
                    // Fallback to execCommand
                    document.execCommand('copy');
                    showCopyToast('Affiliate code copied to clipboard!');
                });
            } else {
                // Fallback for older browsers
                document.execCommand('copy');
                showCopyToast('Affiliate code copied to clipboard!');
            }
        }

        function showCopyToast(message) {
            const toastBody = document.getElementById('copyToastBody');
            const toastEl = document.getElementById('copyToast');

            toastBody.textContent = message;
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000 // Show for 3 seconds
            });
            toast.show();
        }

        // Toggle plus/minus icons
        document.addEventListener('DOMContentLoaded', function() {

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    boundary: 'viewport',
                    placement: 'auto',
                    container: 'body',
                    offset: [0, 10]
                });
            });

            const collapseTriggers = document.querySelectorAll('[data-bs-toggle="collapse"]');

            collapseTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('fa-plus')) {
                        icon.classList.remove('fa-plus');
                        icon.classList.add('fa-minus');
                    } else {
                        icon.classList.remove('fa-minus');
                        icon.classList.add('fa-plus');
                    }
                });
            });

            // Handle responsive behavior if needed
            const handleResponsiveLayout = function() {
                const width = window.innerWidth;
                // Additional responsive adjustments can be added here if needed
            };

            // Initial call
            handleResponsiveLayout();

            // Listen for window resize
            window.addEventListener('resize', handleResponsiveLayout);
        });

        function showHierarchyModal() {
            const modal = new bootstrap.Modal(document.getElementById('hierarchyModal'));
            modal.show();
            loadHierarchyData();
        }

        function loadHierarchyData() {
            // Use existing allLevels data from PHP
            const allLevels = @json($allLevels);
            const currentUser = {
                name: "{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}",
                mobile: "{{ auth()->user()->mobile }}",
                isActive: {{ auth()->user()->employee_package_activated ? 'true' : 'false' }}
            };

            renderHierarchy(currentUser, allLevels);
        }

        function renderHierarchy(currentUser, allLevels) {
            const container = document.getElementById('hierarchyTree');

            let html = `
                <div class="hierarchy-tree">
                    <!-- Root User -->
                    <div class="hierarchy-level">
                        <div class="user-node root-user ${currentUser.isActive ? 'active' : ''}">
                            <div class="user-info">
                                <strong>${currentUser.name}</strong>
                                ${currentUser.isActive ? '<span class="active-badge">Active</span>' : ''}
                                <div class="commission-info">
                                    <small>Root User</small>
                                </div>
                            </div>
                        </div>
                    </div>
            `;

            // Render each level
            Object.keys(allLevels).forEach(level => {
                const levelUsers = allLevels[level];
                if (levelUsers.length > 0) {
                    html += `
                        <div class="hierarchy-level">
                            <div class="level-title">Level ${level}</div>
                            <div class="users-row">
                    `;

                    levelUsers.forEach(user => {
                        html += `
                            <div class="user-node ${user.employee_package_activated == 1 ? 'active' : ''}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-bs-html="true"
                                title="<div class='tooltip-content'>
                                        <strong>${user.firstname} ${user.lastname}</strong><br>
                                        <small>Mobile No: ${user.mobile || 'N/A'}</small><br>
                                        <small>Package Activation: Rs. ${user.package_activation_commission}</small><br>
                                        <small>Product Purchase: Rs. ${user.product_purchase_commission}</small><br>
                                        <small>Bonus Commission: Rs. ${user.bonus_commission}</small><br>
                                        <strong>Total Commission: Rs. ${user.total_commission}</strong>
                                        </div>">
                                <div class="user-info">
                                    <strong>${user.firstname} ${user.lastname}</strong>
                                    ${user.employee_package_activated == 1 ? '<span class="active-badge">Active</span>' : ''}

                                </div>
                            </div>
                        `;
                    });

                    html += `
                            </div>
                        </div>
                    `;
                }
            });

            html += '</div>';

            container.innerHTML = html;

            // Initialize tooltips for hierarchy modal
            var tooltipTriggerList = [].slice.call(container.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    boundary: container,
                    placement: 'top',
                    container: container,
                    offset: [0, 10],
                    fallbackPlacements: ['bottom', 'left', 'right']
                });
            });
        }
    </script>
@endpush

@push('style')
    <style>

        .view-referral-btn {
            background-color: #17433c;
            color: white;
            border-radius: 6px;
            display: inline-block;
            white-space: nowrap;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-referral-btn:hover {
            background-color: #1d574d;
        }
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #333;
        }

        .container {
            padding: 0 15px;
            margin: 0 auto;
        }

        /* Card styles */
        .card,
        .card1 {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .card1 {
            /* background: linear-gradient(to bottom, #009933, #b0e892); */
            background: linear-gradient(to bottom, #17433c, #ffffff);
            border-radius: 12px;
        }

        .card-body {
            padding: 20px;
        }

        /* Referral link and copy button */
        #referralLink {
            font-size: 14px;
            height: 44px;
            background-color: white;
            border-radius: 8px 0 0 8px;
            border: none;
            padding-left: 15px;
            overflow: hidden;
            text-overflow: ellipsis;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .input-group {
            border-radius: 8px;
            overflow: hidden;
            flex-wrap: nowrap;
            width: 100%;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        .alert.alert-warning {
            font-size: 0.7em;
        }


        /* Updated styles for affiliate code section */
        .affiliate-code-group {
            max-width: 25%; /* Make the affiliate code input group half width */
        }

        .affiliate-code-group .form-control {
            min-width: 120px; /* Ensure minimum width for readability */
        }

        /* Enhanced input group styles */
        .input-group {
            border-radius: 8px;
            overflow: hidden;
            flex-wrap: nowrap;
            width: 100%;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        /* Ensure affiliate link stays full width */
        .card-body .input-group:not(.affiliate-code-group) {
            width: 100%;
        }

        /* Updated copy button styles for better functionality */
        .copy-btn {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #2c3e50;
            border-radius: 0 8px 8px 0;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 20px;
            height: 44px;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s ease;
            cursor: pointer;
            min-width: 60px;
        }

        .copy-btn:hover {
            background-color: #1e2b38;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .copy-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        /* Enhanced toast styles */
        #copyToast {
            max-width: 300px;
            border-radius: 8px;
            border: 1px solid rgba(0, 153, 51, 0.2);
        }

        #copyToast .toast-header {
            background-color: rgba(0, 153, 51, 0.1);
            border-bottom: 1px solid rgba(0, 153, 51, 0.2);
        }

        /* Mobile responsive adjustments */
        @media (max-width: 767px) {
            .affiliate-code-group {
                max-width: 60%; /* Slightly wider on mobile for better usability */
            }

            .copy-btn {
                padding: 8px 12px;
                font-size: 13px;
                min-width: 50px;
            }

            .affiliate-code-group .form-control {
                min-width: 100px;
                font-size: 13px;
            }
        }

        /* Extra small devices */
        @media (max-width: 575px) {
            .affiliate-code-group {
                max-width: 70%; /* Even wider on very small screens */
            }

            .copy-btn {
                padding: 8px 10px;
                min-width: 45px;
            }

            .affiliate-code-group .form-control {
                min-width: 90px;
                font-size: 12px;
            }
        }

        /* Progress Bar styles */
        .affiliate-progress-container {
            padding: 10px 0;
        }

        .progress-info {
            margin-bottom: 15px;
        }

        .current-count {
            font-weight: bold;
            font-size: 16px;
            color: #009933;
        }

        .progress-percentage {
            font-weight: bold;
            color: #009933;
        }

        .progress-track {
            height: 30px;
            margin-bottom: 10px;
        }

        .progress-bar-wrapper {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            margin-top: 10px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(to right, #009933, #2ecc71, #3498db, #9b59b6, #e74c3c);
            border-radius: 20px;
            transition: width 1s ease-in-out;
        }

        .progress-icons {
            position: absolute;
            width: 100%;
            top: -5px;
        }

        .icon-start {
            position: absolute;
            left: 0;
            background-color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .icon-end {
            position: absolute;
            right: 0;
            background-color: #f8f9fa;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 2;
            transition: all 0.3s ease;
        }

        .icon-end.active {
            background-color: #009933;
            color: white;
            transform: scale(1.2);
            box-shadow: 0 4px 8px rgba(0, 153, 51, 0.3);
        }

        .icon-current {
            position: absolute;
            top: -2px;
            transform: translateX(-50%);
            z-index: 3;
        }

        .current-marker {
            width: 12px;
            height: 12px;
            background-color: #e74c3c;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.3);
            transition: all 0.3s ease;
        }

        .progress-milestones {
            margin-top: 15px;
            padding: 0 10px;
        }

        /* Referral items */
        .referral-item {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding-top: 0px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .referral-item:hover {
            background-color: #f0f0f0;
            transform: translateX(2px);
        }

        .user-name,
        .user-mobile {
            font-size: 14px;
            padding: 0px;
        }

        .user-mobile {
            margin-left: auto;
            color: #666;
        }

        /* Levels section */
        .level-item {
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .level-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .level-item .collapse {
            padding-left: 15px;
            border-left: 3px solid #f1f1f1;
            margin-left: 5px;
            margin-top: 10px;
        }

        .level-user-item {
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .level-user-item:hover {
            background-color: #f9f9f9;
        }

        /* Employee image */
        .employee {
            max-width: 100px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .car {
            max-width: 50px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .employee:hover {
            transform: scale(1.05);
        }

        /* Toast notification */
        #copyToast {
            max-width: 300px;
        }

        /* Active icon styles */
        .active-icon {
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
        }

        .active-icon i {
            filter: drop-shadow(0px 0px 2px rgba(0, 153, 51, 0.5));
        }

        /* Stats cards */
        .stat-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
            height: 100px;
            /* Reduced height for better mobile fit */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .stat-value {
            font-size: 20px;
            /* Slightly smaller for mobile */
            color: #009933;
        }

        .stat-label {
            color: #6c757d;
            font-size: 10px;
            /* Slightly smaller for mobile */
        }

        .view-all-link {
            color: #009933;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .view-all-link:hover {
            text-decoration: underline;
        }

        /* Level badges */
        .badge {
            font-weight: normal;
            font-size: 12px;
        }

        /* Alert for referral limit */
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #856404;
            border-radius: 8px;
            font-size: 13px;
        }

        /* Level count styles */
        .level-count {
            font-size: 14px;
            font-weight: 500;
            color: #009933;
            background-color: rgba(0, 153, 51, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            min-width: 28px;
            text-align: center;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 767px) {
            .card-body {
                padding: 15px;
            }

            .employee {
                max-width: 80px;
            }

            .car {
                max-width: 40px;
            }

            h3 {
                font-size: 20px;
            }

            h6 {
                font-size: 16px;
            }

            #referralLink {
                font-size: 13px;
            }

            .copy-btn {
                padding: 8px 12px;
                font-size: 13px;
            }

            /* Progress bar mobile adjustments */
            .progress-track {
                height: 25px;
            }

            .icon-start,
            .icon-end {
                width: 20px;
                height: 20px;
                font-size: 12px;
            }

            .current-marker {
                width: 10px;
                height: 10px;
            }

            /* Ensure the levels section appears last on mobile */
            .order-3 {
                order: 3;
            }

            /* Stats card mobile adjustments */
            .stat-card {
                padding: 8px;
                height: 80px;
                /* Smaller height for mobile */
            }

            .stat-value {
                font-size: 16px;
            }

            .stat-label {
                font-size: 7px;
            }

            /* Active icon mobile adjustments */
            .active-icon {
                font-size: 8px;
            }
        }

        /* Extra small devices */
        @media (max-width: 575px) {
            .card-body {
                padding: 12px;
            }

            .employee {
                max-width: 70px;
            }

            .car {
                max-width: 40px;
            }

            h3 {
                font-size: 18px;
            }

            .copy-btn {
                padding: 8px 10px;
            }

            .current-count {
                font-size: 14px;
            }

            .progress-percentage {
                font-size: 14px;
            }

            .progress-milestones small {
                font-size: 11px;
            }

            .stat-card {
                padding: 6px;
                height: 70px;
            }

            .stat-value {
                font-size: 14px;
            }

            .stat-label {
                font-size: 10px;
            }
        }

        /* Desktop specific adjustments */
        @media (min-width: 992px) {
            .container {
                padding: 0 30px;
            }

            .levels-container {
                max-height: 600px;
                overflow-y: auto;
                padding-right: 5px;
            }

            .levels-container::-webkit-scrollbar {
                width: 5px;
            }

            .levels-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .levels-container::-webkit-scrollbar-thumb {
                background: #ddd;
                border-radius: 10px;
            }

            .levels-container::-webkit-scrollbar-thumb:hover {
                background: #ccc;
            }

            .card,
            .card1 {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .card:hover,
            .card1:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            }



            /* Progress bar desktop enhancements */
            .progress-bar-wrapper {
                height: 12px;
            }

            .icon-start,
            .icon-end {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .current-marker {
                width: 14px;
                height: 14px;
                box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.3);
            }

            /* Restore desktop layout - levels on left, stats on right */
            .order-lg-1 {
                order: 1;
            }

            .order-lg-2 {
                order: 2;
            }

            /* Active icon desktop enhancements */
            .active-icon {
                font-size: 11px;
            }
        }


        /* View referral hierarchy button adjustments */
        .view-referral button {
            background-color: #17433c;
            color: white;
            border-radius: 6px;
            display: inline-block;
        }

        .view-referral:hover {
            background-color: #1d574d;
        }

        .view-referral button {
            white-space: nowrap;
            padding: 8px 16px;
        }


        @media (max-width: 767px) {
            .view-referral button {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
    </style>

    <style>
        /* Hierarchy Modal Styles */
        .hierarchy-container {
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            min-height: 400px;
            overflow-x: auto;
        }

        .hierarchy-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            min-width: max-content;
            padding: 20px;
        }

        .hierarchy-level {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .level-title {
            background: #009933;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 153, 51, 0.3);
        }

        .users-row {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
        }

        .user-node {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            min-width: 150px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .user-node:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .user-node.active {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }

        .user-node.root-user {
            background: linear-gradient(135deg, #009933 0%, #28a745 100%);
            color: white;
            border-color: #009933;
            transform: scale(1.1);
        }

        .user-info strong {
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        .user-info small {
            font-size: 12px;
            opacity: 0.8;
        }

        .active-badge {
            background: #28a745;
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 5px;
        }

        .root-user .active-badge {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Connection Lines */
        .hierarchy-level:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 30px;
            background: linear-gradient(to bottom, #009933, #28a745);
            z-index: 1;
        }

        .users-row::before {
            content: '';
            position: absolute;
            top: -30px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(to right, #009933, #28a745);
            z-index: 1;
        }

        .users-row .user-node::before {
            content: '';
            position: absolute;
            top: -32px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 32px;
            background: linear-gradient(to bottom, #009933, #28a745);
            z-index: 1;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hierarchy-container {
                padding: 10px;
                overflow-x: auto;
            }

            .hierarchy-tree {
                gap: 20px;
                padding: 10px;
            }

            .user-node {
                min-width: 120px;
                padding: 10px;
            }

            .users-row {
                gap: 15px;
            }

            .level-title {
                font-size: 14px;
                padding: 6px 12px;
            }
        }

        /* Modal specific adjustments */
        .modal-xl {
            max-width: 95%;
        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }
        }
    </style>

    <style>
        /* Commission display in level items */
        .commission-display {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .commission-display small {
            font-size: 12px;
            font-weight: 600;
            background: rgba(0, 153, 51, 0.1);
            padding: 2px 6px;
            border-radius: 8px;
        }

        /* Updated level user item with commission */
        .level-user-item {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .level-user-item:hover {
            background-color: #f9f9f9;
            transform: translateX(2px);
        }

        /* Hierarchy modal user nodes with commission info */
        .user-info .commission-info {
            margin-top: 5px;
            font-size: 11px;
            color: #28a745;
            font-weight: bold;
        }

        .hierarchy-container .user-node {
            min-width: 160px;
            /* Slightly wider to accommodate commission info */
        }

        /* Enhanced tooltip styles for commission data */
        .tooltip-content {
            line-height: 1.5;
            font-size: 12px;
        }

        .tooltip-content strong {
            color: #3498db;
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .tooltip-content small {
            color: #ecf0f1;
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .tooltip-content small:last-child {
            margin-bottom: 0;
        }

        /* Commission amount highlighting in tooltip */
        .tooltip-content small:contains("Package Activation"),
        .tooltip-content small:contains("Product Purchase"),
        .tooltip-content strong:contains("Total Commission") {
            color: #2ecc71;
            font-weight: bold;
        }

        /* Mobile responsive adjustments for commission display */
        @media (max-width: 767px) {
            .commission-display small {
                font-size: 11px;
                padding: 1px 4px;
            }

            .user-info .commission-info {
                font-size: 10px;
            }

            .hierarchy-container .user-node {
                min-width: 140px;
            }
        }

        /* Extra small devices */
        @media (max-width: 575px) {
            .commission-display small {
                font-size: 10px;
            }

            .user-info .commission-info {
                font-size: 9px;
            }
        }

        /* Enhanced active status for users with commissions */
        .level-user-item .user-name .affiliate-name {
            position: relative;
        }

        .level-user-item .user-name .affiliate-name:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #009933, transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .level-user-item:hover .user-name .affiliate-name:after {
            opacity: 1;
        }

        /* Updated styles for affiliate names with tooltips */
        .affiliate-name {
            cursor: pointer;
            color: #333;
            transition: color 0.2s ease;
            text-decoration: none;
            border-bottom: 1px dotted #ccc;
        }

        .affiliate-name:hover {
            color: #009933;
            border-bottom-color: #009933;
        }

        /* Custom tooltip styles */
        .tooltip {
            font-size: 12px;
        }

        .tooltip-inner {
            background-color: #2c3e50;
            color: white;
            border-radius: 8px;
            padding: 10px 12px;
            max-width: 250px;
            text-align: left;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .tooltip-content {
            line-height: 1.4;
        }

        .tooltip-content strong {
            color: #3498db;
            display: block;
            margin-bottom: 4px;
        }

        .tooltip-content small {
            color: #ecf0f1;
            display: block;
            margin-bottom: 2px;
        }

        .tooltip.bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #2c3e50;
        }

        .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
            border-bottom-color: #2c3e50;
        }

        .tooltip.bs-tooltip-start .tooltip-arrow::before {
            border-left-color: #2c3e50;
        }

        .tooltip.bs-tooltip-end .tooltip-arrow::before {
            border-right-color: #2c3e50;
        }

        /* Updated referral item styles */
        .referral-item {
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            padding: 12px;
        }

        .referral-item:hover {
            background-color: #f0f0f0;
            transform: translateX(2px);
        }

        /* Updated level user item styles */
        .level-user-item {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: background-color 0.2s ease;
        }

        .level-user-item:hover {
            background-color: #f9f9f9;
        }

        /* Hierarchy modal user nodes with tooltips */
        .hierarchy-container .user-node {
            cursor: pointer;
        }

        .hierarchy-container .user-node:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Mobile responsive adjustments for tooltips */
        @media (max-width: 767px) {
            .tooltip-inner {
                font-size: 11px;
                padding: 8px 10px;
                max-width: 200px;
            }

            .tooltip-content strong {
                font-size: 12px;
            }

            .tooltip-content small {
                font-size: 10px;
            }
        }
    </style>
@endpush
