<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
	public function index()
	{
		$admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_categories.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

		$pageTitle  = "All Product Categories";
		$categories = ProductCategory::searchable(['name'])->orderBy('name')->paginate(getPaginate());
		return view('admin.productCategory.index', compact('pageTitle', 'categories'));
	}
	public function create()
	{
		$admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_categories.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

		$pageTitle = "Create ProductCategory";
		return view('admin.productCategory.create', compact('pageTitle'));
	}

	public function store(Request $request, $id = 0)
	{
		$admin = auth()->guard('admin')->user();

		if (!$admin || !$admin->can('product_categories.update') && $id) {
			return response()->view('admin.errors.403', [], 403);
		} elseif (!$admin || !$admin->can('product_categories.create')) {
			return response()->view('admin.errors.403', [], 403);
		}

		$request->validate([
			"name"        => 'required|max:40',
			"description" => 'required',
			'image'       => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
		]);

		if ($id) {
			$category = ProductCategory::findOrFail($id);
			$message  = "Product Category update successfully";
		} else {
			$category = new ProductCategory();
			$message  = "Product Category added successfully";
		}

		if ($request->hasFile('image')) {
			try {
				$old = $category->image;
				$category->image_url = fileUploader($request->image, getFilePath('productCategory'), getFileSize('productCategory'), $old);
			} catch (\Exception $exp) {
				return back()->withErrors(['image' => 'Couldn\'t upload your image. Please try again.']);
			}
		} 

		$category->name        = $request->name;
		$category->description = $request->description;
		$category->save();
		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	public function edit($id)
	{
		$admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_categories.edit')) {
            return response()->view('admin.errors.403', [], 403);
        }

		$pageTitle = "Edit ProductCategory";
		$category  = ProductCategory::findOrFail($id);
		return view('admin.productCategory.create', compact('category', 'pageTitle'));
	}

	public function status($id)
	{
		$admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_categories.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

		$admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('product_categories.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

		$product_category = ProductCategory::findOrFail($id);
		$product_category->status = $product_category->status == 'active' ? 'inactive' : 'active';
		$product_category->save();
		$notify[] = ["success", 'Status Updated Successfully'];
		return back()->withNotify($notify);
	}
}
