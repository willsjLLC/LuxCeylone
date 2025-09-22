<?php

namespace App\Providers;

use App\Constants\Status;
use App\Lib\Searchable;
use App\Models\AdminNotification;
use App\Models\ClaimedRankReward;
use App\Models\Deposit;
use App\Models\JobPost;
use App\Models\Frontend;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\UserTraining;
use App\Models\Withdrawal;
use App\Service\PackageActivationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Advertisement;
use App\Models\GeneralSetting;
use Illuminate\Http\Client\ConnectionException;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(\App\Providers\TelescopeServiceProvider::class); // Your custom provider
        }

        $this->app->singleton(PackageActivationService::class, function ($app) {
            return new PackageActivationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $gs = GeneralSetting::first();
        $socialite_credentials = $gs->socialite_credentials;
        if (!empty($socialite_credentials->google)) {
            $google = $socialite_credentials->google;

            config([
                'services.google.client_id' => $google->client_id,
                'services.google.client_secret' => $google->client_secret,
                'services.google.redirect' => 'https://addciti.com/auth/google/callback',
            ]);
        }

        if (!cache()->get('SystemInstalled')) {
            $envFilePath = base_path('.env');
            if (!file_exists($envFilePath)) {
                header('Location: install');
                exit;
            }
            $envContents = file_get_contents($envFilePath);
            if (empty($envContents)) {
                header('Location: install');
                exit;
            } else {
                cache()->put('SystemInstalled', true);
            }
        }


        $activeTemplate = activeTemplate();
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount' => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount' => User::kycUnverified()->count(),
                'kycPendingUsersCount' => User::kycPending()->count(),
                'kycApprovedUsersCount' => User::kycApproved()->count(),
                'pendingTicketCount' => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount' => Deposit::pending()->count(),
                'pendingWithdrawCount' => Withdrawal::pending()->count(),
                'pendingJobCount' => JobPost::pending()->count(),
                'processingOrdersCount' => Order::where('status', Status::ORDER_PROCESSING)->count(),
                'pendingTrainingsCount' => UserTraining::where('status', Status::TRAINING_PENDING)->count(),
                'completedTrainingsCount' => UserTraining::where('status', Status::TRAINING_COMPLETED)->count(),
                'rejectedTrainingsCount' => UserTraining::where('status', Status::TRAINING_REJECTED)->count(),
                'pendingAdsCount' => Advertisement::where('status', Status::AD_PENDING)->count(),
                'processingRankRewardCount' => ClaimedRankReward::where(function ($query) {
                    $query->where('rank_one_claimed_status', Status::RANK_CLAIM_PROCESSING)
                        ->orWhere('rank_two_claimed_status', Status::RANK_CLAIM_PROCESSING)
                        ->orWhere('rank_three_claimed_status', Status::RANK_CLAIM_PROCESSING)
                        ->orWhere('rank_four_claimed_status', Status::RANK_CLAIM_PROCESSING);
                })->count(),
                'completedRankRewardCount' => ClaimedRankReward::where(function ($query) {
                    $query->where('rank_one_claimed_status', Status::RANK_CLAIM_COMPLETED)
                        ->orWhere('rank_two_claimed_status', Status::RANK_CLAIM_COMPLETED)
                        ->orWhere('rank_three_claimed_status', Status::RANK_CLAIM_COMPLETED)
                        ->orWhere('rank_four_claimed_status', Status::RANK_CLAIM_COMPLETED);
                })->count(),
                'canceledRankRewardCount' => ClaimedRankReward::where(function ($query) {
                    $query->where('rank_one_claimed_status', Status::RANK_CLAIM_CANCELED)
                        ->orWhere('rank_two_claimed_status', Status::RANK_CLAIM_CANCELED)
                        ->orWhere('rank_three_claimed_status', Status::RANK_CLAIM_CANCELED)
                        ->orWhere('rank_four_claimed_status', Status::RANK_CLAIM_CANCELED);
                })->count(),
                'updateAvailable' => version_compare(gs('available_version'), systemDetails()['version'], '>') ? 'v' . gs('available_version') : false,
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications' => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if (gs('force_ssl')) {
            \URL::forceScheme('https');
        }

        Route::middleware('web')
            ->group(base_path('routes/user.php'));

        Paginator::useBootstrapFive();
    }

    // public function boot(): void
    // {
    //     $gs = GeneralSetting::first();

    //     // Add null check before accessing properties
    //     if ($gs && !empty($gs->socialite_credentials)) {
    //         $socialite_credentials = $gs->socialite_credentials;

    //         if (!empty($socialite_credentials->google)) {
    //             $google = $socialite_credentials->google;

    //             config([
    //                 'services.google.client_id' => $google->client_id,
    //                 'services.google.client_secret' => $google->client_secret,
    //                 'services.google.redirect' => 'https://addciti.com/auth/google/callback',
    //             ]);
    //         }
    //     }

    //     if (!cache()->get('SystemInstalled')) {
    //         $envFilePath = base_path('.env');
    //         if (!file_exists($envFilePath)) {
    //             header('Location: install');
    //             exit;
    //         }
    //         $envContents = file_get_contents($envFilePath);
    //         if (empty($envContents)) {
    //             header('Location: install');
    //             exit;
    //         } else {
    //             cache()->put('SystemInstalled', true);
    //         }
    //     }


    //     $activeTemplate = activeTemplate();
    //     $viewShare['activeTemplate'] = $activeTemplate;
    //     $viewShare['activeTemplateTrue'] = activeTemplate(true);
    //     $viewShare['emptyMessage'] = 'Data not found';
    //     view()->share($viewShare);


    //     view()->composer('admin.partials.sidenav', function ($view) {
    //         try{
    //             $litePendingResponse = Http::get(config('services.lite_api.url') . '/api/lite/pending/advertisements');

    //             if ($litePendingResponse->successful()) {
    //                 $pendingLiteAds = json_decode($litePendingResponse->body(), true);
    //             }

    //             $advertisements = Advertisement::pending();

    //             $advertisements = $advertisements
    //                 ->searchable(['category:name', 'user:username', 'title', 'advertisement_code'])
    //                 ->filter(['user_id'])
    //                 ->with('user')
    //                 ->orderBy('id', 'desc')
    //                 ->get();

    //             if ($pendingLiteAds) {
    //                 foreach ($pendingLiteAds as $externalAd) {
    //                     $advertisements->push((object) $externalAd);
    //                 }
    //             }
    //             $totalPendingAds = $advertisements->count();
    //         } catch (ConnectionException $e) {
    //             $totalPendingAds = Advertisement::where('status', Status::AD_PENDING)->count();
    //         }

    //         $view->with([
    //             'bannedUsersCount' => User::banned()->count(),
    //             'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
    //             'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
    //             'kycUnverifiedUsersCount' => User::kycUnverified()->count(),
    //             'kycPendingUsersCount' => User::kycPending()->count(),
    //             'kycApprovedUsersCount' => User::kycApproved()->count(),
    //             'pendingTicketCount' => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
    //             'pendingDepositsCount' => Deposit::pending()->count(),
    //             'pendingWithdrawCount' => Withdrawal::pending()->count(),
    //             'pendingJobCount' => JobPost::pending()->count(),
    //             'processingOrdersCount' => Order::where('status', Status::ORDER_PROCESSING)->count(),
    //             'pendingTrainingsCount' => UserTraining::where('status', Status::TRAINING_PENDING)->count(),
    //             'completedTrainingsCount' => UserTraining::where('status', Status::TRAINING_COMPLETED)->count(),
    //             'rejectedTrainingsCount' => UserTraining::where('status', Status::TRAINING_REJECTED)->count(),
    //             // 'pendingAdsCount' => Advertisement::where('status', Status::AD_PENDING)->count(),
    //             'pendingAdsCount' => $totalPendingAds,
    //             'updateAvailable' => version_compare(gs('available_version'), systemDetails()['version'], '>') ? 'v' . gs('available_version') : false,
    //         ]);
    //     });

    //     view()->composer('admin.partials.topnav', function ($view) {
    //         $view->with([
    //             'adminNotifications' => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
    //             'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
    //         ]);
    //     });

    //     view()->composer('partials.seo', function ($view) {
    //         $seo = Frontend::where('data_keys', 'seo.data')->first();
    //         $view->with([
    //             'seo' => $seo ? $seo->data_values : $seo,
    //         ]);
    //     });

    //     if (gs('force_ssl')) {
    //         \URL::forceScheme('https');
    //     }

    //     Route::middleware('web')
    //         ->group(base_path('routes/user.php'));

    //     Paginator::useBootstrapFive();
    // }
}
