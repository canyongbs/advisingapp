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

use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;
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

        $content = $contentComponent->getState();

        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        if (! is_array($content) || ! isset($content['content'])) {
            $fail('Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.');

            return;
        }

        $result = $this->checkContentForRequiredFields($content['content']);

        if (! $result['email'] || ! $result['name']) {
            $missingFields = [];

            if (! $result['email']) {
                $missingFields[] = 'a required Mapped Primary Email Address field';
            }

            if (! $result['name']) {
                $missingFields[] = 'a required Mapped Name field';
            }

            $message = 'Forms that generate prospects must include ' . implode(' and ', $missingFields) . '.';
            $fail($message);
        }
    }

    public function validateWizardStepsFromRules(mixed $steps, Closure $fail): void
    {
        if (empty($steps) || ! is_array($steps)) {
            $fail('Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.');

            return;
        }

        $hasRequiredEmail = false;
        $hasRequiredName = false;

        $stepsComponent = $this->form->getComponent('steps');

        if (! $stepsComponent) {
            $fail('Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.');

            return;
        }

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

            if (is_string($stepContent)) {
                $stepContent = json_decode($stepContent, true);
            }

            if (! is_array($stepContent) || ! isset($stepContent['content'])) {
                continue;
            }

            $result = $this->checkContentForRequiredFields($stepContent['content']);

            if ($result['email']) {
                $hasRequiredEmail = true;
            }

            if ($result['name']) {
                $hasRequiredName = true;
            }
        }

        if (! $hasRequiredEmail || ! $hasRequiredName) {
            $missingFields = [];

            if (! $hasRequiredEmail) {
                $missingFields[] = 'a required Mapped Primary Email Address field';
            }

            if (! $hasRequiredName) {
                $missingFields[] = 'a required Mapped Name field';
            }

            $message = 'Forms that generate prospects must include ' . implode(' and ', $missingFields) . '.';
            $fail($message);
        }
    }

    /**
     * @param array<int, mixed> $content
     *
     * @return array{email: bool, name: bool}
     */
    protected function checkContentForRequiredFields(array $content): array
    {
        $hasRequiredEmail = false;
        $hasRequiredName = false;

        foreach ($content as $component) {
            if (isset($component['type']) && $component['type'] === 'customBlock') {
                $blockType = $component['attrs']['id'] ?? null;
                $blockConfig = $component['attrs']['config'] ?? [];

                if ($blockType === EducatableEmailFormFieldBlock::type()) {
                    $isRequired = $blockConfig['isRequired'] ?? $blockConfig['is_required'] ?? false;

                    if ($isRequired) {
                        $hasRequiredEmail = true;
                    }
                }

                if ($blockType === EducatableNameFormFieldBlock::type()) {
                    $hasRequiredName = true;
                }
            }

            if (isset($component['content']) && is_array($component['content'])) {
                $nestedResult = $this->checkContentForRequiredFields($component['content']);

                if ($nestedResult['email']) {
                    $hasRequiredEmail = true;
                }

                if ($nestedResult['name']) {
                    $hasRequiredName = true;
                }
            }
        }

        return [
            'email' => $hasRequiredEmail,
            'name' => $hasRequiredName,
        ];
    }
}
