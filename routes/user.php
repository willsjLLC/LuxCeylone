<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\RegisterController;

Route::get('/auth/google/callback', [RegisterController::class, 'callback'])->name('user.auth.google.callback');

Route::namespace('User\Auth')->middleware('guest')->name('user.')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
        // Add these two new routes
        Route::get('add-custom-category', 'showAddCustomCategoryForm')->name('addCustomCategory');
        Route::post('add-custom-category', 'storeCustomCategory')->name('storeCustomCategory');
        // Route to handle the callback from Google
        Route::get('/auth/google/redirect', 'redirect')->name('auth.google.redirect');
        // Route::post('check-referral-limit', 'checkReferralLimit')->name('checkReferralLimit');
    });

    Route::controller('RegisterRole')->group(function () {
        Route::get('registerBack', 'registerBack')->name('registerBack');
        Route::post('roleSelect', 'storeRole')->name('roleSelect');
        Route::post('categorySelect', 'storeCategories')->name('categorySelect');
        Route::post('categorySubSelect', 'storeSubCategories')->name('categorySubSelect');
    });

    // Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
    //     Route::get('reset', 'showLinkRequestForm')->name('request');
    //     Route::post('email', 'sendResetCodeEmail')->name('email');
    //     Route::get('code-verify', 'codeVerify')->name('code.verify');
    //     Route::post('verify-code', 'verifyCode')->name('verify.code');
    // });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::namespace('Auth')->group(function () {
                Route::controller('SwitchRole')->group(function () {
                    Route::get('switchrole', 'switchRole')->name('switchrole');
                    Route::get('switchRoleBack', 'backStep')->name('switchroleBack');
                    Route::post('switchRoleNext', 'nextStep')->name('switchroleNext');
                });
            });

            Route::controller('UserController')->group(function () {

                Route::get('home', 'userHome')->name('home.view');
                Route::get('wallet', 'wallet')->name('wallet');

                Route::post('wallet/transfer-to-lite', 'transferToLite')->name('wallet.transferToLite');

                Route::get('dashboard', function () {
                    return redirect()->route('user.home.view');
                })->name('home');

                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('customer-bonus-transactions', 'customerBonusTransactions')->name('customerBonusTransactions');
                Route::get('leader-bonus-transactions', 'leaderBonusTransactions')->name('leaderBonusTransactions');


                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

                //Favorites
                Route::get('favorite', 'getFavorites')->name('favorite');

                //Advertisement

                // Route::get('advertisement/myAds', 'showMyAdPage')->name('advertisement.myAds');
                // Route::get('advertisement/boost/{id?}', 'advertisementBoost')->name('advertisement.boost');
                // Route::get('advertisement/filter', 'filter')->name('setup.advertisement.filter');

                Route::get('leader/leasing-transfer', 'leaderLeasingTransfer')->name('leasing.transfer');
                Route::get('leader/petrol-transfer', 'leaderPetrolTransfer')->name('petrol.transfer');
                Route::get('leader/bonus-transfer', 'leaderBonusTransfer')->name('bonus.transfer');
                Route::get('festival/transfer', 'customerfestivalTransfer')->name('festival.transfer');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::post('delete-image', 'deleteImage')->name('delete.image');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
                Route::get('second-owner', 'secondOwner')->name('secondOwner');
                Route::post('second-owner-submit', 'secondOwnerSubmit')->name('secondOwner.submit.data');
                Route::POST('second-owner-delete', 'secondOwnerDeleteNIC')->name('secondOwner.delete.nic');
            });

            Route::controller('RankController')->prefix('rank')->name('rank.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('details/{id}', 'detail')->name('detail');
                 Route::post('/claim', 'claimRankReward')->name('claim');

                 
            
            });

            Route::controller('JobPostController')->prefix('job')->name('job.')->middleware('kyc')->group(function () {
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('history', 'history')->name('history');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('prove/{id}', 'prove')->name('prove');
                Route::post('status/{id}', 'status')->name('status');
                Route::get('details/{id}', 'details')->name('details');
                Route::get('finished', 'finished')->name('finished');
                Route::get('apply', 'apply')->name('apply');
                Route::post('/job/update-status', 'updateJobStatus')->name('update-status');
                Route::get('attachment/{id}', 'attachment')->name('attachment');
                Route::get('attachment/download/{id}', 'downloadAttachment')->name('download.attachment');
                Route::post('approve/{id}', 'approve')->name('approve');
                Route::post('reject/{id}', 'reject')->name('reject');
            });

            // Cart
            Route::controller('CartItemController')->prefix('cart')->name('cart.')->group(function () {
                Route::post('add/{product_id}', 'addToCart')->name('add');
                Route::get('items', 'index')->name('index');
                Route::post('update', 'updateCart')->name('update');
                Route::get('checkout', 'checkout')->name('checkout');
                Route::post('remove/{product_id}', 'removeCartItem')->name('item.remove');
                Route::get('count', 'getCartCount')->name('count'); // New endpoint


            });

            // Order
            Route::controller('OrderController')->prefix('order')->name('order.')->group(function () {
                Route::post('exist/checkout/{order_id}', 'existOrderCheckout')->name('exist.checkout');
                Route::post('delivery/update/{order_id}', 'updateDeliveryStatus')->name('delivery.update');
                Route::post('exist/cancel/{order_id}', 'cancelExistOrder')->name('exist.cancel');

                Route::post('delete-all', 'deleteAllHistory')->name('deleteAll');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware(['kyc', 'withdrawal.restriction'])->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });

            // Advertisement
            Route::controller('AdvertisementController')->prefix('advertisement')->name('advertisement.')->group(function () {
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                // Route::get('items', 'index')->name('index');
                // Route::post('update', 'updateCart')->name('update');
                // Route::get('checkout', 'checkout')->name('checkout');
                // Route::post('remove/{product_id}', 'removeCartItem')->name('item.remove');
                // Route::get('count', 'getCartCount')->name('count'); // New endpoint

                Route::get('list', 'getAdvertisement')->name('index');
                Route::get('details/{id}', 'advertisementDetails')->name('details');
                Route::get('category/{categoryName}', 'showCategoryAdvertisements')->name('category');
                Route::get('create', 'showAdSelectionPage')->name('selection');
                Route::get('create/category', 'showCategorySelection')->name('selectCategory');
                Route::get('subcategory/{id}', 'showSubCategoryAdvertisements')->name('subCategory');
                Route::get('create/form/{category}/{subCategory?}', 'showAdForm')->name('form');
                Route::get('get-cities/{district}', 'getCitiesByDistrict')->name('get-cities');
                Route::get('preview/{id}/{type}', 'advertisementPreview')->name('preview');
                Route::post('track-click', 'trackAdClick')->name('track-click');
                Route::get('filter', 'filterAdvertisements')->name('filter');
                Route::post('trackClick', 'trackAdvertisementClick')->name('trackClick');
                Route::get('cancel/{id}', 'cancelAdvertisement')->name('cancel');
                Route::get('complete/{id}', 'completeAdvertisement')->name('complete');

                Route::get('advertisement/myAds', 'showMyAdPage')->name('myAds');
                Route::get('advertisement/boost/{id?}', 'advertisementBoost')->name('boost');
                Route::get('advertisement/filter', 'filter')->name('myAds.filter');
            });

            // Product
            Route::controller('ProductController')->prefix('product')->name('product.')->group(function () {
                Route::get('list', 'getProduct')->name('index');
                Route::get('details/{id}', 'productDetails')->name('details');
                Route::get('category/{categoryName}', 'showCategoryProducts')->name('category');
                Route::get('products/category/{categoryName}/{subcategoryName}',  'showSubcategoryProducts')->name('subcategory');
            });
            //trining
            Route::controller('TrainingController')->group(function () {
                Route::get('training', 'trainingView')->name('training.index');
            });
        });

        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            Route::get('employee/package-active', 'employeePackageActiveForm')->name('employee.package.active');
            Route::post('employee/package-active', 'employeePackageActive')->name('employee.package.active');
            Route::post('customer/purchase-order/{order_id}', 'purchaseOrder')->name('customer.purchase.order');
            Route::get('user/boost-post/{ad_id}/{boost_option_id}', 'boostAd')->name('advertisement.boost');
            Route::post('user/training-ticket-buy/{training_id}', 'buyTicket')->name('training.ticket.buy');
        });

        // Referral
        Route::prefix('referral')->name('referral.')->controller('User\ReferralController')->group(function () {
            Route::any('/', 'referral')->name('index');
            Route::get('/load-more', 'loadMore')->name('load-more');
            Route::get('/hierarchy-data',  'getHierarchyData')->name('hierarchy.data');

        });
    });
});
