<?php

namespace AdvisingApp\Ai\Http\Middleware;

use AdvisingApp\Ai\Models\QnaAdvisor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QnaAdvisorAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $advisor = $request->route('advisor');

        if (! $advisor instanceof QnaAdvisor) {
            return response()->json(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $advisor->is_requires_authentication_enabled) {
            // This Advisor does not require an Educatable to be authorized. Allow request.
            return $next($request);
        }

        // Check auth and login

        return $next($request);
    }
}
