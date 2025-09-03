<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Http\Middleware;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
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

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'Bearer token missing'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return response()->json(['error' => 'Invalid bearer token'], 401);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['error' => 'Bearer token expired'], 401);
        }

        $educatable = $accessToken->tokenable;

        match ($educatable::class) {
            Student::class => Auth::guard('student')->onceUsingId($educatable->getKey()),
            Prospect::class => Auth::guard('prospect')->onceUsingId($educatable->getKey()),
            default => throw new Exception('Unable to resolve to proper entity.')
        };

        return $next($request);
    }
}
