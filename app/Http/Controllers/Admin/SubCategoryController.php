<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('sub_categories.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle     = "All Subcategories";
        $subCategories = SubCategory::searchable(['name', 'category:name'])->with('category')->orderBy('name')->paginate(getPaginate());
        return view('admin.subcategory.index', compact('pageTitle', 'subCategories'));
    }
    public function create()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('sub_categories.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle  = "Create Subcategory";
        $categories = Category::active()->get();
        return view('admin.subcategory.create', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('sub_categories.update') && $id) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('sub_categories.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $request->merge([
            'supports_condition' => $request->has('supports_condition') ? 1 : 0,
        ]);

        $request->validate([
            "name"        => 'required|max:40',
            "category_id" => 'required|exists:categories,id',
            "description" => 'required',
            "image"       => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $subcategory = SubCategory::findOrFail($id);
            $message     = "Subcategory updated successfully";
        } else {
            $subcategory = new SubCategory();
            $message     = "Subcategory added successfully";
        }

        if ($request->hasFile('image')) {
            try {
                $old                = $subcategory->image;
                $subcategory->image = fileUploader($request->image, getFilePath('subcategory'), getFileSize('subcategory'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $subcategory->supports_condition        = $request->supports_condition;
        $subcategory->name        = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->description = $request->description;
        $subcategory->save();

        $notify[] = ["success", $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('sub_categories.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $sub_category = SubCategory::findOrFail($id);

        $sub_category->status = $sub_category->status == 0 ? 1 : 0;

        $sub_category->save();

        $notify[] = ["success", "Status updated successfully"];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('sub_categories.edit')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle   = "Edit Subcategory";
        $categories  = Category::active()->get();
        $subcategory = SubCategory::findOrFail($id);
        return view("admin.subcategory.create", compact('pageTitle', 'subcategory', 'categories'));
    }
}
