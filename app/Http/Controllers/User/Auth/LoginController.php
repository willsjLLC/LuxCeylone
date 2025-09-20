<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Status;
use Illuminate\Support\Facades\Validator;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $username;
    protected $maxAttempts = 3; // Maximum login attempts before lockout
    protected $initialLockoutHours = 1; // Initial lockout duration in hours
    protected $escalatedLockoutHours = 6; // Escalated lockout duration in hours

    public function __construct()
    {
        parent::__construct();
        $this->username = $this->findUsername();
    }

    public function showLoginForm()
    {
        $pageTitle = "Login";
        Intended::identifyRoute();
        return view('Template::user.auth.login', compact('pageTitle'));
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // Get throttle key for current user/request
        $throttleKey = $this->throttleKey($request);

        // Check if user is already locked out
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            // Reset login attempts on successful login
            $this->clearLoginAttempts($request);

            // Reset the lockout history on successful login
            Cache::forget("lockout_history_{$throttleKey}");

            return $this->sendLoginResponse($request);
        }

        // Increment login attempts
        $this->incrementLoginAttempts($request);

        // Get remaining attempts before lockout
        $attempts = $this->limiter()->attempts($throttleKey);

        // Determine message based on attempts
        if ($attempts >= $this->maxAttempts) {
            // User has reached max attempts - determine lockout duration
            $lockoutCount = Cache::get("lockout_history_{$throttleKey}", 0);
            $lockoutHours = $lockoutCount > 0 ? $this->escalatedLockoutHours : $this->initialLockoutHours;

            // Increment lockout count for next time
            Cache::put("lockout_history_{$throttleKey}", $lockoutCount + 1, now()->addDays(7));

            // Override the default decay minutes with our custom duration
            $this->limiter()->clear($throttleKey); // Clear existing rate limits
            $this->limiter()->hit($throttleKey, $lockoutHours * 60); // Set new rate limit

            $notify[] = ['error', "Too many failed login attempts. Please try again after {$lockoutHours} hour" . ($lockoutHours > 1 ? 's' : '') . "."];
        } else {
            $remaining = $this->maxAttempts - $attempts;
            $notify[] = ['warning', "Invalid username or password. You have {$remaining} attempts remaining before lockout."];
        }

        Intended::reAssignSession();
        return back()->withNotify($notify);
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Override the default rate limiting method to customize attempts count
     */
    protected function maxAttempts()
    {
        return $this->maxAttempts;
    }

    /**
     * Get the current lockout duration in minutes
     *
     * @param string $key
     * @return int
     */
    protected function decayMinutes($key = null)
    {
        if ($key) {
            $lockoutCount = Cache::get("lockout_history_{$key}", 0);
            return $lockoutCount > 0 ? $this->escalatedLockoutHours * 60 : $this->initialLockoutHours * 60;
        }

        return $this->initialLockoutHours * 60;
    }

    /**
     * Customize the response for locked out users
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));
        $minutes = ceil($seconds / 60);
        $hours = ceil($minutes / 60);

        $message = $hours > 1
            ? "Too many login attempts. Please try again after {$hours} hours."
            : "Too many login attempts. Please try again after {$minutes} minutes.";

        $notify[] = ['error', $message];

        return back()
            ->withNotify($notify)
            ->withInput($request->only($this->username(), 'remember'));
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

    protected function validateLogin($request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            Intended::reAssignSession();
            $validator->validate();
        }
    }

    public function logout()
    {
        $this->guard()->logout();
        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('user.login')->withNotify($notify);
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

        $redirection = Intended::getRedirection();

        session()->forget(['role', 'category', 'subCategory', 'registerStatus']);

        return $redirection ? $redirection : to_route('user.product.index');
    }
}
