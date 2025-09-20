<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductSubCategoryController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_subcategories.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "All Product Sub Categories";
        $productSubCategories = ProductSubCategory::searchable(['name'])
            ->orderBy('name')
            ->paginate(getPaginate());

        return view('admin.productSubCategory.index', compact('pageTitle', 'productSubCategories'));
    }

    public function create()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_subcategories.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Create Product Sub Category";
        $categories = ProductCategory::all();
        return view('admin.productSubCategory.create', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_subcategories.update') && $id) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('product_subcategories.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->merge([
            'supports_condition' => $request->has('supports_condition') ? 1 : 0,
        ]);

        $request->validate([
            "name" => 'required|max:40',
            "category_id" => 'required|exists:product_categories,id',
            "description" => 'required',
            "image" => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $subcategory = ProductSubCategory::findOrFail($id);
            $message = "Subcategory updated successfully";
        } else {
            $subcategory = new ProductSubCategory();
            $message = "Subcategory added successfully";
        }

        if ($request->hasFile('image')) {
            try {
                $old = $subcategory->image;
                $subcategory->image = fileUploader($request->image, getFilePath('productSubCategory'), getFileSize('productSubCategory'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->description = $request->description;
        $subcategory->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_subcategories.edit')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Edit Product Sub Category";
        $categories = ProductCategory::all();
        $subcategory = ProductSubCategory::findOrFail($id);
        return view('admin.productSubCategory.create', compact('subcategory', 'categories', 'pageTitle'));
    }

    public function status($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_subcategories.update')) {
            return response()->view('admin.errors.403', [], 403);
        }
        $category = ProductSubCategory::findOrFail($id);

        $category->status = $category->status == 0 ? 1 : 0;

        $category->save();

        $notify[] = ["success", "Status updated successfully"];
        return back()->withNotify($notify);
    }

    public function getByCategory($category_id)
    {
        $subcategories = ProductSubCategory::where('category_id', $category_id)
            ->where('status', 1)
            ->get();

        return response()->json($subcategories);
    }
}
