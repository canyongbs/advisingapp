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

namespace AdvisingApp\Application\Actions;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationField;
use AdvisingApp\Application\Models\ApplicationStep;

class DuplicateApplication
{
    public function __construct(
        private Application $original,
        private Application $replica
    ) {}

    public function __invoke(): void
    {
        $stepMap = $this->replicateSteps();
        $fieldMap = $this->replicateFields($stepMap);
        $this->updateStepContent($fieldMap);
    }

    private function replicateSteps(): array
    {
        $stepMap = [];

        $this->original->steps()->each(function (ApplicationStep $step) use (&$stepMap) {
            $newStep = $step->replicate();
            $newStep->application_id = $this->replica->id;
            $newStep->save();

            $stepMap[$step->id] = $newStep->id;
        });

        return $stepMap;
    }

    private function replicateFields(array $stepMap): array
    {
        $fieldMap = [];

        $this->original->fields()->each(function (ApplicationField $field) use (&$fieldMap, $stepMap) {
            $newField = $field->replicate();
            $newField->application_id = $this->replica->id;
            $newField->step_id = $stepMap[$field->step_id] ?? null;
            $newField->save();

            $fieldMap[$field->id] = $newField->id;
        });

        return $fieldMap;
    }

    private function updateStepContent(array $fieldMap): void
    {
        $this->replica->steps()->each(function (ApplicationStep $step) use ($fieldMap) {
            $content = $step->content;

            $step->update([
                'content' => $this->replaceIdsInContent($content, $fieldMap),
            ]);
        });
    }

    private function replaceIdsInContent(&$content, $fieldMap)
    {
        if (is_array($content)) {
            foreach ($content as $key => &$value) {
                if (is_array($value)) {
                    $this->replaceIdsInContent($value, $fieldMap);
                } else {
                    if ($key === 'id' && isset($fieldMap[$value])) {
                        $value = $fieldMap[$value];
                    }
                }
            }
        }

        return $content;
    }
}
