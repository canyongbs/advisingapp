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

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlock;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Form\Models\Submission;

class InjectSubmissionStateIntoTipTapContent
{
    public function __invoke(Submission $submission, array $content, array $blocks): array
    {
        foreach ($content as $componentKey => $component) {
            if (! is_array($component)) {
                continue;
            }

            if (array_key_exists('content', $component)) {
                $content[$componentKey]['content'] = $this($submission, $component['content'], $blocks);

                continue;
            }

            $content[$componentKey] = $this->processBlock($submission, $component, $blocks);
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $component
     * @param  array<string, mixed>  $blocks
     *
     * @return array<string, mixed>
     */
    protected function processBlock(Submission $submission, array $component, array $blocks): array
    {
        $componentType = $component['type'] ?? null;
        $componentAttributes = $component['attrs'] ?? [];

        // Support both new RichEditor (customBlock) and legacy TipTap (tiptapBlock) formats
        if ($componentType === 'customBlock') {
            $config = $componentAttributes['config'] ?? [];
            $fieldId = $config['fieldId'] ?? null;
            $blockType = $componentAttributes['id'] ?? null;
            $stateKey = 'config';
        } elseif ($componentType === 'tiptapBlock') {
            $config = $componentAttributes['data'] ?? [];
            $fieldId = $componentAttributes['id'] ?? null;
            $blockType = $componentAttributes['type'] ?? null;
            $stateKey = 'data';
        } else {
            return $component;
        }

        if (blank($fieldId) || blank($blocks[$blockType] ?? null)) {
            return $component;
        }

        /** @var FormFieldBlock $block */
        $block = $blocks[$blockType];

        $field = $submission->fields
            ->first(fn (SubmissibleField $field): bool => $field->getKey() === $fieldId);

        if (! $field) {
            return $component;
        }

        $component['attrs'][$stateKey] = [
            ...$config,
            ...$block::getSubmissionState($field, $field->pivot->response),
        ];

        return $component;
    }
}
