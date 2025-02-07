<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use PragmaRX\Google2FALaravel\Google2FA;
use Illuminate\Support\Str;

class TwoFactorAuthController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        if (!$user->two_factor_secret) {
            $google2fa = app(Google2FA::class);
            $secret = $google2fa->generateSecretKey();
            
            $user->two_factor_secret = $secret;
            $user->save();
            
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
            
            return Inertia::render('Profile/TwoFactor', [
                'qrCodeUrl' => $qrCodeUrl,
                'secret' => $secret,
                'enabled' => false
            ]);
        }
        
        return Inertia::render('Profile/TwoFactor', [
            'enabled' => !is_null($user->two_factor_confirmed_at),
            'recoveryCodes' => $user->two_factor_recovery_codes ? json_decode($user->two_factor_recovery_codes) : []
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();
        $google2fa = app(Google2FA::class);

        if ($google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            $user->two_factor_confirmed_at = now();
            $user->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
            $user->save();

            return back()->with('success', '2FA đã được kích hoạt');
        }

        return back()->withErrors(['code' => 'Mã không hợp lệ']);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();
        $google2fa = app(Google2FA::class);

        if ($google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            $user->two_factor_secret = null;
            $user->two_factor_confirmed_at = null;
            $user->two_factor_recovery_codes = null;
            $user->save();

            return back()->with('success', '2FA đã được vô hiệu hóa');
        }

        return back()->withErrors(['code' => 'Mã không hợp lệ']);
    }

    private function generateRecoveryCodes()
    {
        return collect(range(1, 8))->map(function () {
            return sprintf('%s-%s', Str::random(10), Str::random(10));
        })->all();
    }
} 