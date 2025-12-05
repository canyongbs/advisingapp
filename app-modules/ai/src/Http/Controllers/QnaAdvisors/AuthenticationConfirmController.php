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

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Http\Requests\QnaAdvisors\AuthenticationConfirmRequest;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Authorization\Enums\TokenAbility;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthenticationConfirmController
{
    public function __invoke(AuthenticationConfirmRequest $request, QnaAdvisor $advisor, PortalAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            abort(403, 'Authentication code is expired.');
        }

        if (! Hash::check($request->safe()['code'], $authentication->code)) {
            abort(403, 'Authentication code is invalid.');
        }

        $educatable = $authentication->educatable;

        if (! $educatable instanceof Student && ! $educatable instanceof Prospect) {
            abort(403, 'Something is wrong with the authentication.');
        }

        // If we reached this point, the authentication was successful

        $accessToken = $educatable->createToken('qna_advisor_access_token', [TokenAbility::AccessQnaAdvisorApi], now()->addMinutes(15));
        $refreshToken = $educatable->createToken('qna_advisor_refresh_token', [TokenAbility::IssueQnaAdvisorAccessToken], now()->addDays(3));

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'websockets_config' => [
                ...config('filament.broadcasting.echo'),
                'authEndpoint' => route('widgets.ai.qna-advisors.api.broadcasting.auth', ['advisor' => $advisor]),
            ],
        ])
            ->withCookie(
                Cookie::make(
                    name: 'advising_app_qna_advisor_refresh_token',
                    value: $refreshToken->plainTextToken,
                    minutes: 60 * 24 * 3, // 3 days
                    secure: true,
                    httpOnly: true,
                    sameSite: 'none',
                )
            );
    }
}
