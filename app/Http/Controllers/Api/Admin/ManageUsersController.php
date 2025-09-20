<?php

namespace App\Http\Controllers\Api\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageUsersController extends Controller
{
    // public function kycApprove(Request $request)
    // {

    //     $username = $request->input('username');
    //     $kyc_data = json_decode($request->input('kyc_data'));

    //     $user = User::where('username', $username)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'remark' => 'validation_error',
    //             'status' => 'error',
    //             'message' => ['error' => 'User Data Not Found In Pro Account'],
    //         ]);
    //     }

    //     // File Downloading
    //     if ($request->hasFile('file_upload')) {
    //         foreach ($request->file('file_upload') as $index => $file) {
    //             $fileInfo = null;
    //             foreach ($kyc_data as $data) {
    //                 if ($data->type === 'file' && basename($data->value) === $file->getClientOriginalName()) {
    //                     $fileInfo = $data;
    //                     break;
    //                 }
    //             }

    //             if ($fileInfo) {
    //                 $filePath = $fileInfo->value;
    //                 $filename = basename($filePath);
    //                 $subdirectories = dirname($filePath);

    //                 $destinationPath = public_path('assets/verify/' . $subdirectories);

    //                 if (!file_exists($destinationPath)) {
    //                     mkdir($destinationPath, 0755, true);
    //                 }

    //                 $file->move($destinationPath, $filename);

    //             }
    //         }
    //     }

    //     $user->kyc_data = $kyc_data;
    //     $user->kyc_rejection_reason = null;
    //     $user->kv = Status::KYC_VERIFIED;
    //     $user->save();
    //     return response()->json([
    //         'remark' => 'data_copied_successfully',
    //         'status' => 'success',
    //         'message' => ['success' => 'KYC data and files received successfully'],
    //     ]);
    // }
}
