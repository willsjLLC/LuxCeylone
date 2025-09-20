<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.extensions_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'Extensions';
        $extensions = Extension::orderBy('name')->get();
        return view('admin.extension.index', compact('pageTitle', 'extensions'));
    }

    public function update(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.extensions_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $extension = Extension::findOrFail($id);
        $validationRule = [];
        foreach ($extension->shortcode as $key => $val) {
            $validationRule = array_merge($validationRule, [$key => 'required']);
        }
        $request->validate($validationRule);

        $shortcode = json_decode(json_encode($extension->shortcode), true);
        foreach ($shortcode as $key => $value) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $extension->shortcode = $shortcode;
        $extension->save();
        $notify[] = ['success', $extension->name . ' updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.extensions_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }
        
        return Extension::changeStatus($id);
    }
}
