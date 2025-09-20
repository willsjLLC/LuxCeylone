@extends($activeTemplate . 'layouts.master')

@section('panel')
    @include('partials.preloader')
    
    <div class="mt-10 dashboard__content">
        <h3 class="mb-4 text-center">LuxCeylone Packages</h3>

        <div class="mb-4">
            <div class="safety-container">
                <h4 class="safety-title text-center">
                    Subscription Details
                </h4>

                <ul class="safety-list">
                    <li>
                        <strong>Current Subscription Status:</strong>
                        @if (auth()->user()->employee_package_activated == 1)
                            <span class="safety-title1">Active</span>
                        @else
                            <span class="safety-title2">Not Active</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        @php
            $activePackage = \App\Models\EmployeePackageActivationHistory::where('user_id', auth()->id())
                ->where('expiry_date', '>', now())
                ->where('activation_expired', \App\Constants\Status::DISABLE)
                ->first();

            $hasExpiredPackage = \App\Models\EmployeePackageActivationHistory::where('user_id', auth()->id())
                ->where('expiry_date', '<', now())
                ->orWhere('activation_expired', \App\Constants\Status::ENABLE)
                ->exists();
        @endphp

        <div class="row justify-content-center g-4">
            @foreach ($packages as $package)
                <div class="col-lg-4 col-md-6">
                    <div class="border-0 shadow-lg card h-100 animate__animated animate__fadeInUp">
                        <div class="card-header text-white text-center fw-bold package-header 
                            {{ $package->type == 1 ? 'basic-header' : ($package->type == 2 ? 'premium-header' : 'enterprise-header') }}">
                            @lang($package->type == 1 ? 'Basic' : ($package->type == 2 ? 'Premium' : 'Enterprise'))
                        </div>

                        <div class="p-4 card-body d-flex flex-column justify-content-between">
                            <div>
                                <h4 class="mb-3 text-center">{{ number_format($package->price) }} LKR</h4>

                                <ul class="list-unstyled text-muted">
                                    <li><i class="fa fa-circle-check text-success me-2"></i>
                                        <strong>Package Duration:</strong> {{ $package->package_duration }} Days
                                    </li>
                                    <li><i class="fa fa-circle-check text-success me-2"></i>
                                        <strong>Ads Duration:</strong> {{ $package->advertisement_duration }} Days
                                    </li>
                                    <li><i class="fa fa-circle-check text-success me-2"></i>
                                        <strong>No of Advertisement:</strong> {{ $package->no_of_advertisements }}
                                    </li>
                                    <li><i class="fa fa-circle-check text-success me-2"></i>
                                        <strong>No of Boost:</strong>
                                        {{ $package->includes_boost ? $package->no_of_boost : 'No Boost Post Available' }}
                                    </li>
                                    @if ($package->description)
                                        <li><i class="fa fa-circle-check text-success me-2"></i>
                                            {{ $package->description }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @if ($package->status == Status::PACKAGE_ACTIVE)
                                <form action="{{ route('user.deposit.employee.package.active') }}" method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $package->id }}">
                                    <button type="submit" class="btn btn-dark w-100">
                                        @lang('Activate Now')
                                    </button>
                                </form>
                            @else
                                <div class="mb-3">
                                    <button type="button" class="btn disabled-btn w-100" disabled>
                                        @lang('Coming Soon')
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        .dashboard__content {
            padding: 20px;
        }

        /* Base Card Styling */
        .card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.12);
        }

        /* Package Headers */
        .package-header {
            padding: 15px;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }

        /* BASIC - Dark Green Gradient */
        .basic-header {
            background: linear-gradient(to bottom, rgb(1, 45, 45), #17433c);
            color: #e8f5e9;
        }

        /* PREMIUM - Dark Blue Gradient */
        .premium-header {
            background: linear-gradient(to bottom, #0a2a4d, #17433c);
            color: #e3f2fd;
        }

        /* ENTERPRISE - Black Gradient */
        .enterprise-header {
            background: linear-gradient(to bottom, #000000, #333333);
            color: #f5f5f5;
        }

        /* Card Body Text */
        .card-body {
            font-size: 0.95rem;
            color: #444;
        }

        /* Price Styling */
        .card-body h4 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #17433c;
        }

        /* Features List */
        .card-body ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        .card-body ul li i {
            font-size: 1rem;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 0;
            font-weight: 600;
        }
        .btn-dark {
            background-color: #17433c;
            border: none;
        }
        .btn-dark:hover {
            background-color: #0f2b27;
        }

        /* Mobile Adjustments */
        @media(max-width:768px) {
            .card {
                margin-bottom: 20px;
            }
        }

        /* Safety tips */
        .safety-container {
            background-color: #fff8e1;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #ffe0b2;
        }

        .safety-title {
            font-size: 16px;
            color: #f57c00;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .safety-title1 {
            font-size: 16px;
            color: #4caf50;
            font-weight: 600;
        }

        .safety-title2 {
            font-size: 16px;
            color: #f44336;
            font-weight: 600;
        }

        .safety-list {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .safety-list li {
            margin-bottom: 0px;
            font-size: 14px;
            color: #555;
        }

        .safety-list li:last-child {
            margin-bottom: 0;
        }
    </style>
@endpush
