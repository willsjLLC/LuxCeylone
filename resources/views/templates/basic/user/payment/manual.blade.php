
@extends($activeTemplate . 'layouts.master')

@section('panel')
    <div class="dashboard__content contact__form__wrapper">
        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="text-center mt-2">@lang('You have requested') <b class="text--success">{{ showAmount($data['amount']) }}
                            </b> , @lang('Please pay')
                        <b class="text--success">{{ showAmount($data['final_amount'], currencyFormat: false) . ' ' . $data['method_currency'] }}
                        </b> @lang('for successful payment')
                    </p>
                    <h4 class="text-center mb-4">@lang('Please follow the instruction below')</h4>

                    <div class="gateway-description my-4 text-left position-relative">
                        <!-- We'll transform this with JavaScript -->
                        @php
                            $description = $data->gateway->description;
                            echo $description;
                        @endphp
                    </div>
                </div>

                <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                <div class="col-md-12">
                    <button type="submit" class="btn btn--base w-100">@lang('Pay Now')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bankDetailsContainer = document.querySelector('.gateway-description');

            // First, remove italic styling from all elements
            const allElements = bankDetailsContainer.querySelectorAll('*');
            allElements.forEach(function(el) {
                if (el.style) {
                    el.style.fontStyle = 'normal';
                }
                // Additionally add a class to ensure CSS can override
                el.classList.add('no-italic');
            });

            if (bankDetailsContainer) {
                // Find all paragraphs with bank details
                const paragraphs = bankDetailsContainer.querySelectorAll('p');

                paragraphs.forEach(function(paragraph) {
                    const text = paragraph.textContent.trim();

                    // Only process bank detail paragraphs
                    if (text.includes('Bank Name:') ||
                        text.includes('Branch:') ||
                        text.includes('Name:') ||
                        text.includes('Account Number:')) {

                        // Split the paragraph text into label and value without removing space after colon
                        const colonIndex = text.indexOf(':');
                        if (colonIndex !== -1) {
                            // Extract label including the colon - don't add spaces
                            const label = text.substring(0, colonIndex + 1).trim();
                            const value = text.substring(colonIndex + 1).trim();

                            // Clear the paragraph's content
                            paragraph.innerHTML = '';

                            // Create label and value spans with no space between them
                            const labelSpan = document.createElement('span');
                            labelSpan.classList.add('label', 'no-italic');
                            labelSpan.textContent = label; // Label includes the colon

                            const valueSpan = document.createElement('span');
                            valueSpan.classList.add('value', 'no-italic');
                            valueSpan.textContent = value;

                            // Create copy button
                            const copyButton = document.createElement('button');
                            copyButton.classList.add('copy-detail');
                            copyButton.type = 'button';
                            copyButton.innerHTML = '<i class="fa-solid fa-copy"></i>';

                            // Add copy functionality
                            copyButton.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation(); // Prevent event bubbling

                                // Copy value to clipboard
                                navigator.clipboard.writeText(value)
                                    .then(() => {
                                        // Show success feedback
                                        const originalHTML = copyButton.innerHTML;
                                        copyButton.innerHTML = '<i class="fa-solid fa-check"></i>';

                                        // Reset button after 2 seconds
                                        setTimeout(() => {
                                            copyButton.innerHTML = originalHTML;
                                        }, 2000);
                                    })
                                    .catch(err => {
                                        console.error('Failed to copy: ', err);
                                        copyButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';

                                        setTimeout(() => {
                                            copyButton.innerHTML = '<i class="fa-solid fa-copy"></i>';
                                        }, 2000);
                                    });
                            });

                            // Create container for label and value with no space between
                            const textContainer = document.createElement('div');
                            textContainer.classList.add('bank-detail-text', 'no-italic');
                            textContainer.appendChild(labelSpan);
                            textContainer.appendChild(valueSpan);

                            // Append all elements to the paragraph
                            paragraph.appendChild(textContainer);
                            paragraph.appendChild(copyButton);
                        }
                    }
                });
            }
        });
    </script>
    <style>
        .copy-detail {
            display: none; /* Hide by default */
            align-items: center;
            justify-content: center;
            font-family: "Montserrat", sans-serif;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            border-radius: 5px;
            transition: all ease 0.3s;
            border: 1px solid #0d6efd;
            outline: none;
            color: #0d6efd;
            background-color: transparent;
        }

        /* Show copy button on hover */
        .gateway-description p:hover .copy-detail {
            display: inline-flex;
        }

        .copy-detail:hover {
            background-color: #0d6efd;
            color: white;
        }

        /* Bank details container styling */
        .gateway-description p {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
            position: relative; /* For positioning the copy button */
        }

        /* Remove italic styling from all gateway description text */
        .gateway-description * {
            font-style: normal !important;
        }

        /* Container for label and value with no spacing */
        .bank-detail-text {
            display: flex;
            flex-grow: 1;
            margin-right: 10px;
        }

        /* Label styling - no right margin to eliminate space */
        .gateway-description p .label {
            font-weight: bold;
            margin-right: 0; /* No space after label */
        }

        .gateway-description p .value {
            margin-left: 0; /* No space before value */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .gateway-description p {
                flex-direction: column;
                align-items: flex-start;
            }

            .bank-detail-text {
                margin-bottom: 5px;
                width: 100%;
            }

            .copy-detail {
                align-self: flex-end;
            }

            /* Always show copy button on mobile */
            .gateway-description p .copy-detail {
                display: inline-flex;
            }
        }
    </style>
@endpush
