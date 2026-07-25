<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Masukan Email',
            'password.required' => 'Masukan Password',
        ]);
        
        $kredensil = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            // Verify credentials without logging in immediately if 2FA enabled
            if (\Illuminate\Support\Facades\Hash::check($request->password, $user->getRawOriginal('password'))) {
                if ($user->hasTwoFactorEnabled()) {
                    session([
                        '2fa:user_id'  => $user->id,
                        '2fa:remember' => $request->boolean('remember'),
                    ]);
                    audit_log('Initiated 2FA challenge for email: ' . $request->email, 'auth.2fa', 'auth');

                    return response()->json([
                        'success'    => true,
                        'two_factor' => true,
                        'message'    => 'Please complete 2FA verification.',
                        'redirect'   => route('two-factor.challenge'),
                    ]);
                }

                Auth::login($user, $request->boolean('remember'));
                audit_log('User logged in successfully', 'auth.login', 'auth');
                return response()->json([
                    'success'  => true,
                    'message'  => 'Selamat Datang di Panel Administrator',
                    'redirect' => route('root'),
                ]);
            } else {
                audit_log('Failed login attempt for email: ' . $request->email, 'auth.failed', 'auth');
                return response()->json([
                    'success'  => false,
                    'message'  => 'Email atau Password yang Anda masukan salah. Silakan coba lagi.',
                    'redirect' => route('root'),
                ]);
            }
        } else {
            audit_log('Failed login attempt for non-existent email: ' . $request->email, 'auth.failed', 'auth');
            return response()->json([
                'success'  => false,
                'message'  => 'Email atau Password yang Anda masukan salah. Silakan coba lagi.',
                'redirect' => route('root'),
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        if (Auth::check()) {
            audit_log('User logged out', 'auth.logout', 'auth');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('root'))->with('success','');
    }
}