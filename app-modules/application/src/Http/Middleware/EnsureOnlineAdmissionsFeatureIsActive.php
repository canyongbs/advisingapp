<?php

namespace Assist\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Settings\LicenseSettings;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnlineAdmissionsFeatureIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app(LicenseSettings::class)->data->addons->onlineAdmissions) {
            return response()->json(['error' => 'Online Admissions is not enabled.'], 403);
        }

        return $next($request);
    }
}
