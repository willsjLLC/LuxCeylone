@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <div class="mt-1 col-12">
        <div class="row mobile-view-margin">
            <h2 class="mb-4 fw-bold text-dark ">Profile Setting</h2>

            <!-- Sidebar (Left) -->
            <div class="col-lg-3 col-md-4 d-none d-md-block">
                <div class="mb-4 accordion" id="sidebarAccordion">
                    <div>
                        <a href="{{ route('user.profile.setting') }}"
                            class="d-block py-2 accordion--button {{ request()->routeIs('user.profile.setting') ? 'text--warning' : '' }}">
                            <i class="la la-user"></i> @lang('My Profile')
                        </a>
                        <a href="{{ route('user.change.password') }}"
                            class="d-block py-2 accordion--button{{ menuActive('user.change.password') }}">
                            <i class="la la-lock"></i> @lang('Change Password')
                        </a>
                        <a href="{{ route('user.twofactor') }}"
                            class="d-block py-2 accordion--button{{ menuActive('user.twofactor') }}">
                            <i class="la la-key"></i> @lang('2FA Security')
                        </a>
                        <a href="{{ route('user.secondOwner') }}"
                            class="d-block py-2 accordion--button {{ menuActive('user.secondOwner') }} scow">
                            <i class="la la-user-tie"></i> @lang('Second Owner')
                        </a>
                        <a href="{{ route('user.logout') }}" class="py-2 d-block accordion--button">
                            <i class="la la-sign-out"></i> @lang('Sign Out')
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="row">
                    <div class="bg-white dashboard__content contact__form__wrapper">
                        <div class="profile__edit__wrapper">
                            <div class="profile__edit__form">

                                <form class="register" action="{{ route('user.secondOwner.submit.data') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            {{-- <div class="image-container">
                                                <div class="image-upload-wrapper">
                                                    <div class="image-upload-preview"
                                                        style="background-image: url('{{ $secondOwner?->image ? getImage('assets/images/users/' . $secondOwner->image, '170x170') : asset('assets/images/default-user.png') }}');">
                                                    </div>
                                                    <div class="image-upload-input-wrapper">
                                                        <input type="file" name="image" class="image-upload-input"
                                                            accept="image/*" onchange="proPicURL(this);">
                                                        <label for="fileInput"><i
                                                                class="fa-solid fa-arrow-up-from-bracket"></i></label>
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label class="form-label">@lang('First Name')</label>
                                                    <input type="text" class="form-control form--control"
                                                        name="first_name" value="{{ $secondOwner?->first_name ?? '' }}"
                                                        required>
                                                </div>
                                                <div class="form-group col-sm-4">
                                                    <label class="form-label">@lang('Last Name')</label>
                                                    <input type="text" class="form-control form--control"
                                                        name="last_name" value="{{ $secondOwner?->last_name ?? '' }}"
                                                        required>
                                                </div>
                                                <div class="form-group col-sm-4">
                                                    <label class="form-label">@lang('Relationship')</label>
                                                    <input type="text" class="form-control form--control"
                                                        name="relationship_to_owner"
                                                        value="{{ $secondOwner?->relationship_to_owner ?? '' }}" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('E-mail Address')</label>
                                                    <input type="email" class="form-control form--control" required
                                                        name="email_address"
                                                        value="{{ $secondOwner?->email_address ?? '' }}">
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('Mobile Number')</label>
                                                    <input type="tel" class="form-control form--control" required
                                                        name="contact_no" value="{{ $secondOwner?->contact_no ?? '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-sm-12">
                                                    <label class="form-label">@lang('Address')</label>
                                                    <input class="form-control form--control" type="text" name="address"
                                                        value="{{ $secondOwner?->address ?? '' }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-sm-12">
                                                    <label class="form-label">@lang('Note')</label>
                                                    <textarea class="form-control form--control" name="note" value="{{ $secondOwner?->note ?? '' }}">{{ $secondOwner?->note ?? '' }}</textarea>
                                                </div>
                                            </div>

                                            <!-- NIC Front Image -->
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('Id Front Image')</label>
                                                    <div class="nic-image-wrapper">
                                                        <div class="nic-image-preview" id="nicFrontPreview">
                                                            @if ($secondOwner?->nic_front_url)
                                                                <img src="{{ getImage(getFilePath('secondOwnerImages') . '/' . $secondOwner->nic_front_url) }}"
                                                                    alt="NIC Front" class="nic-image" alt="NIC Front"
                                                                    class="nic-image">
                                                                <div class="nic-image-overlay">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm delete-nic-btn"
                                                                        onclick="deleteNicImage('front', '{{ $secondOwner->id }}')">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <div class="nic-placeholder">
                                                                    <i class="fa fa-id-card fa-3x text-muted"></i>
                                                                    <p class="text-muted mt-2">@lang('Upload Front ID')</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="nic_front" required
                                                            class="form-control mt-2 nic-file-input" accept="image/*"
                                                            onchange="previewNicImage(this, 'nicFrontPreview')">
                                                    </div>
                                                </div>

                                                <!-- NIC Back Image -->
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('Id Back Image')</label>
                                                    <div class="nic-image-wrapper">
                                                        <div class="nic-image-preview" id="nicBackPreview">
                                                            @if ($secondOwner?->nic_back_url)
                                                                <img src="{{ getImage(getFilePath('secondOwnerImages') . '/' . $secondOwner->nic_back_url) }}"
                                                                    alt="NIC Back" class="nic-image">


                                                                <div class="nic-image-overlay">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm delete-nic-btn"
                                                                        onclick="deleteNicImage('back', '{{ $secondOwner->id }}')">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <div class="nic-placeholder">
                                                                    <i class="fa fa-id-card fa-3x text-muted"></i>
                                                                    <p class="text-muted mt-2">@lang('Upload Back ID')</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="nic_back" required
                                                            class="form-control mt-2 nic-file-input" accept="image/*"
                                                            onchange="previewNicImage(this, 'nicBackPreview')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="mt-3 btn-Profile w-100">@lang('Submit')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .btn-Profile {
            background-color: #027c68;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-Profile:hover {
            background-color: #025f4d;
            color: #fff;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .body {
            overflow-y: auto;
            height: 100%;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-group input.form-control {
            padding: 10px 20px;
        }

        .image-upload-wrapper {
            width: 170px;
            height: 170px;
            position: relative;
            border-radius: 50%;
            overflow: visible;
            border: 3px solid #f1f1f1;
            box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
        }

        .image-upload-preview {
            width: 100%;
            height: 100%;
            display: block;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            border-radius: 50%;
            border: none;
            box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.25);
            justify-content: center;
            align-items: center;
        }

        .image-upload-input-wrapper {
            position: absolute;
            bottom: 4px;
            right: 10px;
            z-index: 10;
        }

        .image-upload-input-wrapper input {
            width: 0;
            opacity: 0;
        }

        .image-upload-input-wrapper label {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-align: center;
            border: 2px solid #fff;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0;
            background-color: #fff;
        }

        /* NIC Image Styles */
        .nic-image-wrapper {
            position: relative;
            margin-bottom: 15px;
        }

        .nic-image-preview {
            width: 100%;
            height: 200px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .nic-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        .nic-placeholder {
            text-align: center;
            padding: 20px;
        }

        .nic-image-overlay {
            position: absolute;
            top: 5px;
            right: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .nic-image-preview:hover .nic-image-overlay {
            opacity: 1;
        }

        .delete-nic-btn {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nic-file-input {
            font-size: 14px;
        }

        .dashboard__content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 20px;
        }

        .scow {
            color: #28A745;
        }


        @media (max-width: 768px) {
            .mobile-view-margin {
                margin-top: 50px;
            }

            .nic-image-preview {
                height: 150px;
            }
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        'use strict'

        function proPicURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $(input).closest('.image-upload-wrapper').find('.image-upload-preview');
                    $(preview).css('background-image', 'url(' + e.target.result + ')');
                    $(preview).addClass('has-image');
                    $(preview).hide();
                    $(preview).fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewNicImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var previewContainer = $('#' + previewId);

                    // Clear existing content
                    previewContainer.empty();

                    // Create new image element
                    var img = $('<img>').addClass('nic-image').attr('src', e.target.result).attr('alt', 'NIC Preview');

                    // Create overlay with remove button
                    var overlay = $('<div>').addClass('nic-image-overlay');
                    var removeBtn = $('<button>').addClass('btn btn-danger btn-sm delete-nic-btn')
                        .attr('type', 'button')
                        .html('<i class="fa fa-times"></i>')
                        .click(function() {
                            removePreviewImage(previewId, input);
                        });

                    overlay.append(removeBtn);
                    previewContainer.append(img).append(overlay);

                    // Add fade effect
                    previewContainer.hide().fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePreviewImage(previewId, inputElement) {
            var previewContainer = $('#' + previewId);
            var placeholderText = previewId.includes('Front') ? '@lang('Upload Front ID')' : '@lang('Upload Back ID')';

            // Reset the file input
            $(inputElement).val('');

            // Restore placeholder
            previewContainer.fadeOut(300, function() {
                previewContainer.html(`
                    <div class="nic-placeholder">
                        <i class="fa fa-id-card fa-3x text-muted"></i>
                        <p class="text-muted mt-2">${placeholderText}</p>
                    </div>
                `).fadeIn(300);
            });
        }

        function deleteNicImage(type, secondOwnerId) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you really want to delete the ${type} ID image?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.secondOwner.delete.nic') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            type: type,
                            second_owner_id: secondOwnerId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', 'The image has been deleted.', 'success');

                                // Update the preview to show placeholder
                                var previewId = type === 'front' ? 'nicFrontPreview' : 'nicBackPreview';
                                var placeholderText = type === 'front' ? '@lang('Upload Front ID')' :
                                    '@lang('Upload Back ID')';

                                $('#' + previewId).fadeOut(300, function() {
                                    $(this).html(`
                                        <div class="nic-placeholder">
                                            <i class="fa fa-id-card fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">${placeholderText}</p>
                                        </div>
                                    `).fadeIn(300);
                                });
                            } else {
                                console.error(response);
                                Swal.fire('Error!', response.message || 'Something went wrong.',
                                    'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }

        $(".image-upload-input").on('change', function() {
            proPicURL(this);
        });

        const secondOwner = @json($secondOwner);
        console.log(secondOwner);
    </script>
@endpush
