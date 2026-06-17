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

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Form\Models\SubmissibleStep;
use Illuminate\Database\Eloquent\Builder;

class SaveSubmissibleFieldsFromContent
{
    /** @param array<string, mixed> $data */
    public function execute(Submissible $submissible, array $data): void
    {
        if ($submissible->is_wizard) {
            $sort = 1;

            foreach ($data['steps'] ?? [] as $stepData) {
                $step = $submissible->steps()->create([
                    'label' => $stepData['label'] ?? 'Untitled Step',
                    'sort' => $sort++,
                ]);

                assert($step instanceof SubmissibleStep);

                $content = $this->decodeContent($stepData['content'] ?? []);

                if (! empty($content)) {
                    $content['content'] = $this->saveFieldsFromComponents(
                        $submissible,
                        $content['content'] ?? [],
                        $step,
                    );
                }

                $step->content = $content;
                $step->save();
            }

            $submissible->content = null;
            $submissible->save();
        } else {
            $content = $this->decodeContent($data['content'] ?? []);

            if (! empty($content)) {
                $content['content'] = $this->saveFieldsFromComponents(
                    $submissible,
                    $content['content'] ?? [],
                    null,
                );
            }

            $submissible->content = $content;
            $submissible->save();

            $submissible->steps()->delete();
        }
    }

    /**
     * @param array<string, mixed>|string|null $content
     *
     * @return array<string, mixed>
     */
    public function replaceFieldsForRecord(Submissible $submissible, ?SubmissibleStep $step, array|string|null $content): array
    {
        $submissible->fields()
            ->when($step, fn (Builder $query) => $query->whereBelongsTo($step, 'step'))
            ->delete();

        $content = $this->decodeContent($content);

        $content['content'] = $this->saveFieldsFromComponents(
            $submissible,
            $content['content'] ?? [],
            $step,
        );

        return $content;
    }

    /**
     * @param array<string, mixed> $components
     *
     * @return array<string, mixed>
     */
    public function saveFieldsFromComponents(Submissible $submissible, array $components, ?SubmissibleStep $step): array
    {
        foreach ($components as $componentKey => $component) {
            if (array_key_exists('content', $component)) {
                $components[$componentKey]['content'] = $this->saveFieldsFromComponents($submissible, $component['content'], $step);

                continue;
            }

            if (($component['type'] ?? null) !== 'customBlock') {
                continue;
            }

            $componentAttributes = $component['attrs'] ?? [];
            $config = $componentAttributes['config'] ?? [];

            $id = $config['fieldId'] ?? null;
            unset($config['fieldId']);

            $label = $config['label'] ?? null;
            unset($config['label']);

            $isRequired = $config['isRequired'] ?? null;
            unset($config['isRequired']);

            /** @var SubmissibleField $field */
            $field = $submissible->fields()->findOrNew($id);
            $field->step()->associate($step);
            $field->label = $label ?? $componentAttributes['id'];
            $field->is_required = $isRequired ?? false;
            $field->type = $componentAttributes['id'];
            $field->config = $config;
            $field->save();

            $components[$componentKey]['attrs']['config']['fieldId'] = $field->getKey();
        }

        return $components;
    }

    /** @return array<string, mixed> */
    private function decodeContent(mixed $content): array
    {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        return is_array($content) ? $content : [];
    }
}
