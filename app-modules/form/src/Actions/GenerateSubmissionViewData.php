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
use AdvisingApp\Form\Models\Submission;

class GenerateSubmissionViewData
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(Submission $submission, GenerateFormKitSchema $generateSchema): array
    {
        /** @var Submissible $submissible */
        $submissible = $submission->submissible;
        $submission->loadMissing('fields');

        $author = $submission->author;

        // @phpstan-ignore-next-line property.notFound
        $submittedAt = $submission->submitted_at ?? $submission->created_at;

        $responses = $this->buildResponseMap($submission);

        $schema = $generateSchema->withAuthor($author)($submissible);
        $schema = $this->disableSchema($schema, $responses);

        return [
            'id' => $submission->getKey(),
            'submitted_at' => $submittedAt?->toIso8601String(),
            'schema' => $schema,
        ];
    }

    /**
     * Build a map of field ID => response value from the submission.
     *
     * @return array<string, mixed>
     */
    protected function buildResponseMap(Submission $submission): array
    {
        $map = [];

        foreach ($submission->fields as $field) {
            /** @var SubmissibleField $field */
            // @phpstan-ignore-next-line property.notFound
            $response = $field->pivot?->response;

            $map[$field->getKey()] = $response;
        }

        return $map;
    }

    /**
     * Recursively walk the FormKit schema tree, disabling all fields
     * and injecting submission response values.
     *
     * @param array<string, mixed> $node
     * @param array<string, mixed> $responses
     *
     * @return array<string, mixed>
     */
    protected function disableSchema(array $node, array $responses): array
    {
        if (isset($node['$formkit'])) {
            $type = $node['$formkit'];

            if ($type === 'submit') {
                return [];
            }

            $node['disabled'] = true;
            unset($node['validation']);

            $name = $node['name'] ?? null;

            if ($name && isset($responses[$name])) {
                $value = $responses[$name];

                $node['value'] = is_array($value) ? ($value[0] ?? '') : $value;
            }
        }

        if (isset($node['children']) && is_array($node['children'])) {
            $node['children'] = array_values(array_filter(
                array_map(fn(array|string $child): array|string => is_array($child) ? $this->disableSchema($child, $responses) : $child, $node['children']),
                fn(array|string $child): bool => $child !== [],
            ));
        }

        if (isset($node['props']['children']) && is_array($node['props']['children'])) {
            $node['props']['children'] = array_values(array_filter(
                array_map(fn(array|string $child): array|string => is_array($child) ? $this->disableSchema($child, $responses) : $child, $node['props']['children']),
                fn(array|string $child): bool => $child !== [],
            ));
        }

        return $node;
    }
}
