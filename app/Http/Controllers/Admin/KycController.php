<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function setting()
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.kyc_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }

        $pageTitle = 'KYC Setting';
        $form = Form::where('act','kyc')->first();
        return view('admin.kyc.setting',compact('pageTitle','form'));
    }

    public function settingUpdate(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin || !$admin->can('settings.kyc_settings')) {
            return response()->view('admin.errors.403', [], 403);
        }
        
        $formProcessor = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'],$generatorValidation['messages']);
        $exist = Form::where('act','kyc')->first();
        $formProcessor->generate('kyc',$exist,'act');

        $notify[] = ['success','KYC data updated successfully'];
        return back()->withNotify($notify);
    }
}
