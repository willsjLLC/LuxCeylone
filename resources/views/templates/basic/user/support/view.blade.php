@extends($activeTemplate . 'layouts.' . $layout)

@if (auth()->check())
    @section('panel')
    @else
    @section('content')
    @endif
    <div class="{{ auth()->check() ? '' : 'container padding-top padding-bottom' }} bg-white mt-3">
        <div class="dashboard__content contact__form__wrapper ">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="text--dark mt-0">
                    @php echo $myTicket->statusBadge; @endphp
                    [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                </h5>
                @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                    <button class="btn btn--danger close-button btn--sm confirmationBtn" type="button"
                        data-question="@lang('Are you sure to close this ticket?')" data-action="{{ route('ticket.close', $myTicket->id) }}"><i
                            class="fa fa-lg fa-times-circle"></i>
                    </button>
                @endif
            </div>
            <div class="message__chatbox__body">
                <form method="post" class="disableSubmission" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <textarea name="message" class="form-control form--control" rows="4" required>{{ old('message') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <button type="button" class="btn btn--dark btn--sm addAttachment my-2"> <i class="fas fa-plus"></i>
                            @lang('Add Attachment') </button>
                        <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                        <div class="row fileUploadsContainer">
                        </div>
                    </div>

                    <button type="submit" class="btn btn--base w-100"> <i class="fa fa-reply"></i>
                        @lang('Reply')</button>
                </form>
            </div>
        </div>

        <div class="contact__form__wrapper mt-2">
            <div class="message__chatbox__body">
                @foreach ($messages as $message)
                    @if ($message->admin_id == 0)
                        <div class="row support-message border border-radius-3 my-3 py-3 mx-2">
                            <div class="col-md-3 border-end text-end d-flex justify-content-center align-items-center">
                                <h5 class="name">{{ $message->ticket->name }}</h5>
                            </div>
                            <div class="col-md-9">
                                <p class="text-muted fw-bold mb-3 text">
                                    @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                <p>{{ $message->message }}</p>
                                @if ($message->attachments->count() > 0)
                                    <div class="mt-2">
                                        @foreach ($message->attachments as $k => $image)
                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="me-3 text--base"><i
                                                    class="fa fa-file"></i> @lang('Attachment')
                                                {{ ++$k }} </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="row border border-warning border-radius-3 my-3 py-3 mx-2"
                            style="background-color: #ffd96729">
                            <div class="col-md-3 border-end text-end">
                                <h5 class="my-3">{{ $message->admin->name }}</h5>
                                <p class="lead text-muted">@lang('Staff')</p>
                            </div>
                            <div class="col-md-9">
                                <p class="text-muted fw-bold my-3">
                                    @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                <p>{{ $message->message }}</p>
                                @if ($message->attachments->count() > 0)
                                    <div class="mt-2">
                                        @foreach ($message->attachments as $k => $image)
                                            <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="me-3"><i
                                                    class="fa fa-file"></i> @lang('Attachment')
                                                {{ ++$k }} </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>



    <x-confirmation-modal />
@endsection
@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-6 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border-0 text-white"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `);
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
