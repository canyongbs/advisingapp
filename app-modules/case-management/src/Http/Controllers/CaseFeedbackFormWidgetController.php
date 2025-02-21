<?php

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
                'is_authenticated' => (bool) $request->user(),
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
        $submitter = auth('sanctum')->user();

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
