@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')

@section('panel')
    <div class="container">
        <div class="arrow-icon">
            <a href="{{ route('user.advertisement.index') }}" class="text-dark">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="text-center col-md-8 col-lg-6 text-md-start">
                <div class="selection-content">
                    <h4 class="mb-4 selection-title">Let's post an ad, Choose the options below</h4>

                    <div class="selection-options">
                        <a href="{{ route('user.advertisement.selectCategory') }}" class="p-3 mb-3 selection-option d-flex justify-content-between align-items-center">
                            <span>Sell an Item or Service</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                        <a href="{{ route('user.advertisement.selectCategory') }}" class="p-3 selection-option d-flex justify-content-between align-items-center">
                            <span>Rent an Item or Service</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Base styles for both mobile and desktop */
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100%;
        }

        .container {
            height: 100vh;
            display: flex;

            position: relative;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .arrow-icon {
            position: absolute;
            top: 10px;
            left: 20px;
            z-index: 1000;
        }

        .selection-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .selection-options {
            background-color: #d9d9d9;
            border-radius: 12px;
            color: #303841;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .selection-option {
            transition: background-color 0.2s ease;
            border-radius: 8px;
            color: #303841;
            text-decoration: none;
        }

        .selection-option:hover {
            background-color: #ebebeb;
            text-decoration: none;
            color: #333;
        }

        .selection-option i {
            color: #777;
        }

        /* Mobile Styles (default) */
        .selection-title {
            margin-top: 30px;
            font-size: 1.4rem;
            text-align: center;
        }

        /* Tablet Styles */
        @media (min-width: 768px) {
            .container {
                padding-top: 80px;
            }

            .selection-title {
                font-size: 1.6rem;
                text-align: left;
                margin-top: 0;
            }

            .selection-content {
                padding: 30px;
            }

            .selection-options {
                width: 100%;
            }
        }

        /* Desktop Styles */
        @media (min-width: 992px) {
            .container {
                justify-content: center;
                align-items: center;
                padding-top: 0;
            }

            .arrow-icon {
                top: 30px;
                left: 30px;
            }

            .selection-title {
                font-size: 1.8rem;
                margin-bottom: 30px;
            }

            .selection-content {
                padding: 40px;
            }

            .selection-option {
                padding: 15px 20px !important;
                font-size: 1.1rem;
            }

            .row {
                width: 100%;
            }
        }
    </style>
@endpush

@push('script')
   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Remove any click event handlers that might be interfering
            const selectionOptions = document.querySelectorAll('.selection-option');
            selectionOptions.forEach(option => {
                // Make sure links work normally without any interference
                option.addEventListener('click', function(e) {
                    // Allow default action (following the href)
                    // Don't add e.preventDefault() here
                });
            });
        });
    </script>
@endpush
