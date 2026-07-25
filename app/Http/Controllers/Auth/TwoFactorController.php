<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    /**
     * Generate 2FA secret & QR Code for user setup.
     */
    public function generate2FA(Request $request, TwoFactorService $twoFactorService)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $secret = $twoFactorService->generateSecret();
        $recoveryCodes = $twoFactorService->generateRecoveryCodes();
        $qrCodeSvg = $twoFactorService->getQrCodeSvg(config('app.name', 'Silva Kit'), $user->email, $secret);

        // Save secret temporarily in session until confirmed
        session(['2fa_secret' => $secret, '2fa_recovery' => $recoveryCodes]);

        return response()->json([
            'success'        => true,
            'message'        => '2FA setup key generated successfully.',
            'redirect'       => route('v1.profile.index'),
            'secret'         => $secret,
            'qr_code_svg'    => $qrCodeSvg,
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Confirm 2FA with 6-digit TOTP code to activate.
     */
    public function confirm2FA(Request $request, TwoFactorService $twoFactorService)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $secret = session('2fa_secret');
        $recoveryCodes = session('2fa_recovery', []);

        if (!$secret || !$twoFactorService->verifyCode($secret, $request->code)) {
            return response()->json([
                'success'  => false,
                'message'  => 'Invalid 6-digit confirmation code. Please try again.',
                'redirect' => route('v1.profile.index')
            ], 422);
        }

        $user->update([
            'two_factor_secret'         => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at'   => now(),
        ]);

        session()->forget(['2fa_secret', '2fa_recovery']);

        audit_log('Enabled Two-Factor Authentication (2FA)');
        send_notification('2FA Enabled', 'Two-Factor Authentication is now active on your account.');

        return response()->json([
            'success'  => true,
            'message'  => 'Two-Factor Authentication has been successfully enabled!',
            'redirect' => route('v1.profile.index')
        ]);
    }

    /**
     * Disable 2FA for the authenticated user.
     */
    public function disable2FA(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->getRawOriginal('password'))) {
            return response()->json([
                'success'  => false,
                'message'  => 'Incorrect password entered.',
                'redirect' => route('v1.profile.index')
            ], 422);
        }

        $user->update([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ]);

        audit_log('Disabled Two-Factor Authentication (2FA)');
        send_notification('2FA Disabled', 'Two-Factor Authentication has been disabled for your account.');

        return response()->json([
            'success'  => true,
            'message'  => 'Two-Factor Authentication has been disabled.',
            'redirect' => route('v1.profile.index')
        ]);
    }

    /**
     * Show 2FA Challenge screen during login.
     */
    public function showChallenge()
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA challenge code or recovery code during login.
     */
    public function verifyChallenge(Request $request, TwoFactorService $twoFactorService)
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        $userId = session('2fa:user_id');
        $remember = session('2fa:remember', false);
        $user = \App\Models\User::find($userId);

        if (!$user || !$user->hasTwoFactorEnabled()) {
            session()->forget(['2fa:user_id', '2fa:remember']);
            return redirect()->route('login');
        }

        $secret = decrypt($user->two_factor_secret);

        // Code verification
        if ($request->filled('code') && $twoFactorService->verifyCode($secret, $request->code)) {
            session()->forget(['2fa:user_id', '2fa:remember']);
            Auth::login($user, $remember);
            audit_log('Passed 2FA Security Challenge');
            return redirect()->intended(route('v1.dashboard'));
        }

        // Recovery code verification
        if ($request->filled('recovery_code')) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
            if (in_array($request->recovery_code, $recoveryCodes)) {
                // Remove used recovery code
                $recoveryCodes = array_values(array_diff($recoveryCodes, [$request->recovery_code]));
                $user->update([
                    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes))
                ]);

                session()->forget(['2fa:user_id', '2fa:remember']);
                Auth::login($user, $remember);
                audit_log('Passed 2FA Security Challenge using Recovery Code');
                return redirect()->intended(route('v1.dashboard'));
            }
        }

        return back()->withErrors(['code' => 'Invalid 2FA code or recovery code entered.']);
    }
}
