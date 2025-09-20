<?php

use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\User\ProductCategoryController; // Ensure this class exists in the specified namespace
use Illuminate\Http\Request;
use App\Models\User; 

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::prefix('api')->middleware('web')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::post('login', 'login'); 
    });
});

Route::get('/login-via-token', function (Request $request) {
    $user = User::where('remember_token', $request->remember_token)->first();

    if ($user) {
        Auth::login($user);
        return redirect()->route('user.home.view');
    }

    return redirect('/login')->with('error', 'Invalid login link.');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::controller('PaymentController')->group(function () {
    Route::get('app/deposit/confirm/{hash}', 'appDepositConfirm')->name('deposit.app.confirm');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('category/list', 'categories')->name('category.list');
    Route::get('category/job/{id}/{name}', 'categoryJobs')->name('category.jobs');
    Route::get('subcategory/{id}/{title}', 'subcategories')->name('subcategory.list');
    Route::get('subcategory/job/{id}/{name}', 'subcategoryJobs')->name('subcategory.jobs');

    Route::get('blogs', 'blogs')->name('blogs');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('advertisements/list', 'allJobs')->name('job.list');
    Route::get('job-details/{id}', 'jobDetails')->name('job.details');
    Route::get('job/sort', 'sortJob')->name('job.sort');
    Route::get('job/pagination', 'sortJob');
    Route::get('job/search', 'jobSearch')->name('job.search');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');

    Route::get('job/fetch', 'fetchJobsData')->name('job.fetch');
    Route::get('slideshow/fetchs', 'getBannerImages')->name('slideshow.fetch');

    // Route::get('product/list', 'getProduct')->name('product.index');
    // Route::get('product/details/{id}', 'productDetails')->name('product.details');
    // Route::get('product/category/{categoryName}', 'showCategoryProducts')->name('product.category');

    Route::get('ads/list', 'getAds')->name('ads.index');
    Route::get('preview/{id}', 'publicAdvertisementPreview')->name('ads.preview');
    Route::get('advertisement/filter', 'filterAdvertisements')->name('ads.filter');
    // Route::get('advertisement/details/{id}', 'advertisementDetails')->name('advertisement.details');
    // Route::get('advertisement/category/{categoryName}', 'showCategoryAdvertisements')->name('advertisement.category');
    // Route::get('advertisement/create', 'showAdSelectionPage')->name('advertisement.selection');
    // Route::get('advertisement/create/category', 'showCategorySelection')->name('advertisement.selectCategory');
    // Route::get('advertisement/subcategory/{id}', 'showSubCategoryAdvertisements')->name('advertisement.subCategory');
    // Route::get('advertisement/create/form/{category}/{subCategory?}', 'showAdForm')->name('advertisement.form');
    // Route::get('advertisement/get-cities/{district}', 'getCitiesByDistrict')->name('advertisement.get-cities');
    // Route::get('advertisement/preview/{id}', 'advertisementPreview')->name('advertisement.preview');
    // Route::post('advertisement/track-click', 'trackAdClick')->name('advertisement.track-click');
    // Route::post('advertisement/trackClick', 'trackAdvertisementClick')->name('advertisement.trackClick');
    // Route::get('advertisement/cancel/{id}', 'cancelAdvertisement')->name('advertisement.cancel');
    // Route::get('advertisement/complete/{id}', 'completeAdvertisement')->name('advertisement.complete');

    Route::get('password/reset', 'showLinkRequestForms')->name('site.password.request');
    Route::post('password/email', 'sendResetCodeEmail')->name('site.password.email');
    Route::get('password/code-verify', 'codeVerify')->name('site.password.code.verify');
    Route::post('password/verify-code', 'verifyCode')->name('site.password.verify.code');
    Route::post('password/reset', 'reset')->name('site.password.update');
    Route::get('password/reset/{token}', 'showResetForm')->name('site.password.reset');

    Route::get('launch/system', 'launchSystem')->name('system.launch');
});

// Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
//     Route::get('reset', 'showLinkRequestForm')->name('request');
//     Route::post('email', 'sendResetCodeEmail')->name('email');
//     Route::get('code-verify', 'codeVerify')->name('code.verify');
//     Route::post('verify-code', 'verifyCode')->name('verify.code');
// });

Route::controller('FavoriteController')->group(function () {
    Route::post('favorite/toggle', 'toggleFavorite')->name('toggle.favorite');
    Route::post('favorite/list', 'getUserFavorites')->name('get.favorite');
    Route::post('product/favorite', 'toggleFavoriteProducts')->name('toggle.favorite.product');
    Route::post('product/list', 'getFavoritesProducts')->name('get.favorite.product');
    Route::post('favorite/job/remove/{favorite_id}', 'removeUserFavorites')->name('job.favorite.remove');
    Route::post('favorite/product/remove/{favorite_id}', 'removeProductFavorites')->name('product.favorite.remove');
});

Route::controller('User\AdvertisementController')->prefix('advertisement')->name('advertisement.')->group(function () {
    Route::get('public/{id}', 'publicPreview')->name('public.preview');
    Route::get('get-cities/{district}', 'getCitiesByDistrict')->name('get-cities');
});
