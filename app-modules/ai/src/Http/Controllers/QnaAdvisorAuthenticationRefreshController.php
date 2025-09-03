<?php

namespace AdvisingApp\Ai\Http\Controllers;

use AdvisingApp\Authorization\Enums\TokenAbility;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\PersonalAccessToken;

class QnaAdvisorAuthenticationRefreshController
{
    public function __invoke(Request $request): JsonResponse
    {
        logger()->debug('entered refresh');
        $refreshTokenValue = $request->cookie('advising_app_qna_advisor_refresh_token');

        if (! $refreshTokenValue) {
            abort(401, 'Unauthorized');
        }

        $refreshToken = PersonalAccessToken::findToken($refreshTokenValue);

        if (! $refreshToken || ! $refreshToken->can(TokenAbility::IssueQnaAdvisorAccessToken->value)) {
            abort(401, 'Unauthorized');
        }

        if ($refreshToken->expires_at && $refreshToken->expires_at->isPast()) {
            abort(401, 'Unauthorized');
        }

        $educatable = $refreshToken->tokenable;

        if (! $educatable instanceof Student && ! $educatable instanceof Prospect) {
            abort(401, 'Unauthorized');
        }

        // Invalidate current refresh token
        $refreshToken->delete();

        // Invalidate any existing access tokens
        PersonalAccessToken::where('tokenable_type', $educatable->getMorphClass())
            ->where('tokenable_id', $educatable->getKey())
            ->where('name', 'qna_advisor_access_token')
            ->delete();

        // Generate new tokens
        $accessToken = $educatable->createToken('qna_advisor_access_token', [TokenAbility::AccessQnaAdvisorApi], now()->addMinutes(1));
        $newRefreshToken = $educatable->createToken('qna_advisor_refresh_token', [TokenAbility::IssueQnaAdvisorAccessToken], now()->addDays(3));

        logger()->debug('refreshed!');

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
        ])
            ->withCookie(
                Cookie::make(
                    name: 'advising_app_qna_advisor_refresh_token',
                    value: $newRefreshToken->plainTextToken,
                    minutes: 60 * 24 * 3, // 3 days
                    secure: true,
                    httpOnly: true,
                )
            );
    }
}
