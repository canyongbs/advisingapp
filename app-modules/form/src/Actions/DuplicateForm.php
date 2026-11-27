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

use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormStep;

class DuplicateForm
{
    public function __construct(
        private Form $original,
        private Form $replica,
        private RemapContentFieldIds $remapContentFieldIds,
    ) {}

    public function __invoke(): void
    {
        $stepMap = $this->replicateSteps();
        $fieldMap = $this->replicateFields($stepMap);
        $this->remapContentFieldIds($fieldMap);
        $this->replicateEmailAutoReply();
    }

    /**
     * @return array<string, string>
     */
    private function replicateSteps(): array
    {
        $stepMap = [];

        $this->original->steps()->each(function (FormStep $step) use (&$stepMap): void {
            $newStep = $step->replicate();
            $newStep->form_id = $this->replica->id;
            $newStep->save();

            $stepMap[$step->id] = $newStep->id;
        });

        return $stepMap;
    }

    /**
     * @param  array<string, string>  $stepMap
     *
     * @return array<string, string>
     */
    private function replicateFields(array $stepMap): array
    {
        $fieldMap = [];

        $this->original->fields()->each(function (FormField $field) use (&$fieldMap, $stepMap): void {
            if ((! $this->allowsMappedBlocks()) && in_array($field->type, FormFieldBlockRegistry::getMappedBlockTypes(), strict: true)) {
                return;
            }

            $newField = $field->replicate();
            $newField->form_id = $this->replica->id;
            $newField->step_id = $stepMap[$field->step_id] ?? null;
            $newField->save();

            $fieldMap[$field->id] = $newField->id;
        });

        return $fieldMap;
    }

    /**
     * @param  array<string, string>  $fieldMap
     */
    private function remapContentFieldIds(array $fieldMap): void
    {
        $this->replica->content = ($this->remapContentFieldIds)($this->removeDisallowedMappedBlocks($this->replica->content), $fieldMap);
        $this->replica->save();

        $this->replica->steps()->each(function (FormStep $step) use ($fieldMap): void {
            $step->update([
                'content' => ($this->remapContentFieldIds)($this->removeDisallowedMappedBlocks($step->content), $fieldMap),
            ]);
        });
    }

    /**
     * Mapped blocks are only available on forms that require authentication or generate prospects.
     */
    private function allowsMappedBlocks(): bool
    {
        return $this->replica->is_authenticated || $this->replica->generate_prospects;
    }

    /**
     * @param array<string, mixed>|null $content
     *
     * @return array<string, mixed>|null
     */
    private function removeDisallowedMappedBlocks(?array $content): ?array
    {
        if (blank($content) || $this->allowsMappedBlocks()) {
            return $content;
        }

        return $this->removeMappedBlocksFromNode($content, FormFieldBlockRegistry::getMappedBlockTypes());
    }

    /**
     * @param array<string, mixed> $node
     * @param array<int, string> $mappedBlockTypes
     *
     * @return array<string, mixed>
     */
    private function removeMappedBlocksFromNode(array $node, array $mappedBlockTypes): array
    {
        if (! is_array($node['content'] ?? null)) {
            return $node;
        }

        $node['content'] = collect($node['content'])
            ->reject(fn (mixed $child): bool => is_array($child)
                && (($child['type'] ?? null) === 'customBlock')
                && in_array($child['attrs']['id'] ?? null, $mappedBlockTypes, strict: true))
            ->map(fn (mixed $child): mixed => is_array($child) ? $this->removeMappedBlocksFromNode($child, $mappedBlockTypes) : $child)
            ->values()
            ->all();

        // A grid column must contain at least one block for the editor to load it.
        if ((($node['type'] ?? null) === 'gridColumn') && ($node['content'] === [])) {
            $node['content'] = [['type' => 'paragraph']];
        }

        return $node;
    }

    private function replicateEmailAutoReply(): void
    {
        $originalEmailAutoReply = $this->original->emailAutoReply;

        if (! $originalEmailAutoReply) {
            return;
        }

        $replicaEmailAutoReply = $this->replica->emailAutoReply()->updateOrCreate([], [
            'subject' => $originalEmailAutoReply->subject,
            'body' => $originalEmailAutoReply->body,
            'is_enabled' => $originalEmailAutoReply->is_enabled,
        ]);

        $mediaUuidMap = [];

        foreach ($originalEmailAutoReply->getMedia('body') as $media) {
            $mediaUuidMap[$media->uuid] = $media->copy($replicaEmailAutoReply, 'body', 's3-public')->uuid;
        }

        if ($mediaUuidMap === []) {
            return;
        }

        $replicaEmailAutoReply->body = $this->remapMediaUuids($replicaEmailAutoReply->body, $mediaUuidMap);
        $replicaEmailAutoReply->save();
    }

    /**
     * @param array<string, mixed>|null $content
     * @param array<string, string> $mediaUuidMap
     *
     * @return array<string, mixed>|null
     */
    private function remapMediaUuids(?array $content, array $mediaUuidMap): ?array
    {
        if (! $content) {
            return $content;
        }

        $json = json_encode($content);
        $json = str_replace(array_keys($mediaUuidMap), array_values($mediaUuidMap), $json);

        return json_decode($json, true);
    }
}
