<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPurchaseCommission;
use App\Models\ProductImage;
use App\Models\ProductSubCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('products.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All Products";
        $products = Product::searchable(['name'])->with('admin')->with('category')->orderBy('created_at', 'desc')->paginate(getPaginate());
        return view('admin.products.index', compact('pageTitle', 'products'));
    }

    private function generateSku($category_id, $product_code)
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $uniqueId = str_pad(($lastProduct ? $lastProduct->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        return $category_id . '-' . strtoupper($product_code) . '-' . $uniqueId;
    }

    private function generateProductCode($category_id)
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $uniqueId = str_pad(($lastProduct ? $lastProduct->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        $categoryCode = str_pad($category_id, 2, '0', STR_PAD_LEFT);
        $date = now()->format('Ymd');

        return 'P' . $categoryCode . '-' . $date . '-' . $uniqueId;
    }

    public function view($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('products.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Product Information";
        $product = Product::where('id', $id)
            ->with(['admin', 'category'])
            ->first();

        // Fetch product images
        $primary_image = ProductImage::where('product_id', $id)
            ->where('is_primary', 1)
            ->first();

        $additional_images = ProductImage::where('product_id', $id)
            ->where('is_primary', 0)
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.view', compact(
            'pageTitle',
            'product',
            'primary_image',
            'additional_images'
        ));
    }


    public function status($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('products.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $product = Product::findOrFail($id);
        $product->status = $product->status == 'active' ? 'inactive' : 'active';
        $product->save();
        $notify[] = ["success", 'Status Updated Successfully'];
        return back()->withNotify($notify);
    }

    public function applyWatermarkToExisting($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('products.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $product = Product::findOrFail($id);
        $directory = 'assets/admin/images/product/';

        if (!$product->image_url || !file_exists(public_path($directory . $product->image_url))) {
            $notify[] = ['error', 'Image not found'];
            return back()->withNotify($notify);
        }

        try {
            // Process the image using Intervention Image
            $manager = new ImageManager(new Driver());
            $img = $manager->read(public_path($directory . $product->image_url));

            $width = $img->width();
            $height = $img->height();

            $watermarkText = $product->watermark_text ?: getValue('DEFAULT_WATERMARK_TEXT');

            // Text watermark with "addCiti.lk"
            $img->text($watermarkText, $width / 2, $height / 2, function ($font) use ($width) {
                // Use a default system font if custom font doesn't exist
                $font_path = public_path('fonts/arial.ttf');
                if (!file_exists($font_path)) {
                    // Use a system font as fallback
                    $system_fonts = [
                        '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf',
                        '/usr/share/fonts/TTF/Arial.ttf',
                        'C:\\Windows\\Fonts\\arial.ttf'
                    ];

                    foreach ($system_fonts as $system_font) {
                        if (file_exists($system_font)) {
                            $font_path = $system_font;
                            break;
                        }
                    }
                }

                $font->filename($font_path);
                $font->size($width * 0.12);
                $font->color('rgba(255, 255, 255, 0.3)');
                $font->align('center');
                $font->valign('middle');
                $font->angle(45);
            });

            // Save the processed image back to the same location
            $img->toJpeg(80)->save(public_path($directory . $product->image_url));

            // Update the watermark flag in the database
            $product->watermark = 1;
            $product->save();

            $notify[] = ["success", "Watermark applied successfully"];
            return back()->withNotify($notify);

        } catch (\Exception $exp) {
            // \Log::error('Watermark Application Error: ' . $exp->getMessage());
            // \Log::error($exp->getTraceAsString());

            $notify[] = ['error', 'Couldn\'t apply watermark: ' . $exp->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function create()
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin->can('products.create')) {
            return response()->view('admin.errors.403', [], 403);
        }
        $pageTitle = "Create Product";
        $product_categories = ProductCategory::where('status', 'active')->get();
        $product_sub_categories = ProductSubCategory::where('status', Status::ENABLE)->get();
        return view('admin.products.create', compact('pageTitle', 'product_categories', 'product_sub_categories'));
    }

    public function edit($id)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin->can('products.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Edit Product";
        $product = Product::findOrFail($id);
        $product_categories = ProductCategory::where('status', 'active')->get();
        $commissions = ProductPurchaseCommission::where('product_id', $id)->first();

        // Get all product images
        $primary_image = ProductImage::where('product_id', $id)
            ->where('is_primary', 1)
            ->first();

        $additional_images = ProductImage::where('product_id', $id)
            ->where('is_primary', 0)
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.create', compact(
            'pageTitle',
            'product',
            'product_categories',
            'commissions',
            'primary_image',
            'additional_images'
        ));
    }

    public function store(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin || !$admin->can('products.update') && $id) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('products.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        // Add watermark to the request
        $request->merge([
            'watermark' => $request->has('watermark') ? 1 : 0,
        ]);

        $rules = [
            "name" => 'required|max:40',
            "description" => 'required',
            "product_code" => 'nullable|string|max:50',
            "category_id" => 'nullable|integer',
            "sub_category_id" => 'nullable|integer',
            "cost" => 'required|numeric|min:0',
            "profit" => 'required|numeric|min:0',
            "discount" => 'nullable|numeric|min:0',
            "selling_price" => 'required|numeric|min:0',
            "original_price" => 'required|numeric|min:0',
            "quantity" => 'required|integer|min:0',
            "unit" => 'required|string',
            "brand" => 'nullable|string|max:100',
            "attachment" => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            "status" => 'required|in:active,inactive',
            "weight" => 'nullable|numeric|min:0',
            "watermark" => 'nullable|in:0,1',
            "watermark_text" => 'nullable|string|max:100',
            'company_commission' => 'required',
            'company_expenses' => 'required',
            'customers_commission' => 'required',
            'customers_voucher' => 'required',
            'customers_festival' => 'required',
            'customers_saving' => 'required',
            'leader_bonus' => 'required',
            'leader_vehicle_lease' => 'required',
            'leader_petrol' => 'required',
            'top_leader_car' => 'required',
            'top_leader_house' => 'required',
            'top_leader_expenses' => 'required',
            'total_commission' => 'required|numeric|min:0|same:profit',
            'images' => 'array',
            'images.0' => !$id ? 'required|image|mimes:jpg,jpeg,png' : 'nullable|image|mimes:jpg,jpeg,png',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png',
            'is_primary' => 'array',
            'sort_order' => 'array',
        ];

        $request->validate($rules);

        if ($id) {
            $product = Product::findOrFail($id);
            $message = "Product updated successfully";
        } else {
            $product = new Product();
            $message = "Product added successfully";
        }

        // Handle product data
        $user = auth()->guard('admin')->user();
        $product->name = $request->name ?? $product->name;
        $product->user_id = $user->id;
        $product->category_id = $request->category_id ?? $product->category_id;
        $product->sub_category_id = $request->sub_category_id ?? $product->sub_category_id;
        $product->description = $request->description ?? $product->description;
        $product->cost = $request->cost ?? $product->cost;
        $product->profit = $request->profit ?? $product->profit;
        $product->discount = $request->discount ?? 0;
        $product->selling_price = $request->selling_price ?? $product->selling_price;
        $product->quantity = $request->quantity ?? $product->quantity;
        $product->unit = $request->unit ?? $product->unit;
        $product->brand = $request->brand ?? $product->brand;
        $product->status = $request->status ?? $product->status;
        $product->weight = $request->weight ?? $product->weight;
        $product->original_price = $request->original_price ?? $product->original_price;
        $product->watermark = $request->watermark ?? 0;
        $product->watermark_text = $request->watermark_text;

        if (empty($request->product_code)) {
            $product->product_code = $this->generateProductCode($request->category_id);
        } else {
            $product->product_code = $request->product_code;
        }

        if (empty($request->sku)) {
            $product->sku = $this->generateSku($request->category_id, $product->product_code);
        } else {
            $product->sku = $request->sku;
        }

        $product->save();

        // Handle the images
        if ($request->has('images')) {
            foreach ($request->file('images', []) as $key => $imageFile) {
                if (!$imageFile) {
                    continue; // Skip if no file uploaded
                }

                try {
                    // Get original file extension
                    $extension = $imageFile->getClientOriginalExtension();
                    // Generate hexadecimal filename
                    $name_gen = hexdec(uniqid()) . '.' . $extension;
                    // Define the directory path for saving the file
                    $directory = 'assets/admin/images/product/';
                    $path = public_path($directory);

                    // Create directory if it doesn't exist
                    if (!is_dir($path)) {
                        mkdir($path, 0755, true);
                    }

                    $save_path = $path . $name_gen;

                    // Process the image using Intervention Image
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($imageFile);

                    // Apply watermark if requested
                    if ($request->watermark == 1) {
                        $width = $img->width();
                        $height = $img->height();
                        // Get watermark text
                        $watermarkText = $request->watermark_text ?: getValue('DEFAULT_WATERMARK_TEXT');

                        // Text watermark
                        $img->text($watermarkText, $width / 2, $height / 2, function ($font) use ($width) {
                            // Use a default system font if custom font doesn't exist
                            $font_path = public_path('fonts/arial.ttf');
                            if (!file_exists($font_path)) {
                                // Use a system font as fallback
                                $system_fonts = [
                                    '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf',
                                    '/usr/share/fonts/TTF/Arial.ttf',
                                    'C:\\Windows\\Fonts\\arial.ttf'
                                ];
                                foreach ($system_fonts as $system_font) {
                                    if (file_exists($system_font)) {
                                        $font_path = $system_font;
                                        break;
                                    }
                                }
                            }
                            $font->filename($font_path);
                            $font->size($width * 0.12);
                            $font->color('rgba(255, 255, 255, 0.3)');
                            $font->align('center');
                            $font->valign('middle');
                            $font->angle(45);
                        });
                    }

                    // Save the processed image
                    $img->toJpeg(80)->save($save_path);

                    // Save image data to database
                    $isPrimary = (int) ($request->input('is_primary.' . $key, 0));
                    $sortOrder = (int) ($request->input('sort_order.' . $key, $key + 1));

                    // Find existing product image or create new one
                    $productImage = ProductImage::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'sort_order' => $sortOrder,
                        ],
                        [
                            'image' => $name_gen,
                            'is_primary' => $isPrimary,
                        ]
                    );

                    // If this is the primary image, update the product's image_url
                    if ($isPrimary) {
                        $product->image_url = $name_gen;
                        $product->save();
                    }

                } catch (\Exception $exp) {
                    // \Log::error('Image Upload Error: ' . $exp->getMessage());
                    // \Log::error($exp->getTraceAsString());
                    $notify[] = ['error', 'Couldn\'t upload image: ' . $exp->getMessage()];
                    return back()->withNotify($notify);
                }
            }
        }

        // Handle commission data
        $commission = $product
            ? ProductPurchaseCommission::where('product_id', $id)->first()
            : new ProductPurchaseCommission();

        if (!$commission) {
            $commission = new ProductPurchaseCommission();
        }

        $commission->product_id = $product->id;
        $commission->fill($request->only([
            'company_commission',
            'company_expenses',
            'customers_commission',
            'customers_voucher',
            'customers_festival',
            'customers_saving',
            'leader_bonus',
            'leader_vehicle_lease',
            'leader_petrol',
            'top_leader_car',
            'top_leader_house',
            'top_leader_expenses'
        ]));
        $commission->save();

        return back()->withNotify([["success", $message]]);
    }
}
