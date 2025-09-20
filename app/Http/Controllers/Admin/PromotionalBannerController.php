<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductPromotionBanner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class PromotionalBannerController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('promotional_banners.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Promotional Banner";
        $promotional_banner = ProductPromotionBanner::first();
        return view('admin.promotionalBanner.index', compact('pageTitle', 'promotional_banner'));
    }

    public function store(Request $request, $id = 0)
    {

        $admin = auth()->guard('admin')->user();

        $promotional_banner = ProductPromotionBanner::first();

        if (!$admin || !$admin->can('promotional_banners.update') && $promotional_banner) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('promotional_banners.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        if (!$promotional_banner) {
            $promotional_banner = new ProductPromotionBanner();
            $message = "Promotion Banner added successfully";
        } else {
            $message = "Promotion Banner updated successfully";
        }

        $rules = [
            'image' => $id
                ? ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
                : ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'title' => ['nullable', 'max:50'],
            'description' => ['nullable', 'max:100'],
        ];

        $request->validate($rules);

        if ($request->hasFile('image')) {
            try {
                $oldImage = $promotional_banner->image;

                $promotional_banner->image = fileUploader(
                    $request->image,
                    getFilePath('promotionalBanner'),
                    getFileSize('promotionalBanner'),
                    $oldImage
                );
            } catch (\Exception $exp) {
                return back()->withErrors(['image' => 'Could not upload your image. Please try again.']);
            }
        }

        $promotional_banner->title = $request->title;
        $promotional_banner->description = $request->description;
        $promotional_banner->status = $request->status;

        $promotional_banner->save();

        return back()->withNotify([["success", $message]]);
    }
}
