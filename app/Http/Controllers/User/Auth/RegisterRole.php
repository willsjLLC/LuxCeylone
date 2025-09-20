<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class RegisterRole extends Controller
{
    public function storeRole(Request $request)
    {
        $request->validate([
            'role' => 'required|integer|numeric|max:2|min:1'
        ]);

        session(['role' => $request->role]);

        if ($request->role == '2') {
            session(['registerStatus' => '4']);
        } else {
            session(['registerStatus' => '2']);
        }

        $notify[] = ['success', 'Role Successfully Selected.'];

        return back()->withNotify($notify);
    }

    public function storeCategories(Request $request)
    {
        $request->validate([
            'category' => 'required|array|min:1', // Ensure 'category' is an array and at least one value is selected
            'category.*' => 'integer|exists:categories,id', // Ensure each selected category exists in the database
        ], [
            'category.required' => 'Please select at least one category.',
            'category.array' => 'The category field must be an array of values.',
            'category.min' => 'You need to select at least one category.',
            'category.*.integer' => 'Each selected category must be a valid integer.',
            'category.*.exists' => 'One or more of the selected categories do not exist in our records.',
        ]);

        session(['category' => $request->category]);
        session(['registerStatus' => '3']);

        $notify[] = ['success', 'Categories Successfully Selected.'];

        return back()->withNotify($notify);
    }

    public function storeSubCategories(Request $request)
    {
        $request->validate([
            'subCategory' => 'required|array|min:1', // Ensure 'subCategory' is an array and at least one value is selected
            'subCategory.*' => 'integer|exists:sub_categories,id', // Ensure each selected subcategory exists in the database
        ], [
            'subCategory.required' => 'Please select at least one subcategory.',
            'subCategory.array' => 'The subcategory field must be an array of values.',
            'subCategory.min' => 'You need to select at least one subcategory.',
            'subCategory.*.integer' => 'Each selected subcategory must be a valid integer.',
            'subCategory.*.exists' => 'One or more of the selected subcategories do not exist in our records.',
        ]);

        session(['subCategory' => $request->subCategory]);
        session(['registerStatus' => '4']);

        $notify[] = ['success', 'Sub Categories Successfully Selected.'];

        return back()->withNotify($notify);
    }

    public function registerBack()
    {
        switch (session('registerStatus')) {
            case '4':
                if (session('role') == '2') {
                    session(['registerStatus' => '1']);
                } else {
                    session(['registerStatus' => '2']);
                }

                break;

            case '3':
                session(['registerStatus' => '2']);
                break;

            case '2':
                session(['registerStatus' => '1']);
                break;

            default:
                session(['registerStatus' => '1']);
                break;
        }

        return redirect()->back();
    }
}
