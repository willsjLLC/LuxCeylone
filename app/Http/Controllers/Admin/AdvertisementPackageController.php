<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdvertisementBoostPackage;
use App\Models\AdvertisementPackage;
use App\Models\Category;
use App\Models\FreeAd;
use App\Models\PackageActivationCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdvertisementPackageController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('main_packages.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All Advertisement Packages";
        $packages = AdvertisementPackage::searchable(['name'])->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.advertisementPackages.index', compact('pageTitle', 'packages'));

        // resources\views\admin\advertisementPackages\index.blade.php
    }

    public function create()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('main_packages.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $boostPackages =  AdvertisementBoostPackage::all();

        $pageTitle = "Advertisement Package";
        $types = AdvertisementPackage::pluck('type')->toArray();
        return view('admin.advertisementPackages.create', compact('pageTitle', 'types', 'boostPackages'));
    }

    public function store(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || (!$admin->can('main_packages.update') && $id) || !$admin->can('main_packages.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $isUpdate = $id > 0;
        $totalPackages = AdvertisementPackage::count();

        if (!$isUpdate && $totalPackages >= 3) {
            return back()->withNotify([['error', "Maximum package limit is 3"]]);
        }

        $rules = [
            'status' => 'required|in:1,0',
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'package_code' => 'nullable|string|max:50',
            'type' => 'required',
            'no_of_advertisements' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'advertisement_duration' => 'required|integer|min:1',
            'package_duration' => 'required|integer|min:1',
            'includes_boost' => 'required|boolean',
            'no_of_boost' => 'nullable|integer|min:0',
            'boost_package_id' => 'nullable',
            'company_commission' => 'required',
            'company_expenses' => 'required',
            'level_one_commission' => 'required',
            'level_two_commission' => 'required',
            'level_three_commission' => 'required',
            'level_four_commission' => 'required',
            'customers_voucher' => 'required',
            'customers_festival' => 'required',
            'customers_saving' => 'required',
            'leader_bonus' => 'required',
            'leader_vehicle_lease' => 'required',
            'leader_petrol' => 'required',
            'max_ref_complete_to_car' => 'required',
            'top_leader_car' => 'required',
            'top_leader_house' => 'required',
            'top_leader_expenses' => 'required',
        ];

        $validated = $request->validate($rules);

        try {
            $package = $isUpdate ? AdvertisementPackage::findOrFail($id) : new AdvertisementPackage();
            $message = $isUpdate ? 'Advertisement Package updated successfully' : 'Advertisement Package added successfully';

            $package->fill($request->only([
                'status',
                'name',
                'description',
                'package_code',
                'type',
                'no_of_advertisements',
                'price',
                'advertisement_duration',
                'package_duration',
                'includes_boost',
                'no_of_boost',
                'boost_package_id'
            ]));
            $package->package_code = $package->package_code ?: $this->generatePackageCode();
            $package->no_of_boost = $request->includes_boost ? ($request->no_of_boost ?? 0) : 0;
            $package->boost_package_id = $request->includes_boost ? ($request->boost_package_id ?? null) : null;
            $package->save();

            $commission = $isUpdate
                ? PackageActivationCommission::where('pkg_id', $id)->first()
                : new PackageActivationCommission();

            if (!$commission) {
                $commission = new PackageActivationCommission();
            }

            $commission->pkg_id = $package->id;
            $commission->fill($request->only([
                'company_commission',
                'company_expenses',
                'level_one_commission',
                'level_two_commission',
                'level_three_commission',
                'level_four_commission',
                'customers_commission',
                'customers_voucher',
                'customers_festival',
                'customers_saving',
                'leader_bonus',
                'leader_vehicle_lease',
                'leader_petrol',
                'max_ref_complete_to_car',
                'top_leader_car',
                'top_leader_house',
                'top_leader_expenses'
            ]));
            $commission->save();


            return back()->withNotify([['success', $message]]);
        } catch (\Exception $e) {
            return back()->withNotify([['error', 'An error occurred while saving the package.']]);
        }
    }


    protected function generatePackageCode()
    {
        return 'PKG-' . strtoupper(Str::random(6));
    }

    public function edit($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('main_packages.edit')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $boostPackages =  AdvertisementBoostPackage::all();
        $pageTitle = "Edit Advertisement Packages ";
        $advertisementPackage  = AdvertisementPackage::findOrFail($id);
        $commissions = PackageActivationCommission::where('pkg_id',$id )->first();
        $types = AdvertisementPackage::pluck('type')->toArray();
        return view('admin.advertisementPackages.create', compact('advertisementPackage', 'pageTitle', 'types', 'boostPackages', 'commissions'));
    }

    public function status($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('main_packages.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $package = AdvertisementPackage::findOrFail($id);
        $package->status = $package->status == 0 ? 1 : 0;
        $package->save();

        $notify[] = ["success", 'Status Updated Successfully'];
        return back()->withNotify($notify);
    }

    public function freeAd()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('free_packages.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $categories = Category::where('status', Status::CATEGORIES_ENABLE)->get();
        $freeAd = FreeAd::with('categories')->first();
        $pageTitle = "Fee Ads Configurations";
        return view('admin.freeAdvertisement.index', compact('pageTitle', 'freeAd', 'categories'));
    }

    public function storeFreeAd(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('free_packages.setup')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $isUpdate = $id > 0;

        $rules = [
            'status' => 'required|in:1,0',
            'no_of_advertisements' => 'required|integer',
            'advertisement_duration' => 'required|integer',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ];

        $request->validate($rules);

        $package = $isUpdate ? FreeAd::findOrFail($id) : FreeAd::firstOrNew([]);

        $message = $isUpdate ? 'Advertisement Free Package updated successfully' : 'Advertisement Free Package added successfully';

        $package->status = $request->status;
        $package->no_of_advertisements = $request->no_of_advertisements;
        $package->advertisement_duration = $request->advertisement_duration;

        $package->save();

        $package->categories()->sync($request->categories);

        return back()->withNotify([['success', $message]]);
    }
}
