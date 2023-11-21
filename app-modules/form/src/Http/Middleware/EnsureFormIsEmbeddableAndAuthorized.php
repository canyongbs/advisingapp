<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
