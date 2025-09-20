@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <div class="container">
        <div class="row justify-content-center">
            <div class="mt-3 col-12 col-md-10 col-lg-8 container-form">
                <!-- Header separate from card -->
                <div class="mb-3 form-header-container">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('user.advertisement.selectCategory') }}" class="text-dark me-3">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('user.product.index') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('user.advertisement.index') }}">Advertisements</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('user.advertisement.selectCategory', $categoryObj->name) }}">{{$categoryObj->name }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('user.advertisement.selectCategory', $subCategoryObj->name) }}">{{ $subCategoryObj->name}}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Form card -->
                <form action="{{ route('user.advertisement.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="category_id" value="{{ $categoryObj->id }}">

                    @if (isset($subCategoryObj))
                        <input type="hidden" name="subcategory_id" value="{{ $subCategoryObj->id }}">
                    @endif

                    <div class="px-3 form-section" style="background-color:none;">
                        <h5 class="mb-0" style="font-weight:bold">Fill in the details</h5>
                    </div>
                    <!-- Location section -->
                    <div class="px-3 py-4 form-section" style="background-color:none;">
                        <div class="row location-row">
                            <div class="mb-3 col-6">
                                <label class="form-label">District</label>
                                <div class="select-dropdown">
                                    <select name="district_id" id="district-selector" class="form-select">
                                        <option value="">Select District</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-right select-icon"></i>
                                </div>
                            </div>

                            <div class="mb-3 col-6">
                                <label class="form-label">City</label>
                                <div class="select-dropdown">
                                    <select name="city_id" id="city-selector" class="form-select">
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-right select-icon"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Condition section -->
                        @if($subCategoryObj->supports_condition)
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Condition</label>
                                <div class="condition-options">
                                    <div class="condition-option">
                                        <input type="radio" class="form-radio-input" name="condition" id="condition-used" value="Used" autocomplete="off">
                                        <label class="form-radio-label" for="condition-used">Used</label>
                                    </div>
                                    <div class="condition-option">
                                        <input type="radio" class="form-radio-input" name="condition" id="condition-new" value="New" autocomplete="off">
                                        <label class="form-radio-label" for="condition-new">New</label>
                                    </div>
                                    <div class="condition-option">
                                        <input type="radio" class="form-radio-input" name="condition" id="condition-recondition" value="Recondition" autocomplete="off">
                                        <label class="form-radio-label" for="condition-recondition">Recondition</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <hr class="my-0" style="border: 1px solid #000000;">

                    <!-- Title, Description and Price section -->
                    <div class="px-3 py-4 form-section">
                        <div class="row">
                            <!-- Title field -->
                            <div class="mb-3 col-12">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <!-- Description field -->
                            <div class="mb-3 col-12">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label">Description</label>
                                    <div class="mt-1 d-flex justify-content-end">
                                        <small class="text-muted"><span id="description-count">0</span>/5000</small>
                                    </div>
                                </div>

                                <textarea name="description" id="description" class="form-control" rows="4" maxlength="5000"></textarea>
                            </div>

                            <!-- Price field -->
                            <div class="mb-3 col-12">
                                <label class="form-label">Price (LKR) (Optional)</label>
                                <input type="text" name="price" class="form-control" placeholder="Pick a good price ">
                            </div>

                            <div class="mb-3 col-12">
                                <div class="mt-2 form-check">
                                    <input class="form-check-input" type="checkbox" name="negotiable" id="negotiable">
                                    <label class="form-check-label" for="negotiable">
                                        Negotiable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-0" style="border: 1px solid #000000;">

                    <!-- Photo upload section -->
                    <div class="px-3 py-4 form-section">
                        <div class="row">
                            <div class="py-2 mb-3 col-12">
                                <label class="form-label d-flex align-items-center">
                                    Add up to 5 photos
                                    <span class="ms-2 info-icon" data-bs-toggle="tooltip" title="Add clear photos to get more interest">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                    <span class="ms-2" style="color: red; font-size: 0.85em;">(for better visibility 400 x 400)</span>
                                </label>
                                <div class="photo-upload-container">
                                    <div class="row">
                                        <!-- First photo slot - always visible and active -->
                                        <div class="mb-3 col-4 col-md-3">
                                            <div class="photo-upload-box active" id="photo-box-1">
                                                <input type="file" name="photos[]" id="photo-input-1" class="photo-input" accept="image/*" required>
                                                <label for="photo-input-1" class="photo-label" id="photo-label-1">
                                                    <div class="photo-preview" id="photo-preview-1">
                                                        <img src="" class="preview-img d-none" id="preview-img-1" alt="Preview">
                                                        <div class="upload-placeholder" id="upload-placeholder-1">
                                                            <i class="fa-solid fa-image"></i>
                                                            <span>Add a photo</span>
                                                        </div>
                                                    </div>
                                                </label>
                                                <button type="button" class="remove-photo-btn d-none" id="remove-photo-1" data-bs-toggle="modal" data-bs-target="#removePhotoModal" data-photo-index="1">
                                                    <i class="fa-solid fa-circle-xmark"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Additional photo slots - visible but inactive -->
                                        @for ($i = 2; $i <= 5; $i++)
                                            <div class="mb-3 col-4 col-md-3" id="photo-container-{{ $i }}">
                                                <div class="photo-upload-box inactive" id="photo-box-{{ $i }}">
                                                    <input type="file" name="photos[]" id="photo-input-{{ $i }}" class="photo-input" accept="image/*" disabled>
                                                    <label for="photo-input-{{ $i }}" class="photo-label" id="photo-label-{{ $i }}">
                                                        <div class="photo-preview" id="photo-preview-{{ $i }}">
                                                            <img src="" class="preview-img d-none" id="preview-img-{{ $i }}" alt="Preview">
                                                            <div class="upload-placeholder" id="upload-placeholder-{{ $i }}">
                                                                <i class="fa-solid fa-image"></i>
                                                                <span>Add a photo</span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <button type="button" class="remove-photo-btn d-none" id="remove-photo-{{ $i }}" data-bs-toggle="modal" data-bs-target="#removePhotoModal" data-photo-index="{{ $i }}">
                                                        <i class="fa-solid fa-circle-xmark"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Removal Confirmation Modal -->
                    <div class="modal fade" id="removePhotoModal" tabindex="-1" aria-labelledby="removePhotoModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="removePhotoModalLabel">Remove Photo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to remove this photo? This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirmRemovePhoto">Yes, Remove Photo</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-0" style="border: 1px solid #000000;">

                    <!-- Contact Details section -->
                    <div class="px-3 py-4 form-section">
                        <h6 class="mb-4 section-title" style="font-weight:bold">Contact Details</h6>

                        <div class="row">
                            <div class="mb-3 col-12 col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="contact_name" class="form-control" required value="{{ auth()->user()->fullname }}">
                            </div>

                            <div class="mb-3 col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="contact_email" class="form-control" required value="{{ auth()->user()->email }}">
                            </div>

                            <!-- Updated Phone Number Section -->
                            <div class="mb-3 col-12">
                                <label class="form-label">Phone number</label>
                                <div class="phone-numbers-container">
                                    <!-- Phone number entries will be added here -->
                                    <div class="mb-2 phone-entry">
                                        <div class="d-flex align-items-center">
                                            <div class="phone-check-container me-2">
                                                <span class="phone-check-circle">
                                                    <i class="text-white fa-solid fa-check"></i>
                                                </span>
                                            </div>
                                            <input type="tel" name="contact_mobile[]" class="form-control phone-input" value="{{ auth()->user()->mobile ?? '' }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-add-phone">
                                        <i class="fa-solid fa-plus text-success me-1"></i>
                                        Add another phone number
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-0" style="border: 1px solid #000000;">

                    <!-- Submit button -->
                    <div class="px-3 py-4">
                        <div class="row justify-content-end">
                            <div class="col-12 col-md-6 col-lg-4">
                                <button type="submit" class="btn btn-success w-100">Post ad</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .container-form {
            width: 780px;
        }
        /* Common styles */
        .form-header-container {
            padding: 12px 0;
        }

        .form-section {
            background-color: none;
        }

        .section-title {
            font-weight: 600;
            color: #333;
        }

        .select-dropdown {
            position: relative;
            width: 100%;
        }

        .select-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            pointer-events: none;
            font-size: 12px;
        }

        .form-control,
        .form-select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            font-size: 14px;
            background-color: white;
        }

        .form-select {
            padding-right: 30px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none;
        }

        .form-label {
            font-weight: bold;
            color: black;
        }

        .form-select option {
            font-size: 14px;
            padding: 8px;
        }

        .btn-success {
            background-color: #25BBA2;
            border-color: #25BBA2;
            padding: 4px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-radio-label {
            font-size: 14px;
        }

        .card-body {
            border: none;
            background-color: none;
        }

        .btn-success:hover {
            background-color: #24b263;
            border-color: #24b263;
            box-shadow: 0 4px 12px rgba(40, 199, 111, 0.2);
        }

        /* Condition radio buttons styling */
        .condition-options {
            display: flex;
            gap: 10px;
        }

        .condition-option {
            flex: 1;
        }

        /* Photo upload styles */
        .photo-upload-container {
            width: 100%;
        }

        .photo-upload-box {
            position: relative;
            width: 100%;
            aspect-ratio: 1/1;
            border: 1px dashed #ccc;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        .photo-upload-box.active {
            border-color: #25BBA2;
            background-color: #f8f8f8;
        }

        .photo-upload-box.inactive {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .photo-input {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .photo-label {
            display: block;
            width: 100%;
            height: 100%;
            cursor: pointer;
            margin: 0;
        }

        .photo-upload-box.inactive .photo-label {
            cursor: not-allowed;
        }

        .photo-preview {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #888;
            height: 100%;
        }

        .upload-placeholder i {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .upload-placeholder span {
            font-size: 12px;
            text-align: center;
        }

        .preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .remove-photo-btn {
            position: absolute;
            top: 0px;
            right: 0px;
            background: none;
            border: none;
            color: #ff4d4d;
            font-size: 20px;
            cursor: pointer;
            z-index: 2;
            padding: 0;
        }

        .info-icon {
            color: #888;
            font-size: 14px;
            cursor: pointer;
        }

        .form-check-input {
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            font-weight: normal;
        }

        /* Phone number styles */
        .phone-numbers-container {
            max-width: 100%;
        }

        .phone-entry {
            position: relative;
        }

        .phone-check-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .phone-check-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #25BBA2;
        }

        .btn-remove-phone {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .btn-add-phone {
            color: #25BBA2;
            background: none;
            border: none;
            padding: 0;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-add-phone i {
            color: #25BBA2;
        }

        /* Desktop styles (default) */
        @media (min-width: 992px) {
            .card {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                border-radius: 8px;
                overflow: hidden;
            }

            .form-section {
                padding: 25px 30px;
            }

            .form-label {
                font-weight: 500;
                margin-bottom: 8px;
            }

            h5 {
                font-size: 18px;
                font-weight: 600;
            }

            h6.section-title {
                font-size: 16px;
            }
        }

        /* Tablet styles */
        @media (min-width: 576px) and (max-width: 991px) {
            .card {
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                border-radius: 6px;
            }

            .form-control,
            .form-select {
                padding: 10px 12px;
            }

            .btn-success {
                padding: 4px;
            }
        }

        /* Mobile styles */
        @media (max-width: 575px) {
            .form-control,
            .form-select {
                padding: 8px 10px;
                font-size: 13px;
            }

            .select-icon {
                right: 8px;
            }

            h5 {
                font-size: 16px;
            }

            h6 {
                font-size: 14px;
            }

            .form-label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .btn-success {
                padding: 4px;
                font-size: 14px;
            }

            .form-section {
                padding: 15px 20px;
            }

            .upload-placeholder i {
                font-size: 18px;
                margin-bottom: 4px;
            }

            .upload-placeholder span {
                font-size: 10px;
            }
        }

        /* Extra small devices */
        @media (max-width: 360px) {
            .form-control,
            .form-select {
                padding: 6px 8px;
                font-size: 12px;
            }

            .form-section {
                padding: 12px 15px !important;
            }

            .btn-success {
                font-size: 13px;
            }

            .upload-placeholder i {
                font-size: 16px;
            }

            .upload-placeholder span {
                font-size: 9px;
            }
        }
    </style>
@endpush


@push('script')
    <script>
        $(document).ready(function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Character counter for description
        $('#description').on('input', function() {
            var currentLength = $(this).val().length;
            $('#description-count').text(currentLength);
        });

        // District and city loading
        $('#district-selector').on('change', function() {
            var districtId = $(this).val();

            $.ajax({
                url: "{{ route('user.advertisement.get-cities', '') }}/" + districtId,
                type: "GET",
                success: function(response) {
                    var citySelector = $('#city-selector');
                    citySelector.empty();

                    $.each(response, function(index, city) {
                        citySelector.append($('<option>', {
                            value: city.id,
                            text: city.name
                        }));
                    });
                },
                error: function(xhr) {
                    console.log('Error loading cities');
                }
            });
        });

        // Phone number handling
        const maxPhones = 2;

        function toggleAddPhoneButton() {
            const phoneCount = $('.phone-entry').length;
            if (phoneCount >= maxPhones) {
                $('.btn-add-phone').hide();
            } else {
                $('.btn-add-phone').show();
            }
        }

        toggleAddPhoneButton();

        $('.btn-add-phone').on('click', function() {
            const phoneCount = $('.phone-entry').length;
            if (phoneCount >= maxPhones) return;

            const phoneEntryTemplate = `
                <div class="mb-2 phone-entry">
                    <div class="d-flex align-items-center">
                        <div class="phone-check-container me-2">
                            <span class="phone-check-circle">
                                <i class="text-white fa-solid fa-check"></i>
                            </span>
                        </div>
                        <input type="tel" name="contact_mobile[]" class="form-control phone-input" required>
                        <button type="button" class="btn btn-remove-phone ms-2">
                            <i class="fa-solid fa-circle-minus text-danger"></i>
                        </button>
                    </div>
                </div>
            `;

            $('.phone-numbers-container').append(phoneEntryTemplate);
            toggleAddPhoneButton();
        });

        $(document).on('click', '.btn-remove-phone', function() {
            if ($('.phone-entry').length > 1) {
                $(this).closest('.phone-entry').remove();
                toggleAddPhoneButton();
            }
        });

        // Photo upload handling - IMPROVED VERSION
        var photoCount = 0;
        var photoIndexToRemove = null;

        // Function to update photo count on page load
        function updateInitialPhotoCount() {
            for (var i = 1; i <= 5; i++) {
                if ($('#photo-input-' + i).val()) {
                    photoCount++;
                }
            }
            updatePhotoSlots();
        }

        // Initialize with any existing photos
        updateInitialPhotoCount();

        // Function to handle file selection
        $('.photo-input').on('change', function(e) {
            var inputId = $(this).attr('id');
            var index = parseInt(inputId.split('-')[2]);

            if (this.files && this.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    // Show image preview
                    $('#preview-img-' + index).attr('src', e.target.result).removeClass('d-none');
                    $('#upload-placeholder-' + index).addClass('d-none');
                    $('#remove-photo-' + index).removeClass('d-none');

                    // Only increment if this is a new photo (not replacing an existing one)
                    if ($('#photo-input-' + index).data('has-image') !== true) {
                        photoCount++;
                        $('#photo-input-' + index).data('has-image', true);
                    }

                    // Update the active/inactive state of all photo slots
                    updatePhotoSlots();
                }

                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle remove photo button click to show modal
        $(document).on('click', '.remove-photo-btn', function() {
            photoIndexToRemove = $(this).data('photo-index');
            $('#removePhotoModal').modal('show');
        });

        // Handle confirmation of photo removal
        $('#confirmRemovePhoto').on('click', function() {
            if (photoIndexToRemove !== null) {
                var index = photoIndexToRemove;

                // Reset the file input
                $('#photo-input-' + index).val('');
                $('#photo-input-' + index).data('has-image', false);

                // Hide the preview and show the placeholder
                $('#preview-img-' + index).addClass('d-none').attr('src', '');
                $('#upload-placeholder-' + index).removeClass('d-none');
                $('#remove-photo-' + index).addClass('d-none');

                photoCount--;

                // Update the active/inactive state of all photo slots
                updatePhotoSlots();

                // Hide the modal
                $('#removePhotoModal').modal('hide');
                photoIndexToRemove = null;
            }
        });

        // Function to update all photo slots based on the current state
        function updatePhotoSlots() {
            // First, set all slots to the proper state
            for (var i = 1; i <= 5; i++) {
                // Check if this slot has an image
                var hasImage = $('#photo-input-' + i).data('has-image') === true || $('#preview-img-' + i).attr('src') !== '';

                if (hasImage) {
                    // Slot has an image, keep it active
                    $('#photo-box-' + i).removeClass('inactive').addClass('active');
                    $('#photo-input-' + i).prop('disabled', false);
                } else if (photoCount < 5) {
                    // No image, but we can still add more photos, so make it active
                    $('#photo-box-' + i).removeClass('inactive').addClass('active');
                    $('#photo-input-' + i).prop('disabled', false);
                } else {
                    // No image and we've reached the photo limit, so make it inactive
                    $('#photo-box-' + i).removeClass('active').addClass('inactive');
                    $('#photo-input-' + i).prop('disabled', true);
                }
            }

            // Make sure at least the first slot is always active if no photos yet
            if (photoCount === 0) {
                $('#photo-box-1').removeClass('inactive').addClass('active');
                $('#photo-input-1').prop('disabled', false);
            }
        }

        // Initialize UI - make sure first photo box is active
        $('#photo-box-1').removeClass('inactive').addClass('active');
        $('#photo-input-1').prop('disabled', false);
       });
    </script>
@endpush
