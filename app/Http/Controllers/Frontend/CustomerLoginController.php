<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class CustomerLoginController extends Controller
{
    public function customerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
        // dd($credentials);

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::guard('customer')->user()->role !== 'Customer') {
                Auth::guard('customer')->logout();
                return back()->with('error', 'Only customers can login here.');
            }

            return redirect()->route('user_dashboard')
                ->with('success', 'Welcome back, customer!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    public function customerLogout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landingPage')
            ->with('success', 'You have been logged out successfully.');
    }



    // ---------------- Google Auth ----------------

    // Redirect to Google
    public function redirect()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    // Handle callback
    public function callback()
    {
        try {
            // Configure Guzzle to handle SSL for local development
            $guzzleOptions = [];
            if (config('app.env') === 'local') {
                $guzzleOptions = [
                    'verify' => false, // Disable SSL verification for local development
                ];
            }

            $googleUser = Socialite::driver('google')
                ->setHttpClient(new Client($guzzleOptions))
                ->user();

            // Log for debugging
            \Log::info('Google User Data:', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ]);

            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'password'  => Hash::make(str()->random(16)),
                    'phone'     => '0000000000', // Default phone for Google users
                    'role'      => 'Customer',
                    'google_id' => $googleUser->getId(),
                    'image'     => $googleUser->getAvatar(),
                ]);

                \Log::info('New user created from Google:', ['user_id' => $user->id]);
            } else {
                // Update existing user if needed
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->image     = $googleUser->getAvatar();
                    $user->save();
                }

                \Log::info('Existing user found:', ['user_id' => $user->id]);
            }

            // Login the user
            Auth::guard('customer')->login($user, true);

            // Regenerate session to prevent session fixation
            request()->session()->regenerate();

            \Log::info('User logged in successfully:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_authenticated' => Auth::guard('customer')->check(),
            ]);

            return redirect()->route('user_dashboard')
                ->with('success', 'Logged in with Google!');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Google login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('landingPage')
                ->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}
