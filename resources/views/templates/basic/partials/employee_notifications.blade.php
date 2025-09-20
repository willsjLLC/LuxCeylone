@if ($needsTopUp || !$isPackageActive)
    <div class="package-activation-notice">
        <div class="package-message">
            <div class="package-text">
                @if ($needsTopUp && !$isPackageActive)
                    {{-- Needs both top-up AND initial package activation --}}
                    <strong>
                        @lang('Rs.') {{ showAmount($outstandingTopUpAmount) }}
                        @lang('TOP-UP & Package Activation Required')
                    </strong>
                    <br>
                    @if ($skippedPackages > 0)
                        @lang('You have') <strong>{{ $skippedPackages }}</strong> @lang('skipped packages to activate.')
                        @lang('Please Top-Up & Update Your Account by activating your LUXCEYLONE Advertisement Package to launch your advertisements. For any inquiries, feel free to get in touch with support.')
                    @else
                        @lang('Please Top-Up & Update Your Account by activating your LUXCEYLONE Advertisement Package to launch your advertisements. For any inquiries, feel free to get in touch with support.')
                    @endif
                @elseif ($needsTopUp && $isPackageActive)
                    {{-- Needs a top-up only --}}
                    <strong>
                        @lang('Rs.') {{ showAmount($outstandingTopUpAmount) }}
                        @lang('TOP-UP Required')
                    </strong>
                    <br>
                    @if ($skippedPackages > 0)
                        @lang('You have') <strong>{{ $skippedPackages }}</strong> @lang('skipped packages to activate.')
                        @lang('Please Top-Up Your Account with a new package to continue earning. For any inquiries, feel free to get in touch with support.')
                    @else
                        @lang('Please Top-Up Your Account with a new package to continue earning. For any inquiries, feel free to get in touch with support.')
                    @endif
                @else
                    {{-- Needs a normal package activation only --}}
                    @lang('Please activate a')
                    <strong>@lang('LUXCEYLONE Advertisement Package')</strong>
                    @lang('to launch your advertisements. For any inquiries, feel free to get in touch with support.')
                @endif
            </div>
        </div>
    </div>
@endif

@push('scripts')
    <style>
        /* Package activation notice styling */
        .package-activation-notice {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .package-message {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .package-icon {
            color: #027c68;
            font-size: 22px;
            padding-top: 2px;
        }

        .package-text {
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }

        .package-text strong {
            color: #027c68;
            font-weight: 600;
        }

        .package-btn {
            background-color: #027c68;
            color: white;
            text-align: center;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            align-self: flex-start;
            transition: background-color 0.3s ease;
            border: none;
        }

        .package-btn:hover {
            background-color: #016353;
            color: white;
            text-decoration: none;
        }

        @media (min-width: 768px) {
            .package-activation-notice {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .package-message {
                flex: 1;
            }

            .package-btn {
                align-self: center;
            }
        }
    </style>
@endpush
