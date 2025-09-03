<?php

namespace AdvisingApp\Ai\Http\Middleware;

use AdvisingApp\Ai\Models\QnaAdvisor;
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
        $advisor = $request->route('advisor');

        if (! $advisor instanceof QnaAdvisor) {
            return response()->json(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $origin = $request->headers->get('origin');

        if (! $origin) {
            return response()->json(['error' => 'Missing origin header.'], 400);
        }

        $appRootDomain = parse_url(config('app.url'))['host'];

        $allowedDomains = [$appRootDomain];

        $authorizedDomains = $advisor->authorized_domains;

        if (is_array($authorizedDomains)) {
            $flatAuthorized = [];
            array_walk_recursive($authorizedDomains, function (string $domain) use (&$flatAuthorized) {
                $flatAuthorized[] = $domain;
            });
            $allowedDomains = array_merge($allowedDomains, $flatAuthorized);
        }

        $origin = parse_url($origin)['host'];

        if (! in_array($origin, $allowedDomains)) {
            return response()->json(['error' => 'Origin not allowed'], 403);
        }

        return $next($request);
    }
}
