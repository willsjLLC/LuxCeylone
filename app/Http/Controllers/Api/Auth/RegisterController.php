<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\ReferralLog;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use RegistersUsers;

    // Direct switch to Lite from Pro
     public function directLiteSwitch(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Pro User Login
            $loginAsPro = env('ADD_CITI_LITE_API') . '/api/login';

            $response = Http::timeout(10)->post($loginAsPro, [
                'username' => $user->username,
                'password' => $user->password,
            ]);

            if ($response) {
                if ($response->successful()) {

                    if ($response['remark'] == 'login_success') {
                        $redirectUrl = env('ADD_CITI_LITE_API') . '/login-via-token?remember_token=' . $response['remember_token'];

                        return response()->json([
                            'success' => true,
                            'redirect_url' => $redirectUrl,

                        ], 200);
                    }
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to connect to Lite system'
                ], 503);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while initiating Lite switch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API endpoint to check if Pro user exists (for Lite system calls)
    public function checkUserExists(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if it's a 4-digit ID or username
            $username = $request->username;

            if (is_numeric($username) && strlen($username) == 4) {
                // It's a 4-digit Pro ID
                $user = User::where('four_digit_unique_id', $username)->first();
            } else {
                // It's a username
                $user = User::where('username', $username)->first();
            }

            if ($user) {
                // Get current referral count (for display purposes only - no limit enforcement)
                $referralCount = User::where('referred_user_id', $user->id)->count();

                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'data' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'four_digit_unique_id' => $user->four_digit_unique_id,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'email' => $user->email,
                        'referral_count' => $referralCount,
                        'has_referral_space' => true, // Always true for Pro accounts
                        'max_referrals' => 'unlimited' // Indicate unlimited for Pro
                    ]
                ], 200);
            }

            return response()->json([
                'success' => true,
                'exists' => false,
                'data' => null,
                'message' => 'Pro user not found'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Pro user check failed: ' . $e->getMessage(), [
                'username' => $request->username ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking user',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    // API endpoint for getting Lite user data for Pro registration
    public function getLiteUserForProRegistration(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lite_user_id' => 'required|integer',
                'lite_email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Make API call to Lite system to get user data
            $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/get-user-for-pro';

            $response = Http::timeout(10)->post($liteApiUrl, [
                'user_id' => $request->lite_user_id,
                'email' => $request->lite_email
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['success']) {
                    // Check if user already has Pro account
                    $existingProUser = User::where('email', $responseData['data']['email'])->first();

                    if ($existingProUser) {
                        return response()->json([
                            'success' => false,
                            'message' => 'User already has a Pro account',
                            'has_pro_account' => true,
                            'pro_user' => [
                                'id' => $existingProUser->id,
                                'username' => $existingProUser->username,
                                'email' => $existingProUser->email
                            ]
                        ], 409);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Lite user data retrieved successfully',
                        'data' => $responseData['data']
                    ], 200);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve Lite user data'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving Lite user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function registerFromLite(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lite_user_id' => 'required|integer',
                'lite_email' => 'required|email',
                'unique_id' => 'required|string|size:16',
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'username' => 'nullable|string|unique:users,username',
                'pro_referrer_username' => 'nullable|string',
                'dial_code' => 'nullable|string',
                'country_code' => 'nullable|string',
                'mobile' => 'nullable|string',
                'country_name' => 'nullable|string',
                'city' => 'nullable|string',
                'district' => 'nullable|string',
                'district_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'state' => 'nullable|string',
                'zip' => 'nullable|string',
                'address' => 'nullable|string',
                'from_pro_account' => 'nullable|boolean',
                'pro_referred_username' => 'nullable|string',
                'referred_user_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!gs('registration')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not allowed'
                ], 403);
            }

            // Check if user already exists
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has a Pro account',
                    'has_pro_account' => true,
                    'data' => [
                        'id' => $existingUser->id,
                        'username' => $existingUser->username,
                        'email' => $existingUser->email
                    ]
                ], 409);
            }

            // Get referral placement
            $referredUserId = $this->determineProReferralPlacementWithInput($request);

            // Create Pro user from Lite data
            $user = $this->createFromLite($request->all(), $referredUserId);

            // Copy KYC data from Lite to Pro (if user has KYC data)
            $this->copyKycDataFromLite($request->username, $user);

            // Generate Pro referral link
            $proReferralLink = $this->generateProReferralLink($user->username);

            // Update Lite user to mark Pro registration
            $this->updateLiteUserForProRegistration($request->lite_user_id, $user->username);

            // Log successful registration
            Log::info("Pro account created from Lite", [
                'pro_user_id' => $user->id,
                'lite_user_id' => $request->lite_user_id,
                'email' => $user->email,
                'pro_referrer_input' => $request->pro_referrer_username,
                'placed_under' => $referredUserId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pro account created successfully from Lite account',
                'data' => [
                    'id' => $user->id,
                    'unique_id' => $user->unique_id,
                    'four_digit_unique_id' => $user->four_digit_unique_id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'username' => $user->username,
                    'referred_user_id' => $user->referred_user_id,
                    'pro_referral_link' => $proReferralLink,
                    'placement_info' => [
                        'pro_referrer_used' => $request->pro_referrer_username,
                        'placed_under_id' => $referredUserId
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Pro registration from Lite failed: ' . $e->getMessage(), [
                'lite_user_id' => $request->lite_user_id ?? null,
                'email' => $request->email ?? null,
                'pro_referrer_input' => $request->pro_referrer_username ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Copy KYC data from Lite to Pro
    private function copyKycDataFromLite($username, $proUser)
    {
        try {
            // Get KYC data from Lite system
            $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/get-user-kyc-data';
            
            $response = Http::timeout(15)->post($liteApiUrl, [
                'username' => $username
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['success'] && isset($responseData['kyc_data'])) {
                    $kycData = $responseData['kyc_data'];
                    $kv = $responseData['kv'] ?? Status::KYC_UNVERIFIED;
                    
                    if (!empty($kycData)) {
                        // Use the existing KYC approve logic
                        $request = new Request([
                            'username' => $proUser->username,
                            'kyc_data' => json_encode($kycData)
                        ]);

                        // Download and process files if they exist
                        $this->downloadKycFilesFromLite($username, $kycData);
                        
                        // Apply KYC data directly to Pro user
                        $proUser->kyc_data = $kycData;
                        $proUser->kyc_rejection_reason = null;
                        $proUser->kv = $kv;
                        $proUser->save();
                        
                        Log::info("KYC data copied from Lite to Pro", [
                            'pro_username' => $proUser->username,
                            'lite_username' => $username,
                            'kv_status' => $kv
                        ]);
                    }
                }
            } else {
                Log::warning("Could not retrieve KYC data from Lite system", [
                    'username' => $username,
                    'response_status' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to copy KYC data from Lite: ' . $e->getMessage(), [
                'username' => $username,
                'pro_user_id' => $proUser->id
            ]);
        }
    }

    // Download KYC files from Lite system
    private function downloadKycFilesFromLite($username, $kycData)
    {
        try {
            $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/download-user-kyc-files';
            
            $response = Http::timeout(30)->post($liteApiUrl, [
                'username' => $username,
                'kyc_data' => json_encode($kycData)
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['success'] && isset($responseData['files'])) {
                    foreach ($responseData['files'] as $fileData) {
                        $this->saveKycFileToProSystem($fileData);
                    }
                    
                    Log::info("KYC files downloaded from Lite to Pro", [
                        'username' => $username,
                        'files_count' => count($responseData['files'])
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to download KYC files from Lite: ' . $e->getMessage(), [
                'username' => $username
            ]);
        }
    }

    // Save KYC files in Pro system
    private function saveKycFileToProSystem($fileData)
    {
        try {
            if (isset($fileData['content'], $fileData['filename'], $fileData['path'])) {
                $filePath = $fileData['path'];
                $filename = basename($filePath);
                $subdirectories = dirname($filePath);
                
                $destinationPath = public_path('assets/verify/' . $subdirectories);
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $fullPath = $destinationPath . '/' . $filename;
                file_put_contents($fullPath, base64_decode($fileData['content']));
                
                Log::info("KYC file saved in Pro system", [
                    'filename' => $filename,
                    'path' => $fullPath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save KYC file in Pro system: ' . $e->getMessage(), [
                'file_data' => $fileData
            ]);
        }
    }

    // Handle Pro referrer input
    private function determineProReferralPlacementWithInput($request)
    {
        $referredUserId = null;

            // Priority 1: Check if user provided a Pro referrer via input
            if (!empty($request->pro_referrer_username)) {
                $referredBy = User::where('username', $request->pro_referrer_username)->first();

                if ($referredBy) {
                    // No referral limit check for Pro accounts - unlimited referrals allowed
                    $referredUserId = $referredBy->id;
                    Log::info("Pro referrer placement: User placed under Pro referrer {$request->pro_referrer_username}");
                } else {
                    // Invalid referrer - place under company
                    $defaultUser = User::where('username', 'luxceylone')->first();
                    $referredUserId = $defaultUser ? $defaultUser->id : null;
                    Log::warning("Pro referrer placement: Invalid referrer {$request->pro_referrer_username}, placed under company");
                }
            }
            // Priority 2: Check if user came from Pro account (from_pro_account flag)
            else if (!empty($request->from_pro_account) && !empty($request->pro_referred_username)) {
                $referredBy = User::where('username', $request->pro_referred_username)->first();

                if ($referredBy) {
                    // No referral limit check for Pro accounts - unlimited referrals allowed
                    $referredUserId = $referredBy->id;
                    Log::info("Pro referrer placement: User came from Pro, placed under {$request->pro_referred_username}");
                }
            }
            // Priority 3: Use existing Lite referral if Pro referrer not found/specified
            else if (!empty($request->referred_user_id)) {
                // Check if the Lite referrer also exists in Pro system
                $liteReferrer = $this->getLiteUserById($request->referred_user_id);
                if ($liteReferrer) {
                    $proReferrer = User::where('email', $liteReferrer['email'])->first();
                    if ($proReferrer) {
                        // No referral limit check for Pro accounts - unlimited referrals allowed
                        $referredUserId = $proReferrer->id;
                        Log::info("Pro referrer placement: Using Lite referrer who exists in Pro");
                    }
                }
            }

            // Default: Place under company head
            if (!$referredUserId) {
                $defaultUser = User::where('username', 'luxceylone')->first();
                $referredUserId = $defaultUser ? $defaultUser->id : null;
                Log::info("Pro referrer placement: Default placement under company");
            }

            return $referredUserId;
    }

    private function determineProReferralPlacement($request)
    {
        $referredUserId = null;

        // Priority 1: Check if user came from Pro referral
        if ($request->pro_referrer_username) {
            $referredBy = User::where('username', $request->pro_referrer_username)->first();

            if ($referredBy) {
                // Check referral limits
                $directReferralsCount = User::where('referred_user_id', $referredBy->id)->count();

                if ($directReferralsCount < 2) {
                    $referredUserId = $referredBy->id;
                } else {
                    // Place under company head if referrer is full
                    $defaultUser = User::where('username', 'luxceylone')->first();
                    $referredUserId = $defaultUser ? $defaultUser->id : null;
                }
            }
        }

        // Priority 2: Use existing referral from Lite if no Pro referrer
        if (!$referredUserId && $request->referred_user_id) {
            // Check if the Lite referrer also exists in Pro system
            $liteReferrer = $this->getLiteUserById($request->referred_user_id);
            if ($liteReferrer) {
                $proReferrer = User::where('email', $liteReferrer['email'])->first();
                if ($proReferrer) {
                    $directReferralsCount = User::where('referred_user_id', $proReferrer->id)->count();
                    if ($directReferralsCount < 2) {
                        $referredUserId = $proReferrer->id;
                    }
                }
            }
        }

        // Default: Place under company head
        if (!$referredUserId) {
            $defaultUser = User::where('username', 'luxceylone')->first();
            $referredUserId = $defaultUser ? $defaultUser->id : null;
        }

        return $referredUserId;
    }

    private function getLiteUserById($userId)
    {
        try {
            $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/get-user-for-pro';

            $response = Http::timeout(10)->post($liteApiUrl, [
                'user_id' => $userId,
                'email' => '' // We'll need to modify this endpoint to accept user_id only
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['success']) {
                    return $responseData['data'];
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get Lite user by ID: ' . $e->getMessage());
            return null;
        }
    }

    // Regular API registration endpoint
    public function register(Request $request)
    {
        try {
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!gs('registration')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not allowed'
                ], 403);
            }

            // Remove referral limit check for Pro accounts - unlimited referrals allowed
            // The referral validation is removed since Pro accounts have unlimited referrals

            $user = $this->create($request->all());

            // Generate Pro referral link
            $proReferralLink = $this->generateProReferralLink($user->username);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'id' => $user->id,
                    'unique_id' => $user->unique_id,
                    'four_digit_unique_id' => $user->four_digit_unique_id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'username' => $user->username,
                    'pro_referral_link' => $proReferralLink
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Check if user can switch to Pro from Lite
    public function checkSwitchToPro(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lite_user_id' => 'required|integer',
                'lite_email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user already has Pro account
            $existingProUser = User::where('email', $request->lite_email)->first();

            if ($existingProUser) {
                return response()->json([
                    'success' => true,
                    'has_pro_account' => true,
                    'message' => 'User already has a Pro account',
                    'data' => [
                        'id' => $existingProUser->id,
                        'username' => $existingProUser->username,
                        'email' => $existingProUser->email
                    ]
                ], 200);
            }

            return response()->json([
                'success' => true,
                'has_pro_account' => false,
                'message' => 'User can create a Pro account'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking Pro account status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function validator(array $data)
    {
        $passwordValidation = Password::min(8);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        return Validator::make($data, [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => ['required', 'confirmed', $passwordValidation],
            'agree' => $agree,
            'referredby' => 'nullable|string',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required',
            'referredby.exists' => 'The referred user is invalid. Please check the referral username.'
        ]);
    }

    protected function create(array $data)
    {
        if (isset($data['referredby'])) {
            try {
                $referredByData = hex2bin($data['referredby']);

                // Check if it's a four-digit ID (Pro referral) or username (regular referral)
                if (is_numeric($referredByData) && strlen($referredByData) == 4) {
                    // It's a four-digit Pro referral
                    $referredBy = User::where('four_digit_unique_id', $referredByData)->first();
                } else {
                    // It's a username referral
                    $referredBy = User::where('username', $referredByData)->first();
                }

                if ($referredBy) {
                    // No referral limit check for Pro accounts - unlimited referrals allowed
                    $data['referred_user_id'] = $referredBy->id;
                } else {
                    $user = User::where('username', 'luxceylone')->first();
                    $data['referred_user_id'] = $user ? $user->id : null;
                }
            } catch (\Exception $e) {
                $user = User::where('username', 'luxceylone')->first();
                $data['referred_user_id'] = $user ? $user->id : null;
            }
        } else {
            $user = User::where('username', 'luxceylone')->first();
            $data['referred_user_id'] = $user ? $user->id : null;
        }

        $fourDigitUniqueId = $this->fourDigitGenerateUniqueId();

        $user = new User();
        $user->unique_id = $data['unique_id'];
        $user->four_digit_unique_id = $fourDigitUniqueId;
        $user->email = strtolower($data['email']);
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->username = $data['username'];
        $user->referred_user_id = $data['referred_user_id'];
        $user->password = Hash::make($data['password']);
        $user->kv = gs('kv') ? Status::NO : Status::YES;
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->ts = Status::DISABLE;
        $user->tv = Status::ENABLE;

        $user->save();

        if ($data['referred_user_id']) {
            ReferralLog::create([
                'referrer_id' => $data['referred_user_id'],
                'referred_id' => $user->id
            ]);
        }

        return $user;
    }

    protected function createFromLite(array $data, $referredUserId)
    {

        $username = $data['username'];
        $fourDigitUniqueId = $this->fourDigitGenerateUniqueId();

        $user = new User();
        $user->unique_id = $data['unique_id'];
        $user->four_digit_unique_id = $fourDigitUniqueId;
        $user->email = strtolower($data['email']);
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->username = $username;
        $user->referred_user_id = $referredUserId;

        // Generate random password - user will login through Lite system
        $user->password = Hash::make(Str::random(16));

        $user->kv = gs('kv') ? Status::NO : Status::YES;
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->ts = Status::DISABLE;
        $user->tv = Status::ENABLE;

        // Copy additional data from Lite
        $user->dial_code = $data['dial_code'] ?? null;
        $user->country_code = $data['country_code'] ?? null;
        $user->mobile = $data['mobile'] ?? null;
        $user->country_name = $data['country_name'] ?? null;
        $user->city = $data['city'] ?? null;
        $user->district = $data['district'] ?? null;
        $user->district_id = $data['district_id'] ?? null;
        $user->city_id = $data['city_id'] ?? null;
        $user->state = $data['state'] ?? null;
        $user->zip = $data['zip'] ?? null;
        $user->address = $data['address'] ?? null;

        $user->save();

        // Create referral log if there's a referrer
        if ($referredUserId) {
            ReferralLog::create([
                'referrer_id' => $referredUserId,
                'referred_id' => $user->id
            ]);
        }

        return $user;
    }

    private function updateLiteUserForProRegistration($liteUserId, $proUsername)
    {
        try {
            $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/update-user-for-pro';

            $response = Http::timeout(10)->post($liteApiUrl, [
                'user_id' => $liteUserId,
                'from_pro_account' => true,
                'pro_referred_username' => $proUsername
            ]);

            if (!$response->successful()) {
                Log::error('Failed to update Lite user via API: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Failed to update Lite user: ' . $e->getMessage());
        }
    }

    private function fourDigitGenerateUniqueId()
    {
        $isUnique = false;
        $fourDigitUniqueId = '';

        while (!$isUnique) {
            $fourDigitUniqueId = '';
            for ($i = 0; $i < 4; $i++) {
                $fourDigitUniqueId .= mt_rand(0, 9);
            }

            if ($fourDigitUniqueId[0] == '0') {
                $fourDigitUniqueId[0] = mt_rand(1, 9);
            }

            $exists = User::where('four_digit_unique_id', $fourDigitUniqueId)->exists();

            if (!$exists) {
                $isUnique = true;
            }
        }

        return $fourDigitUniqueId;
    }

    private function generateProReferralLink($username)
    {
        $baseUrl = env('ADD_CITI_LITE_API') . '/user/register?affiliated_by=';
        $encodedId = bin2hex($username);
        return $baseUrl . $encodedId;
    }



}
