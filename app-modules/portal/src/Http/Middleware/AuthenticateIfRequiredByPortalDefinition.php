<?php

namespace AdvisingApp\Portal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use AdvisingApp\Portal\Settings\PortalSettings;

class AuthenticateIfRequiredByPortalDefinition
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = resolve(PortalSettings::class);

        if ($settings->knowledge_management_portal_requires_authentication === false) {
            return $next($request);
        }

        if (auth('sanctum')->check() === true) {
            return $next($request);
        }

        abort(403);
    }
}
