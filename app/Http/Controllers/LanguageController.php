<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->locale;
        $supportedLocales = ['en', 'vi'];

        if (in_array($locale, $supportedLocales)) {
            Session::put('locale', $locale);
            
            // Lưu vào cookie trong 1 năm
            Cookie::queue('locale', $locale, 525600);

            // Log language change
            \Log::info('User changed language', [
                'user_id' => auth()->id(),
                'locale' => $locale,
                'previous_locale' => Session::get('locale'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        return back()->withCookie('locale', $locale, 525600);
    }

    public function detect(Request $request)
    {
        $locale = app()->getLocale();
        
        return response()->json([
            'locale' => $locale,
            'name' => __('messages.languages.' . $locale),
            'direction' => in_array($locale, ['ar', 'he', 'fa']) ? 'rtl' : 'ltr'
        ]);
    }
} 