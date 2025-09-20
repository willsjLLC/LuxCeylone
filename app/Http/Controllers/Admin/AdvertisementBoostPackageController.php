<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvertisementBoostPackage;
use Illuminate\Support\Str;

class AdvertisementBoostPackageController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('boost_packages.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All Boost Packages";
        $boostPackages = AdvertisementBoostPackage::searchable(['name'])->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.advertisementBoostPackages.index', compact('pageTitle','boostPackages'));
    }

    public function create()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('boost_packages.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Advertisement Boost Package";
        $types = AdvertisementBoostPackage::pluck('type')->toArray();
        return view('admin.advertisementBoostPackages.create', compact('pageTitle', 'types'));
    }

    public function store(Request $request, $id = 0)
    {

        $admin = auth()->guard('admin')->user();

		if (!$admin || !$admin->can('boost_packages.update') && $id) {
			return response()->view('admin.errors.403', [], 403);
		} elseif (!$admin || !$admin->can('boost_packages.create')) {
			return response()->view('admin.errors.403', [], 403);
		}

        $isUpdate = $id > 0;
        $totalBoostPackages = AdvertisementBoostPackage::count();

        if (!$isUpdate && $totalBoostPackages >= 3) {
            return back()->withNotify([['error', "Maximum package limit is 3"]]);
        }

        $rules = [
            'status'                 => 'required|in:1,0',
            'name'                   => 'required|string|max:100',
            'description'            => 'required|string',
            'package_code'           => 'nullable|string|max:50',
            'type'                   => 'required',
            'price'                  => 'required|numeric|min:0',
            'duration'               => 'required|integer|min:1',
            'priority_level'         => 'required',
            'highlighted_color'      => 'nullable|integer|min:0',
        ];

        $request->validate($rules);

        $package = $isUpdate ? AdvertisementBoostPackage::findOrFail($id) : new AdvertisementBoostPackage();
        $message = $isUpdate ? 'Advertisement Boost Package updated successfully' : 'Advertisement Boost Package added successfully';

        $package->status                  = $request->status;
        $package->name                    = $request->name;
        $package->highlighted_color       = $request->highlighted_color;
        $package->description             = $request->description;
        $package->package_code            = $request->package_code ?: $this->generatePackageCode();
        $package->type                    = $request->type;
        $package->priority_level          = $request->priority_level;
        $package->price                   = $request->price;
        $package->duration                = $request->duration;

        $package->save();

        return back()->withNotify([['success', $message]]);
    }

    protected function generatePackageCode()
    {
        return 'PKG-' . strtoupper(Str::random(6));
    }

    public function edit($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('boost_packages.edit')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Edit Advertisement Boost Packages ";
        $advertisementBoostPackage  = AdvertisementBoostPackage::findOrFail($id);
        $types = AdvertisementBoostPackage::pluck('type')->toArray();
        return view('admin.advertisementBoostPackages.create', compact('advertisementBoostPackage', 'pageTitle', 'types'));
    }

    public function status($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('boost_packages.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $package = AdvertisementBoostPackage::findOrFail($id);
        $package->status = $package->status == 0 ? 1 : 0;
        $package->save();

        $notify[] = ["success", 'Status Updated Successfully'];
        return back()->withNotify($notify);
    }
}
