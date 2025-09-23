<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPromotionBanner;
use Illuminate\Http\Request;
use App\Service\PackageActivationService;

class ProductController extends Controller
{
    protected $packageActivationService;

    public function __construct(PackageActivationService $packageActivationService)
    {
        $this->packageActivationService = $packageActivationService;
    }

    public function getProduct(Request $request)
    {
         $user = auth()->user();
        $pageTitle = 'All Products';
        $search = $request->search;

        $productDeliveryChargers = getValue('PRODUCT_DELIVERY_CHARGERS');

        // Get categories with their subcategories and products
        $query = ProductCategory::with([
            'productSubcategories' => function ($query) use ($search) {
                $query->where('status', 1); // Active subcategories (status = 1)
                
                // Include products in subcategories
                $query->with(['products' => function ($productQuery) use ($search) {
                    $productQuery->where('status', 'active');
                    if ($search) {
                        $productQuery->where('name', 'like', "%{$search}%");
                    }
                    $productQuery->orderBy('created_at', 'desc');
                }]);
                
                // Only include subcategories that have active products
                $query->whereHas('products', function ($productQuery) {
                    $productQuery->where('status', 'active');
                });
                
                $query->orderBy('created_at', 'desc');
            }
        ])->where('status', 'active');

        // Apply search filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('productSubcategories', function ($subCatQuery) use ($search) {
                        $subCatQuery->where('status', 1)
                            ->where(function ($subQ) use ($search) {
                                $subQ->where('name', 'like', "%{$search}%")
                                    ->orWhereHas('products', function ($productQuery) use ($search) {
                                        $productQuery->where('status', 'active')
                                            ->where('name', 'like', "%{$search}%");
                                    });
                            });
                    });
            });
        }

        // Only include categories that have subcategories with active products
        $query->whereHas('productSubcategories', function ($subCatQuery) {
            $subCatQuery->where('status', 1)
                ->whereHas('products', function ($productQuery) {
                    $productQuery->where('status', 'active');
                });
        });

        $query->orderBy('created_at', 'desc');
        $categories = $query->get();

    

        // Get all categories for horizontal display (if needed)
        $allCategories = ProductCategory::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $promotion_banner = ProductPromotionBanner::first();

         $needsTopUp = $this->packageActivationService->needsPackageActivation($user);

        $isPackageActive = $this->packageActivationService->isPackageActive($user);

        $recursive_top_up_range = getValue('USER_RECURSIVE_TOP_UP_RANGE');
        $lastActivatedTier = $user->employeePackageActivationHistories()->max('activated_for_earning_tier');

        $currentEarningTier = floor($user->total_earning / $recursive_top_up_range);

        $skippedPackages = 0;
        $outstandingTopUpAmount = 0;

        if ($currentEarningTier > 0 && $needsTopUp) {
            $expectedTier = $lastActivatedTier ? $lastActivatedTier + 1 : 1;
            $skippedPackages = $currentEarningTier - $expectedTier;
            $outstandingTopUpAmount = $expectedTier * $recursive_top_up_range;
        }

        return view('Template::product.index', compact('pageTitle', 'categories', 'allCategories', 'search', 'promotion_banner','needsTopUp',
            'isPackageActive',
            'skippedPackages',
            'outstandingTopUpAmount', 'productDeliveryChargers'));
    }

    public function productDetails($id)
    {
        $product = Product::with(['images' => function($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc');
        }])->findOrFail($id);

        $smilerProducts = Product::where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get();

        $productDeliveryChargers = getValue('PRODUCT_DELIVERY_CHARGERS');

        $pageTitle = $product->name;

        return view('Template::product.details', compact('pageTitle', 'product', 'smilerProducts', 'productDeliveryChargers'));
    }

    public function showCategoryProducts($categoryName, Request $request)
    {
        $category = ProductCategory::where('name', $categoryName)
            ->where('status', 'active')
            ->firstOrFail();
        
        $selectedSubCategory = $request->get('subcategory');
        
        // Get all subcategories for this category
        $subCategories = $category->productSubcategories()
            ->where('status', 1)
            ->whereHas('products', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Build products query
        $productsQuery = Product::where('category_id', $category->id)
            ->where('status', 'active');
        
        // Apply subcategory filter if selected
        if ($selectedSubCategory && $selectedSubCategory !== 'all') {
            $subCategory = $subCategories->where('name', $selectedSubCategory)->first();
            if ($subCategory) {
                $productsQuery->where('sub_category_id', $subCategory->id);
            }
        }
        
        $products = $productsQuery->orderBy('created_at', 'desc')->get();
        
        return view('Template::product.category', [
            'pageTitle' => "Products in {$category->name}",
            'products' => $products,
            'category' => $category,
            'subCategories' => $subCategories,
            'selectedSubCategory' => $selectedSubCategory
        ]);
    }

   public function showSubCategoryProducts($categoryName, $subCategoryName)
    {
        // Find the category
        $category = ProductCategory::where('name', $categoryName)
            ->where('status', 'active')
            ->firstOrFail();
        
        // Find the subcategory belonging to this category
        $subCategory = $category->productSubcategories()
            ->where('name', $subCategoryName)
            ->where('status', 1)
            ->firstOrFail();

        // Get products for this subcategory
        $products = Product::where('sub_category_id', $subCategory->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $pageTitle = "Products in {$subCategory->name}";

        return view('Template::product.subCategory', compact('pageTitle', 'products', 'category', 'subCategory'));
    }
}
