<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\KeyValuePair;
use App\Models\Month;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function systemSetting()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'System Settings';
        $settings = json_decode(file_get_contents(resource_path('views/admin/setting/settings.json')));
        return view('admin.setting.system', compact('pageTitle', 'settings'));
    }
    public function general()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.general_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'General Setting';
        $timezones = timezone_identifiers_list();
        $currentTimezone = array_search(config('app.timezone'), $timezones);
        $months = Month::all();
        return view('admin.setting.general', compact('pageTitle', 'timezones', 'currentTimezone', 'months'));
    }

    public function generalUpdate(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.general_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'site_name' => 'required|string|max:40',
            'cur_text' => 'required|string|max:40',
            'cur_sym' => 'required|string|max:40',
            'base_color' => 'nullable|regex:/^[a-f0-9]{6}$/i',
            'timezone' => 'required|integer',
            'currency_format' => 'required|in:1,2,3',
            'paginate_number' => 'required|integer',

            'ad_boost_commission_percentage_for_company' => 'required|integer',
            'ad_boost_commission_percentage_for_referred_user' => 'required|integer',
            'ad_boost_commission_percentage_for_none_direct_users' => 'required|integer',
            'number_of_non_direct_users_eligible_for_ad_boost' => 'required|integer',

            'sinhalise_bonus_from' => 'required|integer',
            'sinhalise_bonus_to' => 'required|integer',
            'tamil_bonus_from' => 'required|integer',
            'tamil_bonus_to' => 'required|integer',
            'muslims_bonus_from' => 'required|integer',
            'muslims_bonus_to' => 'required|integer',
            'christian_bonus_from' => 'required|integer',
            'christian_bonus_to' => 'required|integer',

            'voucher_remaining_date' => 'required|integer',
            'user_recursive_top_up_range' => 'required|integer',
            'product_delivery_chargers' => 'required|integer'

        ]);

        $bonusTypes = ['sinhalise', 'tamil', 'muslims', 'christian'];

        foreach ($bonusTypes as $type) {
            $from = (int) $request->input("{$type}_bonus_from");
            $to = (int) $request->input("{$type}_bonus_to");

            $expectedTo = ($from % 12) + 1;
            $expectedFrom = ($to + 10) % 12 + 1;

            if ($to !== $expectedTo) {
                return back()->withErrors([
                    "{$type}_bonus_to" => ucfirst($type) . " Bonus To month must be the month after From",
                ])->withInput();
            }

            if ($from !== $expectedFrom) {
                return back()->withErrors([
                    "{$type}_bonus_from" => ucfirst($type) . " Bonus From month must be the month before To",
                ])->withInput();
            }
        }


        $timezones = timezone_identifiers_list();
        $timezone = @$timezones[$request->timezone] ?? 'UTC';

        $general = gs();
        $general->site_name = $request->site_name;
        $general->cur_text = $request->cur_text;
        $general->cur_sym = $request->cur_sym;
        $general->paginate_number = $request->paginate_number;
        $general->base_color = str_replace('#', '', $request->base_color);
        $general->currency_format = $request->currency_format;
        $general->save();

        KeyValuePair::updateOrCreate(['key' => 'AD_BOOST_COMMISSION_PERCENTAGE_FOR_COMPANY'], ['value' => $request->ad_boost_commission_percentage_for_company]);
        KeyValuePair::updateOrCreate(['key' => 'AD_BOOST_COMMISSION_PERCENTAGE_FOR_REFERRED_USER'], ['value' => $request->ad_boost_commission_percentage_for_referred_user]);
        KeyValuePair::updateOrCreate(['key' => 'AD_BOOST_COMMISSION_PERCENTAGE_FOR_NON_DIRECT_USERS'], ['value' => $request->ad_boost_commission_percentage_for_none_direct_users]);
        KeyValuePair::updateOrCreate(['key' => 'NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_AD_BOOST_COMMISSION'], ['value' => $request->number_of_non_direct_users_eligible_for_ad_boost]);
        KeyValuePair::updateOrCreate(['key' => 'SINHALISE_BONUS_FROM'], ['value' => $request->sinhalise_bonus_from]);
        KeyValuePair::updateOrCreate(['key' => 'SINHALISE_BONUS_TO'], ['value' => $request->sinhalise_bonus_to]);
        KeyValuePair::updateOrCreate(['key' => 'TAMIL_BONUS_FROM'], ['value' => $request->tamil_bonus_from]);
        KeyValuePair::updateOrCreate(['key' => 'TAMIL_BONUS_TO'], ['value' => $request->tamil_bonus_to]);
        KeyValuePair::updateOrCreate(['key' => 'MUSLIMS_BONUS_FROM'], ['value' => $request->muslims_bonus_from]);
        KeyValuePair::updateOrCreate(['key' => 'MUSLIMS_BONUS_TO'], ['value' => $request->muslims_bonus_to]);
        KeyValuePair::updateOrCreate(['key' => 'CHRISTIAN_BONUS_FROM'], ['value' => $request->christian_bonus_from]);
        KeyValuePair::updateOrCreate(['key' => 'CHRISTIAN_BONUS_TO'], ['value' => $request->christian_bonus_to]);
        KeyValuePair::updateOrCreate(['key' => 'VOUCHER_REMAINING_DATE'], ['value' => $request->voucher_remaining_date]);
        KeyValuePair::updateOrCreate(['key' => 'USER_RECURSIVE_TOP_UP_RANGE'], ['value' => $request->user_recursive_top_up_range]);
        KeyValuePair::updateOrCreate(['key' => 'PRODUCT_DELIVERY_CHARGERS'], ['value' => $request->product_delivery_chargers]);

        $timezoneFile = config_path('timezone.php');
        $content = '<?php $timezone = "' . $timezone . '" ?>';
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function systemConfiguration()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.system_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'System Configuration';
        return view('admin.setting.configuration', compact('pageTitle'));
    }

    public function systemConfigurationSubmit(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.system_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $general = gs();
        $general->kv = $request->kv ? Status::ENABLE : Status::DISABLE;
        $general->ev = $request->ev ? Status::ENABLE : Status::DISABLE;
        $general->en = $request->en ? Status::ENABLE : Status::DISABLE;
        $general->sv = $request->sv ? Status::ENABLE : Status::DISABLE;
        $general->sn = $request->sn ? Status::ENABLE : Status::DISABLE;
        $general->approve_job = $request->approve_job ? Status::ENABLE : Status::DISABLE;
        $general->pn = $request->pn ? Status::ENABLE : Status::DISABLE;
        $general->force_ssl = $request->force_ssl ? Status::ENABLE : Status::DISABLE;
        $general->secure_password = $request->secure_password ? Status::ENABLE : Status::DISABLE;
        $general->registration = $request->registration ? Status::ENABLE : Status::DISABLE;
        $general->agree = $request->agree ? Status::ENABLE : Status::DISABLE;
        $general->multi_language = $request->multi_language ? Status::ENABLE : Status::DISABLE;
        $general->save();

        KeyValuePair::updateOrCreate(['key' => 'IS_EMPLOYEE_JOB_COMMISION_ENABLE'], ['value' => $request->is_employee_job_commision_enable ? Status::ENABLE : Status::DISABLE]);
        KeyValuePair::updateOrCreate(['key' => 'IS_SLJOB_EMPLOYEE_PACKAGE_ENABLE'], ['value' => $request->is_sljob_employee_package_enable ? Status::ENABLE : Status::DISABLE]);


        $notify[] = ['success', 'System configuration updated successfully'];
        return back()->withNotify($notify);
    }

    public function logoIcon()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.logo_icon_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Logo & Favicon';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.logo_icon_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'logo' => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'favicon' => ['image', new FileTypeValidate(['png'])],
        ]);
        $path = getFilePath('logoIcon');
        if ($request->hasFile('logo')) {
            try {
                fileUploader($request->logo, $path, filename: 'logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                fileUploader($request->favicon, $path, filename: 'favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Logo & favicon updated successfully'];
        return back()->withNotify($notify);
    }

    public function customCss()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.custom_css_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Custom CSS';
        $file = activeTemplate(true) . 'css/custom.css';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.custom_css', compact('pageTitle', 'fileContent'));
    }

    public function customCssSubmit(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.custom_css_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $file = activeTemplate(true) . 'css/custom.css';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->css);
        $notify[] = ['success', 'CSS updated successfully'];
        return back()->withNotify($notify);
    }

    public function sitemap()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.sitemap_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Sitemap XML';
        $file = 'sitemap.xml';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.sitemap', compact('pageTitle', 'fileContent'));
    }

    public function sitemapSubmit(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.sitemap_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $file = 'sitemap.xml';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->sitemap);
        $notify[] = ['success', 'Sitemap updated successfully'];
        return back()->withNotify($notify);
    }

    public function robot()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.robot_txt_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Robots TXT';
        $file = 'robots.xml';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.robots', compact('pageTitle', 'fileContent'));
    }

    public function robotSubmit(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.robot_txt_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $file = 'robots.xml';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->robots);
        $notify[] = ['success', 'Robots txt updated successfully'];
        return back()->withNotify($notify);
    }

    public function maintenanceMode()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.maintain_mode_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Maintenance Mode';
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        return view('admin.setting.maintenance', compact('pageTitle', 'maintenance'));
    }

    public function maintenanceModeSubmit(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.maintain_mode_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'description' => 'required',
            'image' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);
        $general = gs();
        $general->maintenance_mode = $request->status ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        $image = @$maintenance->data_values->image;
        if ($request->hasFile('image')) {
            try {
                $old = $image;
                $image = fileUploader($request->image, getFilePath('maintenance'), getFileSize('maintenance'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $maintenance->data_values = [
            'description' => $request->description,
            'image' => $image
        ];
        $maintenance->save();

        $notify[] = ['success', 'Maintenance mode updated successfully'];
        return back()->withNotify($notify);
    }

    public function cookie()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.cookie_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'GDPR Cookie';
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        return view('admin.setting.cookie', compact('pageTitle', 'cookie'));
    }

    public function cookieSubmit(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.cookie_configurations')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->validate([
            'short_desc' => 'required|string|max:255',
            'description' => 'required',
        ]);
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $cookie->data_values = [
            'short_desc' => $request->short_desc,
            'description' => $request->description,
            'status' => $request->status ? Status::ENABLE : Status::DISABLE,
        ];
        $cookie->save();
        $notify[] = ['success', 'Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }

    public function socialiteCredentials()
    {
        $admin = auth()->guard('admin')->user();

        $gs = GeneralSetting::first();
        $socialite_credentials = $gs->socialite_credentials;
        if (!$admin || !$admin->can('settings.social_login_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Social Login Credentials';
        return view('admin.setting.social_credential', compact('pageTitle', 'socialite_credentials'));
    }

    public function updateSocialiteCredentialStatus($key)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.social_login_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $gs = GeneralSetting::first();
        $socialite_credentials = $gs->socialite_credentials;
        try {
            $socialite_credentials->$key->status = $socialite_credentials->$key->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        } catch (\Throwable $th) {
            abort(404);
        }

        $gs->socialite_credentials = $socialite_credentials;
        $gs->save();

        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }

    public function updateSocialiteCredential(Request $request, $key)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.social_login_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $gs = GeneralSetting::first();
        $socialite_credentials = $gs->socialite_credentials;
        try {
            @$socialite_credentials->$key->client_id = $request->client_id;
            @$socialite_credentials->$key->client_secret = $request->client_secret;
        } catch (\Throwable $th) {
            abort(404);
        }
        $gs->socialite_credentials = $socialite_credentials;
        $gs->save();

        $notify[] = ['success', ucfirst($key) . ' credential updated successfully'];
        return back()->withNotify($notify);
    }
}
