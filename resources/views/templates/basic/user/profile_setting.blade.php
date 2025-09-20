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
                            class="d-block py-2 accordion--button{{ menuActive('user.secondOwner') }}">
                            <i class="la la-user-tie"></i> @lang('Second Owner')
                        </a>
                        <a href="{{ route('user.logout') }}" class="py-2 d-block accordion--button">
                            <i class="la la-sign-out"></i> @lang('Sign Out')
                        </a>
                    </div>
                </div>
            </div>

            @php
                $kyc = getContent('kyc.content', true);
                $userKycStatus = auth()->user()->kv;
                $kycCollection = collect($user->kyc_data);
                $kycByName = $kycCollection->keyBy('name');
                $address = $kycByName->get('Address')->value ?? '';
                $state = $kycByName->get('State')->value ?? '';
                $zip = $kycByName->get('Zip Code')->value ?? '';
                $city = $kycByName->get('City')->value ?? '';
            @endphp
            <div class="col-lg-9 col-md-8">
                <div class="row">
                    <div class="bg-white dashboard__content contact__form__wrapper">
                        <div class="profile__edit__wrapper">
                            <div class="profile__edit__form ">
                                <form class="register" action="" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="container image-container">
                                                <x-image-uploader name="image" :imagePath="getImage(
            getFilePath('userProfile') . '/' . $user->image,
            getFileSize('userProfile'),
            true,
        )" :size="false" id="uploadImage"
                                                    :required="false" />

                                                @if ($user->image)
                                                    <button type="button" class="mt-2 btn btn-danger btn-sm"
                                                        onclick="confirmDelete()">Delete Image</button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-8">

                                             <div class="row">
                                                <div class="form-group ">
                                                    
                                                <label class="form-label">@lang('Join Date')</label>
                                                    <input type="text" 
                                                        class="form-control form--control" 
                                                        name="join_date" 
                                                        value="{{ $user->created_at->format('M d, Y') }}" 
                                                        readonly 
                                                        style="background-color: #f8f9fa; cursor: not-allowed; color:rgb(231, 139, 18);">
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="form-group col-sm-6">
                                                  
                                                    <label class="form-label">@lang('First Name')</label>
                                                    <input type="text" class="form-control form--control" name="firstname"
                                                        value="{{ $user->firstname }}" required>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('Last Name')</label>
                                                    <input type="text" class="form-control form--control" name="lastname"
                                                        value="{{ $user->lastname }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('E-mail Address')</label>
                                                    <input class="form-control form--control" value="{{ $user->email }}"
                                                        disabled>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('Mobile Number')</label>
                                                    <input class="form-control form--control" value="{{ $user->mobile }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('Address')</label>
                                                    @if($userKycStatus == Status::KYC_VERIFIED)
                                                        <input class="form-control form--control" type="text" name="address"
                                                            value="{{ $address }}" disabled>
                                                    @else
                                                    <input type="text" class="form-control form--control" name="address"
                                                        placeholder="KYC verification required" disabled>
                                                    @endif
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <label class="form-label">@lang('State')</label>
                                                    @if($userKycStatus == Status::KYC_VERIFIED)
                                                        <input class="form-control form--control" type="text" name="state"
                                                            value="{{ $state }}" disabled>
                                                    @else
                                                    <input type="text" class="form-control form--control" name="state"
                                                        placeholder="KYC verification required" disabled>
                                                    @endif
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label class="form-label">@lang('Zip Code')</label>
                                                    @if ($userKycStatus == Status::KYC_VERIFIED)
                                                    <input type="text" class="form-control form--control" name="zip"
                                                        value="{{ $zip }}" disabled>
                                                    @else
                                                    <input type="text" class="form-control form--control" name="zip"
                                                        placeholder="KYC verification required" disabled>
                                                    @endif
                                                </div>

                                                <div class="form-group col-sm-4">
                                                    <label class="form-label">@lang('City')</label>
                                                    @if ($userKycStatus == Status::KYC_VERIFIED)
                                                    <input type="text" class="form-control form--control" name="city"
                                                        value="{{ $city }}" disabled>
                                                    @else
                                                    <input type="text" class="form-control form--control" name="city"
                                                        placeholder="KYC verification required" disabled>
                                                    @endif
                                                </div>

                                                <div class="form-group col-sm-4">
                                                    <label class="form-label">@lang('Country')</label>
                                                    <input class="form-control form--control"
                                                        value="{{ @$user->country_name }}" disabled>
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
            /* Removes black border */
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
            /* border-radius: 12px; */
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
            background-size: contain;
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
            /* White background */
        }

        i.fa-solid fa-arrow-up-from-bracket {
            color: #111;
        }

        .dashboard__content {
            height: 100vh;
            /* Take full height of the viewport */
            overflow-y: auto;
            /* Enable scrolling if content overflows */
            padding-bottom: 20px;
        }

        @media (max-width: 768px) {
            .mobile-view-margin {
                margin-top: 50px;
                /* Adjust the value as needed */
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
                reader.onload = function (e) {
                    var preview = $(input).closest('.image-upload-wrapper').find('.image-upload-preview');
                    $(preview).css('background-image', 'url(' + e.target.result + ')');
                    $(preview).addClass('has-image');
                    $(preview).hide();
                    $(preview).fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".image-upload-input").on('change', function () {
            proPicURL(this);
        });

        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete your profile image?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('user.delete.image') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            $('#deleteImageBtn').hide();
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        const user = @json($user);
        console.log(user);
    </script>
@endpush