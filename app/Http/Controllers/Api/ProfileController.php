<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function submitProfile(Request $request)
    {

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
        }

        $filePath = getFilePath('userProfile');
        $fileSize = getFileSize('userProfile');


        $fullPath = public_path($filePath);

        // Create directory if it doesn't exist
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'userData' => 'required|string',
            'username' => 'required|string',
            'file_upload' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $username = $request->input('username');
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Decode userData
        $userData = json_decode($request->input('userData'));
        if (!$userData) {
            return response()->json(['error' => 'Invalid userData JSON'], 422);
        }

        // Validate userData contents
        $innerValidator = Validator::make((array) $userData, [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
        ]);

        if ($innerValidator->fails()) {
            return response()->json(['errors' => $innerValidator->errors()], 422);
        }

        // Handle file upload with detailed debugging
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');

            try {

                $old = $user->image;

                // Manual file upload as alternative
                $destinationPath = 'assets/images/user/profile';
                $fullPath = public_path($destinationPath);

                // Create directory if it doesn't exist
                if (!is_dir($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }

                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename =  uniqid() . time() . '.' . $extension;

                // Delete old file if exists
                if ($old && file_exists(public_path($old))) {
                    unlink(public_path($old));
                }

                // Move file to destination
                $file->move($fullPath, $filename);


                $user->image = $filename;
            } catch (\Exception $e) {
                return response()->json(['error' => 'Could not upload the image: ' . $e->getMessage()], 500);
            }
        }

        // Update user data
        $user->firstname = $userData->firstname;
        $user->lastname = $userData->lastname;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not save user data'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }
}
