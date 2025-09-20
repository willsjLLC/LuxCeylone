@extends('admin.layouts.app')

@section('panel')

    @if ($advertisement->account_type == Status::PRO_ACCOUNT)
        <div>
            <div class="row mb-none-30">
                <div class="col-lg-4 col-md-4 mb-30">
                    {{-- User & Image Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                        <div class="card-header bg--primary text--white">
                            <h5 class="m-0 text--white">@lang('Poster Information')</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="las la-user"></i> @lang('Posted By') - <span class="fw-bold">
                                    {{ $advertisement->user->firstname }} {{ $advertisement->user->lastname }}
                                    @if ($advertisement->account_type == Status::LITE_ACCOUNT)
                                        <span class="badge badge--warning">Lite Account</span>
                                    @else
                                        <span class="badge badge--primary">Pro Account</span>
                                    @endif
                                </span>
                            </h6>
                            @php
                                $primaryImage = $advertisement->images->sortBy('sort_order')->first();
                                $otherImages = $advertisement->images
                                    ->sortBy('sort_order')
                                    // ->filter(function ($img) {
                                    //     return $img->sort_order != 1;
                                    // })
                                    ->take(4);
                            @endphp

                            <div class="p-3 bg--white text-center mb-3">
                                <div class="side_Image">
                                    @if ($primaryImage)
                                        <img id="primaryImageDisplay"
                                            src="{{ getImage(getFilePath('advertisementImages') . '/' . $primaryImage->image, getFileSize('advertisementImages')) }}"
                                            class="product-image primary-img" data-bs-toggle="modal"
                                            data-bs-target="#imageModal">
                                        <p class="text-success mt-2 fw-bold">@lang('Primary Image')</p>
                                    @else
                                        <img id="primaryImageDisplay" src="{{ asset('assets/admin/images/empty.png') }}"
                                            class="b-radius--10 product-image" data-bs-toggle="modal"
                                            data-bs-target="#imageModal">
                                    @endif
                                </div>
                            </div>

                            @if ($otherImages->count())
                                <div class="row gy-2">
                                    @foreach ($otherImages as $image)
                                        <div class="col-6 col-md-3 mb-3 text-center">
                                            <img src="{{ getImage(getFilePath('advertisementImages') . '/' . $image->image, getFileSize('advertisementImages')) }}"
                                                alt="Advertisement Image"
                                                class="img-fluid rounded shadow-sm other-img change-primary"
                                                data-image-path="{{ getImage(getFilePath('advertisementImages') . '/' . $image->image, getFileSize('advertisementImages')) }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Status')
                                    <span
                                        class="badge @if ($advertisement->status == Status::AD_PENDING) badge--dark
                                        @elseif($advertisement->status == Status::AD_APPROVED) badge--success
                                        @elseif($advertisement->status == Status::AD_COMPLETED) badge--success
                                        @elseif($advertisement->status == Status::AD_PAUSE) badge--warning
                                        @elseif($advertisement->status == Status::AD_ONGOING) badge--info
                                        @elseif($advertisement->status == Status::AD_REJECTED) badge--danger
                                        @else badge--secondary @endif">
                                        @if ($advertisement->status == Status::AD_PENDING)
                                            @lang('Pending')
                                        @elseif($advertisement->status == Status::AD_APPROVED)
                                            @lang('Approved')
                                        @elseif($advertisement->status == Status::AD_COMPLETED)
                                            @lang('Completed')
                                        @elseif($advertisement->status == Status::AD_PAUSE)
                                            @lang('Paused')
                                        @elseif($advertisement->status == Status::AD_ONGOING)
                                            @lang('Ongoing')
                                        @elseif($advertisement->status == Status::AD_REJECTED)
                                            @lang('Rejected')
                                        @else
                                            @lang('Unknown')
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-end align-items-center pt-2 pb-2">
                                    @if ($advertisement->status == Status::AD_PENDING)
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline--success confirmationBtn"
                                                data-question="@lang('Are you sure to approve this advertisement?')"
                                                data-action="{{ route('admin.ads.approve', $advertisement->id) }}">
                                                <i class="fas la-check"></i> @lang('Approve')
                                            </button>
                                            <button class="btn btn-outline--danger" data-bs-toggle="modal"
                                                data-bs-target="#adRejectionModal">
                                                <i class="fas la-times-circle"></i> @lang('Reject')
                                            </button>
                                        </div>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Advertisement Information Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1 mb-4">
                        <div class="card-header bg--info text--white">
                            <h5 class="m-0 text--white">@lang('Advertisement Details')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Title') <span class="fw-bold">{{ $advertisement->title }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Advertisement Code') <span class="fw-bold">{{ $advertisement->advertisement_code }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Category') <span class="fw-bold">{{ $advertisement->category->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Subcategory') <span class="fw-bold">{{ $advertisement->subcategory->name }}</span>
                                </li>
                                @if ($advertisement->package)
                                    <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                        @lang('Package') <span class="fw-bold">{{ $advertisement->package->name }}
                                            Package</span>
                                    </li>
                                @else
                                    <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                        @lang('Package') <span class="fw-bold"> @lang('Free Package')</span>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Price') <span class="fw-bold">{{ showAmount($advertisement->price) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Negotiable') <span
                                        class="fw-bold">{{ $advertisement->is_price_negotiable ? 'Yes' : 'No' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Featured') <span
                                        class="fw-bold">{{ $advertisement->is_featured ? 'Yes' : 'No' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Boosted') <span
                                        class="fw-bold">{{ $advertisement->is_boosted ? 'Yes' : 'No' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Posted Date') <span
                                        class="fw-bold">{{ showDateTime($advertisement->posted_date) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Expiry Date') <span
                                        class="fw-bold">{{ showDateTime($advertisement->expiry_date) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Location')
                                    <span class="fw-bold">{{ $advertisement->district->name }},
                                        {{ $advertisement->city->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Payment Option')
                                    @if ($advertisement->payment_option_id == Status::PAY_BY_WALLET)
                                        <span class="fw-bold">@lang('By Wallet')</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Description Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                        <div class="card-header bg--secondary text--white">
                            <h5 class="m-0 text--white">@lang('Description')</h5>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold">{{ $advertisement->description }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Contact & Analytics Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1 mb-4">
                        <div class="card-header bg--warning text--white">
                            <h5 class="m-0 text--white">@lang('Contact Information') & @lang('Analytics')</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="las la-phone-alt"></i> @lang('Contact Details')</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Contact Name') <span class="fw-bold">{{ $advertisement->contact_name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Contact Mobile') <span class="fw-bold">{{ $advertisement->contact_mobile }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Contact Email') <span class="fw-bold">{{ $advertisement->contact_email }}</span>
                                </li>
                            </ul>

                            <h6 class="mb-3"><i class="las la-chart-bar"></i> @lang('Advertisement Analytics')</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Impressions') <span
                                        class="fw-bold">{{ $advertisement->impressions ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Clicks') <span class="fw-bold">{{ $advertisement->clicks ?? 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Boosting Details Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                        <div class="card-header bg--info text--white">
                            <h5 class="m-0 text--white">@lang('Boosting Details')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @if ($advertisement->boostHistories->isNotEmpty())
                                    @foreach ($advertisement->boostHistories as $boostHistory)
                                        <li class="list-group-item bg--lite-secondary mt-{{ $loop->first ? 0 : 2 }} mb-1">
                                            <h6 class="mb-0">@lang('Boost') #{{ $loop->iteration }}</h6>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Boosted Date') <span
                                                class="fw-bold">{{ $boostHistory->boosted_date->format('Y-m-d H:i:s') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Expiry Date') <span
                                                class="fw-bold">{{ $boostHistory->expiry_date->format('Y-m-d H:i:s') }}</span>
                                        </li>
                                        @if ($boostHistory->boostPackage)
                                            <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                                @lang('Boost Package') <span
                                                    class="fw-bold">{{ $boostHistory->boostPackage->name }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                                @lang('Boost Price') <span
                                                    class="fw-bold">{{ showAmount($boostHistory->price) }}</span>
                                            </li>
                                        @else
                                            <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                                @lang('Boost Price') <span
                                                    class="fw-bold">{{ showAmount($boostHistory->price) }}
                                                    (@lang('Custom Boost'))
                                                </span>
                                            </li>
                                        @endif
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Boost Impressions') <span
                                                class="fw-bold">{{ $boostHistory->impressions ?? 'N/A' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Boost Clicks') <span
                                                class="fw-bold">{{ $boostHistory->clicks ?? 'N/A' }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="list-group-item text-center">@lang('No boosting history found.')</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Image Modal --}}
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">@lang('Advertisement Image')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="" id="modalImage" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rejection Modal --}}
            <div id="adRejectionModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">@lang('Reject Advertisement')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.ads.reject', $advertisement->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-warning p-3">
                                    @lang('Please provide a reason for rejecting this advertisement. This will be shown to the user.')
                                </div>

                                <div class="form-group">
                                    <label class="form-label">@lang('Rejection Reason')</label>
                                    <textarea class="form-control" name="reason" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn--dark"
                                    data-bs-dismiss="modal">@lang('Cancel')</button>
                                <button type="submit" class="btn btn--danger">@lang('Reject Advertisement')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div>
            <div class="row mb-none-30">
                <div class="col-lg-4 col-md-4 mb-30">
                    {{-- User & Image Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                        <div class="card-header bg--primary text--white">
                            <h5 class="m-0 text--white">@lang('Poster Information')</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="las la-user"></i> @lang('Posted By') - <span class="fw-bold">
                                    {{ $advertisement->user->firstname }} {{ $advertisement->user->lastname }}
                                    @if ($advertisement->account_type == Status::LITE_ACCOUNT)
                                        <span class="badge badge--warning">Lite Account</span>
                                    @else
                                        <span class="badge badge--primary">Pro Account</span>
                                    @endif
                                </span>
                            </h6>
                            @php
                                $primaryImage = collect($advertisement->images)->sortBy('sort_order')->first();
                                $otherImages = collect($advertisement->images)
                                    ->sortBy('sort_order')
                                    // ->filter(function ($img) {
                                    //     return $img->sort_order != 1;
                                    // })
                                    ->take(4);
                            @endphp

                            <div class="p-3 bg--white text-center mb-3">
                                <div class="side_Image">
                                    @if ($primaryImage)
                                        <img id="primaryImageDisplay"
                                            src="{{ getImage(getFilePath('advertisementImages') . '/' . $primaryImage->image, getFileSize('advertisementImages')) }}"
                                            class="product-image primary-img" data-bs-toggle="modal"
                                            data-bs-target="#imageModal">
                                        <p class="text-success mt-2 fw-bold">@lang('Primary Image')</p>
                                    @else
                                        <img id="primaryImageDisplay" src="{{ asset('assets/admin/images/empty.png') }}"
                                            class="b-radius--10 product-image" data-bs-toggle="modal"
                                            data-bs-target="#imageModal">
                                    @endif
                                </div>
                            </div>

                            @if (count($otherImages))
                                <div class="row gy-2">
                                    @foreach ($otherImages as $image)
                                        <div class="col-6 col-md-3 mb-3 text-center">
                                            <img src="{{ getImage(getFilePath('advertisementImages') . '/' . $image->image, getFileSize('advertisementImages')) }}"
                                                alt="Advertisement Image"
                                                class="img-fluid rounded shadow-sm other-img change-primary"
                                                data-image-path="{{ getImage(getFilePath('advertisementImages') . '/' . $image->image, getFileSize('advertisementImages')) }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Status')
                                    <span
                                        class="badge @if ($advertisement->status == Status::AD_PENDING) badge--dark
                                        @elseif($advertisement->status == Status::AD_APPROVED) badge--success
                                        @elseif($advertisement->status == Status::AD_COMPLETED) badge--success
                                        @elseif($advertisement->status == Status::AD_PAUSE) badge--warning
                                        @elseif($advertisement->status == Status::AD_ONGOING) badge--info
                                        @elseif($advertisement->status == Status::AD_REJECTED) badge--danger
                                        @else badge--secondary @endif">
                                        @if ($advertisement->status == Status::AD_PENDING)
                                            @lang('Pending')
                                        @elseif($advertisement->status == Status::AD_APPROVED)
                                            @lang('Approved')
                                        @elseif($advertisement->status == Status::AD_COMPLETED)
                                            @lang('Completed')
                                        @elseif($advertisement->status == Status::AD_PAUSE)
                                            @lang('Paused')
                                        @elseif($advertisement->status == Status::AD_ONGOING)
                                            @lang('Ongoing')
                                        @elseif($advertisement->status == Status::AD_REJECTED)
                                            @lang('Rejected')
                                        @else
                                            @lang('Unknown')
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-end align-items-center pt-2 pb-2">
                                    @if ($advertisement->status == Status::AD_PENDING)
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline--success confirmationBtn"
                                                data-question="@lang('Are you sure to approve this advertisement?')"
                                                data-action="{{ route('admin.ads.approve', $advertisement->id) }}">
                                                <i class="fas la-check"></i> @lang('Approve')
                                            </button>
                                            <button class="btn btn-outline--danger" data-bs-toggle="modal"
                                                data-bs-target="#adRejectionModal">
                                                <i class="fas la-times-circle"></i> @lang('Reject')
                                            </button>
                                        </div>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Advertisement Information Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1 mb-4">
                        <div class="card-header bg--info text--white">
                            <h5 class="m-0 text--white">@lang('Advertisement Details')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Title') <span class="fw-bold">{{ $advertisement->title }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Advertisement Code') <span
                                        class="fw-bold">{{ $advertisement->advertisement_code }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Category') <span class="fw-bold">{{ $advertisement->category->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Subcategory') <span
                                        class="fw-bold">{{ $advertisement->sub_category->name }}</span>
                                </li>
                                @if ($advertisement->package)
                                    <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                        @lang('Package') <span class="fw-bold">{{ @$advertisement->package->name }}
                                            Package</span>
                                    </li>
                                @else
                                    <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                        @lang('Package') <span class="fw-bold"> @lang('Free Package')</span>
                                    </li>
                                @endif
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Price') <span
                                        class="fw-bold">{{ showAmount($advertisement->price) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Negotiable') <span
                                        class="fw-bold">{{ $advertisement->is_price_negotiable ? 'Yes' : 'No' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Featured') <span
                                        class="fw-bold">{{ $advertisement->is_featured ? 'Yes' : 'No' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Boosted') <span
                                        class="fw-bold">{{ $advertisement->is_boosted ? 'Yes' : 'No' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Posted Date') <span
                                        class="fw-bold">{{ showDateTime($advertisement->posted_date) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Expiry Date') <span
                                        class="fw-bold">{{ showDateTime($advertisement->expiry_date) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Location')
                                    <span class="fw-bold">{{ $advertisement->district->name }},
                                        {{ $advertisement->city->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Payment Option')
                                    @if ($advertisement->payment_option_id == Status::PAY_BY_WALLET)
                                        <span class="fw-bold">@lang('By Wallet')</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Description Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                        <div class="card-header bg--secondary text--white">
                            <h5 class="m-0 text--white">@lang('Description')</h5>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold">{{ $advertisement->description }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Contact & Analytics Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1 mb-4">
                        <div class="card-header bg--warning text--white">
                            <h5 class="m-0 text--white">@lang('Contact Information') & @lang('Analytics')</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3"><i class="las la-phone-alt"></i> @lang('Contact Details')</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Contact Name') <span class="fw-bold">{{ $advertisement->contact_name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Contact Mobile') <span class="fw-bold">{{ $advertisement->contact_mobile }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Contact Email') <span class="fw-bold">{{ $advertisement->contact_email }}</span>
                                </li>
                            </ul>

                            <h6 class="mb-3"><i class="las la-chart-bar"></i> @lang('Advertisement Analytics')</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Impressions') <span
                                        class="fw-bold">{{ $advertisement->impressions ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                    @lang('Clicks') <span class="fw-bold">{{ $advertisement->clicks ?? 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Boosting Details Card --}}
                    <div class="card custom--card b-radius--10 overflow-hidden box--shadow1">
                        <div class="card-header bg--info text--white">
                            <h5 class="m-0 text--white">@lang('Boosting Details')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @if (collect($advertisement->boost_histories)->isNotEmpty())
                                    @foreach ($advertisement->boostHistories as $boostHistory)
                                        <li class="list-group-item bg--lite-secondary mt-{{ $loop->first ? 0 : 2 }} mb-1">
                                            <h6 class="mb-0">@lang('Boost') #{{ $loop->iteration }}</h6>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Boosted Date') <span
                                                class="fw-bold">{{ $boostHistory->boosted_date->format('Y-m-d H:i:s') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Expiry Date') <span
                                                class="fw-bold">{{ $boostHistory->expiry_date->format('Y-m-d H:i:s') }}</span>
                                        </li>
                                        @if ($boostHistory->boostPackage)
                                            <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                                @lang('Boost Package') <span
                                                    class="fw-bold">{{ $boostHistory->boostPackage->name }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                                @lang('Boost Price') <span
                                                    class="fw-bold">{{ showAmount($boostHistory->price) }}</span>
                                            </li>
                                        @else
                                            <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                                @lang('Boost Price') <span
                                                    class="fw-bold">{{ showAmount($boostHistory->price) }}
                                                    (@lang('Custom Boost'))
                                                </span>
                                            </li>
                                        @endif
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Boost Impressions') <span
                                                class="fw-bold">{{ $boostHistory->impressions ?? 'N/A' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between pt-2 pb-2">
                                            @lang('Boost Clicks') <span
                                                class="fw-bold">{{ $boostHistory->clicks ?? 'N/A' }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="list-group-item text-center">@lang('No boosting history found.')</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Image Modal --}}
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">@lang('Advertisement Image')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="" id="modalImage" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rejection Modal --}}
            <div id="adRejectionModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">@lang('Reject Advertisement')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.ads.reject', $advertisement->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-warning p-3">
                                    @lang('Please provide a reason for rejecting this advertisement. This will be shown to the user.')
                                </div>

                                <div class="form-group">
                                    <label class="form-label">@lang('Rejection Reason')</label>
                                    <textarea class="form-control" name="reason" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn--dark"
                                    data-bs-dismiss="modal">@lang('Cancel')</button>
                                <button type="submit" class="btn btn--danger">@lang('Reject Advertisement')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        .product-image {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 5px;
            cursor: pointer;
        }

        .primary-img {
            border: 2px solid #28a745;
        }

        .other-img {
            width: 100%;
            height: 100px;
            object-fit: contain;
            cursor: pointer;
        }

        @media (max-width: 576px) {
            .other-img {
                height: 80px;
            }
        }

        .text-center img {
            display: inline-block;
        }

        #modalImage {
            max-height: 90vh;
            width: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.change-primary').on('click', function() {
                var newImagePath = $(this).data('image-path');
                $('#primaryImageDisplay').attr('src', newImagePath);

                $('.other-img').removeClass('primary-img');
                $(this).addClass('primary-img');
            });

            $('#primaryImageDisplay').on('click', function() {
                var imageSrc = $(this).attr('src');
                $('#modalImage').attr('src', imageSrc);
            });

        })(jQuery);
    </script>
@endpush
