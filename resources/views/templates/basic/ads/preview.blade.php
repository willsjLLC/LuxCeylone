@extends($activeTemplate . 'layouts.master')
@include('partials.preloader')
@section('panel')
    <div class="container ">

        <div class="mt-3 mb-3 advertiesment-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="text-dark me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

        </div>


        <!-- Advertisement Details -->
        <div class="row">
            <div class="col-md-8">
                <!-- Image Carousel -->
                <div class="mb-3 ad-preview-image-container">
                    <div class="main-image-display">
                        @if ($advertisementImages->isNotEmpty())
                            @foreach ($advertisementImages as $index => $image)
                                <img src="{{ asset('assets/admin/images/advertisementImages/' . $image->image) }}"
                                    class="ad-preview-image {{ $index == 0 ? 'active' : '' }}"
                                    data-index="{{ $index }}"
                                    alt="{{ $advertisement->title }} - Image {{ $index + 1 }}">
                            @endforeach
                        @else
                            <img src="{{ asset('assets/images/default-ad-image.jpg') }}" class="ad-preview-image active"
                                data-index="0" alt="Default Ad Image">
                        @endif

                        <!-- Display SOLD tag if advertisement is completed -->
                        @if ($advertisement->status == \App\Constants\Status::AD_COMPLETED)
                            <div class="sold-tag-overlay">
                                <img src="{{ asset('assets/image/sold.png') }}" alt="SOLD" class="sold-tag-image">
                            </div>
                        @endif

                        <!-- Navigation arrows for mobile -->
                        <div class="image-navigation">
                            <button class="arrow-btn arrow-prev"><i class="fa fa-chevron-left"></i></button>
                            <button class="arrow-btn arrow-next"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <!-- Thumbnail images -->
                    <div class="thumbnail-container">
                        <div class="thumbnail-scroll">
                            <button class="thumbnail-arrow thumbnail-prev"><i class="fa fa-chevron-left"></i></button>
                            <div class="thumbnails">
                                @if ($advertisementImages->isNotEmpty())
                                    @foreach ($advertisementImages as $index => $image)
                                        <div class="thumbnail {{ $index == 0 ? 'active' : '' }}"
                                            data-index="{{ $index }}">
                                            <img src="{{ asset('assets/admin/images/advertisementImages/' . $image->image) }}"
                                                alt="{{ $advertisement->title }} - Thumbnail {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="thumbnail active" data-index="0">
                                        <img src="{{ asset('assets/images/default-ad-image.jpg') }}"
                                            alt="Default Ad Image">
                                    </div>
                                @endif
                            </div>
                            <button class="thumbnail-arrow thumbnail-next"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Advertisement Info -->
                <div class="mb-4 ad-preview-info">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 ad-preview-title">{{ $advertisement->title }}</h2>
                        @if(number_format($advertisement->price, 2)>0)
                        <span class="ad-preview-price">LKR {{ number_format($advertisement->price, 2) }}</span>
                        @else
                        <span></span>
                        @endif
                    </div>

                    <div class="flex-wrap mb-3 d-flex">
                        <div class="ad-preview-detail me-4">
                            <i class="fa fa-map-marker-alt text-muted me-2"></i>
                            <span>{{ $advertisement->city->name ?? 'Unknown' }},
                                {{ $advertisement->district->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="ad-preview-detail me-4">
                            <i class="fa fa-calendar text-muted me-2"></i>
                            <span>Posted {{ $advertisement->posted_date->diffForHumans() }}</span>
                        </div>
                        <div class="ad-preview-detail">
                            <i class="fa fa-eye text-muted me-2"></i>
                            <span>{{ $advertisement->impressions }} Views</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        @if ($advertisement->is_price_negotiable)
                            <span class="negotiable-badge">Negotiable</span>
                        @endif

                        @auth
                            @if (auth()->user()->id == $advertisement->user_id)
                                @if ($advertisement->is_boosted)
                                    <span class="boost-ad-btn boosted-btn">
                                        <i class="fa-solid fa-rocket me-2"></i>Boosted
                                    </span>
                                @else
                                    <a href="{{ route('user.advertisement.boost', $advertisement->id) }}" class="boost-ad-btn">
                                        <i class="fa-solid fa-rocket me-2"></i>Boost this ad
                                    </a>
                                @endif

                               @if ($advertisement->status != \App\Constants\Status::AD_REJECTED &&
                                    $advertisement->status != \App\Constants\Status::AD_CANCELED &&
                                    $advertisement->status != \App\Constants\Status::AD_EXPIRED)
                                    <a href="{{ route('user.advertisement.edit', $advertisement->id) }}" class="edit-ad-btn">
                                        <i class="fa-solid fa-pencil me-2"></i>Edit Ad
                                    </a>
                                @endif

                            @endif
                        @endauth
                    </div>

                </div>

                <!-- Advertisement Description -->
                <div class="mb-4 ad-preview-description">
                    <h4 class="section-title">Description</h4>
                    <div class="description-content">
                        {{ $advertisement->description }}
                    </div>
                </div>

                <!-- Advertisement Details -->
                <div class="mb-4 ad-preview-details">
                    <h4 class="section-title">Details</h4>
                    <div class="row">
                        @if($advertisement->condition )
                        <div class="mb-3 col-6 col-md-4">
                            <div class="detail-item">
                                <span class="detail-label">Condition</span>
                                <span class="detail-value">{{ $advertisement->condition }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="mb-3 col-6 col-md-4">
                            <div class="detail-item">
                                <span class="detail-label">Category</span>
                                <span class="detail-value">{{ $advertisement->category->name }}</span>
                            </div>
                        </div>
                        <div class="mb-3 col-6 col-md-4">
                            <div class="detail-item">
                                <span class="detail-label">Sub Category</span>
                                <span class="detail-value">{{ $advertisement->subCategory->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Safety Tips -->
                <div class="mb-4 ad-preview-safety d-md-none">
                    <div class="safety-container">
                        <h4 class="safety-title">
                            <i class="fa fa-shield-alt me-2"></i> Safety Tips
                        </h4>
                        <ul class="safety-list">
                            <li>Meet seller in a safe public place</li>
                            <li>Check the item before you buy</li>
                            <li>Pay only after inspecting the item</li>
                            <li>Never pay in advance</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Seller Contact -->
                <div class="mb-4 seller-contact">
                    <h4 class="section-title">Seller Information</h4>

                    @if (auth()->guest() || auth()->user()->id != $advertisement->user_id)
                        <div class="seller-info">
                            <div class="mb-3 seller-name">
                                <i class="fa fa-user-circle me-2"></i>
                                <span>{{ $advertisement->contact_name }}</span>
                            </div>

                            <!-- Phone numbers display -->
                            <div class="contact-details">
                                <div class="contact-label"><i class="fa fa-phone me-2"></i> Phone:</div>
                                <div class="my-ad-notice">
                                    <div class="d-flex flex-column">
                                        @if ($advertisement->contact_landline)
                                            <div class="contact-value">
                                                {{ $advertisement->contact_landline }}
                                            </div>
                                        @endif

                                        @php
                                            // Split the contact_mobile value if it contains a comma
                                            $phoneNumbers = explode(',', $advertisement->contact_mobile);
                                            $primaryPhone = trim($phoneNumbers[0] ?? '');
                                            $optionalPhone = trim($phoneNumbers[1] ?? '');

                                            // Format phone numbers
                                            if (strlen($primaryPhone) == 10) {
                                                $formattedPrimaryPhone =
                                                    substr($primaryPhone, 0, 3) . ' - ' . substr($primaryPhone, 3);
                                            } else {
                                                $formattedPrimaryPhone = $primaryPhone;
                                            }

                                            if (strlen($optionalPhone) == 10) {
                                                $formattedOptionalPhone =
                                                    substr($optionalPhone, 0, 3) . ' - ' . substr($optionalPhone, 3);
                                            } else {
                                                $formattedOptionalPhone = $optionalPhone;
                                            }
                                        @endphp

                                        @if ($primaryPhone)
                                            <div class="contact-value">
                                                {{ $formattedPrimaryPhone }}
                                            </div>
                                        @endif

                                        @if ($optionalPhone)
                                            <div class="contact-value">
                                                <hr>
                                                {{ $formattedOptionalPhone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Email display -->
                            <div class="mb-3 contact-details">
                                <div class="contact-label"><i class="fa fa-envelope me-2"></i> Email:</div>
                                <div class="my-ad-notice contact-value1">{{ $advertisement->contact_email }}</div>
                            </div>

                            <div class="contact-buttons">
                                <a href="tel:{{ $primaryPhone }}" class="btn contact-btn phone-btn">
                                    <i class="fa fa-phone me-2"></i> Call Seller
                                </a>
                                <a href="mailto:{{ $advertisement->contact_email }}" class="btn contact-btn email-btn">
                                    <i class="fa fa-envelope me-2"></i> Email Seller
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mb-3 seller-name">
                            <i class="fa fa-user-circle me-2"></i>
                            <span>{{ $advertisement->contact_name }}</span>
                        </div>
                    @endif
                </div>

                @auth
                    @if (auth()->user()->id == $advertisement->user_id)
                        <div class="mb-4 ad-status-actions">
                            <h4 class="section-title">Advertisement Status</h4>

                            <!-- Status Badge -->
                            <div class="mb-3 status-badge-container">
                                @php
                                    $statusClass = '';
                                    $statusLabel = '';

                                    switch ($advertisement->status) {
                                        case \App\Constants\Status::AD_APPROVED:
                                            $statusClass = 'active-status';
                                            $statusLabel = 'Active';
                                            break;
                                        case \App\Constants\Status::AD_PENDING:
                                            $statusClass = 'pending-status';
                                            $statusLabel = 'Pending Approval';
                                            break;
                                        case \App\Constants\Status::AD_CANCELED:
                                            $statusClass = 'canceled-status';
                                            $statusLabel = 'Canceled';
                                            break;
                                        case \App\Constants\Status::AD_COMPLETED:
                                            $statusClass = 'completed-status';
                                            $statusLabel = 'Completed';
                                            break;
                                        case \App\Constants\Status::AD_REJECTED:
                                            $statusClass = 'rejected-status';
                                            $statusLabel = 'Rejected';
                                            break;
                                        default:
                                            $statusClass = 'default-status';
                                            $statusLabel = 'Unknown';
                                    }
                                @endphp
                                <div class="status-badge {{ $statusClass }}">
                                    <i class="fa fa-circle status-indicator me-2"></i>{{ $statusLabel }}
                                </div>

                                <!-- Display Rejection Reason if Status is Rejected -->
                                @if ($advertisement->status == \App\Constants\Status::AD_REJECTED && $advertisement->rejection_reason)
                                    <div class="mt-2 rejection-reason">
                                        <strong>Reason:</strong> {{ $advertisement->rejection_reason }}
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons - Only show for active advertisements -->

                            @if (
                                $advertisement->status == \App\Constants\Status::AD_APPROVED ||
                                    $advertisement->status == \App\Constants\Status::AD_PENDING)
                                <div class="action-buttons">
                                    @if ($advertisement->status == \App\Constants\Status::AD_APPROVED)
                                        <button type="button" class="btn action-btn complete-btn" data-bs-toggle="modal"
                                            data-bs-target="#completeConfirmationModal">
                                            <i class="fa fa-check-circle me-2"></i> Mark as Completed
                                        </button>
                                    @endif
                                    <button type="button" class="btn action-btn cancel-btn" data-bs-toggle="modal"
                                        data-bs-target="#cancelConfirmationModal">
                                        <i class="fa fa-times-circle me-2"></i> Cancel Advertisement
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                @endauth

                <!-- Safety Tips (Desktop) -->
                <div class="mb-4 ad-preview-safety d-none d-md-block">
                    <div class="safety-container">
                        <h4 class="safety-title">
                            <i class="fa fa-shield-alt me-2"></i> Safety Tips
                        </h4>
                        <ul class="safety-list">
                            <li>Meet seller in a safe public place</li>
                            <li>Check the item before you buy</li>
                            <li>Pay only after inspecting the item</li>
                            <li>Never pay in advance</li>
                        </ul>
                    </div>
                </div>

                @if ($advertisement->status == \App\Constants\Status::AD_PENDING)
                <div></div>
                @else
                    <!-- Share options -->
                    <div class="mb-4 share-options">
                        <h4 class="section-title">Share This Ad</h4>
                        <div class="share-buttons">
                            <!-- Copy button with notification overlay -->
                            <div class="copy-button-container" style="position: relative; display: inline-block;">
                                <button class="share-btn link-btn" id="copyLinkBtn" onclick="copyShareLink('{{ url('advertisement/public/' . $advertisement->id) }}')">
                                    <i class="fa fa-copy"></i>
                                </button>
                                <span id="copyNotification" style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); background-color: #333; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; opacity: 0; transition: opacity 0.3s; white-space: nowrap;">Link copied</span>
                            </div>

                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url('advertisement/public/' . $advertisement->id) }}"
                                target="_blank" class="share-btn facebook-btn">
                                <i class="fab fa-facebook-f"></i>
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ url('advertisement/public/' . $advertisement->id) }}&text={{ $advertisement->title }}"
                                target="_blank" class="share-btn twitter-btn">
                                <i class="fab fa-twitter"></i>
                            </a>

                            <a href="https://wa.me/?text={{ urlencode($advertisement->title . ' - ' . url('advertisement/public/' . $advertisement->id)) }}"
                                target="_blank" class="share-btn whatsapp-btn">
                                <i class="fab fa-whatsapp"></i>
                            </a>

                            <a href="https://www.instagram.com/create/story?url={{ url('advertisement/public/' . $advertisement->id) }}"
                                target="_blank" class="share-btn instagram-btn">
                                <i class="fab fa-instagram"></i>
                            </a>

                            <a href="https://www.tiktok.com/upload/?redirectUrl={{ url('advertisement/public/' . $advertisement->id) }}"
                                target="_blank" class="share-btn tiktok-btn">
                                <i class="fab fa-tiktok"></i>
                            </a>

                            <a href="mailto:?subject={{ $advertisement->title }}&body=Check out this ad: {{ url('advertisement/public/' . $advertisement->id) }}"
                                class="share-btn email-share-btn">
                                <i class="fa fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Related Advertisements -->
        @if ($relatedAds->count() > 0)
            <div class="mb-4 related-ads ">
                <h4 class="section-title">Similar Advertisements</h4>
                <div class="row ">
                    @foreach ($relatedAds as $relatedAd)
                        @php
                            $isBoosted =
                                isset($relatedAd->latestBoostHistory) &&
                                $relatedAd->latestBoostHistory &&
                                $relatedAd->latestBoostHistory->boostPackage;
                            $boostType = $isBoosted ? $relatedAd->latestBoostHistory->boostPackage->type : null;
                            $highlightColor = $isBoosted
                                ? $relatedAd->latestBoostHistory->boostPackage->highlighted_color
                                : null;
                            $cardClass = $isBoosted
                                ? 'ad-card related-ad-card boosted-card'
                                : 'ad-card related-ad-card';
                            $styleAttr = $highlightColor ? "border: 2px solid {$highlightColor};" : '';
                        @endphp
                        <div class="mb-3 col-12 col-md-6 col-lg-3">
                            <div class="{{ $cardClass }}" data-ad-id="{{ $relatedAd->id }}"
                                style="{{ $styleAttr }}">
                                <div class="ad-image-container position-relative">
                                    @if ($isBoosted)
                                        @php
                                            $boostLabel = '';
                                            $boostClass = '';

                                            switch ($boostType) {
                                                case 1:
                                                    $boostLabel = 'TOP';
                                                    $boostClass = 'boost-badge-top';
                                                    break;
                                                case 2:
                                                    $boostLabel = 'FEATURED';
                                                    $boostClass = 'boost-badge-featured';
                                                    break;
                                                case 3:
                                                    $boostLabel = 'URGENT';
                                                    $boostClass = 'boost-badge-urgent';
                                                    break;
                                            }
                                        @endphp
                                        <div class="boost-tag {{ $boostClass }}">
                                            {{ $boostLabel }}
                                        </div>
                                    @endif

                                    @if ($relatedAd->file_name)
                                        <img src="{{ asset('assets/admin/images/advertisementImages/' . $relatedAd->file_name) }}"
                                            class="card-img-top ad-image" alt="{{ $relatedAd->title }}">
                                    @else
                                        <img src="{{ asset('assets/images/default-ad-image.jpg') }}"
                                            class="card-img-top ad-image" alt="Default Ad Image">
                                    @endif
                                </div>
                                <div class="card-container">
                                    <div class="px-3 card-details">
                                        <p class="mt-3 card-title">{{ $relatedAd->title }}</p>
                                        <p class="card-location text-muted">
                                            {{ $relatedAd->city->name ?? 'Unknown' }},
                                            {{ $relatedAd->district->name ?? 'Unknown' }}
                                        </p>
                                        <div class="flex-row d-flex">
                                            {{-- <p class="card-text ad-price">LKR {{ number_format($relatedAd->price, 2) }}
                                            </p> --}}
                                            @if(number_format($relatedAd->price, 2)>0)
                                            <span class="card-text ad-price">LKR {{ number_format($relatedAd->price, 2) }}</span>
                                            @else
                                            <span></span>
                                            @endif
                                        </div>
                                        <div class="updated-time">
                                            <p>{{ $relatedAd->posted_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">{{ $advertisement->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="modalImage" class="img-fluid" alt="{{ $advertisement->title }}">

                    <!-- Display SOLD tag in the modal if advertisement is completed -->
                    @if ($advertisement->status == \App\Constants\Status::AD_COMPLETED)
                        <div class="sold-tag-overlay-modal">
                            <img src="{{ asset('assets/images/sold-tag.png') }}" alt="SOLD"
                                class="sold-tag-image-modal">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <div class="modal-nav-buttons">
                        <button class="btn btn-outline-secondary modal-prev-btn">
                            <i class="fa fa-chevron-left me-2"></i>Previous
                        </button>
                        <span class="modal-counter">1 of 5</span>
                        <button class="btn btn-outline-secondary modal-next-btn">
                            Next<i class="fa fa-chevron-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelConfirmationModalLabel">Cancel Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this advertisement? This action cannot be undone.</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Active</button>
                    <form id="cancelForm" action="{{ route('user.advertisement.cancel', $advertisement->id) }}"
                        method="GET">
                        @csrf
                        <button type="submit" class="btn btn-danger">Yes, Cancel Advertisement</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Complete Confirmation Modal -->
    {{-- <div class="modal fade" id="completeConfirmationModal" tabindex="-1"
        aria-labelledby="completeConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeConfirmationModalLabel">Complete Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this advertisement as completed? This means the item has been sold or
                        is no longer available.</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Active</button>
                    <form id="completeForm" action="{{ route('user.advertisement.complete', $advertisement->id) }}"
                        method="GET">
                        @csrf
                        <button type="submit" class="btn btn-success">Yes, Mark as Completed</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

@endsection

@push('script')
    <style>
        /* Rejection Reason Warning Style */
        .rejection-reason {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #ffeeba;
            font-size: 14px;
            line-height: 1.5;
        }
        @media(min-width:768px){
            #imagePreviewModal .modal-dialog {
                max-height: 500px;
                height: 500px;
            }
            #imagePreviewModal .modal-content {
                height: 80%;
                display: flex;
                flex-direction: column;
            }
        }
        @media(max-width:767px){
            #imagePreviewModal .modal-dialog {
                max-height: 300px;
                height: 300px;
            }
            #imagePreviewModal .modal-content {
                height: 60%;
                display: flex;
                flex-direction: column;
            }
        }

        .image-navigation {
            cursor: pointer !important;
        }

        .share-btn.link-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 8px 12px;
            margin: 0 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .share-btn.link-btn:hover {
            background-color: #5a6268;
        }

        /* Advertisement Action Buttons */
        .ad-status-actions {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-btn {
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            text-align: left;
            transition: all 0.3s;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #c82333;
            color: white;
        }

        .complete-btn {
            background-color: #28a745;
            color: white;
        }

        .complete-btn:hover {
            background-color: #218838;
            color: white;
        }

        /* Success and error messages */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            position: relative;
            padding-bottom: 8px;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #009933;
        }

        .boost-ad-btn {
            background-color:#f2c61e;
            color: rgb(7, 4, 4);
            padding: 0px 8px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
        }

        .boost-ad-btn:hover {
            background-color: #f2c61e;
            color: rgb(253, 255, 224);
        }

        .boosted-btn {
            background-color: #a1a1a1;
            cursor: default;
            padding: 4px 8px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }

        .boosted-btn:hover {
            background-color: #6c757d;
        }

        .edit-ad-btn {
            background-color: #454c47;
            color: white;
            padding: 0px 8px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
        }

        .edit-ad-btn:hover {
            background-color: #cac8c8;
            color: rgb(17, 17, 17);
        }

        /* Advertisement image styles*/
        .ad-preview-image-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f8f8;
            position: relative;
        }

        /* .main-image-display {
                width: 100%;
                height: 450px;
                position: relative;
                overflow: hidden;
                background-color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #eaeaea;
            } */
        .main-image-display {
            width: 100%;
            height: 450px;
            /* Change this to 100% to fill the parent */
            position: relative;
            overflow: hidden;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eaeaea;
        }

        .ad-preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            cursor: pointer;
        }

        .ad-preview-image.active {
            display: block;
        }

        /* Image navigation arrows*/
        .image-navigation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
        }

        .arrow-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.85);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .arrow-btn:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: scale(1.05);
        }

        .arrow-btn i {
            font-size: 18px;
            color: #333;
        }

        /* Thumbnail styles*/
        .thumbnail-container {
            margin-top: 15px;
            width: 100%;
            overflow: hidden;
        }

        .thumbnail-scroll {
            display: flex;
            align-items: center;
            position: relative;
        }

        .thumbnails {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 15px;
            /* Increased gap between thumbnails */
            padding: 8px 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
            scroll-snap-type: x mandatory;
            flex-grow: 1;
        }

        .thumbnails::-webkit-scrollbar {
            display: none;
            /* Hide scrollbar for Chrome/Safari/Opera */
        }

        .thumbnail {
            flex: 0 0 120px;
            /* Increased size from 80px */
            height: 90px;
            /* Increased height from 60px */
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            scroll-snap-align: start;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .thumbnail:hover {

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .thumbnail.active {
            border-color: #009933;
            box-shadow: 0 0 0 2px rgba(0, 153, 51, 0.3);
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-arrow {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f1f1f1;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            z-index: 2;
            margin: 0 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .thumbnail-arrow:hover {
            background-color: #e0e0e0;
            transform: scale(1.1);
        }

        .thumbnail-arrow i {
            font-size: 16px;
            color: #555;
        }

        /* Modal styling */
        .modal-body {
            padding: 0;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #modalImage {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .modal-footer {
            justify-content: center;
            background-color: #f8f8f8;
        }

        .modal-nav-buttons {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .modal-counter {
            font-weight: 500;
            color: #555;
        }

        /* Advertisement info styles */
        .ad-preview-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }

        .ad-preview-price {
            font-size: 22px;
            font-weight: 700;
            color: #009933;
            background-color: rgba(2, 124, 104, 0.1);
            padding: 6px 12px;
            border-radius: 6px;
        }

        .ad-preview-detail {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .negotiable-badge {
            display: inline-block;
            background-color: #f0f8ff;
            color: 009933;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #b3e0ff;
        }

        /* Description styles */
        .description-content {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Details section */
        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 13px;
            color: #777;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 15px;
            color: #333;
            font-weight: 600;
        }

        /* Seller contact styles */
        .seller-contact {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .seller-name {
            font-size: 16px;
            font-weight: 600;
        }

        .contact-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contact-btn {
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
        }

        .phone-btn {
            background-color: #009933;
            color: white;
        }

        .phone-btn:hover {
            background-color: #009933;
            color: white;
        }

        .email-btn {
            background-color: #f1f1f1;
            color: #333;
            border: 1px solid #ddd;
        }

        .email-btn:hover {
            background-color: #e0e0e0;
            color: #333;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .contact-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 3px;
        }

        .contact-value {
            font-size: 18px;
            font-weight: bold;
            color: green;
        }

        .contact-value1 {
            font-size: 15px;
            font-weight: bold;
            color: green;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .my-ad-notice {
            background-color: #e9f5f2;
            padding: 15px;
            border-radius: 8px;
            text-align: left;
            word-break: break-word;
        }

        .my-ad-notice p {
            margin: 0;
            color: #009933;
            font-weight: 500;
        }

        /* Safety tips */
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

        .safety-list {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .safety-list li {
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }

        .safety-list li:last-child {
            margin-bottom: 0;
        }

        /* Share options */
        .share-buttons {
            display: flex;
            gap: 10px;
        }

        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            transition: all 0.3s;
        }

        .facebook-btn {
            background-color: #3b5998;
        }

        .twitter-btn {
            background-color: #1da1f2;
        }

        .whatsapp-btn {
            background-color: #25d366;
        }

        .instagram-btn {
            background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D);
        }

        .tiktok-btn {
            background-color: #000000;
            position: relative;
            overflow: hidden;
        }

        .tiktok-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background: linear-gradient(90deg, #25F4EE, #FE2C55);
            transition: all 0.3s;
            opacity: 0.3;
        }

        .youtube-btn {
            background-color: #FF0000;
        }


        .email-share-btn {
            background-color: #d14836;
        }

        .share-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
        }

        /* Related ads - UPDATED */
        .related-ads .row {
            justify-content: flex-start;
        }

        .related-ad-card {
            cursor: pointer;
            height: 100%;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .related-ad-card:hover {

            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .related-ad-card:active {
            transform: translateY(0);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .related-ad-card.clicked {
            animation: card-click 0.5s ease;
        }

        @keyframes card-click {
            0% {
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            50% {
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            }

            100% {
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
        }

        .related-ad-card .ad-image-container {
            width: 100%;
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 180px;
            /* Fixed height for consistency */
            border-radius: 8px 8px 0 0;
        }

        .related-ad-card .ad-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensures images fill the container without distortion */
            display: block;

        }


        @media (max-width: 768px) {
            .related-ad-card {
                display: flex;
                flex-direction: row;
                margin-bottom: 10px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                padding: 5px;
                overflow: hidden;
            }

            .related-ad-card .ad-image-container {
                width: 30%;
                max-width: 110px;
                min-width: 90px;
                height: 100px;
                border-radius: 6px;
            }

            .related-ad-card .ad-image-container img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .related-ad-card .card-container {
                width: 70%;
                padding-left: 15px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
            }

            .related-ad-card .card-details {
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .related-ad-card .card-title {
                font-size: 15px;
                font-weight: 600;
                margin-bottom: 3px;
                color: #333;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .related-ad-card .card-location {
                font-size: 13px;
                color: #777;
                margin-bottom: 5px;
            }

            .related-ad-card .ad-price {
                font-size: 15px;
                font-weight: 700;
                color: #009933;
                margin-bottom: 2px;
            }

            .related-ad-card .updated-time {
                position: absolute;
                bottom: 0;
                right: 0;
            }

            .related-ad-card .updated-time p {
                font-size: 12px;
                color: #999;
                margin-bottom: 0;
                text-align: right;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-image-display {
                height: 300px;
            }

            .ad-preview-image {
                max-height: 300px;
            }

            .ad-preview-title {
                font-size: 20px;
            }

            .ad-preview-price {
                font-size: 18px;
            }

            .section-title {
                font-size: 16px;
            }


            .thumbnail {
                flex: 0 0 100px;
                height: 75px;
            }

            .modal-nav-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .modal-counter {
                margin: 5px 0;
            }
        }

        @media (max-width: 576px) {
            .main-image-display {
                height: 250px;
            }

            .ad-preview-image {
                max-height: 250px;
            }

            .thumbnail {
                flex: 0 0 90px;
                height: 65px;
            }

            .seller-contact {
                margin-top: 20px;
            }

            .share-buttons {
                justify-content: space-between;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            let isUserLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            // Image gallery variables
            let currentImage = 0;
            const totalImages = $('.ad-preview-image').length;

            // Arrays to store image data
            const imageSources = [];
            const imageAlts = [];

            // Directly collect from ad-preview-image elements
            $('.ad-preview-image').each(function(index) {
                const src = $(this).attr('src');
                const alt = $(this).attr('alt') || 'Advertisement Image';
                imageSources.push(src);
                imageAlts.push(alt);
            });

            // Initialize with first image
            updateMainImage(0);

            // Hide navigation if only one image
            if (totalImages <= 1) {
                $('.image-navigation').hide();
                $('.thumbnail-container').hide();
            }

            // Remove any existing click handlers first
            $('.main-image-display').off('click');

            // Add click handler to main-image-display instead of individual images
            $('.main-image-display').on('click', function(e) {
                // Only proceed if we didn't click on navigation arrows
                if (!$(e.target).closest('.arrow-btn').length) {
                    e.preventDefault();
                    // Get the index of currently active image
                    const activeImage = $('.ad-preview-image.active');
                    const index = parseInt(activeImage.data('index'));
                    console.log("Main image display clicked, opening modal with active image index:", index);
                    openImageModal(index);
                }
            });

            // Handle thumbnail click
            $('.thumbnail').on('click', function() {
                const index = parseInt($(this).data('index'));
                console.log("Thumbnail clicked, index:", index);
                updateMainImage(index);
            });

            // Navigation arrows functionality
            $('.arrow-prev').on('click', function(e) {
                e.stopPropagation();
                console.log("Previous arrow clicked");
                navigateImages('prev');
            });

            $('.arrow-next').on('click', function(e) {
                e.stopPropagation();
                console.log("Next arrow clicked");
                navigateImages('next');
            });

            // Thumbnail scroll arrows
            $('.thumbnail-prev').on('click', function() {
                $('.thumbnails').animate({
                    scrollLeft: '-=200'
                }, 300);
            });

            $('.thumbnail-next').on('click', function() {
                $('.thumbnails').animate({
                    scrollLeft: '+=200'
                }, 300);
            });

            // Modal navigation
            $('.modal-prev-btn').on('click', function() {
                console.log("Modal previous button clicked");
                navigateModalImages('prev');
            });

            $('.modal-next-btn').on('click', function() {
                console.log("Modal next button clicked");
                navigateModalImages('next');
            });

            // Enhanced scroll to active thumbnail function
            function scrollToActiveThumbnail() {
                const activeThumb = $('.thumbnail.active');
                if (activeThumb.length) {
                    const container = $('.thumbnails');
                    const containerWidth = container.width();
                    const thumbPos = activeThumb.position().left;
                    const thumbWidth = activeThumb.width();

                    // Center the active thumbnail
                    container.animate({
                        scrollLeft: container.scrollLeft() + thumbPos - (containerWidth / 2) + (thumbWidth / 2)
                    }, 300);
                }
            }

            // Update main image function - enhanced
            function updateMainImage(index) {
                console.log("Updating main image to index:", index);
                currentImage = index;

                // Hide all images and show the current one
                $('.ad-preview-image').removeClass('active');
                $(`.ad-preview-image[data-index="${index}"]`).addClass('active');

                // Update active thumbnail
                $('.thumbnail').removeClass('active');
                $(`.thumbnail[data-index="${index}"]`).addClass('active');

                // Scroll thumbnail into view if needed
                scrollToActiveThumbnail();
            }

            // Navigate between images
            function navigateImages(direction) {
                console.log("Navigating images:", direction);
                if (direction === 'prev' && currentImage > 0) {
                    updateMainImage(currentImage - 1);
                } else if (direction === 'next' && currentImage < totalImages - 1) {
                    updateMainImage(currentImage + 1);
                }
            }

            // Open modal with specific image
            function openImageModal(index) {
                console.log("Opening modal with image index:", index);
                console.log("Available image sources:", imageSources);

                // Make sure we have a valid index
                if (index < 0 || index >= totalImages) {
                    console.error("Invalid image index:", index);
                    index = 0; // Default to first image
                }

                // Set the modal image source
                const imageSource = imageSources[index];
                const imageAlt = imageAlts[index] || 'Advertisement Image';

                console.log("Setting modal image to:", imageSource);
                $('#modalImage').attr('src', imageSource);
                $('#modalImage').attr('alt', imageAlt);

                // Update counter
                updateModalCounter(index);

                // Store current index
                currentImage = index;

                // Try multiple methods to ensure modal opens
                // Method 1: jQuery modal method
                $('#imagePreviewModal').modal('show');

                // Method 2: Bootstrap 5 API (fallback)
                try {
                    const modalElement = document.getElementById('imagePreviewModal');
                    if (modalElement && typeof bootstrap !== 'undefined') {
                        const modalInstance = bootstrap.Modal.getInstance(modalElement) ||
                                            new bootstrap.Modal(modalElement);
                        modalInstance.show();
                    }
                } catch (error) {
                    console.log("Bootstrap 5 modal method failed:", error);
                }

                console.log("Modal should now be visible");
            }

            // Navigate modal images
            function navigateModalImages(direction) {
                console.log("Navigating modal images:", direction);
                let newIndex = currentImage;

                if (direction === 'prev' && currentImage > 0) {
                    newIndex = currentImage - 1;
                } else if (direction === 'next' && currentImage < totalImages - 1) {
                    newIndex = currentImage + 1;
                } else {
                    console.log("Cannot navigate further in direction:", direction);
                    return; // No change if at the limits
                }

                console.log("New modal image index:", newIndex);
                $('#modalImage').attr('src', imageSources[newIndex]);
                $('#modalImage').attr('alt', imageAlts[newIndex]);
                updateModalCounter(newIndex);
                currentImage = newIndex;

                // Also update the main image view to stay in sync
                updateMainImage(newIndex);
            }

            // Update modal counter
            function updateModalCounter(index) {
                $('.modal-counter').text(`${index + 1} of ${totalImages}`);
            }

            // Add keyboard navigation
            $(document).keydown(function(e) {
                if ($('#imagePreviewModal').hasClass('show')) {
                    // Modal is open, use arrow keys for modal navigation
                    if (e.keyCode === 37) { // Left arrow
                        navigateModalImages('prev');
                    } else if (e.keyCode === 39) { // Right arrow
                        navigateModalImages('next');
                    } else if (e.keyCode === 27) { // Escape
                        $('#imagePreviewModal').modal('hide');
                    }
                } else {
                    // Normal navigation
                    if (e.keyCode === 37) { // Left arrow
                        navigateImages('prev');
                    } else if (e.keyCode === 39) { // Right arrow
                        navigateImages('next');
                    }
                }
            });

            // Make main-image-display have a pointer cursor to indicate it's clickable
            $('.main-image-display').css('cursor', 'pointer');

                    // Enhanced click handling for related ad cards
                    $('.related-ad-card').on('click', function() {
                        var adId = $(this).data('ad-id');

                        // Add clicked class for animation
                        $(this).addClass('clicked');

                        // redirecting urls based on used authentication
                        let userAdPreviewBaseUrl = "{{ route('user.advertisement.preview', '') }}/" + adId;
                        let guestAdPreviewBaseUrl = "{{ route('ads.preview', '') }}/" + adId;
                        // Small delay before navigation to allow animation to be seen
                        setTimeout(function() {
                            let targetUrl = isUserLoggedIn 
                                        ? userAdPreviewBaseUrl
                                        : guestAdPreviewBaseUrl;

                            window.location.href = targetUrl;
                        }, 300);
                        
                    });

                    // Remove animation class after animation completes
                    $('.related-ad-card').on('animationend', function() {
                        $(this).removeClass('clicked');
                    });

                    // Track ad clicks for analytics
                    // function trackAdClick() {
                    //     const adId = {{ $advertisement->id }};
                    //     $.ajax({
                    //         url: "{{ route('user.advertisement.track-click') }}",
                    //         type: "POST",
                    //         data: {
                    //             ad_id: adId,
                    //             _token: "{{ csrf_token() }}"
                    //         },
                    //         success: function(response) {
                    //             console.log("Click tracked");
                    //         }
                    //     });
                    // }

                    // // Track click on contact buttons
                    // $('.contact-btn').on('click', function() {
                    //     trackAdClick();
                    // });

                    // Enhanced touch swipe support for image navigation
                    let touchStartX = 0;
                    let touchEndX = 0;
                    let isSwiping = false;

                    $('.main-image-display').on('touchstart', function(e) {
                        touchStartX = e.originalEvent.touches[0].clientX;
                        isSwiping = true;
                    });

                    $('.main-image-display').on('touchmove', function(e) {
                        if (!isSwiping) return;
                        touchEndX = e.originalEvent.touches[0].clientX;

                        // Prevent default scrolling while swiping images
                        if (Math.abs(touchEndX - touchStartX) > 10) {
                            e.preventDefault();
                        }
                    });

                    $('.main-image-display').on('touchend', function(e) {
                        if (!isSwiping) return;
                        touchEndX = e.originalEvent.changedTouches[0].clientX;
                        handleSwipe();
                        isSwiping = false;
                    });

                    function handleSwipe() {
                        const swipeThreshold = 40;
                        if (touchEndX < touchStartX - swipeThreshold) {
                            // Swipe left
                            navigateImages('next');
                        } else if (touchEndX > touchStartX + swipeThreshold) {
                            // Swipe right
                            navigateImages('prev');
                        }
                    }

                    // Initial setup - scroll to active thumbnail
                    setTimeout(scrollToActiveThumbnail, 100);
        });
    </script>
    <script>
       function copyShareLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                // Show the notification
                const notification = document.getElementById('copyNotification');
                notification.style.opacity = "1";

                // Hide the notification after 2 seconds
                setTimeout(() => {
                    notification.style.opacity = "0";
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy link: ', err);
            });
        }
    </script>

    <style>
        /* Boost tag styles for related ads */
        .ad-image-container {
            position: relative;
            overflow: hidden;
        }

        .boost-tag {
            position: absolute;
            top: 0;
            left: 0;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            z-index: 2;
            text-transform: uppercase;
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0 100%);
        }

        .boost-badge-top {
            background-color: #ff9800;
            color: #fff;
            min-width: 60px;
            text-align: center;
        }

        .boost-badge-featured {
            background-color: #2196F3;
            color: #fff;
            min-width: 90px;
            text-align: center;
        }

        .boost-badge-urgent {
            background-color: #f44336;
            color: #fff;
            min-width: 80px;
            text-align: center;
        }

        .boosted-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .boosted-card:hover {
            transform: translateY(-0.1px);
        }

        /* SOLD tag overlay styles */
        .main-image-display {
            position: relative;
        }

        .sold-tag-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            width: 70%;
            max-width: 300px;
            pointer-events: none;
            /* This allows clicks to pass through to the image */
        }

        .sold-tag-image {
            width: 100%;
            height: auto;
            filter: drop-shadow(0px 0px 5px rgba(0, 0, 0, 0.5));
        }

        /* SOLD tag styles for the modal */
        .sold-tag-overlay-modal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            width: 70%;
            max-width: 400px;
            pointer-events: none;
        }

        .sold-tag-image-modal {
            width: 100%;
            height: auto;
            filter: drop-shadow(0px 0px 5px rgba(0, 0, 0, 0.5));
        }
    </style>

    <style>
        /* Status badge styles */
        .status-badge-container {
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }

        .active-status {
            background-color: #e3f7e9;
            color: #28a745;
            border: 1px solid #c3e6cb;
        }

        .pending-status {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .canceled-status {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .completed-status {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .rejected-status {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .default-status {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .status-indicator {
            font-size: 10px;
            vertical-align: middle;
        }
    </style>

    <style>
        /* Modal Confirmation Styling */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            background-color: #fff;
            border-radius: 10px 10px 0 0;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .modal-header .btn-close {
            font-size: 12px;
            padding: 8px;
            opacity: 0.6;
        }

        .modal-body {
            padding: 20px;
            background-color: #fff;
            height: auto;
        }

        /* Fix for the dark background issue in your image preview modal */
        #imagePreviewModal .modal-body {
            background-color: #000;
        }

        /* Cancel and Complete Confirmation Modals */
        #cancelConfirmationModal .modal-body,
        #completeConfirmationModal .modal-body {
            background-color: #fff;
            height: auto;
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }

        /* Style for package and price display as in your image */
        .modal-body p {
            margin-bottom: 15px;
            font-size: 15px;
            color: #333;
        }

        .confirmation-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #eee;
        }

        .confirmation-details .d-flex {
            margin-bottom: 8px;
            padding: 5px 0;
            font-size: 15px;
        }

        .confirmation-details .d-flex:last-child {
            margin-bottom: 0;
        }

        .confirmation-details span:first-child {
            font-weight: 500;
            color: #666;
        }

        .confirmation-details span:last-child {
            color: #333;
            font-weight: 500;
        }

        /* Button styling to match your image */
        .modal-footer .btn {
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s;
            box-shadow: none;
        }

        .modal-footer .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .modal-footer .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .modal-footer .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .modal-footer .btn-danger:hover {
            background-color: #c82333;
        }

        .modal-footer .btn-success {
            background-color: #009933;
            border: none;
        }

        .modal-footer .btn-success:hover {
            background-color: #008029;
        }

        /* For the payment confirmation modal in your image */
        .modal-body .package-info {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .modal-body .package-info div {
            font-size: 15px;
            color: #555;
        }

        .modal-body .package-info div:last-child {
            font-weight: 500;
            color: #333;
        }

        /* For the Confirm Payment button in your image */
        .btn-confirm-payment {
            background-color: #009933;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            font-weight: 500;
        }

        .btn-confirm-payment:hover {
            background-color: #008029;
        }
    </style>
@endpush
