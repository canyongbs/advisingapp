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

namespace AdvisingApp\CaseManagement\Http\Controllers;

use AdvisingApp\CaseManagement\Models\CaseFeedback;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Portal\Settings\PortalSettings;
use AdvisingApp\Theme\Settings\ThemeSettings;
use App\Http\Controllers\Controller;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CaseFeedbackFormWidgetController extends Controller
{
    public function view(Request $request, CaseModel $case): JsonResponse
    {
        $logo = ThemeSettings::getSettingsPropertyModel('theme.is_logo_active')->getFirstMedia('logo');
        $portalSettings = app(PortalSettings::class);

        return response()->json(
            [
                'requires_authentication' => true,
                'is_authenticated' => (bool) auth('student')->check() || (bool) auth('prospect')->check(),
                'guard' => auth('student')->check() ? 'student' : (auth('prospect')->check() ? 'prospect' : null),
                'authentication_url' => URL::to(
                    URL::signedRoute(
                        name: 'api.portal.resource-hub.request-authentication',
                        absolute: false,
                    )
                ),
                'submission_url' => URL::signedRoute(
                    name: 'cases.feedback.submit',
                    parameters: ['case' => $case],
                    absolute: false
                ),
                'header_logo' => $logo?->getTemporaryUrl(
                    expiration: now()->addMinutes(5),
                    conversionName: 'logo-height-250px',
                ),
                'feedback_submitted' => $case?->feedback()->exists() ? true : false,
                'app_name' => config('app.name'),
                'has_enabled_csat' => $case->priority?->type?->has_enabled_csat,
                'has_enabled_nps' => $case->priority?->type?->has_enabled_nps,
                'footer_logo' => Vite::asset('resources/images/canyon-logo-light.svg'),
                'primary_color' => Color::all()[$portalSettings->resource_hub_portal_primary_color ?? 'blue'],
                'rounding' => $portalSettings->resource_hub_portal_rounding,
                'case_number' => $case->case_number,
            ],
        );
    }

    public function store(
        Request $request,
        CaseModel $case,
    ): JsonResponse {
        $submitter = $request->user($request->guard);

        abort_if(is_null($submitter), Response::HTTP_UNAUTHORIZED);

        $validator = Validator::make($request->all(), [
            'csat' => [Rule::requiredIf($case?->priority?->type?->has_enabled_csat), 'between:1,5'],
            'nps' => [Rule::requiredIf($case?->priority?->type?->has_enabled_nps), 'between:1,5'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => (object) $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        /** @var CaseFeedback $feedback */
        $feedback = $case->feedback()->make([
            'csat_answer' => $data['csat'] ?? null,
            'nps_answer' => $data['nps'] ?? null,
            'assignee_type' => $submitter->getMorphClass(),
            'assignee_id' => $submitter->getKey(),
        ]);

        $feedback->save();

        return response()->json([
            'message' => 'Case feedback submitted successfully.',
        ]);
    }
}
