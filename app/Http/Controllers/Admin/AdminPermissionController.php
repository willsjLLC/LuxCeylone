<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('admins.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Permission Management';
        $admins = $this->adminData();
        return view('admin.management.index', compact('pageTitle', 'admins'));
    }

    protected function adminData($scope = null)
    {
        $admin = auth()->guard('admin')->user();

        // Permission check
        if (!$admin || !$admin->can('admins.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $currentRole = $admin->getRoleNames()->first();

        $roleHierarchy = ['super admin', 'admin', 'sub admin'];

        $currentRoleIndex = array_search($currentRole, $roleHierarchy);

        if ($currentRoleIndex === false || $currentRole === 'sub-admin') {
            return collect();
        }

        $visibleRoles = array_slice($roleHierarchy, $currentRoleIndex + 1);

        $admins = $scope ? Admin::$scope() : Admin::query();

        return $admins
            ->whereHas('roles', function ($q) use ($visibleRoles) {
                $q->whereIn('name', $visibleRoles);
            })
            ->searchable(['username', 'email'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
    }


    public function create()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('admins.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Create New Admin";
        return view('admin.management.create', compact('pageTitle'));
    }


    public function store(Request $request, $id = 0)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('admins.update') && $id) {
            return response()->view('admin.errors.403', [], 403);
        } elseif (!$admin || !$admin->can('admins.create')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $rules = [
            'name' => 'required|string|max:40',
            'email' => 'required|email|max:40|unique:admins,email' . ($id ? ",$id" : ''),
            'username' => 'nullable|string|max:40|unique:admins,username' . ($id ? ",$id" : ''),
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'designation' => 'nullable|string|max:255',
            'status' => 'required',
            'joined_at' => 'nullable|date',
            'password' => $id ? 'nullable|string|min:6|confirmed' : 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,sub admin',
        ];

        if (!$id) {
            $rules['image'] = ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
        } else {
            $rules['image'] = ['sometimes', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
        }

        $validated = $request->validate($rules);

        try {
            $admin = $id ? Admin::findOrFail($id) : new Admin();

            if ($request->hasFile('image')) {
                $old = $admin->image;
                $admin->image = fileUploader(
                    $request->file('image'),
                    getFilePath('adminManagement'),
                    getFileSize('adminManagement'),
                    $old
                );
            }

            if ($id) {
                $token = $admin->remember_token;
                $created_by = $admin->created_by;
            } else {
                $token = Str::random(60);
                $created_by = auth()->user()->id;
            }

            $admin->name = $validated['name'];
            $admin->email = $validated['email'];
            $admin->username = $validated['username'] ?? $admin->username;
            $admin->phone = $validated['phone'] ?? null;
            $admin->address = $validated['address'] ?? null;
            $admin->designation = $validated['designation'] ?? null;
            $admin->status = $validated['status'];
            $admin->joined_at = $validated['joined_at'] ?? null;
            $admin->created_by = $created_by;
            $admin->remember_token = $token;

            if (!empty($validated['password'])) {
                $admin->password = bcrypt($validated['password']);
            }

            $admin->save();

            // if ($id) {
            //     $admin->syncRoles([]);
            // }

            $admin->syncRoles([$validated['role']]);

            $admin->syncPermissions(['dashboard.view']);

            // $role = 'admin';
            // $admin->assignRole($role);

            $notify[] = ['success', $id ? 'Admin updated successfully' : 'Admin created successfully'];

            if (!$id) {
                return redirect()->route('admin.management.permission', $admin->id)->withNotify($notify);
            } else {
                return back()->withNotify($notify);
            }
        } catch (\Exception $e) {
            return $e;
            return back()->withErrors(['error' => 'Something went wrong.']);
        }
    }

    public function edit($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('admins.edit')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Edit Admin";
        $admin = Admin::findOrFail($id);
        $admin->role = $admin->roles->pluck('name')->first();

        return view('admin.management.create', compact('admin', 'pageTitle'));
    }

    public function permission($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('permissions.view')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = "Edit Admin Permissions";
        $admin = Admin::findOrFail($id);
        $allPermissions = Permission::all();
        return view('admin.management.permissions', compact('admin', 'pageTitle', 'allPermissions'));
    }

    public function updatePermission(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('permissions.update')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $admin = Admin::findOrFail($id);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $admin->syncPermissions($request->permissions ?? []);

        $notify[] = ['success', 'Permissions updated successfully'];
        return back()->withNotify($notify);
    }

    public function syncPermission()
    {
        $superAdmin = Admin::where('username', 'SuperAdmin')->first();

        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);
        $superAdmin->syncRoles(['super admin']);

        $notify[] = ['success', 'Main Admin Permission updated successfully'];
        return back()->withNotify($notify);
    }
}
