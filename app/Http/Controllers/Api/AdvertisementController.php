<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdvertisementImage;
use App\Models\AdvertisementView;
use App\Models\AdvertisementBoostedHistory;
use Illuminate\Http\Request;
use App\Constants\Status;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AdvertisementController extends Controller
{
    // public function getAdvertisements()
    // {
    //     $proAdvertisements = Advertisement::with([
    //         'district',
    //         'city',
    //         'category',
    //         'subCategory',
    //         'latestBoostHistory' => function ($q) {
    //             $q->where('status', 1)->orderByDesc('id')->limit(1);
    //         },
    //         'latestBoostHistory.boostPackage',
    //     ])->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED])->get();
    //     foreach ($proAdvertisements as $advertisement) {
    //         $advertisement->file_name = getImage(getFilePath('advertisementImages') . '/' . $advertisement->file_name);
    //     }

    //     return $proAdvertisements;
    // }

    // public function advertisementPreview($id)
    // {
    //     $advertisement = Advertisement::with([
    //         'district',
    //         'city',
    //         'category',
    //         'subCategory',
    //         'user'
    //     ])
    //         ->where('id', $id)
    //         ->firstOrFail();
    //     // Update main image url
    //     $advertisement->file_name = getImage(getFilePath('advertisementImages') . '/' . $advertisement->file_name);
    //     // Get advertisement images
    //     $advertisementImages = AdvertisementImage::where('advertisement_id', $id)
    //         ->orderBy('is_primary', 'desc')
    //         ->orderBy('sort_order', 'asc')
    //         ->get();

    //     foreach($advertisementImages as $advertisementImage) {
    //         $advertisementImage->image = getImage(getFilePath('advertisementImages') . '/' . $advertisementImage->image);            
    //     }

    //     // Set default image if no images are found
    //     if ($advertisementImages->isEmpty()) {
    //         $advertisementImages = collect([
    //             (object)[
    //                 'image' => getImage(getFilePath('advertisementImages') . '/' . $advertisementImages->image) ?? 'default-ad-image.jpg',
    //                 // 'image' => $advertisement->file_name ?? 'default-ad-image.jpg',
    //                 'is_primary' => 1
    //             ]
    //         ]);
    //     }

    //     // Track view and potentially increment impressions
    //     $this->trackAdvertisementView($advertisement);

    //     // Get related ads from the same subcategory with boosting priority
    //     $relatedAds = Advertisement::with([
    //         'latestBoostHistory' => function ($q) {
    //             $q->where('status', 1)->orderByDesc('id')->limit(1);
    //         },
    //         'latestBoostHistory.boostPackage',
    //        ])
    //         ->where('subcategory_id', $advertisement->subcategory_id)
    //         ->where('id', '!=', $advertisement->id)
    //         ->where('status', Status::AD_APPROVED)
    //         ->orderByRaw("
    //             CASE
    //                 WHEN EXISTS (
    //                     SELECT 1
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 ) THEN (
    //                     SELECT
    //                         CASE abp.type
    //                             WHEN 1 THEN 1 -- Top
    //                             WHEN 2 THEN 2 -- Featured
    //                             WHEN 3 THEN 3 -- Urgent
    //                             ELSE 99
    //                         END
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 )
    //                 ELSE 99
    //             END ASC,
    //             CASE
    //                 WHEN EXISTS (
    //                     SELECT 1
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 ) THEN (
    //                     SELECT abp.priority_level
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 )
    //                 ELSE 99
    //             END ASC,
    //             created_at DESC
    //         ")
    //         ->with(['city','district'])
    //         ->take(4)
    //         ->get();

    //     foreach($relatedAds as $relatedAd) {
    //         $relatedAd->file_name = getImage(getFilePath('advertisementImages') . '/' . $relatedAd->file_name);
    //     }

    //     $data = new \stdClass();
    //     $data->advertisement = $advertisement;
    //     $data->relatedAds = $relatedAds;
    //     $data->advertisementImages = $advertisementImages;
    //     return $data;
    // }

    // private function trackAdvertisementView(Advertisement $advertisement)
    // {
    //     $currentUserId = Auth::id();
    //     $viewerIp = request()->ip();

    //     // Don't count impressions if the viewer is the owner of the advertisement
    //     if ($currentUserId && $currentUserId == $advertisement->user_id) {
    //         return;
    //     }

    //     // Check if this viewer (by user ID or IP) has already viewed this ad
    //     $view = AdvertisementView::where('advertisement_id', $advertisement->id)
    //         ->where(function ($query) use ($currentUserId, $viewerIp) {
    //             if ($currentUserId) {
    //                 $query->where('user_id', $currentUserId);
    //             } else {
    //                 $query->where('viewer_ip', $viewerIp);
    //             }
    //         })
    //         ->first();

    //     if ($view) {
    //         // Update the viewed_at timestamp
    //         $view->update([
    //             'viewed_at' => Carbon::now(),
    //         ]);
    //     } else {
    //         // Create a new view record and increment the impression count
    //         AdvertisementView::create([
    //             'advertisement_id' => $advertisement->id,
    //             'user_id' => $currentUserId,
    //             'viewer_ip' => $viewerIp,
    //             'viewed_at' => Carbon::now()
    //         ]);

    //         // Increment impression count
    //         $advertisement->increment('impressions');

    //         $boostedAdvertisementImpression = AdvertisementBoostedHistory::where('status', Status::BOOST_STARTED)
    //             ->where('advertisement_id', $advertisement->id)
    //             ->first();

    //         if ($boostedAdvertisementImpression) {
    //             if ($boostedAdvertisementImpression->impressions == null) {
    //                 $boostedAdvertisementImpression->impressions = 1;
    //                 $boostedAdvertisementImpression->save();
    //             } else {
    //                 $boostedAdvertisementImpression->increment('impressions');
    //             }

    //             if ($boostedAdvertisementImpression->clicks == null) {
    //                 $boostedAdvertisementImpression->clicks = 1;
    //                 $boostedAdvertisementImpression->save();
    //             } else {
    //                 $boostedAdvertisementImpression->increment('clicks');
    //             }
    //         }
    //     }
    // }
}
