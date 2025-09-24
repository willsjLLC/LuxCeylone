<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\JobPost;
use App\Models\SubCategory;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\BannerImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductPromotionBanner;
use App\Models\Advertisement;
use App\Models\AdvertisementBoostedHistory;
use App\Models\AdvertisementImage;
use App\Models\AdvertisementView;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ConnectException;

class SiteController extends Controller
{
    public function index()
    {

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        // Change this line to use where() instead of active()
        $categories = Category::where('status', Status::CATEGORIES_ENABLE)
            ->orderBy('created_at', 'desc')
            ->get();

        $keywords   = JobPost::approved()->groupBy('category_id')
            ->with('category')
            ->selectRaw('count(*) as count, category_id')
            ->orderBy('count', 'desc')
            ->take(4)
            ->get();

        $products = Product::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $pageTitle = 'Home';
        $sections = Page::where('tempname', activeTemplate())
            ->where('slug', '/')
            ->first();

        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ?
            getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) :
            null;


        return view('Template::home', compact('pageTitle', 'sections', 'seoContents', 'seoImage', 'categories', 'keywords', 'products'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        $user = auth()->user();
        $sections = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }


    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];
        $user = auth()->user();
        if($user) {
            return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
        } else {
            return back()->withNotify([['success','Successfully submitted and our agent will reach you ASAP!']]);
        }
        
    }

    public function policyPages($slug)
    {
        $policy = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blogDetails($slug)
    {
        $blog = Frontend::where('slug', $slug)->where('data_keys', 'blog.element')->firstOrFail();
        $blogs                             = Frontend::where('data_keys', 'blog.element')->where('slug', '!=', $slug)->orderBy('id', 'desc')->take(5)->get();
        $pageTitle = $blog->data_values->title;
        $seoContents = $blog->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blog_details', compact('blog', 'blogs', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function blogs()
    {
        $pageTitle = "Blogs";
        $blogs     = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->paginate(getPaginate(9));
        $sections  = Page::where('tempname', activeTemplate())->where('slug', 'blog')->first();
        return view('Template::blogs', compact('pageTitle', 'blogs', 'sections'));
    }

    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy()
    {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }

    public function allJobs()
    {
        $pageTitle  = "All Jobs";

        $categories = Category::with('subCategories')->active()->orderBy('name')->take(20)->get();

        return view('Template::job.index', compact('pageTitle', 'categories'));
    }

    public function jobDetails($id)
    {
        $pageTitle = 'Job Details';
        $job       = JobPost::approved()->where('id', $id)->firstOrFail();

        $seoContents['keywords']           = explode(' ', $job->title) ?? [];
        $seoContents['social_title']       = $job->title;
        $seoContents['description']        = strLimit(strip_tags($job->description), 150);
        $seoContents['social_description'] = strLimit(strip_tags($job->description), 150);
        $seoContents['image']              = getImage('assets/images/job/' . @$job->attachment, '550x400');
        $seoContents['image_size']         = '600x400';

        // dd($job);

        return view('Template::job.details', compact('pageTitle', 'job', 'seoContents'));
    }

    public function subcategories($id, $title)
    {
        $pageTitle = ucwords(str_replace('-', ' ', $title));
        $category  = Category::active()->withCount('subcategory as subcategory')->findOrFail($id);
        if (!$category->subcategory) {
            return to_route('category.jobs', ['name' => slug($category->name), 'id' => $category->id]);
        }

        $subCategories = SubCategory::active()->where('category_id', $id)->with('posts')->withCount([
            'posts as jobApprove' => function ($jobPost) {
                $jobPost->approved();
            },
        ])->paginate(getPaginate());

        return view('Template::subcategories', compact('pageTitle', 'subCategories'));
    }

    public function categories()
    {
        $pageTitle  = "All Categories";
        $categories = Category::active()->with('jobPosts')->orderBy('name')->paginate(getPaginate());
        return view('Template::categories', compact('pageTitle', 'categories'));
    }

    public function subcategoryJobs($id, $name)
    {

        $pageTitle  = ucwords(str_replace('-', ' ', $name));
        $jobs       = JobPost::approved()->where('subcategory_id', $id)->orderBy('id', 'desc')->paginate(getPaginate());
        $categories = Category::featured()->orderBy('name')->get();
        return view('Template::job.index', compact('pageTitle', 'jobs', 'categories'));
    }

    public function categoryJobs($id, $name)
    {
        $pageTitle  = ucwords(str_replace('-', ' ', $name));
        $jobs       = JobPost::approved()->where('category_id', $id)->orderBy('id', 'desc')->paginate(getPaginate());
        $categories = Category::featured()->orderBy('name')->get();
        return view('Template::job.index', compact('pageTitle', 'jobs', 'categories'));
    }

    public function sortJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|string|in:today,monthly,weekly',
            'sort' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }

        $date       = $request->get('date');
        $sort       = $request->get('sort');
        $categoryId = $request->get('category_id');

        $jobs = JobPost::query();

        if ($request->ajax()) {
            $jobs = $this->filterJob($jobs, $categoryId, $sort, $date);
        }
        $jobs = $jobs->approved()->paginate(getPaginate(9));
        return view('Template::partials.jobs', compact('jobs'));
    }

    public function jobSearch()
    {
        $category  = request()->category;
        $search    = request()->search;
        $pageTitle = "Search Result";
        $jobs      = JobPost::query();

        if ($search) {
            $jobs = $jobs->where('title', 'LIKE', '%' . $search . '%');
        }

        if ($category) {
            $jobs = $jobs->where('category_id', $category);
        }

        $jobs       = $jobs->approved()->paginate(getPaginate());
        $categories = Category::active()->get();
        return view('Template::job.index', compact('jobs', 'categories', 'pageTitle'));
    }

    protected function filterJob($jobs, $categoryId, $sort, $date)
    {

        if ($categoryId && !in_array('all', $categoryId)) {
            $jobs = $jobs->whereIn('category_id', $categoryId);
        }
        if ($sort) {
            $jobs = $jobs->orderBy('rate', $sort);
        }
        if ($date) {
            if ($date == 'today') {
                $jobs = $jobs->whereDate('created_at', Carbon::today()->format('Y-m-d H:i:s'));
            }
            if ($date == 'weekly') {
                $jobs = $jobs->whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')]);
            }
            if ($date == 'monthly') {
                $jobs = $jobs->whereDate('created_at', '>=', Carbon::now()->subDay(30)->format('Y-m-d H:i:s'));
            }
        }
        return $jobs;
    }

    function fetchJobsData(Request $request)
    {
        $query = JobPost::with('user');
        $userId = Auth::id();

        if ($request->has('categories') && !empty($request->categories)) {
            $subCategoryIds = explode(',', $request->categories);
            $query->whereIn('subcategory_id', $subCategoryIds);
        }

        if ($request->has('job_states') && !empty($request->job_states)) {

            $jobStates = explode(',', $request->job_states);
            $query->whereIn('status', $jobStates);
        } else {
            $query->whereIn('status', [Status::JOB_APPROVED, Status::JOB_COMPLETED, Status::JOB_ONGOING]);
        }

        $query->orderBy('id', 'desc');

        $jobs = $query->paginate(10, ['*'], 'page', $request->page);

        $posts = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'user_name' => $job->user->firstname . ' ' . $job->user->lastname,
                'title' => $job->title,
                'rate' => showAmount($job->rate),
                'details_url' => route('job.details', $job->id),
                'category_name' => $job->subCategory->name,
                'created_at' => showDateTime($job->created_at, 'F j, Y'),
                'description' => $job->description,
                'vacancy_available' => $job->vacancy_available,
                'status' => $job->status,
                'location' => '?',
                'attachment' => $job->attachment,
                'user_image' => asset('assets/templates/basic/images/user.png')
            ];
            // $seoContents['image']              = getImage('assets/images/job/' . @$job->attachment, '550x400');
        });

        return response()->json([
            'posts' => $posts,
            'userId' => $userId,
            'has_more_posts' => $jobs->hasMorePages(),
        ]);
    }

    // Fetch images for banner
    function getBannerImages()
    {
        try {
            $bannerImages = BannerImage::all();
            $imagePaths = $bannerImages->map(function ($image) {
                return getImage(getFilePath('bannerImage') . '/' . $image->images);
            });

            return response()->json([
                'success' => true,
                'images' => $imagePaths,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    function getUserBalance(Request $request)
    {
        $id = $request->input('user_id');

        try {
            $user = User::select('id', 'balance')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'balance' => $user->balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving user'
            ], 500);
        }
    }

    public function activatePackage(Request $request)
    {
        // After successful activation, get user balance
        $user = auth()->user();
        $user->refresh(); // Make sure we have the latest user data

        // Store in session for the next page load
        session()->flash('package_activated', true);
        session()->flash('user_balance', $user->balance);

        return redirect()->back()->with('success', 'Package activated successfully!');
    }

    function getUserBalanceAfterActivation(Request $request)
    {
        try {
            $user = auth()->user();
            // Activate package logic here

            return response()->json([
                'status' => 'success',
                'message' => 'Package activated successfully!',
                'balance' => $user->balance,
                'currency' => gs('cur_sym')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error activating package: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function getProduct(Request $request)
    // {
    //     $pageTitle = 'All Products';
    //     $search = $request->search;

    //     $query = ProductCategory::with(['products' => function ($query) use ($search) {
    //         $query->where('status', 'active');
    //         if ($search) {
    //             $query->where('name', 'like', "%{$search}%");
    //         }
    //     }])->where('status', 'active');

    //     if ($search) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%")
    //                 ->orWhereHas('products', function ($q) use ($search) {
    //                     $q->where('status', 'active')->where('name', 'like', "%{$search}%");
    //                 });
    //         });
    //     }

    //     // Use whereHas to only include categories that have at least one active product
    //     $query->whereHas('products', function ($q) {
    //         $q->where('status', 'active');
    //     });

    //     // Order categories by creation date (newest first)
    //     $query->orderBy('created_at', 'desc');

    //     $categories = $query->get();

    //     // Get all categories for the horizontal category display
    //     $allCategories = ProductCategory::where('status', 'active')
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     $promotion_banner = ProductPromotionBanner::first();

    //     return view('Template::product.index', compact('pageTitle', 'categories', 'allCategories', 'search', 'promotion_banner'));
    // }

    // public function showCategoryProducts($categoryName)
    // {
    //     $category = ProductCategory::where('name', $categoryName)->firstOrFail();
    //     $products = Product::where('category_id', $category->id)
    //         ->where('status', 'active')
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('Template::product.category', [
    //         'pageTitle' => "Products in {$category->name}",
    //         'products' => $products
    //     ]);
    // }

    // public function productDetails($id)
    // {
    //     $product = Product::findOrFail($id);
    //     $smilerProducts = Product::where('id', '!=',  $product->id)->where('category_id', $product->category_id)->latest()->take(5)->get();
    //     $pageTitle = $product->name;
    //     return view('Template::product.details', compact('pageTitle', 'product', 'smilerProducts'));
    // }

    public function getAds(Request $request)
    {
        $pageTitle = 'All Advertisements';
        $search = $request->search;

        // Fetch advertisements with relationships
        $query = Advertisement::with(['district', 'city', 'category', 'subCategory'])
            ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subCategory', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Order advertisements by posted date (newest first)
        $query->orderBy('posted_date', 'desc');

        $advertisements = $query->get();

        // Get all categories for the horizontal category display
        $allCategories = Category::where('status', Status::CATEGORIES_ENABLE)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories with subcategories for the category selector
        $categories = Category::with(['subCategories' => function ($query) {
            $query->where('status', Status::SUB_CATEGORIES_ENABLE)
                ->orderBy('created_at', 'desc');
        }])
            ->where('status', Status::CATEGORIES_ENABLE)
            ->whereHas('subCategories', function ($q) {
                $q->where('status', Status::SUB_CATEGORIES_ENABLE);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    

        $promotion_banner = ProductPromotionBanner::first();


        return view('Template::ads.index', compact('pageTitle', 'advertisements', 'categories', 'allCategories', 'search', 'promotion_banner'));
    }

    // public ads preview
    public function publicAdvertisementPreview($id)
    {
        $advertisement = Advertisement::with([
            'district',
            'city',
            'category',
            'subCategory',
            'user'
        ])
            ->where('id', $id)
            ->firstOrFail();

        // Get advertisement images
        $advertisementImages = AdvertisementImage::where('advertisement_id', $id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();

        // Set default image if no images are found
        if ($advertisementImages->isEmpty()) {
            $advertisementImages = collect([
                (object)[
                    'image' => $advertisement->file_name ?? 'default-ad-image.jpg',
                    'is_primary' => 1
                ]
            ]);
        }

        // Track view and potentially increment impressions
        $this->trackAdvertisementView($advertisement);

        // Get related ads from the same subcategory with boosting priority
        $relatedAds = Advertisement::with([
            'latestBoostHistory' => function ($q) {
                $q->where('status', 1)->orderByDesc('id')->limit(1);
            },
            'latestBoostHistory.boostPackage',
        ])
            ->where('subcategory_id', $advertisement->subcategory_id)
            ->where('id', '!=', $advertisement->id)
            ->where('status', Status::AD_APPROVED)
            ->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    ) THEN (
                        SELECT
                            CASE abp.type
                                WHEN 1 THEN 1 -- Top
                                WHEN 2 THEN 2 -- Featured
                                WHEN 3 THEN 3 -- Urgent
                                ELSE 99
                            END
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    )
                    ELSE 99
                END ASC,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    ) THEN (
                        SELECT abp.priority_level
                        FROM advertisement_boosted_histories abh
                        JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                        WHERE abh.advertisement_id = advertisements.id
                        AND abh.status = 1
                        ORDER BY abh.id DESC
                        LIMIT 1
                    )
                    ELSE 99
                END ASC,
                created_at DESC
            ")
            ->take(4)
            ->get();

        $pageTitle = $advertisement->title;

        return view('Template::ads.preview', compact('pageTitle', 'advertisement', 'relatedAds', 'advertisementImages'));
    }

    private function trackAdvertisementView(Advertisement $advertisement)
    {
        $viewerIp = request()->ip();

        // Check if this viewer (by user ID or IP) has already viewed this ad
        $view = AdvertisementView::where('advertisement_id', $advertisement->id)
            ->where(function ($query) use ($viewerIp) {
                if ($viewerIp) {
                    $query->where('viewer_ip', $viewerIp);
                }
            })
            ->first();

        if ($view) {
            // Update the viewed_at timestamp
            $view->update([
                'viewed_at' => Carbon::now(),
            ]);
        } else {
            // Create a new view record and increment the impression count
            AdvertisementView::create([
                'advertisement_id' => $advertisement->id,
                'viewer_ip' => $viewerIp,
                'viewed_at' => Carbon::now()
            ]);

            // Increment impression count
            $advertisement->increment('impressions');

            $boostedAdvertisementImpression = AdvertisementBoostedHistory::where('status', Status::BOOST_STARTED)
                ->where('advertisement_id', $advertisement->id)
                ->first();

            if ($boostedAdvertisementImpression) {
                if ($boostedAdvertisementImpression->impressions == null) {
                    $boostedAdvertisementImpression->impressions = 1;
                    $boostedAdvertisementImpression->save();
                } else {
                    $boostedAdvertisementImpression->increment('impressions');
                }

                if ($boostedAdvertisementImpression->clicks == null) {
                    $boostedAdvertisementImpression->clicks = 1;
                    $boostedAdvertisementImpression->save();
                } else {
                    $boostedAdvertisementImpression->increment('clicks');
                }
            }
        }
    }

    // boosting
    // public function filterAdvertisements(Request $request)
    // {
    //     $search = $request->search;
    //     $subcategoryIds = $request->subcategory_ids ? explode(',', $request->subcategory_ids) : [];
    //     $page = $request->page ?? 1;
    //     $perPage = 20000000;

    //     $query = Advertisement::with([
    //         'district',
    //         'city',
    //         'category',
    //         'subCategory',
    //         'latestBoostHistory' => function ($q) {
    //             $q->where('status', 1)->orderByDesc('id')->limit(1);
    //         },
    //         'latestBoostHistory.boostPackage',
    //     ])
    //         ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);

    //     if (!empty($subcategoryIds)) {
    //         $query->whereIn('subcategory_id', $subcategoryIds);
    //     }

    //     if (!empty($search)) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('title', 'like', "%{$search}%")
    //                 ->orWhere('description', 'like', "%{$search}%")
    //                 ->orWhereHas('category', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('subCategory', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('city', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('district', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 });
    //         });
    //     }

    //     // advertisements based on boost type, priority, and then by posted date
    //     $query->orderByRaw("
    //         CASE
    //             WHEN EXISTS (
    //                 SELECT 1
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                   AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             ) THEN (
    //                 SELECT
    //                     CASE abp.type
    //                         WHEN 1 THEN 1 -- Top
    //                         WHEN 2 THEN 2 -- Featured
    //                         WHEN 3 THEN 3 -- Urgent
    //                         ELSE 99
    //                     END
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                   AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             )
    //             ELSE 99
    //         END ASC,
    //         CASE
    //             WHEN EXISTS (
    //                 SELECT 1
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                   AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             ) THEN (
    //                 SELECT abp.priority_level
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                   AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             )
    //             ELSE 99
    //         END ASC,
    //         posted_date DESC
    //     ");

    //     $totalCount = $query->count();

    //     $advertisements = $query->skip(($page - 1) * $perPage)
    //         ->take($perPage + 1)
    //         ->get();

    //     $hasMore = count($advertisements) > $perPage;

    //     if ($hasMore) {
    //         $advertisements = $advertisements->take($perPage);
    //     }

    //     $formattedAds = $advertisements->map(function ($ad) {
    //         $boostPackage = null;
    //         if ($ad->latestBoostHistory && $ad->latestBoostHistory->boostPackage) {
    //             $boostPackage = [
    //                 'id' => $ad->latestBoostHistory->boostPackage->id,
    //                 'name' => $ad->latestBoostHistory->boostPackage->name,
    //                 'price' => $ad->latestBoostHistory->boostPackage->price,
    //                 'duration' => $ad->latestBoostHistory->boostPackage->duration,
    //                 'priority_level' => $ad->latestBoostHistory->boostPackage->priority_level,
    //                 'type' => $ad->latestBoostHistory->boostPackage->type,
    //                 'highlighted_color' => $ad->latestBoostHistory->boostPackage->highlighted_color ?? null,
    //             ];
    //         }

    //         return [
    //             'id' => $ad->id,
    //             'title' => $ad->title,
    //             'price_formatted' => number_format($ad->price, 2),
    //             'city_name' => $ad->city->name ?? 'Unknown',
    //             'district_name' => $ad->district->name ?? 'Unknown',
    //             'posted_date' => $ad->posted_date->diffForHumans(),
    //             'image_url' => $ad->file_name
    //                 ? asset('assets/admin/images/advertisementImages/' . $ad->file_name)
    //                 : asset('assets/images/default-ad-image.jpg'),
    //             'status' => $ad->status,
    //             'boost_package' => $boostPackage,
    //             'highlighted_color' => $boostPackage ? $boostPackage['highlighted_color'] : null,
    //         ];
    //     });

    //     return response()->json([
    //         'advertisements' => $formattedAds,
    //         'hasMore' => $hasMore,
    //         'totalCount' => $totalCount,
    //         'currentPage' => $page
    //     ]);
    // }

     public function filterAdvertisements(Request $request)
    {
        $search = $request->search;
        $subcategoryIds = $request->subcategory_ids ? explode(',', $request->subcategory_ids) : [];
        $page = $request->page ?? 1;
        $perPage = 20000000;

        $query = Advertisement::with([
            'district',
            'city',
            'category',
            'subCategory',
            'latestBoostHistory' => function ($q) {
                $q->where('status', 1)->orderByDesc('id')->limit(1);
            },
            'latestBoostHistory.boostPackage',
        ])
            ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);

        if (!empty($subcategoryIds)) {
            $query->whereIn('subcategory_id', $subcategoryIds);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subCategory', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('city', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('district', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // advertisements based on boost type, priority, and then by posted date
        $query->orderByRaw("
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                ) THEN (
                    SELECT
                        CASE abp.type
                            WHEN 1 THEN 1 -- Top
                            WHEN 2 THEN 2 -- Featured
                            WHEN 3 THEN 3 -- Urgent
                            ELSE 99
                        END
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                )
                ELSE 99
            END ASC,
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                ) THEN (
                    SELECT abp.priority_level
                    FROM advertisement_boosted_histories abh
                    JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
                    WHERE abh.advertisement_id = advertisements.id
                      AND abh.status = 1
                    ORDER BY abh.id DESC
                    LIMIT 1
                )
                ELSE 99
            END ASC,
            posted_date DESC
        ");

        $totalCount = $query->count();

        $advertisements = $query->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();

        $hasMore = count($advertisements) > $perPage;

        if ($hasMore) {
            $advertisements = $advertisements->take($perPage);
        }

        $formattedAds = $advertisements->map(function ($ad) {
            $boostPackage = null;
            if ($ad->latestBoostHistory && $ad->latestBoostHistory->boostPackage) {
                $boostPackage = [
                    'id' => $ad->latestBoostHistory->boostPackage->id,
                    'name' => $ad->latestBoostHistory->boostPackage->name,
                    'price' => $ad->latestBoostHistory->boostPackage->price,
                    'duration' => $ad->latestBoostHistory->boostPackage->duration,
                    'priority_level' => $ad->latestBoostHistory->boostPackage->priority_level,
                    'type' => $ad->latestBoostHistory->boostPackage->type,
                    'highlighted_color' => $ad->latestBoostHistory->boostPackage->highlighted_color ?? null,
                ];
            }

            return [
                'id' => $ad->id,
                'title' => $ad->title,
                'price_formatted' => number_format($ad->price, 2),
                'city_name' => $ad->city->name ?? 'Unknown',
                'district_name' => $ad->district->name ?? 'Unknown',
                'posted_date' => $ad->posted_date->diffForHumans(),
                'image_url' => $ad->file_name
                    ? asset('assets/admin/images/advertisementImages/' . $ad->file_name)
                    : asset('assets/images/default-ad-image.jpg'),
                'status' => $ad->status,
                'boost_package' => $boostPackage,
                'highlighted_color' => $boostPackage ? $boostPackage['highlighted_color'] : null,
            ];
        });

        return response()->json([
            'advertisements' => $formattedAds,
            'hasMore' => $hasMore,
            'totalCount' => $totalCount,
            'currentPage' => $page
        ]);
    }

    // public function filterAdvertisements(Request $request)
    // {
    //     $search = $request->search;
    //     $subcategoryIds = $request->subcategory_ids ? explode(',', $request->subcategory_ids) : [];
    //     $page = $request->page ?? 1;
    //     $perPage = 20000000;

    //     // Get local advertisements with existing filtering
    //     $query = Advertisement::with([
    //         'district',
    //         'city',
    //         'category',
    //         'subCategory',
    //         'latestBoostHistory' => function ($q) {
    //             $q->where('status', 1)->orderByDesc('id')->limit(1);
    //         },
    //         'latestBoostHistory.boostPackage',
    //     ])
    //         ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED]);
    //     if (!empty($subcategoryIds)) {
    //         $query->whereIn('subcategory_id', $subcategoryIds);
    //     }

    //     if (!empty($search)) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('title', 'like', "%{$search}%")
    //                 ->orWhere('description', 'like', "%{$search}%")
    //                 ->orWhereHas('category', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('subCategory', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('city', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('district', function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%");
    //                 });
    //         });
    //     }

    //     // Order local advertisements
    //     $query->orderByRaw("
    //         CASE
    //             WHEN EXISTS (
    //                 SELECT 1
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                 AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             ) THEN (
    //                 SELECT
    //                     CASE abp.type
    //                         WHEN 1 THEN 1 -- Top
    //                         WHEN 2 THEN 2 -- Featured
    //                         WHEN 3 THEN 3 -- Urgent
    //                         ELSE 99
    //                     END
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                 AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             )
    //             ELSE 99
    //         END ASC,
    //         CASE
    //             WHEN EXISTS (
    //                 SELECT 1
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                 AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             ) THEN (
    //                 SELECT abp.priority_level
    //                 FROM advertisement_boosted_histories abh
    //                 JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                 WHERE abh.advertisement_id = advertisements.id
    //                 AND abh.status = 1
    //                 ORDER BY abh.id DESC
    //                 LIMIT 1
    //             )
    //             ELSE 99
    //         END ASC,
    //         posted_date DESC
    //     ");

    //     $localAdvertisements = $query->get();

    //     // Format local advertisements
    //     $formattedLocalAds = $localAdvertisements->map(function ($ad) {
    //         $boostPackage = null;
    //         if ($ad->latestBoostHistory && $ad->latestBoostHistory->boostPackage) {
    //             $boostPackage = [
    //                 'id' => $ad->latestBoostHistory->boostPackage->id,
    //                 'name' => $ad->latestBoostHistory->boostPackage->name,
    //                 'price' => $ad->latestBoostHistory->boostPackage->price,
    //                 'duration' => $ad->latestBoostHistory->boostPackage->duration,
    //                 'priority_level' => $ad->latestBoostHistory->boostPackage->priority_level,
    //                 'type' => $ad->latestBoostHistory->boostPackage->type,
    //                 'highlighted_color' => $ad->latestBoostHistory->boostPackage->highlighted_color ?? null,
    //             ];
    //         }

    //         return [
    //             'id' => $ad->id,
    //             'title' => $ad->title,
    //             'price_formatted' => number_format($ad->price, 2),
    //             'city_name' => $ad->city->name ?? 'Unknown',
    //             'district_name' => $ad->district->name ?? 'Unknown',
    //             'posted_date' => $ad->posted_date->diffForHumans(),
    //             'image_url' => $ad->file_name
    //                 ? asset('assets/admin/images/advertisementImages/' . $ad->file_name)
    //                 : asset('assets/images/default-ad-image.jpg'),
    //             'status' => $ad->status,
    //             'account_type' => $ad->account_type,
    //             'boost_package' => $boostPackage,
    //             'highlighted_color' => $boostPackage ? $boostPackage['highlighted_color'] : null,
    //             'source' => 'local', // Add source identifier
    //         ];
    //     });

    //     // Format external advertisements and apply filtering
    //     $formattedExternalAds = collect($externalAds)->map(function ($ad) {
    //         // Map external ad structure to match local format
    //         return [
    //             'id' => ($ad['id'] ?? uniqid()),
    //             'title' => $ad['title'] ?? 'Unknown Title',
    //             'price_formatted' => isset($ad['price']) ? number_format($ad['price'], 2) : '0.00',
    //             'city_name' => $ad['city']['name'] ?? 'Unknown',
    //             // 'city_name' => $ad->city->name ?? 'Unknown',
    //             'district_name' => $ad['district']['name'] ?? 'Unknown',
    //             'posted_date' => isset($ad['posted_date']) ? 
    //                 \Carbon\Carbon::parse($ad['posted_date'])->diffForHumans() : 'Unknown',
    //             'image_url' => $ad['file_name'],
    //             'status' => $ad['status'] ?? 'active',
    //             'boost_package' => null, // External ads don't have boost packages
    //             'highlighted_color' => null,
    //             'source' => 'external', // Add source identifier
    //             // Additional fields from external API
    //             'description' => $ad['description'] ?? '',
    //             'category_name' => $ad['category']['name'] ?? '',
    //             'subcategory_name' => $ad['subcategory']['name'] ?? '',
    //             'subcategory_id' => $ad['subcategory_id'] ?? null,
    //             'account_type' => $ad['account_type']
    //         ];
    //     })->filter(function ($ad) use ($search, $subcategoryIds) {
    //         // Apply search filter to external ads
    //         $matchesSearch = true;
    //         if (!empty($search)) {
    //             $searchTerm = strtolower($search);
    //             $matchesSearch = (
    //                 str_contains(strtolower($ad['title']), $searchTerm) ||
    //                 str_contains(strtolower($ad['description']), $searchTerm) ||
    //                 str_contains(strtolower($ad['category_name']), $searchTerm) ||
    //                 str_contains(strtolower($ad['subcategory_name']), $searchTerm) ||
    //                 str_contains(strtolower($ad['city_name']), $searchTerm) ||
    //                 str_contains(strtolower($ad['district_name']), $searchTerm)
    //             );
    //         }

    //         // Apply subcategory filter to external ads
    //         $matchesSubcategory = true;
    //         if (!empty($subcategoryIds) && !empty($ad['subcategory_id'])) {
    //             $matchesSubcategory = in_array($ad['subcategory_id'], $subcategoryIds);
    //         }

    //         return $matchesSearch && $matchesSubcategory;
    //     });

    //     // Merge local and external advertisements
    //     $allAdvertisements = $formattedLocalAds->concat($formattedExternalAds);

    //     // Sort the merged collection (external ads will be treated as non-boosted)
    //     $sortedAdvertisements = $allAdvertisements->sortBy([
    //         // First by boost priority (local ads with boost packages come first)
    //         function ($ad) {
    //             if ($ad['source'] === 'local' && $ad['boost_package']) {
    //                 $type = $ad['boost_package']['type'];
    //                 return match($type) {
    //                     1 => 1, // Top
    //                     2 => 2, // Featured
    //                     3 => 3, // Urgent
    //                     default => 99
    //                 };
    //             }
    //             return 99; // Non-boosted ads (including all external ads)
    //         },
    //         // Then by priority level within boost type
    //         function ($ad) {
    //             if ($ad['source'] === 'local' && $ad['boost_package']) {
    //                 return $ad['boost_package']['priority_level'];
    //             }
    //             return 99;
    //         }
    //     ])->values();

    //     // Apply pagination
    //     $totalCount = $sortedAdvertisements->count();
    //     $paginatedAds = $sortedAdvertisements->skip(($page - 1) * $perPage)->take($perPage + 1);
        
    //     $hasMore = $paginatedAds->count() > $perPage;
    //     if ($hasMore) {
    //         $paginatedAds = $paginatedAds->take($perPage);
    //     }
    //     return response()->json([
    //         'advertisements' => $paginatedAds->values(),
    //         'hasMore' => $hasMore,
    //         'totalCount' => $totalCount,
    //         'currentPage' => $page,
    //         'localCount' => $formattedLocalAds->count(),
    //         'externalCount' => $formattedExternalAds,
    //     ]);
    // }

    // public function showCategoryAdvertisements($categoryName)
    // {
    //     // Find the category by name
    //     $category = Category::where('name', $categoryName)
    //         ->where('status', Status::CATEGORIES_ENABLE)
    //         ->firstOrFail();

    //     // Get subcategories
    //     $subCategories = SubCategory::where('category_id', $category->id)
    //         ->where('status', Status::SUB_CATEGORIES_ENABLE)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     // Get advertisements for this category
    //     $advertisements = Advertisement::with(['district', 'city', 'category', 'subCategory'])
    //         ->where('category_id', $category->id)
    //         ->where('status', Status::AD_APPROVED)
    //         ->orderBy('posted_date', 'desc')
    //         ->get();

    //     $pageTitle = $category->name . ' - Advertisements';

    //     return view('Template::advertisement.category', compact('pageTitle', 'category', 'subCategories', 'advertisements'));
    // }

    // public function advertisementDetails($id)
    // {
    //     $subCategory = SubCategory::findOrFail($id);
    //     $pageTitle = $subCategory->name;
    //     return view('Template::advertisement.details', compact('pageTitle', 'subCategory'));
    // }

    // public function showAdSelectionPage()
    // {
    //     $pageTitle = 'Post an Advertisement';
    //     return view('Template::advertisement.selection', compact('pageTitle'));
    // }

    // public function showCategorySelection()
    // {
    //     $pageTitle = 'Select a Category';

    //     // Changed from ProductCategory to Category
    //     $categories = Category::with(['subCategories' => function ($query) {
    //         $query->where('status', Status::SUB_CATEGORIES_ENABLE)
    //             ->orderBy('created_at', 'desc');
    //     }])
    //         ->where('status', Status::CATEGORIES_ENABLE)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('Template::advertisement.selectCategory', compact('pageTitle', 'categories'));
    // }

    // public function showAdForm($category, $subCategory = null)
    // {
    //     $categoryName = str_replace('-', ' ', ucfirst($category));
    //     $subCategoryName = $subCategory ? str_replace('-', ' ', ucfirst($subCategory)) : null;

    //     // Find the category
    //     $categoryObj = Category::where('name', 'like', $categoryName)
    //         ->where('status', Status::CATEGORIES_ENABLE)
    //         ->firstOrFail();

    //     // Find the subcategory if provided
    //     $subCategoryObj = null;
    //     if ($subCategoryName) {
    //         $subCategoryObj = SubCategory::where('name', 'like', $subCategoryName)
    //             ->where('category_id', $categoryObj->id)
    //             ->where('status', Status::SUB_CATEGORIES_ENABLE)
    //             ->first();
    //     }

    //     $pageTitle = $subCategoryName ? "Post ad for $subCategoryName" : "Post ad in $categoryName";

    //     // Get districts from the database
    //     $districts = DB::table('districts')->orderBy('name')->get();


    //     // Get cities for the first district by default
    //     $defaultDistrictId = DB::table('districts')->first()->id;
    //     $cities = DB::table('cities')->where('district_id', $defaultDistrictId)->orderBy('name')->get();

    //     return view('Template::advertisement.form', compact('pageTitle', 'categoryObj', 'subCategoryObj', 'districts', 'cities'));
    // }

    // public function getCitiesByDistrict($districtId)
    // {
    //     $district = DB::table('districts')->find($districtId);

    //     if (!$district) {
    //         return response()->json(['error' => 'District not found'], 404);
    //     }

    //     $cities = DB::table('cities')->where('district_id', $district->id)->orderBy('name')->get();

    //     return response()->json($cities);
    // }

    // public function showSubCategoryAdvertisements($id)
    // {
    //     $subCategory = SubCategory::with('category')->findOrFail($id);
    //     $pageTitle = $subCategory->name . ' Advertisements';

    //     // Get advertisements for this subcategory
    //     $advertisements = Advertisement::with(['district', 'city', 'category', 'subCategory'])
    //         ->where('subcategory_id', $id)
    //         ->where('status', Status::AD_APPROVED)
    //         ->orderBy('posted_date', 'desc')
    //         ->get();

    //     return view('Template::advertisement.subCategory', compact('pageTitle', 'subCategory', 'advertisements'));
    // }

    // public function trackAdClick(Request $request)
    // {
    //     $advertisement = Advertisement::find($request->ad_id);
    //     if ($advertisement) {
    //         $advertisement->increment('clicks');
    //         return response()->json(['success' => true]);
    //     }
    //     return response()->json(['success' => false], 404);
    // }

    // public function trackAdvertisementClick(Request $request)
    // {
    //     $adId = $request->input('ad_id');
    //     $advertisement = Advertisement::findOrFail($adId);

    //     // Increment click count
    //     $advertisement->increment('clicks');

    //     return response()->json(['status' => 'success']);
    // }

    // public function advertisementPreview($id)
    // {
    //     $advertisement = Advertisement::with([
    //         'district',
    //         'city',
    //         'category',
    //         'subCategory',
    //         'user'
    //     ])
    //         ->where('id', $id)
    //         ->firstOrFail();

    //     // Get advertisement images
    //     $advertisementImages = AdvertisementImage::where('advertisement_id', $id)
    //         ->orderBy('is_primary', 'desc')
    //         ->orderBy('sort_order', 'asc')
    //         ->get();

    //     // Set default image if no images are found
    //     if ($advertisementImages->isEmpty()) {
    //         $advertisementImages = collect([
    //             (object)[
    //                 'image' => $advertisement->file_name ?? 'default-ad-image.jpg',
    //                 'is_primary' => 1
    //             ]
    //         ]);
    //     }

    //     // Track view and potentially increment impressions
    //     $this->trackAdvertisementView($advertisement);

    //     // Get related ads from the same subcategory with boosting priority
    //     $relatedAds = Advertisement::with([
    //         'latestBoostHistory' => function ($q) {
    //             $q->where('status', 1)->orderByDesc('id')->limit(1);
    //         },
    //         'latestBoostHistory.boostPackage',
    //     ])
    //         ->where('subcategory_id', $advertisement->subcategory_id)
    //         ->where('id', '!=', $advertisement->id)
    //         ->where('status', Status::AD_APPROVED)
    //         ->orderByRaw("
    //             CASE
    //                 WHEN EXISTS (
    //                     SELECT 1
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 ) THEN (
    //                     SELECT
    //                         CASE abp.type
    //                             WHEN 1 THEN 1 -- Top
    //                             WHEN 2 THEN 2 -- Featured
    //                             WHEN 3 THEN 3 -- Urgent
    //                             ELSE 99
    //                         END
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 )
    //                 ELSE 99
    //             END ASC,
    //             CASE
    //                 WHEN EXISTS (
    //                     SELECT 1
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 ) THEN (
    //                     SELECT abp.priority_level
    //                     FROM advertisement_boosted_histories abh
    //                     JOIN advertisement_boost_packages abp ON abh.boost_package_id = abp.id
    //                     WHERE abh.advertisement_id = advertisements.id
    //                     AND abh.status = 1
    //                     ORDER BY abh.id DESC
    //                     LIMIT 1
    //                 )
    //                 ELSE 99
    //             END ASC,
    //             created_at DESC
    //         ")
    //         ->take(4)
    //         ->get();

    //     $pageTitle = $advertisement->title;

    //     return view('Template::advertisement.preview', compact('pageTitle', 'advertisement', 'relatedAds', 'advertisementImages'));
    // }

    // private function trackAdvertisementView(Advertisement $advertisement)
    // {
    //     $currentUserId = Auth::id();
    //     $viewerIp = request()->ip();

    //     // Don't count impressions if the viewer is the owner of the advertisement
    //     if ($currentUserId && $currentUserId == $advertisement->user_id) {
    //         return;
    //     }

    //     // Check if this viewer (by user ID or IP) has already viewed this ad
    //     $view = AdvertisementView::where('advertisement_id', $advertisement->id)
    //         ->where(function ($query) use ($currentUserId, $viewerIp) {
    //             if ($currentUserId) {
    //                 $query->where('user_id', $currentUserId);
    //             } else {
    //                 $query->where('viewer_ip', $viewerIp);
    //             }
    //         })
    //         ->first();

    //     if ($view) {
    //         // Update the viewed_at timestamp
    //         $view->update([
    //             'viewed_at' => Carbon::now(),
    //         ]);
    //     } else {
    //         // Create a new view record and increment the impression count
    //         AdvertisementView::create([
    //             'advertisement_id' => $advertisement->id,
    //             'user_id' => $currentUserId,
    //             'viewer_ip' => $viewerIp,
    //             'viewed_at' => Carbon::now()
    //         ]);

    //         // Increment impression count
    //         $advertisement->increment('impressions');

    //         $boostedAdvertisementImpression = AdvertisementBoostedHistory::where('status', Status::BOOST_STARTED)->where('advertisement_id', $advertisement->id)->first();

    //         if ($boostedAdvertisementImpression->impressions == null) {
    //             $boostedAdvertisementImpression->impressions = 1;
    //             $boostedAdvertisementImpression->save();
    //         } else {
    //             $boostedAdvertisementImpression->increment('impressions');
    //         }
    //         if ($boostedAdvertisementImpression->clicks == null) {
    //             $boostedAdvertisementImpression->clicks = 1;
    //             $boostedAdvertisementImpression->save();
    //         } else {
    //             $boostedAdvertisementImpression->increment('clicks');
    //         }
    //     }
    // }

    // public function cancelAdvertisement($id)
    // {
    //     $advertisement = Advertisement::where('id', $id)
    //         ->where('user_id', auth()->id())
    //         ->firstOrFail();

    //     $advertisement->status = Status::AD_CANCELED;
    //     $advertisement->save();

    //     return redirect()->route('advertisement.preview', $id)
    //         ->with('success', 'Advertisement has been successfully canceled.');
    // }

    // public function completeAdvertisement($id)
    // {
    //     $advertisement = Advertisement::where('id', $id)
    //         ->where('user_id', auth()->id())
    //         ->firstOrFail();

    //     if ($advertisement->status == Status::AD_APPROVED) {
    //         $advertisement->status = Status::AD_COMPLETED;
    //         $advertisement->save();

    //         return redirect()->route('advertisement.preview', $id)
    //             ->with('success', 'Advertisement has been marked as completed.');
    //     }
    //     return redirect()->route('advertisement.preview', $id)
    //         ->with('error', 'Only approved advertisements can be marked as completed.');
    // }

    // public function showAdFormPreview($id)
    // {
    //     $advertisement = Advertisement::findOrFail($id);
    //     $pageTitle = 'Preview Advertisement';
    //     return view('Template::advertisement.preview', compact('pageTitle', 'advertisement'));
    // }

    // public function showAdFormEdit($id)
    // {
    //     $advertisement = Advertisement::findOrFail($id);
    //     $pageTitle = 'Edit Advertisement';
    //     return view('Template::advertisement.edit', compact('pageTitle', 'advertisement'));
    // }

    public function showAdFormUpdate(Request $request, $id)
    {
        // Validate and update advertisement logic here
        // ...
    }

    public function showLinkRequestForms()
    {
        $pageTitle = "Account Recovery";
        return view('Template::user.auth.passwords.email', compact('pageTitle'));
    }

    // public function sendResetCodeEmail(Request $request)
    // {
    //     $request->validate([
    //         'value' => 'required'
    //     ]);

    //     if (!verifyCaptcha()) {
    //         $notify[] = ['error', 'Invalid captcha provided'];
    //         return back()->withNotify($notify);
    //     }

    //     $fieldType = $this->findFieldType();
    //     $user = User::where($fieldType, $request->value)->first();

    //     if (!$user) {
    //         $notify[] = ['error', 'The account could not be found'];
    //         return back()->withNotify($notify);
    //     }

    //     PasswordReset::where('email', $user->email)->delete();
    //     $code = verificationCode(6);
    //     $password = new PasswordReset();
    //     $password->email = $user->email;
    //     $password->token = $code;
    //     $password->created_at = \Carbon\Carbon::now();
    //     $password->save();

    //     $userIpInfo = getIpInfo();
    //     $userBrowserInfo = osBrowser();
    //     notify($user, 'PASS_RESET_CODE', [
    //         'code' => $code,
    //         'operating_system' => @$userBrowserInfo['os_platform'],
    //         'browser' => @$userBrowserInfo['browser'],
    //         'ip' => @$userIpInfo['ip'],
    //         'time' => @$userIpInfo['time']
    //     ], ['email']);

    //     $email = $user->email;
    //     session()->put('pass_res_mail', $email);
    //     $notify[] = ['success', 'Password reset email sent successfully'];
    //     return to_route('site.password.code.verify')->withNotify($notify);
    // }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            'value' => 'required'
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $fieldType = $this->findFieldType();
        $user = User::where($fieldType, $request->value)->first();

        if (!$user) {
            $notify[] = ['error', 'The account could not be found'];
            return back()->withNotify($notify);
        }

        PasswordReset::where('email', $user->email)->delete();

        $code = verificationCode(6);
        PasswordReset::create([
            'email' => $user->email,
            'token' => $code,
            'created_at' => now(),
        ]);

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();

        try {
            $user->notify(new PasswordResetNotification(
                $code,
                $userIpInfo['ip'] ?? 'N/A',
                $userBrowserInfo['browser'] ?? 'Unknown',
                $userBrowserInfo['os_platform'] ?? 'Unknown',
                $userIpInfo['time'] ?? now()->toDateTimeString()
            ));

            // Log::info('Password reset email sent to ' . $user->email);
        } catch (\Exception $e) {
            return $e;
            // Log::error('Failed to send password reset email: ' . $e->getMessage());
            $notify[] = ['error', 'Failed to send password reset email. Please try again.'];
            return back()->withNotify($notify);
        }

        session()->put('pass_res_mail', $user->email);
        $notify[] = ['success', 'Password reset email sent successfully'];
        return to_route('site.password.code.verify')->withNotify($notify);
    }

    public function findFieldType()
    {
        $input = request()->input('value');

        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }

    public function codeVerify(Request $request)
    {
        $pageTitle = 'Verify Email';
        $email = $request->session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('site.password.request')->withNotify($notify);
        }
        return view('Template::user.auth.passwords.code_verify', compact('pageTitle', 'email'));
    }

    public function verifyCode(Request $request)
    {

        $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);

        $code =  str_replace(' ', '', $request->code);

        if (PasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return to_route('site.password.request')->withNotify($notify);
        }
        $notify[] = ['success', 'You can change your password'];
        session()->flash('fpass_email', $request->email);
        return to_route('site.password.reset', $code)->withNotify($notify);
    }

    public function showResetForm(Request $request, $token = null)
    {

        $email = session('fpass_email');
        $token = session()->has('token') ? session('token') : $token;
        if (PasswordReset::where('token', $token)->where('email', $email)->count() != 1) {
            $notify[] = ['error', 'Invalid token'];
            return to_route('user.password.request')->withNotify($notify);
        }
        return view('Template::user.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $email, 'pageTitle' => 'Reset Password']
        );
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules());
        $reset = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $notify[] = ['error', 'Invalid verification code'];
            return to_route('user.login')->withNotify($notify);
        }

        $user = User::where('email', $reset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();



        $userIpInfo = getIpInfo();
        $userBrowser = osBrowser();
        notify($user, 'PASS_RESET_DONE', [
            'operating_system' => @$userBrowser['os_platform'],
            'browser' => @$userBrowser['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ], ['email']);


        $notify[] = ['success', 'Password changed successfully'];
        return to_route('user.login')->withNotify($notify);
    }


    protected function rules()
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', $passwordValidation],
        ];
    }

    public function launchSystem()
    {
        return view('Template::launch');
    }
}
