<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdvertisementController;

use App\Http\Controllers\Api\TestApiController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Admin\ManageUsersController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::
        namespace('Api')->name('api.')->group(function () {

            Route::controller('AppController')->group(function () {
                Route::get('general-setting', 'generalSetting');
                Route::get('get-countries', 'getCountries');
                Route::get('language/{key}', 'getLanguage');
                Route::get('policies', 'policies');
                Route::get('faq', 'faq');
            });

            Route::namespace('Auth')->group(function () {

                Route::controller('RegisterController')->group(function () {
                    Route::post('register', 'register');

                    // Existing endpoints for Lite system calls
                    Route::post('check-user-exists', 'checkUserExists');
                    Route::post('check-switch-to-pro', 'checkSwitchToPro');

                    // New endpoints for Lite to Pro registration flow
                    Route::post('get-lite-user-for-pro', 'getLiteUserForProRegistration');
                    Route::post('register-from-lite', 'registerFromLite');

                    // Pro to Lite switch endpoint
                    Route::post('direct-lite-switch', 'directLiteSwitch');
                });

                Route::controller('ForgotPasswordController')->group(function () {
                    Route::post('password/email', 'sendResetCodeEmail');
                    Route::post('password/verify-code', 'verifyCode');
                    Route::post('password/reset', 'reset');
                });
            });

            Route::get('logout', 'Auth\LoginController@logout');

            Route::prefix('user')->name('user.')->group(function () {

                Route::post('password/reset', [UserController::class,'saveResetPassword'])->name('reset.password');

                Route::prefix('wallet')->name('wallet.')->group(function () {
                    Route::post('/add', [WalletController::class, 'add'])->name('add');
                });
            });

            Route::prefix('admin')->name('admin.')->group(function () {
                Route::prefix('users')->name('users.')->group(function () {
                    Route::post('/kyc-approve', [ManageUsersController::class, 'kycApprove'])->name('kyc.approve');
                });
            });

            Route::prefix('user')->name('user.')->group(function () {
                // Route::prefix('users')->name('users.')->group(function () {
                    // Route::post('/profile-setting', [ProfileController::class, 'submitProfile']);
                // });
            });

            // Route::post('check-transaction-id', 'PaymentController@checkTransactionId');

        });

    
Route::middleware('auth:sanctum')->group(function () {

    Route::post('user-data-submit', 'UserController@userDataSubmit');

    //authorization
    Route::middleware('registration.complete')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode');
        Route::post('verify-email', 'emailVerification');
        Route::post('verify-mobile', 'mobileVerification');
        Route::post('verify-g2fa', 'g2faVerification');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::middleware('registration.complete')->group(function () {
            Route::get('dashboard', function () {
                return auth()->user();
            });


            Route::controller('UserController')->group(function () {

                Route::post('profile-setting', 'submitProfile');
                Route::post('change-password', 'submitPassword');

                Route::get('user-info', 'userInfo');
                //KYC
                Route::get('kyc-form', 'kycForm');
                Route::post('kyc-submit', 'kycSubmit');

                //Report
                Route::any('deposit/history', 'depositHistory');
                Route::get('transactions', 'transactions');

                Route::post('add-device-token', 'addDeviceToken');
                Route::get('push-notifications', 'pushNotifications');
                Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                //2FA
                Route::get('twofactor', 'show2faForm');
                Route::post('twofactor/enable', 'create2fa');
                Route::post('twofactor/disable', 'disable2fa');

                Route::post('delete-account', 'deleteAccount');
            });

            // Withdraw
            Route::controller('WithdrawController')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('withdraw-method', 'withdrawMethod');
                    Route::post('withdraw-request', 'withdrawStore');
                    Route::post('withdraw-request/confirm', 'withdrawSubmit');
                });
                Route::get('withdraw/history', 'withdrawLog');
            });

            // Payment
            Route::controller('PaymentController')->group(function () {
                Route::get('deposit/methods', 'methods');
                Route::post('deposit/insert', 'depositInsert');
                Route::post('app/payment/confirm', 'appPaymentConfirm');
            });

            Route::controller('TicketController')->prefix('ticket')->group(function () {
                Route::get('/', 'supportTicket');
                Route::post('create', 'storeSupportTicket');
                Route::get('view/{ticket}', 'viewTicket');
                Route::post('reply/{id}', 'replyTicket');
                Route::post('close/{id}', 'closeTicket');
                Route::get('download/{attachment_id}', 'ticketDownload');
            });
        });
    });

    Route::get('logout', 'Auth\LoginController@logout');
});

Route::get('test-api', [TestApiController::class, 'textAPI']);


