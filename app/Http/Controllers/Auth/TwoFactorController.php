<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function enable(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $user = $request->user();
        $user->two_factor_secret = $secret;
        $user->save();
        
        return response()->json([
            'secret' => $secret,
            'qr_code' => $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            )
        ]);
    }
} 