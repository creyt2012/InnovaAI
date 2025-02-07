<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    protected $supportedLocales = ['en', 'vi'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $this->determineLocale($request);
        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    protected function determineLocale(Request $request)
    {
        // 1. Kiểm tra session
        if (Session::has('locale') && in_array(Session::get('locale'), $this->supportedLocales)) {
            return Session::get('locale');
        }

        // 2. Kiểm tra cookie
        if ($request->hasCookie('locale') && in_array($request->cookie('locale'), $this->supportedLocales)) {
            return $request->cookie('locale');
        }

        // 3. Kiểm tra Accept-Language header
        $browserLocales = $request->getLanguages();
        foreach ($browserLocales as $browserLocale) {
            $lang = substr($browserLocale, 0, 2);
            if (in_array($lang, $this->supportedLocales)) {
                return $lang;
            }
        }

        // 4. Kiểm tra địa chỉ IP để xác định quốc gia
        try {
            $ip = $request->ip();
            $location = geoip()->getLocation($ip);
            $countryCode = strtolower($location['iso_code']);
            
            // Map country code to locale
            $countryLocaleMap = [
                'vn' => 'vi',
                'us' => 'en',
                'gb' => 'en',
                'au' => 'en',
                'ca' => 'en',
            ];

            if (isset($countryLocaleMap[$countryCode])) {
                return $countryLocaleMap[$countryCode];
            }
        } catch (\Exception $e) {
            \Log::warning('GeoIP detection failed: ' . $e->getMessage());
        }

        // 5. Fallback to default locale
        return config('app.fallback_locale', 'en');
    }
} 