<?php

namespace AdvisingApp\Ai\Http\Middleware;

use AdvisingApp\Ai\Models\QnaAdvisor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureQnaAdvisorEmbedIsEnabled
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

        if (! $advisor->is_embed_enabled) {
            return response()->json(['error' => 'Embed is not enabled for this advisor.', 'embed_enabled' => false], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $next($request);
    }
}
