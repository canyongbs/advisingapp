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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns;

use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Validation\ValidationException;

trait ValidatesProspectGenerationFields
{
    protected function beforeCreate(): void
    {
        $this->validateProspectGenerationRequirements();
    }

    protected function beforeSave(): void
    {
        $this->validateProspectGenerationRequirements();
    }

    protected function afterCreate(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function afterSave(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function validateProspectGenerationRequirements(): void
    {
        $formState = $this->form->getRawState();

        $isAuthenticated = $formState['is_authenticated'] ?? false;
        $generateProspects = $formState['generate_prospects'] ?? false;

        if (! $generateProspects || $isAuthenticated) {
            return;
        }

        $isWizard = $formState['is_wizard'] ?? false;

        if ($isWizard) {
            $this->validateWizardSteps($formState);
        } else {
            $this->validateNormalForm();
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function validateWizardSteps(array $data): void
    {
        $steps = $data['steps'] ?? [];
        $hasRequiredEmail = false;
        $hasRequiredName = false;

        foreach ($steps as $step) {
            $content = $step['content']['content'] ?? [];

            $result = $this->checkContentForRequiredFields($content);

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
                $missingFields[] = 'a required Educatable Email field';
            }

            if (! $hasRequiredName) {
                $missingFields[] = 'an Educatable Name field';
            }

            $message = 'Forms that generate prospects must include ' . implode(' and ', $missingFields) . '.';

            throw ValidationException::withMessages([
                'steps' => $message,
            ]);
        }
    }

    protected function validateNormalForm(): void
    {
        $contentComponent = $this->form->getComponent('content');

        if (! $contentComponent instanceof TiptapEditor) {
            throw ValidationException::withMessages([
                'data.content' => 'Forms that generate prospects must include required email and name fields.',
            ]);
        }

        $componentState = $contentComponent->getState();

        if (! filled($componentState)) {
            throw ValidationException::withMessages([
                'data.content' => 'Forms that generate prospects must include required email and name fields.',
            ]);
        }

        $content = $contentComponent->decodeBlocks($contentComponent->getJSON(decoded: true));

        if (! $content || ! isset($content['content'])) {
            throw ValidationException::withMessages([
                'data.content' => 'Forms that generate prospects must include required email and name fields.',
            ]);
        }

        $result = $this->checkContentForRequiredFields($content['content']);

        if (! $result['email'] || ! $result['name']) {
            $missingFields = [];

            if (! $result['email']) {
                $missingFields[] = 'a required Educatable Email field';
            }

            if (! $result['name']) {
                $missingFields[] = 'an Educatable Name field';
            }

            $message = 'Forms that generate prospects must include ' . implode(' and ', $missingFields) . '.';

            throw ValidationException::withMessages([
                'data.content' => $message,
            ]);
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
            if (isset($component['type']) && $component['type'] === 'tiptapBlock') {
                $blockType = $component['attrs']['type'] ?? null;
                $blockData = $component['attrs']['data'] ?? [];

                if ($blockType === EducatableEmailFormFieldBlock::type()) {
                    $isRequired = $blockData['isRequired'] ?? $blockData['is_required'] ?? false;

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
