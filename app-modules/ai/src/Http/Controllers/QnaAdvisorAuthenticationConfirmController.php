<?php

namespace AdvisingApp\Ai\Http\Controllers;

use AdvisingApp\Ai\Http\Requests\QnaAdvisorAuthenticationConfirmRequest;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Authorization\Enums\TokenAbility;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class QnaAdvisorAuthenticationConfirmController
{
    public function __invoke(QnaAdvisorAuthenticationConfirmRequest $request, QnaAdvisor $advisor, PortalAuthentication $authentication): JsonResponse
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

        $accessToken = $educatable->createToken('qna_advisor_access_token', [TokenAbility::AccessQnaAdvisorApi], now()->addMinutes(1));
        $refreshToken = $educatable->createToken('qna_advisor_refresh_token', [TokenAbility::IssueQnaAdvisorAccessToken], now()->addDays(3));

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
        ])
            ->withCookie(
                Cookie::make(
                    name: 'advising_app_qna_advisor_refresh_token',
                    value: $refreshToken->plainTextToken,
                    minutes: 60 * 24 * 3, // 3 days
                    secure: true,
                    httpOnly: true,
                )
            );
    }
}
