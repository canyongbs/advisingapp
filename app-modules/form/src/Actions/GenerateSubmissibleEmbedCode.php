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

use AdvisingApp\Application\Models\Application;
use AdvisingApp\CaseManagement\Models\CaseForm;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\Survey\Models\Survey;
use Exception;
use Illuminate\Support\Facades\Storage;

class GenerateSubmissibleEmbedCode
{
    public function handle(Submissible $submissible): string
    {
        return match ($submissible::class) {
            Form::class => (function () use ($submissible) {
                $manifestPath = Storage::disk('public')->get('widgets/forms/.vite/manifest.json');

                if (is_null($manifestPath)) {
                    throw new Exception('Vite manifest file not found.');
                }

                /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
                $manifest = json_decode($manifestPath, true, 512, JSON_THROW_ON_ERROR);

                $loaderScriptUrl = url("widgets/forms/{$manifest['src/loader.js']['file']}");

                $assetsUrl = route(name: 'widgets.forms.api.assets', parameters: ['form' => $submissible]);

                return <<<EOD
                <form-embed url="{$assetsUrl}"></form-embed>
                <script src="{$loaderScriptUrl}"></script>
                EOD;
            })(),
            Application::class => (function () use ($submissible) {
                $manifestPath = Storage::disk('public')->get('widgets/applications/.vite/manifest.json');

                if (is_null($manifestPath)) {
                    throw new Exception('Vite manifest file not found.');
                }

                /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
                $manifest = json_decode($manifestPath, true, 512, JSON_THROW_ON_ERROR);

                $loaderScriptUrl = url("widgets/applications/{$manifest['src/loader.js']['file']}");

                $assetsUrl = route(name: 'widgets.applications.api.assets', parameters: ['application' => $submissible]);

                return <<<EOD
                <application-embed url="{$assetsUrl}"></application-embed>
                <script src="{$loaderScriptUrl}"></script>
                EOD;
            })(),
            Survey::class => (function () use ($submissible) {
                $manifestPath = Storage::disk('public')->get('widgets/surveys/.vite/manifest.json');

                if (is_null($manifestPath)) {
                    throw new Exception('Vite manifest file not found.');
                }

                /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
                $manifest = json_decode($manifestPath, true, 512, JSON_THROW_ON_ERROR);

                $loaderScriptUrl = url("widgets/surveys/{$manifest['src/loader.js']['file']}");

                $assetsUrl = route(name: 'widgets.surveys.api.assets', parameters: ['survey' => $submissible]);

                return <<<EOD
                <survey-embed url="{$assetsUrl}"></survey-embed>
                <script src="{$loaderScriptUrl}"></script>
                EOD;
            })(),
            EventRegistrationForm::class => (function () use ($submissible) {
                /** @var EventRegistrationForm $submissible */
                $manifestPath = Storage::disk('public')->get('widgets/event-registration/.vite/manifest.json');

                if (is_null($manifestPath)) {
                    throw new Exception('Vite manifest file not found.');
                }

                /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
                $manifest = json_decode($manifestPath, true, 512, JSON_THROW_ON_ERROR);

                $loaderScriptUrl = url("widgets/event-registration/{$manifest['src/loader.js']['file']}");

                $assetsUrl = route(name: 'widgets.event-registration.api.assets', parameters: ['event' => $submissible->event]);

                return <<<EOD
                <event-registration-embed url="{$assetsUrl}"></event-registration-embed>
                <script src="{$loaderScriptUrl}"></script>
                EOD;
            })(),
            CaseForm::class => (function () use ($submissible) {
                /** @var CaseForm $submissible */
                $manifestPath = Storage::disk('public')->get('widgets/case-forms/.vite/manifest.json');

                if (is_null($manifestPath)) {
                    throw new Exception('Vite manifest file not found.');
                }

                /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
                $manifest = json_decode($manifestPath, true, 512, JSON_THROW_ON_ERROR);

                $loaderScriptUrl = url("widgets/case-forms/{$manifest['src/loader.js']['file']}");

                $assetsUrl = route(name: 'widgets.case-forms.api.assets', parameters: ['caseForm' => $submissible]);

                return <<<EOD
                <case-form-embed url="{$assetsUrl}"></case-form-embed>
                <script src="{$loaderScriptUrl}"></script>
                EOD;
            })(),
            default => throw new Exception('Unsupported submissible type.'),
        };
    }
}
