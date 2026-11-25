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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Repairs records duplicated before duplication remapped content field IDs, whose content still references the original record's fields.
 *
 * TODO: Cleanup Task (duplicated-content-field-ids): delete this class, and its test, along with the temporary migrations that use it.
 */
class RepairDuplicatedContentFieldIds
{
    /**
     * @return int The number of custom blocks repaired.
     */
    public function __invoke(Submissible $submissible): int
    {
        $fields = $submissible->fields()->get()->map(function (Model $field): SubmissibleField {
            assert($field instanceof SubmissibleField);

            return $field;
        });

        $steps = $submissible->steps()->get()->map(function (Model $step): SubmissibleStep {
            assert($step instanceof SubmissibleStep);

            return $step;
        });

        $ownFieldIds = $fields->map(fn (SubmissibleField $field): string => $field->getKey())->all();

        $claimedFieldIds = collect([$submissible->content, ...$steps->pluck('content')])
            ->flatMap(fn (?array $content): array => $this->getReferencedFieldIds($content ?? []))
            ->intersect($ownFieldIds)
            ->all();

        $unclaimedFields = $fields
            ->reject(fn (SubmissibleField $field): bool => in_array($field->getKey(), $claimedFieldIds, strict: true))
            ->values();

        $repairedCount = $this->repairRecordContent($submissible, null, $ownFieldIds, $unclaimedFields);

        foreach ($steps as $step) {
            $repairedCount += $this->repairRecordContent($step, $step->getKey(), $ownFieldIds, $unclaimedFields);
        }

        return $repairedCount;
    }

    /**
     * @param array<int, string> $ownFieldIds
     * @param Collection<int, SubmissibleField> $unclaimedFields
     */
    private function repairRecordContent(Submissible | SubmissibleStep $record, ?string $stepId, array $ownFieldIds, Collection $unclaimedFields): int
    {
        $content = $record->content;

        if (blank($content)) {
            return 0;
        }

        $repairedCount = 0;

        $content = $this->repairNode($content, $record, $stepId, $ownFieldIds, $unclaimedFields, $repairedCount);

        if ($repairedCount === 0) {
            return 0;
        }

        $record->content = $content;

        Model::withoutTimestamps(fn (): bool => $record->saveQuietly());

        return $repairedCount;
    }

    /**
     * @param array<string, mixed> $node
     * @param array<int, string> $ownFieldIds
     * @param Collection<int, SubmissibleField> $unclaimedFields
     *
     * @return array<string, mixed>
     */
    private function repairNode(array $node, Submissible | SubmissibleStep $record, ?string $stepId, array $ownFieldIds, Collection $unclaimedFields, int &$repairedCount): array
    {
        $fieldId = $node['attrs']['config']['fieldId'] ?? null;

        if ((($node['type'] ?? null) === 'customBlock') && filled($fieldId) && (! in_array($fieldId, $ownFieldIds, strict: true))) {
            $matchingFieldKey = $unclaimedFields->search(fn (SubmissibleField $field): bool => $this->fieldMatchesBlock($field, $node, $stepId));

            if ($matchingFieldKey === false) {
                Log::warning('Could not repair a duplicated content field reference because no matching field was found.', [
                    'record_type' => $record::class,
                    'record_id' => $record->getKey(),
                    'field_id' => $fieldId,
                ]);
            } else {
                $node['attrs']['config']['fieldId'] = $unclaimedFields->pull($matchingFieldKey)->getKey();
                $repairedCount++;
            }
        }

        if (is_array($node['content'] ?? null)) {
            foreach ($node['content'] as $childKey => $child) {
                if (is_array($child)) {
                    $node['content'][$childKey] = $this->repairNode($child, $record, $stepId, $ownFieldIds, $unclaimedFields, $repairedCount);
                }
            }
        }

        return $node;
    }

    /**
     * Mirrors how SaveSubmissibleFieldsFromContent derives a field from its block.
     *
     * @param array<string, mixed> $block
     */
    private function fieldMatchesBlock(SubmissibleField $field, array $block, ?string $stepId): bool
    {
        $type = $block['attrs']['id'] ?? null;
        $config = $block['attrs']['config'] ?? [];

        $label = $config['label'] ?? $type;
        $isRequired = (bool) ($config['isRequired'] ?? false);

        unset($config['fieldId'], $config['label'], $config['isRequired']);

        return ($field->getAttribute('step_id') === $stepId)
            && ($field->type === $type)
            && ($field->label === $label)
            && ((bool) $field->is_required === $isRequired)
            && (($field->config ?? []) == $config);
    }

    /**
     * @param array<string, mixed> $node
     *
     * @return array<int, string>
     */
    private function getReferencedFieldIds(array $node): array
    {
        $fieldId = $node['attrs']['config']['fieldId'] ?? null;

        $fieldIds = ((($node['type'] ?? null) === 'customBlock') && is_string($fieldId)) ? [$fieldId] : [];

        foreach ($node['content'] ?? [] as $child) {
            if (is_array($child)) {
                $fieldIds = [...$fieldIds, ...$this->getReferencedFieldIds($child)];
            }
        }

        return $fieldIds;
    }
}
