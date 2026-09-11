<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolution order:
     *   1. Logged-in user's saved preference (users.locale)
     *   2. Locale already chosen this session (set by LanguageController)
     *   3. Browser's Accept-Language header, if it matches a supported locale
     *   4. config('languages.default')
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('languages.supported', []));
        $locale = null;

        if ($request->user() && in_array($request->user()->locale, $supported, true)) {
            $locale = $request->user()->locale;
        } elseif ($request->session()->has('locale') && in_array($request->session()->get('locale'), $supported, true)) {
            $locale = $request->session()->get('locale');
        } else {
            $preferred = $request->getPreferredLanguage($supported);
            $locale = $preferred ?: config('languages.default', 'en');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
