<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns;

use AdvisingApp\Form\Actions\FindMissingProspectGenerationFields;
use AdvisingApp\Form\Enums\ProspectGenerationField;
use Closure;
use Filament\Forms\Components\RichEditor;

trait ValidatesProspectGenerationFields
{
    public function validateNormalFormFromRules(Closure $fail): void
    {
        $contentComponent = $this->form->getComponent('content');

        if (! $contentComponent instanceof RichEditor) {
            $fail('Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.');

            return;
        }

        $this->failForMissingProspectGenerationFields([$contentComponent->getState()], $fail);
    }

    public function validateWizardStepsFromRules(mixed $steps, Closure $fail): void
    {
        if (empty($steps) || ! is_array($steps)) {
            $fail('Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.');

            return;
        }

        $stepsComponent = $this->form->getComponent('steps');

        if (! $stepsComponent) {
            $fail('Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.');

            return;
        }

        $stepContents = [];

        foreach ($steps as $stepIndex => $step) {
            $childContainer = $stepsComponent->getChildComponentContainer($stepIndex);

            if (! $childContainer) {
                continue;
            }

            $contentComponent = $childContainer->getComponent('content');

            if (! $contentComponent instanceof RichEditor) {
                continue;
            }

            $stepContent = $step['content'] ?? null;

            if (! filled($stepContent)) {
                continue;
            }

            $stepContents[] = $stepContent;
        }

        $this->failForMissingProspectGenerationFields($stepContents, $fail);
    }

    /**
     * @param array<int, mixed> $documents
     */
    protected function failForMissingProspectGenerationFields(array $documents, Closure $fail): void
    {
        $missingFields = app(FindMissingProspectGenerationFields::class)($documents);

        if ($missingFields === []) {
            return;
        }

        $fail(ProspectGenerationField::getRequirementMessage($missingFields));
    }
}
