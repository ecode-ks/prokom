<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetMarketingLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('active') === true) {
            $locale = session('role') === 'admin' ? 'sq' : session('language', 'sq');
        } else {
            $locale = session('marketing_locale', 'sq');
        }

        if (!in_array($locale, ['sq', 'en', 'de'], true)) {
            $locale = 'sq';
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
