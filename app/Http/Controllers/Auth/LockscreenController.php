<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockscreenController extends Controller
{
    public function lock(Request $request)
    {
        session(['user_is_locked' => true]);
        return redirect()->route('lockscreen');
    }

    public function show(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!session('user_is_locked')) {
            return redirect()->route('root');
        }

        return view('auth.lockscreen');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            session(['user_is_locked' => false]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Screen unlocked!',
                    'redirect' => route('root')
                ]);
            }

            return redirect()->intended(route('root'));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.'
            ], 422);
        }

        return back()->withErrors(['password' => 'Incorrect password. Please try again.']);
    }
}
