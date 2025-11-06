<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Fetch language and RTL settings directly from the database
        $settings = Setting::pluck('value', 'key')->toArray();

        // Default values from database or fallback to 'en' and 'ltr'
        $locale = $settings['default_locale'] ?? 'en';  // Default language (ar, en)
        $rtlEnabled = filter_var($settings['rtl_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Determine direction based on RTL setting
        $dir = ($locale === 'ar' || $rtlEnabled) ? 'rtl' : 'ltr';

        // Set locale and direction in the session
        App::setLocale($locale);
        View::share('htmlLang', $locale);
        View::share('htmlDir', $dir);

        // Store in session (optional, but keeps consistency)
        session(['locale' => $locale, 'dir' => $dir]);

        return $next($request);
    }
}
