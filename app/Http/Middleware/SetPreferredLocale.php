<?php

namespace App\Http\Middleware;

use Closure;

class SetPreferredLocale
{
    public function handle($request, Closure $next)
    {
        // If user has locale then set it
        if ($userLocale = optional(auth()->user())->locale) {
            app()->setLocale($userLocale);

            return $next($request);
        }

        // Otherwise auto-detect locale between browser and supported languages
        $languages = array_column(config('project.supported_languages'), 'short_code');
        $locale    = $request->getPreferredLanguage($languages);

        app()->setLocale($locale);

        return $next($request);
    }
}
