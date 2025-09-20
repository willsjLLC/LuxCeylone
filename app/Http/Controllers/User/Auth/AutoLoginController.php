<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoLoginController extends Controller
{
    /**
     * Handle auto-login from token
     */
    public function autoLogin(Request $request)
    {
        $token = $request->get('auto_login');
        
        if (!$token) {
            return redirect()->route('user.login')->with('error', 'Invalid login token');
        }
        
        // Retrieve token data from cache
        $tokenData = cache("auto_login_token_{$token}");
        
        if (!$tokenData) {
            return redirect()->route('user.login')->with('error', 'Login token has expired or is invalid');
        }
        
        // Check if token has expired
        if (now()->gt($tokenData['expires_at'])) {
            cache()->forget("auto_login_token_{$token}");
            return redirect()->route('user.login')->with('error', 'Login token has expired');
        }
        
        // Get the user
        $user = User::find($tokenData['user_id']);
        
        if (!$user) {
            cache()->forget("auto_login_token_{$token}");
            return redirect()->route('user.login')->with('error', 'User not found');
        }
        
        // Check if user is active
        if ($user->status != 1) {
            cache()->forget("auto_login_token_{$token}");
            return redirect()->route('user.login')->with('error', 'Account is inactive');
        }
        
        // Login the user
        Auth::guard('web')->login($user, true); // 'true' for remember me
        
        // Update user verification status (similar to your existing login controller)
        $user->tv = $user->ts == 1 ? 0 : 1; // Adjust based on your Status constants
        $user->save();
        
        // Create login record
        $this->createLoginRecord($user, $request);
        
        // Clear the token
        cache()->forget("auto_login_token_{$token}");
        
        // Redirect to dashboard with success message
        return redirect()->route('user.product.index')->with('success', 'Welcome to LuxCeylone! You have been automatically logged in.');
    }
    
    /**
     * Create login record for tracking
     */
    private function createLoginRecord($user, $request)
    {
        try {
            $ip = $request->ip();
            
            // Check if we have existing location data for this IP
            $exist = UserLogin::where('user_ip', $ip)->first();
            
            $userLogin = new UserLogin();
            
            if ($exist) {
                $userLogin->longitude = $exist->longitude;
                $userLogin->latitude = $exist->latitude;
                $userLogin->city = $exist->city;
                $userLogin->country_code = $exist->country_code;
                $userLogin->country = $exist->country;
            } else {
                // You can implement IP geolocation here
                // For now, setting default values
                $userLogin->longitude = '0';
                $userLogin->latitude = '0';
                $userLogin->city = 'Unknown';
                $userLogin->country_code = 'Unknown';
                $userLogin->country = 'Unknown';
            }

            $userAgent = $request->header('User-Agent');
            $userAgentInfo = $this->parseUserAgent($userAgent);
            
            $userLogin->user_id = $user->id;
            $userLogin->user_ip = $ip;
            $userLogin->browser = $userAgentInfo['browser'];
            $userLogin->os = $userAgentInfo['os'];
            $userLogin->save();
            
        } catch (\Exception $e) {
            Log::error('Failed to create auto-login record: ' . $e->getMessage());
        }
    }
    
    /**
     * Parse user agent string
     */
    private function parseUserAgent($userAgent)
    {
        $browser = 'Unknown';
        $os = 'Unknown';
        
        // Browser detection
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            $browser = 'Opera';
        }
        
        // OS detection
        if (strpos($userAgent, 'Windows NT') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Mac OS X') !== false) {
            $os = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $os = 'iOS';
        }
        
        return ['browser' => $browser, 'os' => $os];
    }
}