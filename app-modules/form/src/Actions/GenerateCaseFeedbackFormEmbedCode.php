<?php

namespace AdvisingApp\Form\Actions;

use AdvisingApp\CaseManagement\Models\CaseModel;
use Illuminate\Support\Facades\URL;

class GenerateCaseFeedbackFormEmbedCode
{
    public function handle(CaseModel $case): string
    {
        $scriptUrl = url('js/widgets/case-feedback-form/advising-app-case-feedback-form-widget.js?');

        $formDefinitionUrl = URL::to(
            URL::signedRoute(
                name: 'cases.feedback.define',
                parameters: ['case' => $case],
                absolute: false,
            )
        );

        $portalAccessUrl = route('portal.resource-hub.show');

        $userAuthenticationUrl = route('api.user.auth-check');

        $appUrl = config('app.url');

        $apiUrl = route('api.portal.resource-hub.define');

        return <<<EOD
        <case-feedback-form-embed url="{$formDefinitionUrl}" user-authentication-url={$userAuthenticationUrl} access-url={$portalAccessUrl} app-url="{$appUrl}" api-url="{$apiUrl}"></case-feedback-form-embed>
        <script src="{$scriptUrl}"></script>
        EOD;
    }
}
