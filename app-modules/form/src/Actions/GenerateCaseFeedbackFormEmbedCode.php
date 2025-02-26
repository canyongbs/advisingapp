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
