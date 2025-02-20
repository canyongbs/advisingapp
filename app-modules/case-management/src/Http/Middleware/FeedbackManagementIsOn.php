<?php

namespace AdvisingApp\CaseManagement\Http\Middleware;

use App\Settings\LicenseSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedbackManagementIsOn
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app(LicenseSettings::class)->data->addons->caseManagement) {
            return response()->json(['error' => 'Feedback Management is not enabled.'], 403);
        }

        return $next($request);
    }
}
