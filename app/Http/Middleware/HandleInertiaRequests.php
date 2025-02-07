<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware\HandleInertiaRequests as Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'locale' => app()->getLocale(),
            'language' => __('messages'), // Truyền toàn bộ translations
        ]);
    }
} 