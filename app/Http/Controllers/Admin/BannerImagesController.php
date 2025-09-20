<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerImage;

class BannerImagesController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('banner_images.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Add Banner Images";
        $bannerImage = BannerImage::all();
        return view('admin.banner.index', compact('pageTitle', 'bannerImage'));
    }

    public function store(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('banner_images.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            $bannerImage = new BannerImage();

            if ($request->hasFile('image')) {
                try {
                    $bannerImage->images = fileUploader($request->image, getFilePath('bannerImage'));
                } catch (\Exception $exp) {
                    return back()->withErrors(['image' => 'Couldn\'t upload your image. Please try again.']);
                }
            }

            $bannerImage->save();

            $notify[] = ["success", 'Banner Image Uploaded Successfully'];

            return back()->withNotify($notify);
        } catch (\Exception $e) {
            return back()->withErrors(['Error' => $e->getMessage()]);
        }
    }

    public function deleteImage($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('banner_images.delete')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $bannerImage = BannerImage::findOrFail($id);

        $bannerImage->delete();
        $notify[] = ["success", 'Banner Image deleted Successfully'];
        return back()->withNotify($notify);
    }
}
