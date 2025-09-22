<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\ClaimedRankReward;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\PurchaseHistory;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\UserRankDetail;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\EmployeePackageActivationHistory;
use App\Models\JobPost;
use App\Models\KeyValuePair;
use App\Models\Language;
use App\Models\PendingJobCommission;
use App\Models\Transaction;
use App\Models\User;
use App\Notify\Notify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laramin\Utility\VugiChugi;


function systemDetails()
{
    $system['name'] = 'microlab';
    $system['version'] = '3.0';
    $system['build_version'] = '5.0.9';
    return $system;
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0)
        return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $template = session('template') ?? gs('active_template');
    if ($asset)
        return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}



function activeTemplateName()
{
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null)
{
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}
function siteFavicon()
{
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } elseif (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = VugiChugi::gttmp() . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = null, $avatar = false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }

    if ($avatar) {
        return asset('assets/images/avatar.jpg');
    }

    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null)
{
    $globalShortCodes = [
        'site_name' => gs('site_name'),
        'site_currency' => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->pushImage = $pushImage;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null)
{
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3)
        $class = 'side-menu--open';
    elseif ($type == 2)
        $class = 'sidebar-submenu__open';
    else
        $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value))
                return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param))
                return $class;
            else
                return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');

    if (!$lang) {
        $language = Language::where('is_default', Status::ENABLE)->first();
        $lang = $language ? $language->code : 'en';
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key)
        return @$general->$key;
    return $general;
}
function isImage($string)
{
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}


function convertToReadableSize($size)
{
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int) $matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}


function frontendImage($sectionName, $image, $size = null, $seo = false)
{
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function shortDescription($string, $length = 100)
{
    $cleanText = strip_tags($string);
    return Str::limit($cleanText, $length);
}

function getValue($key)
{
    $keyValuePair = KeyValuePair::where('key', $key)->first();
    return $keyValuePair ? $keyValuePair->value : null;
}

function isUserEmployeePackageActivated($user)
{
    return $user->employee_package_activated == Status::ENABLE;
}

function getUserPendingJobCommissionTotal($user)
{
    return $user->pending_job_commision_total;
}

function jobCommission($user_id, $jobRate, $paymentOptionId)
{
    $total = $jobRate;
    $totalCommision = $total / 100 * getValue('JOB_COMMISSION_FOR_COMPANY');
    $companyCommisoion = $totalCommision / 2;
    $commissionForReferrals = $totalCommision / 2;

    $directReferralCommissionTotal = $commissionForReferrals / 100 * getValue('JOB_COMMISSION_PERCENTAGE_FOR_REFERRED_USER');
    $nonDirectCommisionTotalForOneUser = ($commissionForReferrals - $directReferralCommissionTotal) / getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT');

    $user = User::findOrFail($user_id);

    $companyAccount = User::where('username', 'sljob')->first();

    if ($totalCommision <= $user->balance) {

        Transaction::create([
            'user_id' => $companyAccount->id,
            'amount' => $companyCommisoion,
            'charge' => 0,
            'trx_type' => '+',
            'remark' => 'job_commission_for_company',
            'details' => 'Job Commission For Company',
            'trx' => getTrx(),
            'post_balance' => $companyAccount->balance + $companyCommisoion,
        ]);

        $user->balance -= $companyCommisoion;
        $user->save();

        if ($user->referred_user_id) {
            Transaction::create([
                'user_id' => $user->referred_user_id,
                'amount' => $directReferralCommissionTotal,
                'charge' => 0,
                'trx_type' => '+',
                'remark' => 'job_commission_for_rerfferd_user',
                'details' => 'Job Commission For Refferd User',
                'trx' => getTrx(),
                'post_balance' => getAmount($user->referrer->balance + $directReferralCommissionTotal),
            ]);

            $refferedUser = User::find($user->referred_user_id);
            $refferedUser->balance += $directReferralCommissionTotal;
            $refferedUser->save();

            $user->balance -= $directReferralCommissionTotal;
            $user->save();
        }
        $refUser = User::find($user->referred_user_id);
        if ($refUser && $refUser->referred_user_id !== null) {

            $refUser = User::find($user->referrer->referred_user_id);
            $visitedUsers = [];

            DB::beginTransaction();

            try {
                for ($x = 0; $x < getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT'); $x++) {
                    if (!$refUser->referred_user_id || in_array($refUser->id, $visitedUsers)) {
                        break;
                    }

                    $visitedUsers[] = $refUser->id;

                    Transaction::create([
                        'user_id' => $refUser->id,
                        'amount' => $nonDirectCommisionTotalForOneUser,
                        'charge' => 0,
                        'trx_type' => '+',
                        'remark' => 'job_commission_for_non_direct_users',
                        'details' => 'Job Commission For Non Direct Users',
                        'trx' => getTrx(),
                        'post_balance' => getAmount($refUser->balance + $nonDirectCommisionTotalForOneUser),
                    ]);

                    $refUser->balance += $nonDirectCommisionTotalForOneUser;
                    $refUser->save();


                    $refUser = User::find($refUser->referred_user_id);
                }
                $totalSendCommisionAmount = $x * $nonDirectCommisionTotalForOneUser;

                $balance = ($nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT')) - $totalSendCommisionAmount;

                if ($balance > 0) {
                    Transaction::create([
                        'user_id' => $companyAccount->id,
                        'amount' => $balance,
                        'charge' => 0,
                        'trx_type' => '+',
                        'remark' => 'job_commission_non_direct_users_balance',
                        'details' => 'Job Commission Non Direct Users Balance',
                        'trx' => getTrx(),
                        'post_balance' => getAmount($companyAccount->balance + $balance),
                    ]);

                    $companyAccount->balance += $balance;
                    $companyAccount->save();
                }

                $user->balance -= $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT');
                $user->save();
                DB::commit();
                return 'Commission transactions successfully created';
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Job Commission transaction failed: ' . $e->getMessage());
                return 'Transaction failed';
            }
        } else {
            Transaction::create([
                'user_id' => $companyAccount->id,
                'amount' => $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT'),
                'charge' => 0,
                'trx_type' => '+',
                'remark' => 'job_commission_non_direct_users_balance',
                'details' => 'Job Commission Non Direct Users Balance',
                'trx' => getTrx(),
                'post_balance' => getAmount($companyAccount->balance + ($nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT'))),
            ]);
            $companyAccount->balance += $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT');
            $companyAccount->save();
        }

        pendingCommisions($user->id);
        processEmployeePackageActivations($user->id);
    } else {

        if ($companyCommisoion <= $user->balance) {
            Transaction::create([
                'user_id' => $companyAccount->id,
                'amount' => $companyCommisoion,
                'charge' => 0,
                'trx_type' => '+',
                'remark' => 'job_commission_for_company',
                'details' => 'Job Commission For Company',
                'trx' => getTrx(),
                'post_balance' => $companyAccount->balance + $companyCommisoion,
            ]);

            $user->balance -= $companyCommisoion;
            $user->save();
        } else {
            PendingJobCommission::create([
                'user_id' => $user->id,
                'amount' => $companyCommisoion,
                'remark' => 'job_commission_for_company',
                'details' => 'Job Commission For Company',
                'send_to_user_id' => $companyAccount->id,
                'status' => Status::PAYMENT_PENDING,
            ]);

            $user->pending_job_commision_total += $companyCommisoion;
            $user->save();
        }

        if ($user->referred_user_id) {
            if ($directReferralCommissionTotal <= $user->balance) {
                Transaction::create(attributes: [
                    'user_id' => $user->referred_user_id,
                    'amount' => $directReferralCommissionTotal,
                    'charge' => 0,
                    'trx_type' => '+',
                    'remark' => 'job_commission_for_rerfferd_user',
                    'details' => 'Job Commission For Refferd User',
                    'trx' => getTrx(),
                    'post_balance' => getAmount($user->referrer->balance + $directReferralCommissionTotal),
                ]);


                $user->balance -= $directReferralCommissionTotal;
                $user->save();

                $refferedUser = User::find($user->referred_user_id);
                $refferedUser->balance += $directReferralCommissionTotal;
                $refferedUser->save();
            } else {
                PendingJobCommission::create([
                    'user_id' => $user->id,
                    'amount' => $directReferralCommissionTotal,
                    'remark' => 'job_commission_for_rerfferd_user',
                    'details' => 'Job Commission For Refferd User',
                    'send_to_user_id' => $user->referred_user_id,
                    'status' => Status::PAYMENT_PENDING,
                ]);

                $user->pending_job_commision_total += $directReferralCommissionTotal;
                $user->save();
            }

            $refUser = User::find($user->referred_user_id);
            if ($refUser && $refUser->referred_user_id !== null) {
                if ($nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT') <= $user->balance) {

                    $visitedUsers = [];
                    $refUser = User::find($user->referrer->referred_user_id);
                    DB::beginTransaction();

                    try {
                        for ($x = 0; $x < getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT'); $x++) {
                            if (!$refUser->referred_user_id || in_array($refUser->id, $visitedUsers)) {
                                break;
                            }

                            $visitedUsers[] = $refUser->id;

                            Transaction::create([
                                'user_id' => $refUser->id,
                                'amount' => $nonDirectCommisionTotalForOneUser,
                                'charge' => 0,
                                'trx_type' => '+',
                                'remark' => 'job_commission_for_non_direct_users',
                                'details' => 'Job Commission For Non Direct Users',
                                'trx' => getTrx(),
                                'post_balance' => getAmount($refUser->balance + $nonDirectCommisionTotalForOneUser),
                            ]);

                            $refUser->balance += $nonDirectCommisionTotalForOneUser;
                            $refUser->save();
                            $refUser = User::find($refUser->referred_user_id);
                        }
                        $totalSendCommisionAmount = $x * $nonDirectCommisionTotalForOneUser;

                        $balance = ($nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT')) - $totalSendCommisionAmount;
                        if ($balance > 0) {
                            Transaction::create([
                                'user_id' => $companyAccount->id,
                                'amount' => $balance,
                                'charge' => 0,
                                'trx_type' => '+',
                                'remark' => 'job_commission_non_direct_users_balance',
                                'details' => 'Job Commission Non Direct Users Balance',
                                'trx' => getTrx(),
                                'post_balance' => getAmount($companyAccount->balance + $balance),
                            ]);
                            $companyAccount->balance += $balance;
                            $companyAccount->save();
                        }

                        DB::commit();

                        $user->balance -= $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT');
                        $user->save();
                        return 'Commission transactions successfully created';
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Job Commission transaction failed: ' . $e->getMessage());
                        return 'Transaction failed';
                    }
                } else {
                    $refUser = User::find($user->referred_user_id);
                    $visitedUsers = [];

                    DB::beginTransaction();

                    try {
                        for ($x = 0; $x < getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT'); $x++) {
                            if (!$refUser->referred_user_id || in_array($refUser->id, $visitedUsers)) {
                                break;
                            }

                            $visitedUsers[] = $refUser->id;

                            PendingJobCommission::create([
                                'user_id' => $user->id,
                                'amount' => $nonDirectCommisionTotalForOneUser,
                                'remark' => 'job_commission_for_non_direct_users',
                                'details' => 'Job Commission For Non Direct User',
                                'send_to_user_id' => $refUser->id,
                                'status' => Status::PAYMENT_PENDING,
                            ]);
                            $refUser = User::find($refUser->referred_user_id);
                        }
                        $totalSendCommisionAmount = $x * $nonDirectCommisionTotalForOneUser;

                        $balance = ($nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT')) - $totalSendCommisionAmount;
                        if ($balance > 0) {

                            PendingJobCommission::create([
                                'user_id' => $user->id,
                                'amount' => $balance,
                                'remark' => 'job_commission_non_direct_users_balance',
                                'details' => 'Job Commission Non Direct Users Balance',
                                'send_to_user_id' => $companyAccount->id,
                                'status' => Status::PAYMENT_PENDING,
                            ]);
                        }
                        DB::commit();

                        $user->pending_job_commision_total += $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT');
                        $user->save();
                        return 'Commission transactions successfully created';
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Job Commission transaction failed: ' . $e->getMessage());
                        return 'Transaction failed';
                    }
                }
            } else {
                PendingJobCommission::create([
                    'user_id' => $user->id,
                    'amount' => $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT'),
                    'remark' => 'job_commission_non_direct_users_balance',
                    'details' => 'Job Commission Non Direct Users Balance',
                    'send_to_user_id' => $companyAccount->id,
                    'status' => Status::PAYMENT_PENDING,
                ]);

                $user->pending_job_commision_total += $nonDirectCommisionTotalForOneUser * getValue('JOB_COMMISSION_GET_NON_DIRECTLY_REFERRED_USERS_COUNT');
                $user->save();
            }
        }
    }

    return 'Insufficient funds for commission';
}


function updateEmployeePackageHistory($userId)
{
    $user = User::find($userId);
    $activatedPackage = EmployeePackageActivationHistory::where('user_id', $userId)->where('activation_expired', Status::DISABLE)->where('total_jobs_did', '<', getValue('SLJOB_EMPLOYEE_PACKAGE_JOB_COUNT'))->first();
    $activatedPackage->total_jobs_did = $activatedPackage->total_jobs_did + 1;
    if ($activatedPackage->total_jobs_did >= getValue('SLJOB_EMPLOYEE_PACKAGE_JOB_COUNT')) {
        $activatedPackage->activation_expired = Status::ENABLE;
        $activatedPackage->save();
        $user->employee_package_activated = Status::DISABLE;
    }
    $user->save();
}

function pendingCommisions($user_id)
{

    $user = User::find($user_id);
    $userBalance = $user->balance;

    $pendingCommissions = PendingJobCommission::where('user_id', $user->id)
        ->where('status', Status::PAYMENT_PENDING)
        ->orderBy('amount')
        ->get();

    $paidCommissions = [];
    $totalPaidAmount = 0;

    foreach ($pendingCommissions as $commission) {
        if ($user->balance >= $commission->amount) {
            $userBalance -= $commission->amount;

            $commission->status = Status::PAYMENT_SUCCESS;
            $commission->save();

            $sendToUser = User::where('id', $commission->send_to_user_id)->first();

            Transaction::create([
                'user_id' => $commission->send_to_user_id,
                'amount' => $commission->amount,
                'charge' => 0,
                'trx_type' => '+',
                'remark' => $commission->remark,
                'details' => $commission->details,
                'trx' => getTrx(),
                'post_balance' => $sendToUser->balance + $commission->amount,
            ]);

            $sendToUser->balance += $commission->amount;
            $sendToUser->save();

            $paidCommissions[] = $commission;
            $totalPaidAmount += $commission->amount;
        } else {
            break;
        }
    }

    $user->balance = $userBalance;
    $user->save();
}

function processEmployeePackageActivations($user_id)
{

    $user = User::find($user_id);
    $userBalance = $user->balance;

    $activations = EmployeePackageActivationHistory::where('user_id', $user->id)
        ->where('payment_status', Status::PAYMENT_PENDING)
        ->get();

    $paidActivations = [];
    $totalPaidAmount = 0;

    foreach ($activations as $activation) {

        if ($userBalance >= $activation->transaction->amount) {

            $companyCommision = $activation->transaction->amount / 100 * getValue('PACKAGE_ACTIVATION_COMMISSION_PERCENTAGE_FOR_COMPANY');
            $refferdUserCommission = $activation->transaction->amount / 100 * getValue('PACKAGE_ACTIVATION_COMMISSION_PERCENTAGE_FOR_REFFERED_USER');
            $nonDirectUserCommision = $activation->transaction->amount / 100 * getValue('PACKAGE_ACTIVATION_COMMISSION_PERCENTAGE_FOR_NON_DIRECT_USERS');
            $nonDirectOneUserCommision = $nonDirectUserCommision / getValue('NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_PACKAGE_ACTIVATION_COMMISSION');

            $userBalance -= $activation->transaction->amount;

            $activation->payment_status = Status::PAYMENT_SUCCESS;
            $activation->save();

            $companyAccount = User::where('username', 'sljob')->first();
            Transaction::create([
                'user_id' => $companyAccount->id,
                'amount' => $companyCommision,
                'charge' => 0,
                'trx_type' => '+',
                'remark' => 'package_activation_commission_for_company',
                'details' => 'Package Activation Commission For Company',
                'trx' => getTrx(),
                'post_balance' => $companyAccount->balance + $companyCommision,
            ]);
            $companyAccount->balance += $companyCommision;
            $companyAccount->save();

            $refUser = User::find($user->referred_user_id);
            if ($refUser) {
                Transaction::create([
                    'user_id' => $refUser->id,
                    'amount' => $refferdUserCommission,
                    'charge' => 0,
                    'trx_type' => '+',
                    'remark' => 'package_activation_commission_for_reffered_user',
                    'details' => 'Package Activation Commission For Reffered User',
                    'trx' => getTrx(),
                    'post_balance' => $refUser->balance + $refferdUserCommission,
                ]);

                $refUser->balance += $refferdUserCommission;
                $refUser->save();

                if ($refUser && $refUser->referred_user_id !== null) {

                    $refUser = User::find($refUser->referred_user_id);
                    $visitedUsers = [];

                    DB::beginTransaction();

                    try {
                        for ($x = 0; $x < getValue('NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_PACKAGE_ACTIVATION_COMMISSION'); $x++) {
                            if (!$refUser->referred_user_id || in_array($refUser->id, $visitedUsers)) {
                                break;
                            }

                            $visitedUsers[] = $refUser->id;

                            Transaction::create([
                                'user_id' => $refUser->id,
                                'amount' => $nonDirectOneUserCommision,
                                'charge' => 0,
                                'trx_type' => '+',
                                'remark' => 'package_activation_commission_for_non_direct_users',
                                'details' => 'Package Activation Commission For Non Direct Users',
                                'trx' => getTrx(),
                                'post_balance' => getAmount($refUser->balance + $nonDirectOneUserCommision),
                            ]);

                            $refUser->balance += $nonDirectOneUserCommision;
                            $refUser->save();
                            $refUser = User::find($refUser->referred_user_id);
                        }

                        $totalSendCommisionAmount = $x * $nonDirectOneUserCommision;

                        $balance = ($nonDirectOneUserCommision * getValue('NUMBER_OF_NON_DIRECT_USERS_ELIGIBLE_FOR_PACKAGE_ACTIVATION_COMMISSION')) - $totalSendCommisionAmount;

                        if ($balance > 0) {
                            Transaction::create([
                                'user_id' => $companyAccount->id,
                                'amount' => $balance,
                                'charge' => 0,
                                'trx_type' => '+',
                                'remark' => 'package_activation_commission_for_non_direct_users_balance',
                                'details' => 'Package Activation Commission For Non Direct Users Balance',
                                'trx' => getTrx(),
                                'post_balance' => getAmount($companyAccount->balance + $balance),
                            ]);
                            $companyAccount->balance += $balance;
                            $companyAccount->save();
                        }

                        DB::commit();
                        return 'Commission transactions successfully created';
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Job Commission transaction failed: ' . $e->getMessage());
                        return 'Transaction failed';
                    }
                }
            }



            $paidActivations[] = $activation;
            $totalPaidAmount += $activation->transaction->amount;
        } else {
            break;
        }
    }
}

function updateUserRankRequirements()
{
    $users = User::all();

    foreach ($users as $user) {
        $rank = UserRankDetail::firstOrCreate(['user_id' => $user->id], [
            'level_one_user_count' => 0,
            'level_two_user_count' => 0,
            'level_three_user_count' => 0,
            'level_four_user_count' => 0,
        ]);

        $rank->level_one_user_count = 0;
        $rank->level_two_user_count = 0;
        $rank->level_three_user_count = 0;
        $rank->level_four_user_count = 0;

        $firstLevelUsers = User::where('referred_user_id', $user->id)->get();
        $firstLevelUserIds = $firstLevelUsers->pluck('id')->toArray();
        if (!empty($firstLevelUserIds)) {
            $rank->level_one_user_count = count($firstLevelUserIds);
        }

        $secondLevelUsers = User::whereIn('referred_user_id', $firstLevelUserIds)->get();
        $secondLevelUserIds = $secondLevelUsers->pluck('id')->toArray();
        if (!empty($secondLevelUserIds)) {
            $rank->level_two_user_count = count($secondLevelUserIds);
        }

        $thirdLevelUsers = User::whereIn('referred_user_id', $secondLevelUserIds)->get();
        $thirdLevelUserIds = $thirdLevelUsers->pluck('id')->toArray();
        if (!empty($thirdLevelUserIds)) {
            $rank->level_three_user_count = count($thirdLevelUserIds);
        }

        $fourthLevelUsers = User::whereIn('referred_user_id', $thirdLevelUserIds)->get();
        $fourthLevelUserIds = $fourthLevelUsers->pluck('id')->toArray();
        if (!empty($fourthLevelUserIds)) {
            $rank->level_four_user_count = count($fourthLevelUserIds);
        }

        $rank->save();
    }
}

function updateUserRanks(): void
{
    $users = User::all();
    $rankRequirements = RankRequirement::join('ranks', 'rank_requirements.rank_id', '=', 'ranks.id')
        ->orderBy('ranks.rank', 'desc')
        ->select('rank_requirements.*', 'ranks.rank as rank_level')
        ->get();

    foreach ($users as $user) {
        $rankDetail = UserRankDetail::where('user_id', $user->id)->first();
        if (!$rankDetail)
            continue;

        $hasPurchase = PurchaseHistory::where('customer_id', $user->id)
            ->where('payment_status', Status::PAYMENT_SUCCESS)
            ->exists();

        foreach ($rankRequirements as $requirement) {
            $meetsUserCounts = $rankDetail->level_one_user_count >= $requirement->level_one_user_count &&
                $rankDetail->level_two_user_count >= $requirement->level_two_user_count &&
                $rankDetail->level_three_user_count >= $requirement->level_three_user_count &&
                $rankDetail->level_four_user_count >= $requirement->level_four_user_count;

            $meetsPurchase = !$requirement->required_at_least_one_product_purchase || $hasPurchase;

            if ($meetsUserCounts && $meetsPurchase && $rankDetail->current_rank_id != $requirement->rank_id) {
                $rankDetail->current_rank_id = $requirement->rank_id;
                $rankDetail->save();

                updateRankRewards($user->id, $requirement->rank_level, $requirement->rank_id);
                break;
            }
        }
    }
}

function updateRankRewards(int $userId, int $rankLevel, int $newRankId): void
{
    $reward = ClaimedRankReward::firstOrNew(['user_id' => $userId]);

    for ($i = 1; $i <= $rankLevel; $i++) {
        $statusField = "rank_" . numberToWord($i) . "_status";
        $claimField = "rank_" . numberToWord($i) . "_claimed_status";

        if ($reward->$statusField == Status::RANK_PENDING) {
            $reward->$statusField = Status::RANK_ACHIEVED;
            $reward->$claimField = Status::RANK_CLAIM_PENDING;
        }
    }

    $reward->current_rank_id = $newRankId;
    $reward->save();
}

function numberToWord(int $num): string
{
    return ['', 'one', 'two', 'three', 'four'][$num];
}

function updateClaimedRankRewards()
{
    $users = User::all();


}