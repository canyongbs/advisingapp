<?php

namespace Assist\Form\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFormIsEmbeddableAndAuthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        $referer = $request->headers->get('referer');

        if (! $referer) {
            return response()->json(['error' => 'Missing referer header.'], 400);
        }

        $referer = parse_url($referer)['host'];

        if ($referer != parse_url(config('app.url'))['host']) {
            if (! $request->form->embed_enabled) {
                return response()->json(['error' => 'Embedding is not enabled for this form.'], 403);
            }

            $allowedDomains = collect($request->form->allowed_domains ?? []);

            if (! $allowedDomains->contains($referer)) {
                return response()->json(['error' => 'Referer not allowed. Domain must be added to allowed domains list'], 403);
            }
        }

        return $next($request);
    }
}
