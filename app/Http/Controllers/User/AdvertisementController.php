<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdvertisementBoostedHistory;
use App\Models\AdvertisementImage;
use App\Models\AdvertisementView;
use App\Models\CategoryFreeAd;
use App\Models\EmployeePackageActivationHistory;
use App\Models\FreeAd;
use App\Models\FreeUserAd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductPromotionBanner;
use Illuminate\Support\Facades\DB;
use App\Models\AdvertisementBoostPackage;
use App\Models\City;
use App\Models\District;
use Illuminate\Support\Facades\Auth;

class AdvertisementController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'      => 'required',
            'subcategory_id'   => 'nullable',
            'district_id'      => 'required|string|max:255',
            'city_id'          => 'required|string|max:255',
            'condition'        => 'nullable|string|in:New,Used,Recondition',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'price'            => 'nullable|numeric|min:0',
            'negotiable'       => 'nullable|in:on',
            'photos'           => 'required|array|max:5',
            'photos.*'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'contact_name'     => 'required|string|max:255',
            'contact_email'    => 'required|email|max:255',
            'contact_mobile'   => 'required|array',
            'contact_mobile.*' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $isNegotiable = $request->input('negotiable') === 'on';
        $phoneNumbers = implode(',', $request->input('contact_mobile'));

        $activatedPackage = EmployeePackageActivationHistory::with('package')
            ->where('user_id', $user->id)
            ->where('activation_expired', 0)
            ->where('payment_status', Status::PAYMENT_SUCCESS)
            ->first();

        $freeAdDetails = FreeAd::first();

        if (!$activatedPackage && !$freeAdDetails) {
            return back()->withNotify([['error', 'No Free advertisement available right now. Please renew or activate a new package to continue.']]);
        }

        $freeUserAds = FreeUserAd::where('user_id', $user->id)->first();

        $isCategoryFreeAd = CategoryFreeAd::where('category_id', $request->category_id)->first();

        $pendingAds = Advertisement::where('user_id', $user->id)
            ->where('status', Status::AD_PENDING)
            ->where('is_free', Status::PAID_AD)
            ->count();

        $pendingFreeAds = Advertisement::where('user_id', $user->id)
            ->where('status', Status::AD_PENDING)
            ->where('is_free', Status::FREE_AD)
            ->count();

        if ($freeAdDetails) {
            $canUseFreeAd = ($freeUserAds && $freeUserAds->remaining_ads > 0 && (($pendingFreeAds + 1) <= $freeAdDetails->no_of_advertisements)) || (!$freeUserAds && (($pendingFreeAds + 1) <= $freeAdDetails->no_of_advertisements));
        } else {
            $canUseFreeAd = false;
        }

        // Check eligibility (Paid or Free)
        if ($activatedPackage && $activatedPackage->remaining_ads >= 1) {
            if (($pendingAds + $activatedPackage->used_ads) >= $activatedPackage->package->no_of_advertisements) {
                return back()->withNotify([['error', 'You’ve reached your advertisement limit. Please renew or activate a new package to continue.']]);
            }

            $expiryDate = now()->addDays($activatedPackage->package->advertisement_duration);
            $packageId = $activatedPackage->package->id;
            $paymentOption = $activatedPackage->payment_method === 'PAY_BY_WALLET' ? Status::PAY_BY_WALLET : null;
            $is_free = Status::PAID_AD;
        } elseif ($canUseFreeAd) {
            if (!$isCategoryFreeAd) {
                return back()->withNotify([
                    ['error', 'You cannot create a free advertisement in this category. Please activate a package to continue.']
                ]);
            }

            $expiryDate = now()->addDays(optional($freeAdDetails)->advertisement_duration ?? 7);
            $packageId = Status::FREE_PACKAGE;
            $paymentOption = Status::FREE;
            $is_free = Status::FREE_AD;
        } else {
            return back()->withNotify([['error', 'Please renew or activate a new package to continue.']]);
        }

        // Define default watermark text
        $defaultWatermarkText = getValue('DEFAULT_WATERMARK_TEXT') ?: 'luxceylone.com';

        // Create Advertisement
        $advertisement = new Advertisement([
            'user_id'             => $user->id,
            'category_id'         => $request->category_id,
            'subcategory_id'      => $request->subcategory_id ?? null,
            'package_id'          => $packageId,
            'district_id'         => $request->district_id,
            'city_id'             => $request->city_id,
            'condition'           => $request->condition,
            'title'               => $request->title,
            'description'         => $request->description,
            'price'               => $request->price ?: 0,
            'is_price_negotiable' => $isNegotiable,
            'contact_name'        => $request->contact_name,
            'contact_email'       => $request->contact_email,
            'contact_mobile'      => $phoneNumbers,
            'advertisement_code'  => getTrx(),
            'payment_option_id'   => $paymentOption,
            'is_boosted'          => Status::ADVERTISEMENT_NOT_BOOSTED,
            'is_free'             => $is_free,
            'status'              => Status::AD_PENDING,
            'posted_date'         => now(),
            'expiry_date'         => $expiryDate,
            'watermark'           => 1,
            'watermark_text'      => $defaultWatermarkText,
        ]);
        $advertisement->save();

        // Upload Images with Watermark
        $imagePaths = [];
        foreach ($request->file('photos') as $index => $image) {
            try {
                // Define the correct directory path
                $directory = 'assets/admin/images/advertisementImages/';
                $size = getFileSize('advertisementImages');

                // Create a unique filename
                $extension = $image->getClientOriginalExtension();
                $name_gen = hexdec(uniqid()) . '.' . $extension;

                $save_url = $directory . $name_gen;

                $this->processImageWithWatermark($image, $save_url, $defaultWatermarkText);

                $imagePaths[] = $name_gen;

                AdvertisementImage::create([
                    'advertisement_id' => $advertisement->id,
                    'image'            => $name_gen,
                    'sort_order'       => $index + 1,
                    'is_primary'       => $index === 0,
                ]);
            } catch (\Exception $e) {
                return back()->withNotify([['error', 'Image Upload Error']]);
            }
        }

        $advertisement->file_name = $imagePaths[0] ?? null;
        $advertisement->save();

        return redirect()->route('user.advertisement.myAds')->withNotify([['success', 'Advertisement created successfully.']]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $advertisement = Advertisement::findOrFail($id);

        if ($advertisement->user_id != $user->id || in_array($advertisement->status, [
            Status::AD_EXPIRED,
            Status::AD_CANCELED,
            Status::AD_REJECTED
        ])) {
            return back()->withNotify([['error', 'You cannot edit this advertisement.']]);
        }

        $validator = Validator::make($request->all(), [
            'category_id'      => 'required',
            'subcategory_id'   => 'nullable',
            'district_id'      => 'required|string|max:255',
            'city_id'          => 'required|string|max:255',
            'condition'        => 'nullable|string|in:New,Used,Recondition',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'price'            => 'nullable|numeric|min:0',
            'negotiable'       => 'nullable',
            'photos'           => 'nullable|array|max:5',
            'photos.*'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'contact_name'     => 'required|string|max:255',
            'contact_email'    => 'required|email|max:255',
            'contact_mobile'   => 'required|array',
            'contact_mobile.*' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $isNegotiable = $request->input('negotiable') === '1';
        $phoneNumbers = implode(',', $request->input('contact_mobile'));
        $defaultWatermarkText = getValue('DEFAULT_WATERMARK_TEXT') ?: 'luxceylone.com';

        // Update advertisement basic details
        $advertisement->category_id    = $request->category_id;
        $advertisement->subcategory_id = $request->subcategory_id ?? null;
        $advertisement->district_id    = $request->district_id;
        $advertisement->city_id        = $request->city_id;
        $advertisement->condition      = $request->condition;
        $advertisement->title          = $request->title;
        $advertisement->description    = $request->description;
        $advertisement->price          = $request->price ?: 0;
        $advertisement->is_price_negotiable = $isNegotiable;
        $advertisement->contact_name   = $request->contact_name;
        $advertisement->contact_email  = $request->contact_email;
        $advertisement->contact_mobile = $phoneNumbers;
        $advertisement->status         = Status::AD_PENDING;
        $advertisement->watermark      = 1;
        $advertisement->watermark_text = $defaultWatermarkText;

        // Process photos to be removed
        if ($request->has('remove_photos') && is_array($request->remove_photos)) {
            foreach ($request->remove_photos as $imageId) {
                $image = AdvertisementImage::where('id', $imageId)
                    ->where('advertisement_id', $advertisement->id)
                    ->first();

                if ($image) {
                    // Delete the physical file if it exists
                    $filePath = 'assets/admin/images/advertisementImages/' . $image->image;
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }

                    // Delete the database record
                    $image->delete();
                }
            }
        }

        // Handle new photo uploads
        $newPhotos = [];
        if ($request->hasFile('photos')) {
            $directory = 'assets/admin/images/advertisementImages/';

            foreach ($request->file('photos') as $key => $image) {
                if (!$image) continue;

                // Generate unique filename
                $extension = $image->getClientOriginalExtension();
                $name_gen = hexdec(uniqid()) . '.' . $extension;
                $save_url = $directory . $name_gen;

                // Process and save image with watermark
                $this->processImageWithWatermark($image, $save_url, $defaultWatermarkText);

                $newPhotos[] = [
                    'image' => $name_gen,
                    'key' => $key
                ];
            }
        }

        // Get current images and determine how many we can add
        $currentImages = AdvertisementImage::where('advertisement_id', $advertisement->id)->get();
        $availableSlots = 5 - $currentImages->count();

        // If we have removed some images, we need to recalculate available slots
        if ($request->has('remove_photos')) {
            $removedCount = count($request->remove_photos);
            $availableSlots += $removedCount;
        }

        // Add new photos up to the available limit
        $newPhotosToAdd = array_slice($newPhotos, 0, $availableSlots);
        $sortOrder = $currentImages->max('sort_order') ?: 0;

        foreach ($newPhotosToAdd as $photo) {
            $sortOrder++;

            AdvertisementImage::create([
                'advertisement_id' => $advertisement->id,
                'image'            => $photo['image'],
                'sort_order'       => $sortOrder,
                'is_primary'       => ($sortOrder === 1), // First image is primary
            ]);
        }

        // Update primary image if needed
        $primaryImage = AdvertisementImage::where('advertisement_id', $advertisement->id)
            ->where('is_primary', true)
            ->first();

        if (!$primaryImage) {
            // No primary image exists, set the first available image as primary
            $firstImage = AdvertisementImage::where('advertisement_id', $advertisement->id)
                ->orderBy('sort_order')
                ->first();

            if ($firstImage) {
                $firstImage->is_primary = true;
                $firstImage->save();

                $advertisement->file_name = $firstImage->image;
            } else {
                $advertisement->file_name = null;
            }
        } else {
            $advertisement->file_name = $primaryImage->image;
        }

        $advertisement->save();

        return redirect()->route('user.advertisement.myAds')->withNotify([
            ['success', 'Advertisement updated successfully. Wait for review.']
        ]);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Advertisement';
        $advertisement = Advertisement::with(['district', 'city', 'category', 'subCategory', 'images', 'user'])->find($id);
        $districts = DB::table('districts')->orderBy('name')->get();
        $defaultDistrictId = DB::table('districts')->first()->id;
        $cities = DB::table('cities')->where('district_id', $defaultDistrictId)->orderBy('name')->get();
        return view('Template::advertisement.editForm', compact('advertisement', 'pageTitle', 'districts', 'cities'));
    }

    private function processImageWithWatermark($image, $savePath, $watermarkText = null)
    {
        try {
            // Create directory if it doesn't exist
            $path = public_path(dirname($savePath));
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            // Process the image using Intervention Image
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image);

            $width = $img->width();
            $height = $img->height();

            // Get watermark text from parameter or use default from settings
            $watermarkText = $watermarkText ?: getValue('DEFAULT_WATERMARK_TEXT');

            // Apply watermark text
            $img->text($watermarkText, $width / 2, $height / 2, function ($font) use ($width) {
                // Use a default system font if custom font doesn't exist
                $font_path = public_path('fonts/arial.ttf');
                if (!file_exists($font_path)) {
                    // Use a system font as fallback
                    $system_fonts = [
                        '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf',
                        '/usr/share/fonts/TTF/Arial.ttf',
                        'C:\\Windows\\Fonts\\arial.ttf'
                    ];

                    foreach ($system_fonts as $system_font) {
                        if (file_exists($system_font)) {
                            $font_path = $system_font;
                            break;
                        }
                    }
                }

                $font->filename($font_path);
                $font->size($width * 0.12);  // Size proportional to image width
                $font->color('rgba(255, 255, 255, 0.3)');  // Semi-transparent white
                $font->align('center');
                $font->valign('middle');
                $font->angle(45);  // Diagonal watermark
            });

            // Save the processed image
            $img->toJpeg(80)->save(public_path($savePath));

            return true;
        } catch (\Exception $e) {
            return back()->withNotify([['error', 'Watermark Application Error']]);
            // Log the exception and rethrow
            // \Log::error('Watermark Application Error: ' . $e->getMessage());
            // throw $e;
        }
    }

    public function applyWatermarkToExisting($id, $imageId = null)
    {
        $user = auth()->user();

        // Get the advertisement and verify ownership
        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // If imageId provided, apply to specific image, otherwise apply to all
        if ($imageId) {
            $images = AdvertisementImage::where('id', $imageId)
                ->where('advertisement_id', $advertisement->id)
                ->get();
        } else {
            $images = $advertisement->images;
        }

        if ($images->isEmpty()) {
            return back()->withNotify([['error', 'No images found']]);
        }

        $watermarkText = $advertisement->watermark_text ?: 'luxceylone.com';
        try {
            foreach ($images as $image) {
                $imagePath = 'public/assets/admin/images/advertisementImages/' . $image->image;

                if (!file_exists(public_path($imagePath))) {
                    continue;
                }

                // Process image with watermark
                $manager = new ImageManager(new Driver());
                $img = $manager->read(public_path($imagePath));

                $width = $img->width();
                $height = $img->height();

                // Apply watermark text
                $img->text($watermarkText, $width / 2, $height / 2, function ($font) use ($width) {
                    // Font setup as before
                    $font_path = public_path('fonts/arial.ttf');
                    if (!file_exists($font_path)) {
                        $system_fonts = [
                            '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf',
                            '/usr/share/fonts/TTF/Arial.ttf',
                            'C:\\Windows\\Fonts\\arial.ttf'
                        ];

                        foreach ($system_fonts as $system_font) {
                            if (file_exists($system_font)) {
                                $font_path = $system_font;
                                break;
                            }
                        }
                    }

                    $font->filename($font_path);
                    $font->size($width * 0.12);
                    $font->color('rgba(255, 255, 255, 0.3)');
                    $font->align('center');
                    $font->valign('middle');
                    $font->angle(45);
                });

                // Save the processed image
                $img->toJpeg(80)->save(public_path($imagePath));
            }

            // Update the watermark flag in the database
            $advertisement->watermark = 1;
            $advertisement->watermark_text = $watermarkText;
            $advertisement->save();

            return back()->withNotify([['success', 'Watermark applied successfully']]);
        } catch (\Exception $e) {
            return back()->withNotify([['error', 'Watermark Application Error']]);
            // \Log::error('Watermark Application Error: ' . $e->getMessage());
            // \Log::error($e->getTraceAsString());

            // return back()->withNotify([['error', 'Couldn\'t apply watermark: ' . $e->getMessage()]]);
        }
    }

    public function publicPreview($id)
    {
        $pageTitle = "Advertisement";
        $advertisement = Advertisement::with(['district', 'city'])->findOrFail($id);
        $advertisementImages = $advertisement->images; // adjust if your relation is named differently

        $seoTitle = $advertisement->title;
        $seoDescription = Str::limit(strip_tags($advertisement->description), 150);
        $seoImage = $advertisementImages->first()
            ? url('assets/admin/images/advertisementImages/' . $advertisementImages->first()->image)
            : url('assets/images/default-ad-image.jpg');
        $relatedAds = Advertisement::with([
            'latestBoostHistory' => function ($q) {
                $q->where('status', 1)->orderByDesc('id')->limit(1);
            },
            'latestBoostHistory.boostPackage',
        ])
            ->where('subcategory_id', $advertisement->subcategory_id)
            ->where('id', '!=', $advertisement->id)
            ->where('status', Status::AD_APPROVED)
            ->orderByRaw("
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM advertisement_boosted_histories abh
                            JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                            WHERE abh.advertisement_id = advertisements.id
                            AND abh.status = 1
                            ORDER BY abh.id DESC
                            LIMIT 1
                        ) THEN (
                            SELECT
                                CASE abp.type
                                    WHEN 1 THEN 1 -- Top
                                    WHEN 2 THEN 2 -- Featured
                                    WHEN 3 THEN 3 -- Urgent
                                    ELSE 99
                                END
                            FROM advertisement_boosted_histories abh
                            JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                            WHERE abh.advertisement_id = advertisements.id
                            AND abh.status = 1
                            ORDER BY abh.id DESC
                            LIMIT 1
                        )
                        ELSE 99
                    END ASC,
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM advertisement_boosted_histories abh
                            JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                            WHERE abh.advertisement_id = advertisements.id
                            AND abh.status = 1
                            ORDER BY abh.id DESC
                            LIMIT 1
                        ) THEN (
                            SELECT abp.priority_level
                            FROM advertisement_boosted_histories abh
                            JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                            WHERE abh.advertisement_id = advertisements.id
                            AND abh.status = 1
                            ORDER BY abh.id DESC
                            LIMIT 1
                        )
                        ELSE 99
                    END ASC,
                    created_at DESC
                ")
            ->take(4)
            ->get();

        return view('Template::advertisement.public_preview', compact(
            'advertisement',
            'advertisementImages',
            'seoTitle',
            'seoDescription',
            'seoImage',
            'pageTitle',
            'relatedAds'
        ));
    }

    // newly added
    public function getAdvertisement(Request $request)
    {
        $pageTitle = 'All Advertisements';
        $search = $request->search;

        // Fetch advertisements with relationships
        $query = Advertisement::with(['district', 'city', 'category', 'subCategory'])
            ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subCategory', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Order advertisements by posted date (newest first)
        $query->orderBy('posted_date', 'desc');

        $advertisements = $query->get();

        // Get all categories for the horizontal category display
        $allCategories = Category::where('status', Status::CATEGORIES_ENABLE)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories with subcategories for the category selector
        $categories = Category::with(['subCategories' => function ($query) {
            $query->where('status', Status::SUB_CATEGORIES_ENABLE)
                ->orderBy('created_at', 'desc');
        }])
            ->where('status', Status::CATEGORIES_ENABLE)
            ->whereHas('subCategories', function ($q) {
                $q->where('status', Status::SUB_CATEGORIES_ENABLE);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $promotion_banner = ProductPromotionBanner::first();
        $activatedPackage = EmployeePackageActivationHistory::where('user_id', auth()->user()->id)
            ->where('activation_expired', 0)
            ->latest()
            ->first();
            if($activatedPackage){
                $remainingAdsInThePackage = $activatedPackage->remaining_ads;
            } else {
                $remainingAdsInThePackage = null;
        }

        $freePackage = FreeUserAd::where('user_id', auth()->user()->id)->first();
            if($freePackage){
            $remainingFreeAds = $freePackage->remaining_ads;
            } else {
                $remainingFreeAds = null;
            }

        if($remainingAdsInThePackage>=0 && $remainingFreeAds>=0 ) {
            $remainingAdCount =  $remainingAdsInThePackage + $remainingFreeAds;
        } else if($remainingAdsInThePackage>=0 && !($remainingFreeAds)) {
            $remainingAdCount = $remainingAdsInThePackage;
        } else if($remainingFreeAds>=0 && !($remainingAdsInThePackage)) {
            $remainingAdCount = $remainingFreeAds;
        } else {
            $remainingAdCount = null;
        }
        return view('Template::advertisement.index', compact('pageTitle', 'advertisements', 'categories', 'allCategories', 'search', 'promotion_banner','remainingAdCount'));
    }

    // boosting
    public function filterAdvertisements(Request $request)
    {
        $search = $request->search;
        $subcategoryIds = $request->subcategory_ids ? explode(',', $request->subcategory_ids) : [];
        $page = $request->page ?? 1;
        $perPage = 20000000;

        $query = Advertisement::with([
            'district',
            'city',
            'category',
            'subCategory',
            'latestBoostHistory' => function ($q) {
                $q->where('status', 1)->orderByDesc('id')->limit(1);
            },
            'latestBoostHistory.boostPackage',
        ])
            ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);

        if (!empty($subcategoryIds)) {
            $query->whereIn('subcategory_id', $subcategoryIds);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subCategory', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('city', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('district', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // advertisements based on boost type, priority, and then by posted date
        $query->orderByRaw("
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                ) THEN (
                    SELECT
                        CASE abp.type
                            WHEN 1 THEN 1 -- Top
                            WHEN 2 THEN 2 -- Featured
                            WHEN 3 THEN 3 -- Urgent
                            ELSE 99
                        END
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                )
                ELSE 99
            END ASC,
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                ) THEN (
                    SELECT abp.priority_level
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                )
                ELSE 99
            END ASC,
            posted_date DESC
        ");

        $totalCount = $query->count();

        $advertisements = $query->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();

        $hasMore = count($advertisements) > $perPage;

        if ($hasMore) {
            $advertisements = $advertisements->take($perPage);
        }

        $formattedAds = $advertisements->map(function ($ad) {
            $boostPackage = null;
            if ($ad->latestBoostHistory && $ad->latestBoostHistory->boostPackage) {
                $boostPackage = [
                    'id' => $ad->latestBoostHistory->boostPackage->id,
                    'name' => $ad->latestBoostHistory->boostPackage->name,
                    'price' => $ad->latestBoostHistory->boostPackage->price,
                    'duration' => $ad->latestBoostHistory->boostPackage->duration,
                    'priority_level' => $ad->latestBoostHistory->boostPackage->priority_level,
                    'type' => $ad->latestBoostHistory->boostPackage->type,
                    'highlighted_color' => $ad->latestBoostHistory->boostPackage->highlighted_color ?? null,
                ];
            }

            return [
                'id' => $ad->id,
                'title' => $ad->title,
                'price_formatted' => number_format($ad->price, 2),
                'city_name' => $ad->city->name ?? 'Unknown',
                'district_name' => $ad->district->name ?? 'Unknown',
                'posted_date' => $ad->posted_date->diffForHumans(),
                'image_url' => $ad->file_name
                    ? asset('assets/admin/images/advertisementImages/' . $ad->file_name)
                    : asset('assets/images/default-ad-image.jpg'),
                'status' => $ad->status,
                'boost_package' => $boostPackage,
                'highlighted_color' => $boostPackage ? $boostPackage['highlighted_color'] : null,
            ];
        });

        return response()->json([
            'advertisements' => $formattedAds,
            'hasMore' => $hasMore,
            'totalCount' => $totalCount,
            'currentPage' => $page
        ]);
    }

    public function showCategoryAdvertisements($categoryName)
    {
        // Find the category by name
        $category = Category::where('name', $categoryName)
            ->where('status', Status::CATEGORIES_ENABLE)
            ->firstOrFail();

        // Get subcategories
        $subCategories = SubCategory::where('category_id', $category->id)
            ->where('status', Status::SUB_CATEGORIES_ENABLE)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get advertisements for this category
        $advertisements = Advertisement::with(['district', 'city', 'category', 'subCategory'])
            ->where('category_id', $category->id)
            ->where('status', Status::AD_APPROVED)
            ->orderBy('posted_date', 'desc')
            ->get();

        $pageTitle = $category->name . ' - Advertisements';

        return view('Template::advertisement.category', compact('pageTitle', 'category', 'subCategories', 'advertisements'));
    }

    public function advertisementDetails($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $pageTitle = $subCategory->name;
        return view('Template::advertisement.details', compact('pageTitle', 'subCategory'));
    }

    public function showAdSelectionPage()
    {
        $pageTitle = 'Post an Advertisement';
        return view('Template::advertisement.selection', compact('pageTitle'));
    }

    public function showCategorySelection()
    {
        $pageTitle = 'Select a Category';

        // Changed from ProductCategory to Category
        $categories = Category::with(['subCategories' => function ($query) {
            $query->where('status', Status::SUB_CATEGORIES_ENABLE)
                ->orderBy('created_at', 'desc');
        }])
            ->where('status', Status::CATEGORIES_ENABLE)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Template::advertisement.selectCategory', compact('pageTitle', 'categories'));
    }

    public function showAdForm($category, $subCategory = null)
    {
        $categoryName = str_replace('-', ' ', ucfirst($category));
        $subCategoryName = $subCategory ? str_replace('-', ' ', ucfirst($subCategory)) : null;

        // Find the category
        $categoryObj = Category::where('name', 'like', $categoryName)
            ->where('status', Status::CATEGORIES_ENABLE)
            ->firstOrFail();

        // Find the subcategory if provided
        $subCategoryObj = null;
        if ($subCategoryName) {
            $subCategoryObj = SubCategory::where('name', 'like', $subCategoryName)
                ->where('category_id', $categoryObj->id)
                ->where('status', Status::SUB_CATEGORIES_ENABLE)
                ->first();
        }

        $pageTitle = $subCategoryName ? "Post ad for $subCategoryName" : "Post ad in $categoryName";

        // Get districts from the database
        $districts = DB::table('districts')->orderBy('name')->get();


        // Get cities for the first district by default
        $defaultDistrictId = DB::table('districts')->first()->id;
        $cities = DB::table('cities')->where('district_id', $defaultDistrictId)->orderBy('name')->get();

        return view('Template::advertisement.form', compact('pageTitle', 'categoryObj', 'subCategoryObj', 'districts', 'cities'));
    }

    public function getCitiesByDistrict($districtId)
    {
        $district = DB::table('districts')->find($districtId);

        if (!$district) {
            return response()->json(['error' => 'District not found'], 404);
        }

        $cities = DB::table('cities')->where('district_id', $district->id)->orderBy('name')->get();

        return response()->json($cities);
    }

    public function showSubCategoryAdvertisements($id)
    {
        $subCategory = SubCategory::with('category')->findOrFail($id);
        $pageTitle = $subCategory->name . ' Advertisements';

        // Get advertisements for this subcategory
        $advertisements = Advertisement::with(['district', 'city', 'category', 'subCategory'])
            ->where('subcategory_id', $id)
            ->where('status', Status::AD_APPROVED)
            ->orderBy('posted_date', 'desc')
            ->get();

        return view('Template::advertisement.subCategory', compact('pageTitle', 'subCategory', 'advertisements'));
    }

    public function trackAdClick(Request $request)
    {
        $advertisement = Advertisement::find($request->ad_id);
        if ($advertisement) {
            $advertisement->increment('clicks');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function trackAdvertisementClick(Request $request)
    {
        $adId = $request->input('ad_id');
        $advertisement = Advertisement::findOrFail($adId);

        // Increment click count
        $advertisement->increment('clicks');

        return response()->json(['status' => 'success']);
    }

    public function advertisementPreview($id)
    {
        
        $advertisement = Advertisement::with([
            'district',
            'city',
            'category',
            'subCategory',
            'user'
        ])
            ->where('id', $id)
            ->firstOrFail();

        // Get advertisement images
        $advertisementImages = AdvertisementImage::where('advertisement_id', $id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();

        // Set default image if no images are found
        if ($advertisementImages->isEmpty()) {
            $advertisementImages = collect([
                (object)[
                    'image' => $advertisement->file_name ?? 'default-ad-image.jpg',
                    'is_primary' => 1
                ]
            ]);
        }

        // Track view and potentially increment impressions
        $this->trackAdvertisementView($advertisement);

        // Get related ads from the same subcategory with boosting priority
        $relatedAds = Advertisement::with([
            'latestBoostHistory' => function ($q) {
                $q->where('status', 1)->orderByDesc('id')->limit(1);
            },
            'latestBoostHistory.boostPackage',
        ])
            ->where('subcategory_id', $advertisement->subcategory_id)
            ->where('id', '!=', $advertisement->id)
            ->where('status', Status::AD_APPROVED)
            ->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    ) THEN (
                        SELECT
                            CASE abp.type
                                WHEN 1 THEN 1 -- Top
                                WHEN 2 THEN 2 -- Featured
                                WHEN 3 THEN 3 -- Urgent
                                ELSE 99
                            END
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    )
                    ELSE 99
                END ASC,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    ) THEN (
                        SELECT abp.priority_level
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    )
                    ELSE 99
                END ASC,
                created_at DESC
            ")
            ->take(4)
            ->get();

        $pageTitle = $advertisement->title;

        return view('Template::advertisement.preview', compact('pageTitle', 'advertisement', 'relatedAds', 'advertisementImages'));
    }

    private function trackAdvertisementView(Advertisement $advertisement)
    {
        $currentUserId = Auth::id();
        $viewerIp = request()->ip();

        // Don't count impressions if the viewer is the owner of the advertisement
        if ($currentUserId && $currentUserId == $advertisement->user_id) {
            return;
        }

        // Check if this viewer (by user ID or IP) has already viewed this ad
        $view = AdvertisementView::where('advertisement_id', $advertisement->id)
            ->where(function ($query) use ($currentUserId, $viewerIp) {
                if ($currentUserId) {
                    $query->where('user_id', $currentUserId);
                } else {
                    $query->where('viewer_ip', $viewerIp);
                }
            })
            ->first();

        if ($view) {
            // Update the viewed_at timestamp
            $view->update([
                'viewed_at' => Carbon::now(),
            ]);
        } else {
            // Create a new view record and increment the impression count
            AdvertisementView::create([
                'advertisement_id' => $advertisement->id,
                'user_id' => $currentUserId,
                'viewer_ip' => $viewerIp,
                'viewed_at' => Carbon::now()
            ]);

            // Increment impression count
            $advertisement->increment('impressions');

            $boostedAdvertisementImpression = AdvertisementBoostedHistory::where('status', Status::BOOST_STARTED)
                ->where('advertisement_id', $advertisement->id)
                ->first();

            if ($boostedAdvertisementImpression) {
                if ($boostedAdvertisementImpression->impressions == null) {
                    $boostedAdvertisementImpression->impressions = 1;
                    $boostedAdvertisementImpression->save();
                } else {
                    $boostedAdvertisementImpression->increment('impressions');
                }

                if ($boostedAdvertisementImpression->clicks == null) {
                    $boostedAdvertisementImpression->clicks = 1;
                    $boostedAdvertisementImpression->save();
                } else {
                    $boostedAdvertisementImpression->increment('clicks');
                }
            }
        }
    }

    public function cancelAdvertisement($id)
    {
        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $advertisement->status = Status::AD_CANCELED;
        $advertisement->save();

        return redirect()->route('user.advertisement.preview', $id)
            ->with('success', 'Advertisement has been successfully canceled.');
    }

    public function completeAdvertisement($id)
    {
        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($advertisement->status == Status::AD_APPROVED) {
            $advertisement->status = Status::AD_COMPLETED;
            $advertisement->save();

            return redirect()->route('user.advertisement.preview', $id)
                ->with('success', 'Advertisement has been marked as completed.');
        }
        return redirect()->route('user.advertisement.preview', $id)
            ->with('error', 'Only approved advertisements can be marked as completed.');
    }

    public function showAdFormPreview($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $pageTitle = 'Preview Advertisement';
        return view('Template::advertisement.preview', compact('pageTitle', 'advertisement'));
    }

    public function showAdFormEdit($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $pageTitle = 'Edit Advertisement';
        return view('Template::advertisement.edit', compact('pageTitle', 'advertisement'));
    }

    public function filter(Request $request)
    {
        $search = $request->search;
        $subcategoryIds = $request->filled('subcategory_ids') && !empty($request->subcategory_ids) ? explode(',', $request->subcategory_ids) : [];
        $page = $request->page ?? 1;
        $perPage = 200;

        $query = Advertisement::with([
            'district',
            'city',
            'category',
            'subCategory',
            'latestBoostHistory' => function ($q) {
                $q->where('status', 1)->orderByDesc('id')->limit(1);
            },
            'latestBoostHistory.boostPackage',
        ]);

        // Always filter by the authenticated user for "My Ads" page
        if ($request->filled('user_ads_only')) {
            $query->where('user_id', auth()->id());
        } else {
            // For public ads, only show approved ads
            $query->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);
        }

        // Filter by subcategories if provided
        if (!empty($subcategoryIds)) {
            $query->whereIn('subcategory_id', $subcategoryIds);
        }

        // Filter by status if provided and filter_by_status is true
        if (
            $request->filled('filter_by_status') && filter_var($request->filter_by_status, FILTER_VALIDATE_BOOLEAN) &&
            $request->filled('statuses') && !empty($request->statuses)
        ) {
            $statuses = explode(',', $request->statuses);
            // Make sure statuses are valid integers
            $validStatuses = array_map('intval', $statuses);
            if (!empty($validStatuses)) {
                $query->whereIn('status', $validStatuses);
            }
        }

        // Enhanced search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($subq) use ($search) {
                        $subq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subCategory', function ($subq) use ($search) {
                        $subq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('city', function ($subq) use ($search) {
                        $subq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('district', function ($subq) use ($search) {
                        $subq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Order advertisements based on boost type, priority, and then by posted date
        if (!$request->filled('user_ads_only')) {
            $query->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    ) THEN (
                        SELECT
                            CASE abp.type
                                WHEN 1 THEN 1 -- Top
                                WHEN 2 THEN 2 -- Featured
                                WHEN 3 THEN 3 -- Urgent
                                ELSE 99
                            END
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    )
                    ELSE 99
                END ASC,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    ) THEN (
                        SELECT abp.priority_level
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    )
                    ELSE 99
                END ASC,
                posted_date DESC
            ");
        } else {
            // For user's own advertisements, just order by posted date
            $query->orderByDesc('posted_date');
        }

        $totalCount = $query->count();

        $advertisements = $query->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();

        $hasMore = count($advertisements) > $perPage;

        if ($hasMore) {
            $advertisements = $advertisements->take($perPage);
        }

        $formattedAds = $advertisements->map(function ($ad) {
            $boostPackage = null;
            if ($ad->latestBoostHistory && $ad->latestBoostHistory->boostPackage) {
                $boostPackage = [
                    'id' => $ad->latestBoostHistory->boostPackage->id,
                    'name' => $ad->latestBoostHistory->boostPackage->name,
                    'price' => $ad->latestBoostHistory->boostPackage->price,
                    'duration' => $ad->latestBoostHistory->boostPackage->duration,
                    'priority_level' => $ad->latestBoostHistory->boostPackage->priority_level,
                    'type' => $ad->latestBoostHistory->boostPackage->type,
                    'highlighted_color' => $ad->latestBoostHistory->boostPackage->highlighted_color ?? null,
                ];
            }

            return [
                'id' => $ad->id,
                'title' => $ad->title,
                'price' => $ad->price,
                'price_formatted' => number_format($ad->price, 2),
                'city_name' => $ad->city->name ?? 'Unknown',
                'district_name' => $ad->district->name ?? 'Unknown',
                'posted_date' => $ad->posted_date->diffForHumans(),
                'status' => $ad->status,
                'is_completed' => $ad->status == Status::AD_COMPLETED,
                'image_url' => $ad->file_name
                    ? asset('assets/admin/images/advertisementImages/' . $ad->file_name)
                    : asset('assets/images/default-ad-image.jpg'),
                'boost_package' => $boostPackage,
                'highlighted_color' => $boostPackage ? $boostPackage['highlighted_color'] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'advertisements' => $formattedAds,
            'hasMore' => $hasMore,
            'totalCount' => $totalCount,
            'currentPage' => $page
        ]);
    }

    public function showMyAdPage()
    {
        $pageTitle = 'My Advertisements';
        $user = auth()->user();

        // Load categories with their subcategories for filtering
        $categories = Category::with('subcategories')->orderBy('name')->get();



        // Pass status constants to the view
        $adStatuses = [
            'PENDING' => Status::AD_PENDING,
            'APPROVED' => Status::AD_APPROVED,
            'COMPLETED' => Status::AD_COMPLETED,
            'PAUSE' => Status::AD_PAUSE,
            'ONGOING' => Status::AD_ONGOING,
            'EXPIRED' => Status::AD_EXPIRED,
            'CANCELED' => Status::AD_CANCELED,
            'REJECTED' => Status::AD_REJECTED
        ];
        $activatedPackage = EmployeePackageActivationHistory::where('user_id', auth()->user()->id)
            ->where('activation_expired', 0)
            ->latest()
            ->first();
            if($activatedPackage){
            $remainingAdsInThePackage = $activatedPackage->remaining_ads;
        } else {
            $remainingAdsInThePackage = null;
        }

        $freePackage = FreeUserAd::where('user_id', auth()->user()->id)->first();
            if($freePackage){
            $remainingFreeAds = $freePackage->remaining_ads;
        } else {
            $remainingFreeAds = null;
        }

        if($remainingAdsInThePackage>=0 && $remainingFreeAds>=0) {
            $remainingAdCount =  $remainingAdsInThePackage + $remainingFreeAds;
        } else if($remainingAdsInThePackage>=0 && !($remainingFreeAds)) {
            $remainingAdCount = $remainingAdsInThePackage;
        } else if($remainingFreeAds>=0 && !($remainingAdsInThePackage)) {
            $remainingAdCount = $remainingFreeAds;
        } else {
            $remainingAdCount = null;
        }

        return view('Template::user.advertisement.myAds', compact('pageTitle', 'adStatuses', 'categories','remainingAdCount'));
    }

    public function advertisementBoost($id)
    {
        $pageTitle = 'Boost Advertisement';
        $user = auth()->user();

        // Find the specific advertisement
        $advertisement = Advertisement::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', Status::AD_APPROVED) // Ensure the advertisement is active
            ->with(['category', 'subCategory', 'district', 'city']) // Eager load related models
            ->first();

        // If advertisement doesn't exist or doesn't belong to user, redirect with error
        if (!$advertisement) {
            $notify[] = ['error', 'You do not have permission to boost it.'];
            return back()->withNotify($notify);
        }

        $all_boost_packages = AdvertisementBoostPackage::all();

        return view('Template::user.advertisement.boost', compact('pageTitle', 'advertisement', 'all_boost_packages'));
    }
}
