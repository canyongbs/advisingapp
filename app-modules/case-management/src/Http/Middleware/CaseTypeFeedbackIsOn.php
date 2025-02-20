<?php

namespace AdvisingApp\CaseManagement\Http\Middleware;

use App\Settings\LicenseSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaseTypeFeedbackIsOn
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $case = $request->route('case');

        if (app(LicenseSettings::class)->data->addons->caseManagement) {
            if ($case && $case?->priority?->type?->has_enabled_feedback_collection) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Feedback collection is not enabled for this case.'], 403);
    }
}
