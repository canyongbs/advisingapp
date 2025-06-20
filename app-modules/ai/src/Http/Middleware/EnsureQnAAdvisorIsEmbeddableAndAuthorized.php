<?php

namespace AdvisingApp\Ai\Http\Middleware;

use AdvisingApp\Ai\Models\QnAAdvisor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureQnAAdvisorIsEmbeddableAndAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var QnAAdvisor $advisor */
        $advisor = $request->route('qnAAdvisor');

        $referer = $request->headers->get('referer');

        if (! $referer) {
            return response()->json(['Missing referer header.'], 400);
        }

        $parsedUrl = parse_url($referer);

        if ($parsedUrl === false || ! isset($parsedUrl['host'])) {
            return response()->json(['Invalid referer URL.'], 400);
        }
        $referer = $parsedUrl['host'];

        $configuration = $advisor->qnAAdvisorEmbed;

        if (is_null($configuration) || ! $configuration->is_enabled) {
            return response()->json(['Embedding is not enabled for this QnA Advisor.'], 403);
        }

        $allowedDomains = collect($configuration->allowed_domains ?? []);

        if (! $allowedDomains->contains($referer)) {
            return response()->json(['Referer not allowed. Domain must be added to allowed domains list'], 403);
        }

        return $next($request);
    }
}
