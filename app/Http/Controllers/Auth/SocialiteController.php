<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect user to social provider login page.
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            return redirect()->route('login')->with('error', 'Unsupported OAuth provider.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            Log::error("Socialite redirect error for {$provider}: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'OAuth configuration for ' . ucfirst($provider) . ' is not yet configured in your .env file (CLIENT_ID & CLIENT_SECRET required).');
        }
    }

    /**
     * Handle social provider callback.
     */
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'github'])) {
            return redirect()->route('login')->with('error', 'Unsupported OAuth provider.');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Extract email or generate fallback for GitHub private emails
            $email = $socialUser->getEmail();
            if (!$email) {
                $nickname = $socialUser->getNickname();
                $email = $nickname ? $nickname . '@github.noreply.com' : $socialUser->getId() . '@' . $provider . '.local';
            }

            if (Auth::check()) {
                // Currently logged in user (or locked session) linking their social account
                $user = Auth::user();
                $user->update([
                    'provider_name'  => $provider,
                    'provider_id'    => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                ]);
            } else {
                // Find existing user by provider_id or email
                $user = User::where('provider_name', $provider)
                    ->where('provider_id', $socialUser->getId())
                    ->first();

                if (!$user) {
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser) {
                        $existingUser->update([
                            'provider_name'  => $provider,
                            'provider_id'    => $socialUser->getId(),
                            'provider_token' => $socialUser->token,
                        ]);
                        $user = $existingUser;
                    } else {
                        $userName = $socialUser->getName() ?? $socialUser->getNickname() ?? 'OAuth User';

                        $user = User::create([
                            'name'              => $userName,
                            'email'             => $email,
                            'password'          => Hash::make(Str::random(24)),
                            'email_verified_at' => now(),
                            'provider_name'     => $provider,
                            'provider_id'       => $socialUser->getId(),
                            'provider_token'    => $socialUser->token,
                        ]);

                        $userRole = Role::where('name', 'User')->first();
                        if ($userRole) {
                            $user->roles()->attach($userRole->id);
                        }
                    }
                }

                Auth::login($user, true);
            }

            session()->forget('user_is_locked');
            session(['user_is_locked' => false]);
            session()->save();

            audit_log('Logged in / Unlocked via ' . ucfirst($provider) . ' OAuth');
            send_notification('Social Login', 'You successfully logged in using ' . ucfirst($provider) . '.');

            return redirect()->route('v1.dashboard');

        } catch (\Exception $e) {
            Log::error("Socialite callback error for {$provider}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('login')->with('error', 'Authentication failed with ' . ucfirst($provider) . ': ' . $e->getMessage());
        }
    }
}
