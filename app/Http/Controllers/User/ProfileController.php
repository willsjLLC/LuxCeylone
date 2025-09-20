<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SecondOwner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle', 'user'));
    }

    public function submitProfile(Request $request)
    {

        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string'
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required'
        ]);

        $user = auth()->user();
        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function deleteImage(Request $request)
    {
        $user = auth()->user();

        if ($user->image) {
            // Get image path using getFilePath() function
            $imagePath = getFilePath('userProfile') . '/' . $user->image;

            // Check if file exists & delete
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }

            // Remove image from database
            $user->image = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile image deleted successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No profile image to delete.'
        ], 404);
    }



    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }

    public function secondOwner()
    {
        $user = auth()->user();
        $pageTitle = "Profile Setting";
        $secondOwner = SecondOwner::where('original_owner_id', $user->id)->first();
        return view('Template::user.second_owner', compact('pageTitle', 'secondOwner'));
    }

    public function secondOwnerSubmit(Request $request)
    {
        $today = date('Y-m-d');
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'relationship_to_owner' => 'required|string',
            'contact_no' => 'required|string',
            'address' => 'required|string',
            'email_address' => 'required|email',
            'nic_front' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'nic_back' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $data = [
            'status' => 0,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'relationship_to_owner' => $request->relationship_to_owner,
            'dial_code' => $request->dial_code,
            'contact_no' => $request->contact_no,
            'address' => $request->address,
            'email_address' => $request->email_address,
            'note' => $request->note,
            'assigned_date' => $today,
        ];


        if ($request->hasFile('nic_front')) {
            try {
                $data['nic_front_url'] = fileUploader($request->nic_front, getFilePath('secondOwnerImages'));
            } catch (\Exception $exp) {
                return back()->withErrors(['image' => 'Couldn\'t upload your image. Please try again.']);
            }
        }

        if ($request->hasFile('nic_back')) {
            try {
                $data['nic_back_url'] = fileUploader($request->nic_back, getFilePath('secondOwnerImages'));
            } catch (\Exception $exp) {
                return back()->withErrors(['image' => 'Couldn\'t upload your image. Please try again.']);
            }
        }


        SecondOwner::updateOrCreate(
            ['original_owner_id' => auth()->id()],
            $data
        );

        $notify[] = ['success', 'Second owner information submitted successfully'];
        return back()->withNotify($notify);
    }

    public function secondOwnerDeleteNIC(Request $request)
    {
        $user = auth()->user();
        $secondOwner = SecondOwner::where('original_owner_id', $user->id)->first();

        if (!$secondOwner) {
            return response()->json(['error' => 'Second owner not found'], 404);
        }

        if ($request->type === 'front') {
            if ($secondOwner->nic_front_url) {
                @unlink($secondOwner->nic_front_url);
                $secondOwner->nic_front_url = null;
            }
        } elseif ($request->type === 'back') {
            if ($secondOwner->nic_back_url) {
                @unlink($secondOwner->nic_back_url);
                $secondOwner->nic_back_url = null;
            }
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $secondOwner->save();

        return response()->json(['success' => 'NIC image deleted successfully']);

    }


}
