<?php
namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\ReferralLog;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    // public function showRegistrationForm()
    // {
    //     $pageTitle = "Register Pro";
    //     Intended::identifyRoute();
    //     $register = true;

    //     // Handle different registration scenarios
    //     $fromLite = false;
    //     $liteUserData = null;
    //     $referredBy = null;
    //     $skipPasswordFields = false;
    //     $proReferrerInput = null; // NEW: For Pro referrer input

    //     if (request()->has('affiliated_by')) {
    //         session(['referred_by' => request('affiliated_by')]);
            
    //         // Decode the referrer username/ID
    //         try {
    //             $referredBy = hex2bin(request('affiliated_by'));
                
    //             // Check if it's a 4-digit Pro ID
    //             if (is_numeric($referredBy) && strlen($referredBy) == 4) {
    //                 $proUser = User::where('four_digit_unique_id', $referredBy)->first();
    //                 if ($proUser) {
    //                     $proReferrerInput = $proUser->username;
    //                 }
    //             } else {
    //                 // It's a username
    //                 $proReferrerInput = $referredBy;
    //             }
    //         } catch (\Exception $e) {
    //             $referredBy = null;
    //             $proReferrerInput = null;
    //         }
    //     } else {
    //         session()->forget('referred_by');
    //     }

    //     // Check if coming from Lite account
    //     if (request()->has('from_lite') && request()->has('lite_user_id') && request()->has('lite_email')) {
    //         $fromLite = true;
    //         $skipPasswordFields = true;
    //         $liteUserData = $this->getLiteUserData(request('lite_user_id'), request('lite_email'));
            
    //         if (!$liteUserData) {
    //             $notify[] = ['error', 'Unable to retrieve Lite account data. Please try again.'];
    //             return redirect()->route('user.login')->withNotify($notify);
    //         }

    //         // NEW: Check if Lite user has existing Pro referrer preference
    //         if (!empty($liteUserData['from_pro_account']) && !empty($liteUserData['pro_referred_username'])) {
    //             $proReferrerInput = $liteUserData['pro_referred_username'];
    //         }
    //     }

    //     return view('Template::user.auth.register', compact(
    //         'pageTitle', 
    //         'register', 
    //         'fromLite', 
    //         'liteUserData', 
    //         'referredBy',
    //         'skipPasswordFields',
    //         'proReferrerInput' // NEW: Pass Pro referrer input to view
    //     ));
    // }
    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        Intended::identifyRoute();
        $register = true;

        if (request()->has('affiliated_by')) {
        session(['referred_by' => request('affiliated_by')]);
        } else {
            session()->forget('referred_by');
        }
        return view('Template::user.auth.register', compact('pageTitle', 'register'));
    }
    
    // Get user data from Lite system 
    // private function getLiteUserData($userId, $email)
    // {
    //     try {
    //         $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/get-user-for-pro';
            
            
    //         $response = Http::timeout(10)->post($liteApiUrl, [
    //             'user_id' => $userId,
    //             'email' => $email
    //         ]);

    //         if ($response->successful()) {
    //             $responseData = $response->json();
    //             if ($responseData['success']) {
    //                 return $responseData['data'];
    //             }
    //         }
    //         return null;
    //     } catch (\Exception $e) {
    //         Log::error('Failed to get Lite user data: ' . $e->getMessage());
    //         return null;
    //     }
    // }

    // Check if Pro referrer exists - API endpoint for Lite system
    // public function checkProReferrer(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $user = User::where('username', $request->username)->first();

    //     return response()->json([
    //         'success' => true,
    //         'exists' => $user ? true : false,
    //         'data' => $user ? [
    //             'id' => $user->id,
    //             'username' => $user->username,
    //             'firstname' => $user->firstname,
    //             'lastname' => $user->lastname,
    //             'email' => $user->email,
    //             'referral_count' => User::where('referred_user_id', $user->id)->count(), // For display only
    //             'has_unlimited_referrals' => true // Indicate Pro accounts have unlimited referrals
    //         ] : null
    //     ]);
    // }

    // protected function validator(array $data)
    // {
    //     $passwordValidation = Password::min(8);

    //     if (gs('secure_password')) {
    //         $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
    //     }

    //     $agree = 'nullable';
    //     if (gs('agree')) {
    //         $agree = 'required';
    //     }

    //     $rules = [
    //         'firstname' => 'required',
    //         'lastname' => 'required',
    //         'email' => 'required|string|email|unique:users',
    //         'agree' => $agree,
    //         'referredby' => 'nullable|string',
    //         'pro_referrer_input' => 'nullable|string|max:50', // NEW: Pro referrer input validation
    //         'lite_user_id' => 'nullable|integer',
    //         'from_lite' => 'nullable|boolean'
    //     ];

    //     // Password is not required if coming from Lite
    //     if (!isset($data['from_lite']) || !$data['from_lite']) {
    //         $rules['password'] = ['required', 'confirmed', $passwordValidation];
    //     }

    //     $messages = [
    //         'firstname.required' => 'The first name field is required',
    //         'lastname.required' => 'The last name field is required',
    //         'email.unique' => 'This email is already registered in our Pro system',
    //         'pro_referrer_input.max' => 'Pro referrer input cannot exceed 50 characters',
    //         'referredby.exists' => 'The referred user is invalid. Please check the referral username.'
    //     ];

    //     return Validator::make($data, $rules, $messages);
    // }
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

        $validate = Validator::make($data, [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => ['required', 'confirmed', $passwordValidation],
            'captcha' => 'sometimes|required',
            'agree' => $agree,
            'referredby' => 'nullable|string',   // Changed to string to accept hex value
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required',
            'referredby.exists' => 'The referred user is invalid. Please check the referral username.'
        ]);

        return $validate;
    }

    // public function register(Request $request)
    // {
    //     if (!gs('registration')) {
    //         $notify[] = ['error', 'Registration not allowed'];
    //         return back()->withNotify($notify);
    //     }

    //     $this->validator($request->all())->validate();

    //     $request->session()->regenerateToken();

    //     // Skip captcha verification for Lite users
    //     if (!$request->from_lite && !verifyCaptcha()) {
    //         $notify[] = ['error', 'Invalid captcha provided'];
    //         return back()->withNotify($notify);
    //     }

    //     // Remove Pro referrer input validation for referral limits - Pro accounts have unlimited referrals
    //     if ($request->filled('pro_referrer_input')) {
    //         $proReferrer = User::where('username', $request->pro_referrer_input)
    //                         ->orWhere('four_digit_unique_id', $request->pro_referrer_input)
    //                         ->first();
            
    //         if (!$proReferrer) {
    //             $notify[] = ['error', 'The Pro referrer you entered does not exist'];
    //             return back()->withNotify($notify)->withInput();
    //         }
    //         // Remove referral limit check - Pro accounts can have unlimited referrals
    //     }

    //     // Handle regular referral logic for Pro registration (from URL) - remove limit check
    //     if (isset($request['referredby'])) {
    //         try {
    //             $referredByData = hex2bin($request['referredby']);
                
    //             // Check if it's a 4-digit Pro ID or username
    //             if (is_numeric($referredByData) && strlen($referredByData) == 4) {
    //                 $referredBy = User::where('four_digit_unique_id', $referredByData)->first();
    //             } else {
    //                 $referredBy = User::where('username', $referredByData)->first();
    //             }

    //             // Remove referral limit check for Pro accounts - unlimited referrals allowed
    //             if (!$referredBy) {
    //                 $notify[] = ['error', 'Invalid referral link - referrer not found'];
    //                 return back()->withNotify($notify);
    //             }
    //         } catch (\Exception $e) {
    //             $notify[] = ['error', 'Invalid referral link format'];
    //             return back()->withNotify($notify);
    //         }
    //     }

    //     // Create Pro user
    //     $user = $this->create($request->all());

    //     // If coming from Lite, update the Lite account
    //     if ($request->from_lite && $request->lite_user_id) {
    //         $this->updateLiteUserForProRegistration($request->lite_user_id, $user->username);
    //     }

    //     event(new Registered($user));
    //     $this->guard()->login($user);

    //     // Log successful registration
    //     Log::info("Pro account created via web", [
    //         'user_id' => $user->id,
    //         'email' => $user->email,
    //         'from_lite' => $request->from_lite ?? false,
    //         'lite_user_id' => $request->lite_user_id ?? null,
    //         'pro_referrer_input' => $request->pro_referrer_input ?? null,
    //         'referred_by_url' => $request->referredby ?? null
    //     ]);

    //     $notify[] = ['success', 'Pro account created successfully! Welcome to luxceylone Pro.'];
        
    //     return $this->registered($request, $user) ?: redirect($this->redirectPath())->withNotify($notify);
    // }

     public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }

        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // Check referral limits before creating the user
        // if (isset($request['referredby'])) {
        //     try {
        //         $referredByUsername = hex2bin($request['referredby']);
        //         $referredBy = User::where('username', $referredByUsername)->first();

        //         if ($referredBy) {
        //             // Check if this user has already reached the limit of 2 direct referrals
        //             $directReferralsCount = User::where('referred_user_id', $referredBy->id)->count();

        //             if ($directReferralsCount >= 2) {
        //                 $notify[] = ['error', 'This user has reached the maximum number of direct referrals'];
        //                 return back()->withNotify($notify);
        //             }
        //         }
        //     } catch (\Exception $e) {
        //         $notify[] = ['error', 'Invalid referral link format'];
        //         return back()->withNotify($notify);
        //     }
        // }

        // If we reach here, the referral check passed
        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    // Update Lite user when Pro registration is completed
    // private function updateLiteUserForProRegistration($liteUserId, $proUsername)
    // {
    //     try {
    //         $liteApiUrl = env('ADD_CITI_LITE_API') . '/api/update-user-for-pro';
            
    //         $response = Http::timeout(10)->post($liteApiUrl, [
    //             'user_id' => $liteUserId,
    //             'from_pro_account' => true,
    //             'pro_referred_username' => $proUsername
    //         ]);

    //         if (!$response->successful()) {
    //             Log::error('Failed to update Lite user via API: ' . $response->body());
    //         }
    //     } catch (\Exception $e) {
    //         // Log error but don't fail the registration
    //         Log::error('Failed to update Lite user: ' . $e->getMessage());
    //     }
    // }

    // protected function create(array $data)
    // {
    //     // Handle referral logic with priority system
    //     $referredUserId = null;
        
    //     // Priority 1: Pro referrer from input field (highest priority)
    //     if (!empty($data['pro_referrer_input'])) {
    //         $proReferrer = User::where('username', $data['pro_referrer_input'])
    //                         ->orWhere('four_digit_unique_id', $data['pro_referrer_input'])
    //                         ->first();
            
    //         if ($proReferrer) {
    //             // No referral limit check for Pro accounts - unlimited referrals allowed
    //             $referredUserId = $proReferrer->id;
    //             Log::info("Web registration: Placed under Pro referrer input", [
    //                 'pro_referrer_input' => $data['pro_referrer_input'],
    //                 'referrer_id' => $proReferrer->id
    //             ]);
    //         }
    //     }
        
    //     // Priority 2: URL referral (if no Pro referrer input)
    //     if (!$referredUserId && isset($data['referredby'])) {
    //         try {
    //             $referredByData = hex2bin($data['referredby']);
                
    //             // Check if it's a 4-digit Pro ID or username
    //             if (is_numeric($referredByData) && strlen($referredByData) == 4) {
    //                 $referredBy = User::where('four_digit_unique_id', $referredByData)->first();
    //             } else {
    //                 $referredBy = User::where('username', $referredByData)->first();
    //             }

    //             if ($referredBy) {
    //                 // No referral limit check for Pro accounts - unlimited referrals allowed
    //                 $referredUserId = $referredBy->id;
    //                 Log::info("Web registration: Placed under URL referrer", [
    //                     'url_referrer' => $referredByData,
    //                     'referrer_id' => $referredBy->id
    //                 ]);
    //             }
    //         } catch (\Exception $e) {
    //             Log::warning("Web registration: Invalid URL referrer format", [
    //                 'referredby' => $data['referredby'] ?? null
    //             ]);
    //         }
    //     }

    //     // Default: Place under company head if no valid referrer
    //     if (!$referredUserId) {
    //         $defaultUser = User::where('username', 'luxceylone')->first();
    //         $referredUserId = $defaultUser ? $defaultUser->id : null;
    //         Log::info("Web registration: Placed under company default", [
    //             'company_user_id' => $referredUserId
    //         ]);
    //     }

    //     // Generate unique IDs
    //     $uniqueId = $this->generateUniqueId();
    //     $fourDigitUniqueId = $this->fourDigitGenerateUniqueId();

    //     // Generate unique username if not provided
    //     $username = $data['username'] ?? $this->generateUniqueUsername($data['firstname']);

    //     // Create Pro user
    //     $user = new User();
    //     $user->unique_id = $uniqueId;
    //     $user->four_digit_unique_id = $fourDigitUniqueId;
    //     $user->email = strtolower($data['email']);
    //     $user->firstname = $data['firstname'];
    //     $user->lastname = $data['lastname'];
    //     $user->username = $username;
    //     $user->referred_user_id = $referredUserId;
        
    //     // Set password (use a default if coming from Lite)
    //     if (isset($data['password'])) {
    //         $user->password = Hash::make($data['password']);
    //     } else {
    //         // Generate a random password for Lite users - they'll use Lite login
    //         $user->password = Hash::make(Str::random(16));
    //     }
        
    //     $user->kv = gs('kv') ? Status::NO : Status::YES;
    //     $user->ev = gs('ev') ? Status::NO : Status::YES;
    //     $user->sv = gs('sv') ? Status::NO : Status::YES;
    //     $user->ts = Status::DISABLE;
    //     $user->tv = Status::ENABLE;

    //     // Copy additional data from Lite if available
    //     if (isset($data['dial_code'])) $user->dial_code = $data['dial_code'];
    //     if (isset($data['country_code'])) $user->country_code = $data['country_code'];
    //     if (isset($data['mobile'])) $user->mobile = $data['mobile'];
    //     if (isset($data['country_name'])) $user->country_name = $data['country_name'];
    //     if (isset($data['city'])) $user->city = $data['city'];
    //     if (isset($data['district'])) $user->district = $data['district'];
    //     if (isset($data['district_id'])) $user->district_id = $data['district_id'];
    //     if (isset($data['city_id'])) $user->city_id = $data['city_id'];
    //     if (isset($data['state'])) $user->state = $data['state'];
    //     if (isset($data['zip'])) $user->zip = $data['zip'];
    //     if (isset($data['address'])) $user->address = $data['address'];

    //     $user->save();

    //     // Create referral log if there's a referrer
    //     if ($referredUserId) {
    //         ReferralLog::create([
    //             'referrer_id' => $referredUserId,
    //             'referred_id' => $user->id
    //         ]);
    //     }

    //     return $user;
    // }

    protected function create(array $data)
    {
        if (isset($data['referredby'])) {
            // Decode the hexadecimal referred_by value
            try {
                $referredByUsername = hex2bin($data['referredby']);
                $referredBy = User::where('username', $referredByUsername)->first();

                if ($referredBy) {
                    $data['referred_user_id'] = $referredBy->id;
                } else {
                    // Default referral if referrer not found
                    $user = User::where('username', 'luxceylone')->first();
                    $data['referred_user_id'] = $user ? $user->id : null;
                }
            } catch (\Exception $e) {
                // Default referral if any exception occurs
                $user = User::where('username', 'luxceylone')->first();
                $data['referred_user_id'] = $user ? $user->id : null;
            }
        } else {
            $user = User::where('username', 'luxceylone')->first();
            $data['referred_user_id'] = $user ? $user->id : null;
        }

        // Generate a unique 16-digit ID
        $uniqueId = $this->generateUniqueId();
         $fourDigitUniqueId = $this->fourDigitGenerateUniqueId();

        // User Create
        $user = new User();
        $user->unique_id = $uniqueId;
        $user->four_digit_unique_id = $fourDigitUniqueId;
        $user->email = strtolower($data['email']);
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        // $user->username = $data['username'] ?? strtolower($data['firstname'] . rand(1000, 9999));
        $user->referred_user_id = $data['referred_user_id'];
        $user->password = Hash::make($data['password']);
        $user->kv = gs('kv') ? Status::NO : Status::YES;
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->ts = Status::DISABLE;
        $user->tv = Status::ENABLE;
        $user->save();

        // Create a referral log entry if there's a referrer
        if ($data['referred_user_id']) {
            ReferralLog::create([
                'referrer_id' => $data['referred_user_id'],
                'referred_id' => $user->id
            ]);
        }


        return $user;
    }


    private function generateUniqueId()
    {
        $isUnique = false;
        $uniqueId = '';

        while (!$isUnique) {
            $uniqueId = '';
            for ($i = 0; $i < 16; $i++) {
                $uniqueId .= mt_rand(0, 9);
            }

            if ($uniqueId[0] == '0') {
                $uniqueId[0] = mt_rand(1, 9);
            }

            $exists = User::where('unique_id', $uniqueId)->exists();

            if (!$exists) {
                $isUnique = true;
            }
        }

        return $uniqueId;
    }

    private function fourDigitGenerateUniqueId()
    {
        $isUnique = false;
        $fourDigitUniqueId = '';

        while (!$isUnique) {
            // Generate a random 4-digit number
            $fourDigitUniqueId = '';
            for ($i = 0; $i < 4; $i++) {
                $fourDigitUniqueId .= mt_rand(0, 9);
            }

            // Make sure it starts with a non-zero digit
            if ($fourDigitUniqueId[0] == '0') {
                $fourDigitUniqueId[0] = mt_rand(1, 9);
            }

            // Check if this ID already exists in the database
            $exists = User::where('four_digit_unique_id', $fourDigitUniqueId)->exists();

            if (!$exists) {
                $isUnique = true;
            }
        }

        return $fourDigitUniqueId;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        
        if ($request->email) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile', $request->mobile)->where('dial_code', $request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username', $request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        
        return response($exist);
    }

    public function registered()
    {
        return to_route('user.product.index');
    }


    // google login functions
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }


    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            // Check if user exists
            $user = User::where('email', $googleUser->email)->first();
            $uniqueId = $this->generateUniqueId();
            $fourDigitUniqueId = $this->fourDigitGenerateUniqueId();

            if (!$user) {
                $referred_by = null;
                // check if request consists referred id
                if (session()->has('referred_by')) {
                    try {
                        $referredByUsername = hex2bin(session('referred_by'));
                        $referredBy = User::where('username', $referredByUsername)->first();

                        if ($referredBy) {
                            $directReferralsCount = User::where('referred_user_id', $referredBy->id)->count();

                            if ($directReferralsCount >= 2) {
                            } else {
                                $referred_by = $referredBy->id;
                            }
                        } else {
                            $defaultUser = User::where('username', 'luxceylone')->first();
                            $referred_by = $defaultUser ? $defaultUser->id : null;
                        }
                    } catch (\Exception $e) {
                        $notify[] = ['error', 'Invalid referral link format'];
                        return back()->withNotify($notify);
                    }
                    session()->forget('referred_by');
                } else {
                    $defaultUser = User::where('username', 'luxceylone')->first();
                    $referred_by = $defaultUser ? $defaultUser->id : null;
                }

                // Create new user
                $user = User::create([
                    'firstname'         => $googleUser->user['given_name'] ?? explode(' ', $googleUser->name)[0],
                    'lastname'          => $googleUser->user['family_name'] ?? (explode(' ', $googleUser->name)[1] ?? ''),
                    'referred_user_id'  => $referred_by ? $referred_by : null,
                    'email'             => $googleUser->email,
                    'unique_id'         => $uniqueId,
                    'four_digit_unique_id' =>$fourDigitUniqueId,
                    'password'          => Hash::make(Str::random(16)),
                    'kv'                => gs('kv') ? Status::NO : Status::YES,
                    'ev'                => Status::YES, // Email already verified by Google
                    'sv'                => gs('sv') ? Status::NO : Status::YES,
                    'ts'                => Status::DISABLE,
                    'tv'                => Status::ENABLE,
                    'role'              => Status::CUSTOMER,
                ]);

                if ($referred_by) {
                    ReferralLog::create([
                        'referrer_id' => $referred_by,
                        'referred_id' => $user->id
                    ]);
                }

                // Create admin notification
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'New member registered via Google';
                $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
                $adminNotification->save();

                // Create login log
                $this->createLoginLog($user);
            }

            Auth::login($user, true);

            Intended::reAssignSession();

            return to_route('user.product.index');

        } catch (\Throwable $e) {
            $notify[] = ['error', 'Google authentication failed: ' . $e->getMessage()];
            return redirect()->route('user.login')->withNotify($notify);
        }
    }

    private function createLoginLog($user)
    {
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude = @implode(',', $info['long']);
            $userLogin->latitude = @implode(',', $info['lat']);
            $userLogin->city = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country = @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }
}