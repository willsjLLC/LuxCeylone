@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        <div class="col-xl-6 col-md-8 mb-30">
            <div class="card overflow-hidden box--shadow1">
                @if ($secondOwner)
                    <div class="card-body">
                        <h5 class="mb-20 text-muted">@lang('Second Owner Details')</h5>
                        <ul class="list-group">
                            {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Original Owner')
                                <span class="fw-bold">{{ $secondOwner?->originalOwner?->firstname }} {{ $secondOwner?->originalOwner?->lastname }}</span>
                            </li> --}}

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('First Name')
                                <span class="fw-bold">{{ $secondOwner->first_name }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Last Name')
                                <span class="fw-bold">{{ $secondOwner->last_name }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Relationship')
                                <span class="fw-bold">{{ $secondOwner->relationship_to_owner }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Contact No')
                                <span class="fw-bold">{{ $secondOwner->dial_code }} {{ $secondOwner->contact_no }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email')
                                <span class="fw-bold">{{ $secondOwner->email_address }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Address')
                                <span class="fw-bold">{{ $secondOwner->address }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Assigned Date')
                                <span class="fw-bold">{{ showDateTime($secondOwner->assigned_date) }}</span>
                            </li>

                            {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Approved Date')
                                <span class="fw-bold">{{ showDateTime($secondOwner->approved_date) }}</span>
                            </li> --}}

                            @if ($secondOwner->note)
                                <li class="list-group-item">
                                    <strong>@lang('Note')</strong><br>
                                    <p>{{ $secondOwner->note }}</p>
                                </li>
                            @endif

                            <div class="d-flex">
                                <div class="form-group col-sm-6 ">
                                    <label class="form-label">@lang('Id Front Image')</label>
                                    <div class="nic-image-wrapper ">
                                        <div class="nic-image-preview" id="nicFrontPreview">
                                            @if ($secondOwner?->nic_front_url)
                                                <img src="{{ getImage(getFilePath('secondOwnerImages') . '/' . $secondOwner->nic_front_url) }}"
                                                    alt="NIC Front" class="nic-image" alt="NIC Front" class="nic-image"
                                                    data-bs-toggle="modal" data-bs-target="#nicModal"
                                                    data-image="{{ getImage(getFilePath('secondOwnerImages') . '/' . $secondOwner->nic_front_url) }}">
                                            @else
                                                <div class="nic-placeholder">
                                                    <i class="fa fa-id-card fa-3x text-muted"></i>
                                                    <p class="text-muted mt-2">@lang('Image Not Found')</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Id Front Image')</label>
                                    <div class="nic-image-wrapper ">
                                        <div class="nic-image-preview" id="nicFrontPreview">
                                            @if ($secondOwner?->nic_back_url)
                                                <img src="{{ getImage(getFilePath('secondOwnerImages') . '/' . $secondOwner->nic_back_url) }}"
                                                    alt="NIC Front" class="nic-image" alt="NIC Front" class="nic-image"
                                                    data-bs-toggle="modal" data-bs-target="#nicModal"
                                                    data-image="{{ getImage(getFilePath('secondOwnerImages') . '/' . $secondOwner->nic_back_url) }}">
                                            @else
                                                <div class="nic-placeholder">
                                                    <i class="fa fa-id-card fa-3x text-muted"></i>
                                                    <p class="text-muted mt-2">@lang('Image Not Found')</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @if ($secondOwner->document_verified_at)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Verified At')
                                    <span class="fw-bold">{{ showDateTime($secondOwner->document_verified_at) }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                @else
                    <div class="card-body">
                        <h5 class="mb-20 text-muted">@lang('Second Owner Information Not Available')</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- NIC Image Modal -->
    <div class="modal fade" id="nicModal" tabindex="-1" aria-labelledby="nicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('NIC Image Preview')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="@lang('Close')"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalNicImage" src="" alt="@lang('NIC Image')" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

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

            .custom-close-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 10;
                background-color: white;
                border: none;
                border-radius: 50%;
                width: 30px;
                height: 30px;
            }
        </style>
    @endpush

    @push('script')
        <script>
            const nicModal = document.getElementById('nicModal');
            nicModal.addEventListener('show.bs.modal', function(event) {
                const triggerImage = event.relatedTarget;
                const imageUrl = triggerImage.getAttribute('data-image');
                const modalImage = nicModal.querySelector('#modalNicImage');
                modalImage.src = imageUrl;
            });
        </script>
    @endpush
@endsection
