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
        if(!empty($user)){
            if(Auth::attempt($kredensil)){
                return response()->json([
                    'success' => true,
                    'message' => 'Selamat Datang di Panel Administrator',
                    'redirect' => route('root'),
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau Password yang Anda masukan salah. Silakan coba lagi.',
                    'redirect' => route('root'),
                ]);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password yang Anda masukan salah. Silakan coba lagi.',
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
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('root'))->with('success','');
    }
}