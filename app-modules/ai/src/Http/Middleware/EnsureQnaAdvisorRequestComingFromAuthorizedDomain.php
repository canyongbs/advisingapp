<?php

namespace AdvisingApp\Ai\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureQnaAdvisorRequestComingFromAuthorizedDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $referer = $request->headers->get('referer');
        $appRootDomain = parse_url(config('app.url'))['host'];

        // If we are on the root domain
        if (parse_url($request->url())['host'] === $appRootDomain) {
            return $next($request);
        }

        if (! $referer) {
            return response()->json(['error' => 'Missing referer header.'], 400);
        }

        $referer = parse_url($referer)['host'];

        if ($referer != $appRootDomain) {
            return response()->json(['error' => 'Referer not allowed'], 403);
        }

        return $next($request);
    }
}
