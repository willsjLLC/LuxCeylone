<?php

use Illuminate\Support\Facades\Route;


Route::
        namespace('Auth')->group(function () {
            Route::middleware('admin.guest')->group(function () {
                Route::controller('LoginController')->group(function () {
                    Route::get('/', 'showLoginForm')->name('login');
                    Route::post('/', 'login')->name('login');
                    Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
                });

                // Admin Password Reset
                Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
                    Route::get('reset', 'showLinkRequestForm')->name('reset');
                    Route::post('reset', 'sendResetCodeEmail');
                    Route::get('code-verify', 'codeVerify')->name('code.verify');
                    Route::post('verify-code', 'verifyCode')->name('verify.code');
                });

                Route::controller('ResetPasswordController')->group(function () {
                    Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
                    Route::post('password/reset/change', 'reset')->name('password.change');
                });
            });
        });

Route::middleware('auth:admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
        Route::get('chart/transaction', 'transactionReport')->name('chart.transaction');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('leaders/active', 'activeLeaders')->name('leaders.active');
        Route::get('top-leaders', 'topLeaders')->name('top.leaders');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('kyc-approved', 'kycApprovedUsers')->name('kyc.approved');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');

        // cash transfer
        Route::get('top-leader/expenses-transfer/{id}', 'topLeaderExpensesTransfer')->name('topLeader.expenses.transfer');
        Route::get('top-leader/house-transfer/{id}', 'topLeaderHouseTransfer')->name('topLeader.house.transfer');
        Route::get('top-leader/car-transfer/{id}', 'topLeaderCarTransfer')->name('topLeader.car.transfer');

        Route::get('leader/leasing-transfer/{id}', 'leaderLeasingTransfer')->name('leader.leasing.transfer');
        Route::get('leader/petrol-transfer/{id}', 'leaderPetrolTransfer')->name('leader.petrol.transfer');
        Route::get('leader/referral-transfer/{id}', 'leaderReferralCalculate')->name('leader.referral.calculate');

        Route::get('customer/saving-transfer/{id}', 'customerSavingTransfer')->name('customer.saving.transfer');
        Route::get('customer/festival-transfer/{id}', 'customerFestivalTransfer')->name('customer.festival.transfer');
        Route::get('customer/voucher-transfer/{id}', 'customerVoucherTransfer')->name('customer.voucher.transfer');

        Route::get('company/saving-transfer/{id}', 'companySavingTransfer')->name('company.saving.transfer');

        Route::get('second-owner/{id}', 'secondOwner')->name('secondOwner');
    });

    // Manage Admins
    Route::controller('AdminPermissionController')->name('management.')->prefix('management')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('list', 'list')->name('list');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('permission/{id}', 'permission')->name('permission');
        Route::post('permission/update/{id}', 'updatePermission')->name('permission.update');
        Route::get('sync', 'syncPermission')->name('permission.sync');
    });

    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function () {
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });


    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
        Route::get('all/{user_id?}', 'deposit')->name('list');
        Route::get('pending/{user_id?}', 'pending')->name('pending');
        Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
        Route::get('approved/{user_id?}', 'approved')->name('approved');
        Route::get('successful/{user_id?}', 'successful')->name('successful');
        Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });


    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('withdraw')->group(function () {

        Route::controller('WithdrawalController')->name('data.')->group(function () {
            Route::get('pending/{user_id?}', 'pending')->name('pending');
            Route::get('approved/{user_id?}', 'approved')->name('approved');
            Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
            Route::get('all/{user_id?}', 'all')->name('all');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });


        // Withdraw Method
        Route::controller('WithdrawMethodController')->prefix('method')->name('method.')->group(function () {
            Route::get('/', 'methods')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
        Route::get('user/package-activation-histories', 'employeePackageActivationHistories')->name('employee.package-activation-histories');
        Route::get('referral-logs', 'referralLogs')->name('referral.logs');
        Route::get('advertisement/boost-history', 'advertisementBoostHistory')->name('advertisement.boost-history');
        Route::get('leader/bonus-history', 'leaderBonusHistory')->name('leader.bonus-history');
        Route::get('customer/bonus-history', 'customerBonusHistory')->name('customer.bonus-history');
        Route::get('top-leader-bonus-history', 'topLeaderBonusHistory')->name('top-leader-bonus-history');
        Route::get('company/expenses-saving-history', 'companyExpensesSavingHistory')->name('company.expenses-saving-history');
        Route::get('bonus/transaction-history', 'bonusTransactionHistory')->name('bonus.transaction-history');
        Route::get('product-purchase-commission-history', 'productPurchaseCommissionHistory')->name('product.purchase.commission.history');

    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });


    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::controller('GeneralSettingController')->group(function () {

        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');

        // social login
        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        Route::get('sitemap', 'sitemap')->name('setting.sitemap');
        Route::post('sitemap', 'sitemapSubmit');

        Route::get('robot', 'robot')->name('setting.robot');
        Route::post('robot', 'robotSubmit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');

        //In app purchase
        Route::get('in-app-purchase', 'inAppPurchase')->name('setting.app.purchase');
        Route::post('in-app-purchase', 'inAppPurchaseConfigure');
        Route::get('in-app-purchase/file/download', 'inAppPurchaseFileDownload')->name('setting.app.purchase.file.download');
    });


    //KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting', 'setting')->name('kyc.setting');
        Route::post('kyc-setting', 'settingUpdate');
    });

    Route::controller("CategoryController")->prefix('category')->name('category.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('featured/{id}', 'featured')->name('featured');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Manage SubCategory
    Route::controller('SubCategoryController')->prefix('sub-category')->name('subcategory.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('edit/{id}', 'edit')->name('edit');
    });

    Route::controller('FileTypeController')->prefix('filetype')->name('filetype.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('store/{id?}', 'store')->name('store');
    });

    //Manage banner images
    Route::controller('BannerImagesController')->prefix('banner')->name('banner.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('upload-image', 'store')->name('image');
        Route::post('delete/{id}', 'deleteImage')->name('delete');
    });

    // Manage jobs
    Route::controller('JobController')->prefix('job')->name('jobs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('pending', 'pending')->name('pending');
        Route::get('approved', 'approved')->name('approved');
        Route::get('complete', 'complete')->name('complete');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('view/{id}', 'view')->name('view');
        Route::post('approve/{id}', 'approve')->name('approve');
        Route::post('reject/{id}', 'reject')->name('reject');
        Route::get('details/{id}', 'details')->name('details');
    });

    // Manage advertisement
    Route::controller('AdvertisementController')->prefix('ads')->name('ads.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('pending', 'pending')->name('pending');
        Route::get('approved', 'approved')->name('approved');
        Route::get('complete', 'complete')->name('complete');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('expired', 'expired')->name('expired');
        Route::get('canceled', 'canceled')->name('canceled');
        Route::get('view/{id}', 'view')->name('view');
        Route::post('approve/{id}', 'approve')->name('approve');
        Route::post('reject/{id}', 'reject')->name('reject');

        Route::get('details/{id}', 'details')->name('details');

    });

    // Manage Advertisement Package
    Route::controller('AdvertisementPackageController')->prefix('advertisement-package')->name('advertisements.package.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('view/{id}', 'view')->name('view');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('status/{id}', 'status')->name('status');
        Route::put('product/{id}', 'update')->name('update');

        Route::get('/free', 'freeAd')->name('free');
        Route::post('/free/store', 'storeFreeAd')->name('freeStore');

    });

    // Manage Advertisement Boost Package
    Route::controller('AdvertisementBoostPackageController')->prefix('advertisement-boost-package')->name('advertisements.boost.package.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('view/{id}', 'view')->name('view');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('status/{id}', 'status')->name('status');
        Route::put('product/{id}', 'update')->name('update');
    });

    // Manage products
    Route::controller('ProductController')->prefix('product')->name('products.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('view/{id}', 'view')->name('view');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('status/{id}', 'status')->name('status');
        Route::put('product/{id}', 'update')->name('update');
        Route::get('/apply-watermark/{id}', 'applyWatermarkToExisting')->name('apply-watermark');
    });

    // Manage product categories
    Route::controller("ProductCategoryController")->prefix('product-category')->name('productCategories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Manage Product Sub Categories
    Route::controller('ProductSubCategoryController')->prefix('product-sub-category')->name('productSubCategory.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::get('related-category/{category_id}',  'getByCategory')->name('related.category.get');

    });

    // Manage orders
    Route::controller("OrderController")->prefix('order')->name('order.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('delivery/update/{order_id}', 'updateDeliveryStatus')->name('delivery.update');
        Route::get('view/{id}', 'view')->name('view');
        Route::get('processing', 'processingOrders')->name('processing');
        Route::get('completed', 'completedOrders')->name('completed');
        Route::get('canceled', 'canceledOrders')->name('canceled');
    });

    // Manage product promotion banners
    Route::controller('PromotionalBannerController')->prefix('product-promotion-banner')->name('productPromotionBanners.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
    });


    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });


    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update', 'systemUpdate')->name('update');
        Route::post('system-update', 'systemUpdateProcess')->name('update.process');
        Route::get('system-update/log', 'systemUpdateLog')->name('update.log');
    });


    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    Route::controller("CompanyExpensesController")->prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::put('store/{id?}', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
    });

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');

            Route::get('manage-seo/{id}', 'manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}', 'manageSeoStore');
        });
    });

    // Manage trainings
    Route::controller("TrainingController")->prefix('training')->name('training.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store/{id?}', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('status/{id}', 'updateStatus')->name('status');
        Route::post('delete/{id}', 'delete')->name('delete');

        Route::get('list', 'list')->name('list');
        Route::get('pending', 'pending')->name('pending');
        Route::get('complete', 'complete')->name('complete');
        Route::get('reject', 'reject')->name('reject');
    });

});
