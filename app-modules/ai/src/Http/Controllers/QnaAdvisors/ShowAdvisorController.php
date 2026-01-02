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

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Http\Controllers\QnaAdvisors\Concerns\CanRefreshQnaAdvisorTokens;
use AdvisingApp\Ai\Models\QnaAdvisor;
use App\Features\QnaAdvisorCardViewFeature;
use App\Features\QnaAdvisorThemeFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;

class ShowAdvisorController
{
    use CanRefreshQnaAdvisorTokens;

    public function __invoke(Request $request, QnaAdvisor $advisor): JsonResponse
    {
        $data = [
            'requires_authentication' => $advisor->is_requires_authentication_enabled ?? false,
            'authentication_url' => URL::signedRoute(name: 'widgets.ai.qna-advisors.api.authentication.request', parameters: ['advisor' => $advisor]),
            'refresh_url' => URL::signedRoute(name: 'widgets.ai.qna-advisors.api.authentication.refresh', parameters: ['advisor' => $advisor]),
            'start_thread_url' => URL::temporarySignedRoute(
                name: 'widgets.ai.qna-advisors.api.threads.start',
                expiration: now()->addDays(3),
                parameters: ['advisor' => $advisor],
            ),
            'send_message_url' => URL::temporarySignedRoute(
                name: 'widgets.ai.qna-advisors.api.messages.send',
                expiration: now()->addDays(3),
                parameters: ['advisor' => $advisor],
            ),
            'websockets_config' => [
                ...config('filament.broadcasting.echo'),
                'authEndpoint' => route('widgets.ai.qna-advisors.api.broadcasting.auth', ['advisor' => $advisor]),
            ],
            'advisor' => [
                'name' => $advisor->name,
                'description' => $advisor->description,
                'avatar_url' => $advisor->getFirstTemporaryUrl(now()->addHour(), 'avatar') ?: null,
                'title_text_color' => QnaAdvisorCardViewFeature::active() && $advisor->title_text_color ? $advisor->title_text_color : '#000000',
                'description_text_color' => QnaAdvisorCardViewFeature::active() && $advisor->description_text_color ? $advisor->description_text_color : '#000000',
                'button_text_color' => QnaAdvisorCardViewFeature::active() && $advisor->button_text_color ? $advisor->button_text_color : '#ffffff',
                'button_text_hover_color' => QnaAdvisorCardViewFeature::active() && $advisor->button_text_hover_color ? $advisor->button_text_hover_color : '#ffffff',
                'button_background_color' => QnaAdvisorCardViewFeature::active() && $advisor->button_background_color ? $advisor->button_background_color : '#f59e0b',
                'button_background_hover_color' => QnaAdvisorCardViewFeature::active() && $advisor->button_background_hover_color ? $advisor->button_background_hover_color : '#ffc159',
                'default_theme' => QnaAdvisorThemeFeature::active() ? $advisor->default_theme : 'light',
            ],
        ];

        $response = new JsonResponse();

        $tokens = $this->refreshFromRequest($request);

        if ($tokens) {
            $data['access_token'] = $tokens['access_token']->plainTextToken;

            $response->withCookie(
                Cookie::make(
                    name: 'advising_app_qna_advisor_refresh_token',
                    value: $tokens['refresh_token']->plainTextToken,
                    minutes: 60 * 24 * 3, // 3 days
                    secure: true,
                    httpOnly: true,
                    sameSite: 'none',
                )
            );
        }

        $response->setData($data);

        return $response;
    }
}
