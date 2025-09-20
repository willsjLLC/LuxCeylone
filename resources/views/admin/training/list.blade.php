@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Ticket Image')</th>
                                    <th class="text-end">@lang('Ticket Price')</th>
                                    <th class="text-start">@lang('User')</th>
                                    <th class="text-end">@lang('Contact Number')</th>
                                    <th class="text-center">@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($userTrainings as $training)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb ticket-image">
                                                    <img src="{{ getImage(getFilePath('training') . '/' . $training->training->image) }}"
                                                        alt="@lang('Training Image')" class="plugin_bg" data-bs-toggle="modal"
                                                        data-bs-target="#imageModal"
                                                        data-image-url="{{ getImage(getFilePath('training') . '/' . $training->training->image) }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="name">{{ $training?->training?->ticket_price }}</span>
                                        </td>
                                        <td class="text-start">
                                            <span class="fw-bold">{{ $training?->user?->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $training?->user?->id) }}"><span>@</span>{{ $training?->user?->username }}</a>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="name">{{ $training->user?->mobile }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline--primary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    @if ($training->status == Status::TRAINING_PENDING)
                                                        @lang('Pending')
                                                    @elseif($training->status == Status::TRAINING_COMPLETED)
                                                        @lang('Completed')
                                                    @elseif($training->status == Status::TRAINING_REJECTED)
                                                        @lang('Rejected')
                                                    @endif
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item status-change-btn" href="#"
                                                            data-id="{{ $training->id }}"
                                                            data-status="{{ Status::TRAINING_PENDING }}"
                                                            data-status-text="Pending">@lang('Pending')</a>
                                                    </li>
                                                    <li><a class="dropdown-item status-change-btn" href="#"
                                                            data-id="{{ $training->id }}"
                                                            data-status="{{ Status::TRAINING_COMPLETED }}"
                                                            data-status-text="Completed">@lang('Completed')</a>
                                                    </li>
                                                    <li><a class="dropdown-item status-change-btn" href="#"
                                                            data-id="{{ $training->id }}"
                                                            data-status="{{ Status::TRAINING_REJECTED }}"
                                                            data-status-text="Rejected">@lang('Rejected')</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for displaying the full-size image --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">@lang('Training Image')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="@lang('Training Image')" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal for Status Change --}}
    <div class="modal fade" id="statusConfirmationModal" tabindex="-1" aria-labelledby="statusConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusConfirmationModalLabel">@lang('Confirm Status Change')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure you want to change the status to') <strong id="newStatusText"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="button" class="btn btn--primary" id="confirmStatusChangeBtn">@lang('Confirm')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / ticket price" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let userTrainingId;
            let newStatus;
            let statusText;
            let currentDropdownToggle;

            // When a status button in the dropdown is clicked
            $('.status-change-btn').on('click', function(e) {
                e.preventDefault();
                userTrainingId = $(this).data('id');
                newStatus = $(this).data('status');
                statusText = $(this).data('status-text');
                currentDropdownToggle = $(this).closest('.dropdown').find('.dropdown-toggle');

                // Update the text in the confirmation modal
                $('#newStatusText').text(statusText);

                // Show the confirmation modal
                $('#statusConfirmationModal').modal('show');
            });

            // When the 'Confirm' button in the confirmation modal is clicked
            $('#confirmStatusChangeBtn').on('click', function() {
                // Hide the confirmation modal
                $('#statusConfirmationModal').modal('hide');

                // Perform the AJAX request
                $.ajax({
                    url: "{{ url('admin/training/status') }}" + '/' + userTrainingId,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: newStatus
                    },
                    success: function(response) {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });
                        currentDropdownToggle.text(response.status_text);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to update status. Please try again.',
                            position: 'topRight'
                        });
                    }
                });
            });

            // Handle image modal
            $(document).on('click', '.user .thumb img', function() {
                const imageUrl = $(this).data('image-url');
                $('#modalImage').attr('src', imageUrl);
            });

            // The following JavaScript is for your 'Add New' and 'View' buttons.
            $('.addfile').on('click', function() {
                let modal = $('.addFileType');
                let title = "@lang('Add New Training')";
                let action = "{{ route('admin.training.store') }}";
                $('.resetFormData')[0].reset();
                modal.find('form').attr('action', action);
                modal.find('.modal-title').text(title);
                modal.modal('show');
            });

            $('.updateFile').on('click', function() {
                let modal = $('.addFileType');
                let training = $(this).data('training');
                let imageUrl = $(this).data('image-url');
                let action = "{{ route('admin.training.store', ':id') }}";
                modal.find('form').attr('action', action.replace(':id', training.id));
                modal.find('.modal-title').text("@lang('View Training')");
                modal.find('input[name=ticket_price]').val(training.ticket_price);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .ticket-image:hover {
            cursor: pointer;
        }
    </style>
@endpush
