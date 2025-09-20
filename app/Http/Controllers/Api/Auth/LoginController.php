<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Lib\SocialLogin;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Status;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
        parent::__construct();
        $this->username = $this->findUsername();
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'remark' => 'validation_error',
                    'status' => 'error',
                    'message' => ['error' => $validator->errors()->all()],
                ]);
            }

            $user = User::where('username', $request->username)->first();

            $remember_token = Str::random(60);
            $user->remember_token = $remember_token;
            $user->save();

            if (!$user) {
                return response()->json([
                    'remark' => 'user_not_found',
                    'status' => 'error',
                    'message' => ['error' => ['User not found']],
                ]);
            }

            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            return response()->json([
                'remark' => 'login_success',
                'status' => 'success',
                'message' => ['success' => ['Login Successful']],
                'remember_token' => $remember_token,
                'data' => [
                    'user' => $user,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => ['error' => ['Internal server error']],
            ], 500);
        }
    }
    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {
        $validationRule = [
            $this->username() => 'required|string'
        ];

        $validate = Validator::make($request->all(), $validationRule);
        return $validate;
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        $notify[] = 'Logout Successful';
        return response()->json([
            'remark' => 'logout',
            'status' => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }

    public function checkToken(Request $request)
    {
        $validationRule = [
            'token' => 'required',
        ];

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
        $exists = PersonalAccessToken::where('token', hash('sha256', $request->token))->exists();
        if ($exists) {
            $notify[] = 'Token exists';
            return response()->json([
                'remark' => 'token_exists',
                'status' => 'success',
                'message' => ['success' => $notify],
            ]);
        }
        $notify[] = 'Token doesn\'t exists';
        return response()->json([
            'remark' => 'token_not_exists',
            'status' => 'error',
            'message' => ['success' => $notify],
        ]);
    }

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:google,facebook,linkedin',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $provider = $request->provider;
        $socialLogin = new SocialLogin($provider, true);
        try {
            $loginResponse = $socialLogin->login();
            $response[] = 'Login Successful';
            return response()->json([
                'remark' => 'login_success',
                'status' => 'success',
                'message' => ['success' => $response],
                'data' => $loginResponse,
            ]);
        } catch (\Exception $e) {
            $notify[] = $e->getMessage();
            return response()->json([
                'remark' => 'login_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }
}
