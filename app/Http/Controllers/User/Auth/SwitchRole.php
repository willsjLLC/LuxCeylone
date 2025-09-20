<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\UserCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class SwitchRole extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function switchRole(Request $request)
    {

        // $role = $request->session()->get('role');

        $user = auth()->user();

        $role = $user->role;

        $validator = Validator::make([], []);

        if (empty($role) || is_null($role)) {

            $validator->errors()->add('role', 'System Error Contact Admin');

            return redirect()->back()->withErrors($validator)->withInput();

        } else {
            $user = auth()->user();

            if ($role == "1") {
                $user->role = 2;
                $user->save();

                session()->forget(['role', 'category', 'subCategory']);
                session(['role' => 2]);

                $notify[] = ['success', 'Role has Changed to Empolyer .'];
                return back()->withNotify($notify);

            } elseif ($role == '2') {

                $user->role = 1;
                $user->save();

                session()->forget(['role', 'category', 'subCategory']);
                session(['role' => 1]);

                $notify[] = ['success', 'Role has Changed to Empolyee .'];
                return back()->withNotify($notify);

            }
        }
    }

    public function nextStep(Request $request)
    {
        $user = auth()->user();

        if (session('registerStatus') == null) {

            session(['registerStatus' => '1']);

            $categories = Category::with('subcategory')->get();

        } else if (session('registerStatus') == '1') {

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

            $categories = Category::with('subcategory')->whereIn('id', session('category'))->get();
            $subCategories = SubCategory::where('category_id', session('category'))->count();

            if ($subCategories <= 0) {
                $this->saveCategories($user);

                $notify[] = ['success', 'Role has Changed to Empolyee .'];

                return redirect()->route('user.product.index')->withNotify($notify);
            }

            session(['registerStatus' => '2']);

        } else if (session('registerStatus') == '2') {

            $validator = Validator::make($request->all(), [
                'subCategory' => 'required|array|min:1',
                'subCategory.*' => 'integer|exists:sub_categories,id',
            ], [
                'subCategory.required' => 'Please select at least one subcategory.',
                'subCategory.array' => 'The subcategory field must be an array of values.',
                'subCategory.min' => 'You need to select at least one subcategory.',
                'subCategory.*.integer' => 'Each selected subcategory must be a valid integer.',
                'subCategory.*.exists' => 'One or more of the selected subcategories do not exist in our records.',
            ]);

            // Set custom redirection on validation failure
            if ($validator->fails()) {
                return redirect()->route('user.switchrole')  // Replace 'custom.route.name' with your actual route name
                    ->withErrors($validator)
                    ->withInput();
            }

            session(['subCategory' => $request->subCategory]);

            $categories = Category::with('subcategory')->whereIn('id', session('category'))->get();

            $this->saveCategories($user);

            $notify[] = ['success', 'Role has Changed to Empolyee .'];

            return redirect()->route('user.product.index')->withNotify($notify);
        }

        $pageTitle = 'Change User Role';

        // return view($this->activeTemplate . 'user.auth.roleSwitch', compact('pageTitle', 'categories'));
        return view('Template::user.auth.roleSwitch', compact('pageTitle', 'categories'));

    }

    public function backStep(Request $request)
    {
        switch (session('registerStatus')) {

            case '2':
                session(['registerStatus' => '1']);
                break;

            default:
                session(['registerStatus' => '1']);
                break;
        }

        return redirect()->route('user.switchrole');
    }

    private function saveCategories(User $user)
    {
        if (session('subCategory') == null) {
            foreach (session('category') as $key => $category) {
                $userCategories = new UserCategories();
                $userCategories->user_id = $user->id;
                $userCategories->category_id = $category;
                $userCategories->save();
            }

            $user->role = 1;
            $user->save();

            session()->forget(['role', 'category', 'subCategory', 'registerStatus']);
            session(['role' => 1]);
        } else {
            $subCategories = SubCategory::whereIn('id', session('subCategory'))->get();

            foreach ($subCategories as $key => $subcategory) {

                $userCategories = new UserCategories();
                $userCategories->user_id = $user->id;
                $userCategories->category_id = $subcategory->category_id;
                $userCategories->sub_category_id = $subcategory->id;
                $userCategories->save();
            }

            $user->role = 1;
            $user->save();

            session()->forget(['role', 'category', 'subCategory', 'registerStatus']);
            session(['role' => 1]);
        }
    }
}
