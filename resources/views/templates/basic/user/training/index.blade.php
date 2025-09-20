@extends($activeTemplate . 'layouts.master')
@section('panel')
    @include('partials.preloader')

    <div class="container training-container">

        <div class="safety-container mb-2">
            <h3 class=" text-center">
                Training Eligibilities
            </h3>
            <ul class="safety-title text-center">
                <li>
                    <strong>Your Total Commission Earnings: <strong class="text-success">
                            Rs. {{ number_format($user->total_earning, 2) }}</strong></strong>
                </li>
            </ul>
        </div>

        <div class="row g-4">
            @foreach ($trainings as $training)
                <div class="col-lg-3 col-md-6">
                    @php
                        $userTrainingRecord = $userTrainingData->firstWhere('training_id', $training->id);
                        $buttonText = 'Buy Ticket';
                        $isDisabled = false;
                        $buttonClass = 'buy-ticket-btn';

                        if ($userTrainingRecord) {
                            $isDisabled = true;
                            $buttonClass = 'buy-ticket-btn disabled';

                            if ($userTrainingRecord->status == 1) {
                                $buttonText = 'Pending';
                            } elseif ($userTrainingRecord->status == 2) {
                                $buttonText = 'Completed';
                            } elseif ($userTrainingRecord->status == 3) {
                                $buttonText = 'Rejected';
                            } else {
                                $buttonText = 'Enrolled';
                            }
                        }
                    @endphp

                    <div class="training-card p-1" data-bs-toggle="modal" data-bs-target="#trainingModal"
                        data-id="{{ $training->id }}" data-name="{{ $training->name }}"
                        data-price="{{ number_format($training->ticket_price, 2) }}"
                        data-image="{{ getImage(getFilePath('training') . '/' . $training->image, getFileSize('training')) }}"
                        data-min_income_threshold="{{ number_format($training->min_income_threshold, 2) }}"
                        data-description="{{ $training->description ?? 'No description available.' }}"
                        data-button-text="{{ $buttonText }}" data-is-disabled="{{ $isDisabled ? 'true' : 'false' }}"
                        data-status="{{ $userTrainingRecord ? $userTrainingRecord->status : 0 }}">
                        <div class="card-image">
                            <img src="{{ getImage(getFilePath('training') . '/' . $training->image, getFileSize('training')) }}"
                                alt="{{ $training->name }}">
                        </div>

                        <div>
                            <span class="fw-bold"> Target: <span class="text-success"> Rs.
                                    {{ number_format($training->min_income_threshold, 2) }}</span></span>
                            <span class="fw-bold"> Ticket Price: <span class="text-danger"> Rs.
                                    {{ number_format($training->ticket_price, 2) }}</span></span>
                        </div>

                        <div class="card-content">
                            <button type="button" class="{{ $buttonClass }}" id="buyTicketBtn"
                                {{ $isDisabled ? 'disabled' : '' }}
                                data-status="{{ $userTrainingRecord ? $userTrainingRecord->status : 0 }}">
                                <i class="fas fa-ticket-alt me-2"></i>{{ $buttonText }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="trainingModal" tabindex="-1" aria-labelledby="trainingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-white">Training Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body align-item-center justify-content-center d-flex">
                    <img id="modalImage" src="" alt="Training Image" class="modal-image">
                </div>
                <div class="modal-info">
                    <!-- Desktop version (sm and above) -->
                    <div class="price-container d-none d-sm-block text-start">

                        <div class="mb-2">
                            <span class="fw-bold">Ticket Price: Rs. </span>
                            <span class="modal-price">0</span>
                        </div>
                        <div class="mb-2">
                            <span class="fw-bold">Minimum Target: Rs. </span>
                            <span class="modal-t-price">0</span>

                        </div>
                    </div>

                    <!-- Mobile version (below md) -->
                    <div class="d-block d-md-none text-start">
                        <div class="mb-2">
                            <span class="fw-bold">Ticket Price: Rs. </span>
                            <span class="modal-price">0</span>
                        </div>

                        <div class="mb-2">
                            <span class="fw-bold">Minimum Target: Rs. </span>
                            <span class="modal-t-price">0</span>
                        </div>
                    </div>
                    <button type="button" class="buy-ticket-btn" id="modalBuyTicketBtn">
                        <i class="fas fa-ticket-alt me-2"></i>Buy Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
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

        .price-container {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: center;
        }

        .training-container {
            padding: 2rem 0;
        }

        .training-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(114, 114, 113, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
            position: relative;
            opacity: 0;
        }

        .training-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(96, 97, 96, 0.4);
        }

        .card-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .training-card:hover .card-image img {
            transform: scale(1.05);
        }

        .price-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #ffd06bff, #ee8624ff);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 8px 25px rgba(127, 243, 18, 0.4);
        }

        .card-content {
            padding: 1.5rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .claim-btn {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 25px rgba(127, 243, 18, 0.4);
        }

        .claim-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(127, 243, 18, 0.4);
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 0;
        }

        .modal-image {
            width: 600px;
            height: 600px;
            object-fit: cover;
        }

        .modal-info {
            padding: 2rem;
            text-align: center;
        }

        .modal-price {
            font-size: 2rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 1rem;
        }

        .modal-t-price {
            font-size: 2rem;
            font-weight: 700;
            color: #3ce767ff;
            margin-bottom: 1rem;
        }

        .modal-description {
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .modal-footer {
            border: none;
            padding: 1.5rem 2rem;
            background: #f8f9fa;
        }

        .buy-ticket-btn {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 30px;
            width: 100%;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 25px rgba(127, 243, 18, 0.4);
        }

        .buy-ticket-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(127, 243, 18, 0.4);
        }

        /* Status-based button colors */
        .buy-ticket-btn.disabled {
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .buy-ticket-btn.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Pending - Ash/Gray */
        .buy-ticket-btn[data-status="1"] {
            background: linear-gradient(45deg, #6c757d, #5a6268);
            color: white;
        }

        /* Completed - Green */
        .buy-ticket-btn[data-status="2"] {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }

        /* Rejected - Red */
        .buy-ticket-btn[data-status="3"] {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }

        /* Enrolled - Default disabled (ash) */
        .buy-ticket-btn.disabled:not([data-status="1"]):not([data-status="2"]):not([data-status="3"]) {
            background: linear-gradient(45deg, #6c757d, #5a6268);
            color: white;
        }

        .close-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .training-container {
                padding: 1rem;
            }

            .card-image {
                height: 200px;
            }

            .modal-image {
                height: 300px;
            }

            .modal-info {
                padding: 1.5rem;
            }

            .modal-price {
                font-size: 1.5rem;
            }

            .modal-t-price {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-content {
                padding: 1rem;
            }

            .card-title {
                font-size: 1rem;
            }

            .claim-btn {
                padding: 10px 20px;
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            const trainingModal = $('#trainingModal');
            const modalBuyTicketBtn = $('#modalBuyTicketBtn');

            trainingModal.on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');
                const price = button.data('price');
                const image = button.data('image');
                const min_income_threshold = button.data('min_income_threshold');
                const description = button.data('description');
                const buttonText = button.data('button-text') || 'Buy Ticket';
                const isDisabled = button.data('is-disabled') === 'true';
                const status = button.data('status') || 0;

                $('#trainingModalLabel').text(name);
                $('#modalImage').attr('src', image);
                $('.modal-price').text(price);
                $('.modal-t-price').text(min_income_threshold);

                modalBuyTicketBtn.data('id', id);
                modalBuyTicketBtn.html('<i class="fas fa-ticket-alt me-2"></i>' + buttonText);
                modalBuyTicketBtn.attr('data-status', status);

                if (isDisabled) {
                    modalBuyTicketBtn.addClass('disabled').prop('disabled', true);
                } else {
                    modalBuyTicketBtn.removeClass('disabled').prop('disabled', false);
                }
            });

            modalBuyTicketBtn.on('click', function() {
                const id = $(this).data('id');

                if (id && !$(this).hasClass('disabled')) {
                    $.ajax({
                        url: "{{ route('user.deposit.training.ticket.buy', '') }}/" + id,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            training_id: id
                        },
                        success: function(response) {
                            iziToast.success({
                                title: response.status,
                                message: response.message,
                                position: "topRight",
                                timeout: 3000,
                            });

                            trainingModal.modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        },
                        error: function(xhr) {

                            try {
                                const response = JSON.parse(xhr.responseText);

                                iziToast.error({
                                    title: response.status || 'Error',
                                    message: response.message ||
                                        'An unknown error occurred.',
                                    position: "topRight",
                                    timeout: 3000,
                                });

                            } catch (e) {
                                iziToast.error({
                                    title: "Error",
                                    message: "An unexpected error occurred.",
                                    position: "topRight",
                                    timeout: 3000,
                                });
                            }
                        }
                    });
                }
            });

            $('.training-card').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.1) + 's',
                    'animation': 'fadeInUp 0.6s ease forwards'
                });
            });
        });
    </script>
@endpush
