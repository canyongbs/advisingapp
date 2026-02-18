<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Form\Http\Middleware;

use AdvisingApp\Form\Models\Submissible;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubmissibleIsEmbeddableAndAuthorized
{
    public function handle(Request $request, Closure $next, string $binding): Response
    {
        $submissible = $request->route($binding);

        if (is_string($submissible)) {
            $modelClass = Relation::getMorphedModel($binding);

            if ($modelClass) {
                $submissible = (new $modelClass())->resolveRouteBinding($submissible);
                $request->route()->setParameter($binding, $submissible);
            }
        }

        if (! $submissible instanceof Submissible) {
            return response()->json(['error' => 'Resource not found.'], 404);
        }

        /** @var Submissible $submissible */

        $requestingUrlHeader = $request->headers->get('origin') ?? $request->headers->get('referer');

        if (! $requestingUrlHeader) {
            return response()->json(['error' => 'Missing origin/referer header.'], 400);
        }

        $requestingUrlHeader = parse_url($requestingUrlHeader)['host'];

        if ($requestingUrlHeader != parse_url(config('app.url'))['host']) {
            if (! $submissible->embed_enabled) {
                return response()->json(['error' => 'Embedding is not enabled for this form.'], 403);
            }

            $allowedDomains = collect($submissible->allowed_domains ?? []);

            if (! $allowedDomains->contains($requestingUrlHeader)) {
                return response()->json(['error' => 'Origin/Referer not allowed. Domain must be added to allowed domains list'], 403);
            }
        }

        return $next($request);
    }
}
