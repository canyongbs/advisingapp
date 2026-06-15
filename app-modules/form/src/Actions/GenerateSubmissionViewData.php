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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Form\Models\Submission;
use AdvisingApp\Form\Models\SubmissibleField;

class GenerateSubmissionViewData
{
    public function __invoke(Submission $submission): array
    {
        $submissible = $submission->submissible;
        $submission->loadMissing('fields');

        $authorEmail = $submission->author?->primaryEmailAddress?->address;

        $submittedAt = $submission->submitted_at ?? $submission->created_at;

        $fieldResponses = fn(iterable $fields): array => collect($fields)
            ->map(fn(SubmissibleField $field) => [
                'label' => $field->label,
                'type' => $field->type,
                'response' => $field->pivot?->response,
            ])
            ->values()
            ->all();

        if ($submissible->is_wizard) {
            $submissible->loadMissing('steps');

            $steps = $submissible->steps
                ->sortBy('sort')
                ->map(function ($step) use ($submission, $fieldResponses) {
                    $stepFieldIds = $step->fields()->pluck('id')->all();

                    $submittedFields = $submission->fields
                        ->filter(fn(SubmissibleField $field) => in_array($field->getKey(), $stepFieldIds));

                    return [
                        'label' => $step->label,
                        'fields' => $fieldResponses($submittedFields),
                    ];
                })
                ->values()
                ->all();

            return [
                'id' => $submission->getKey(),
                'submitted_at' => $submittedAt?->toIso8601String(),
                'author_email' => $authorEmail,
                'is_wizard' => true,
                'steps' => $steps,
            ];
        }

        return [
            'id' => $submission->getKey(),
            'submitted_at' => $submittedAt?->toIso8601String(),
            'author_email' => $authorEmail,
            'is_wizard' => false,
            'fields' => $fieldResponses($submission->fields),
        ];
    }
}
