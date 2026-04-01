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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        $this->processTable('forms', 'content');
        $this->processTable('form_steps', 'content');
        $this->processTable('surveys', 'content');
        $this->processTable('survey_steps', 'content');
        $this->processTable('applications', 'content');
        $this->processTable('application_steps', 'content');
    }

    public function down(): void {}

    protected function processTable(string $table, string $column): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->eachById(function (object $record) use ($table, $column) {
                $content = json_decode($record->{$column}, associative: true);

                if (! is_array($content)) {
                    return;
                }

                $changed = false;

                $this->transformNode($content, $changed);

                if (! $changed) {
                    return;
                }

                DB::table($table)
                    ->where('id', $record->id)
                    ->update([$column => json_encode($content)]);
            }, 100);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformNode(array &$node, bool &$changed): void
    {
        // 1. Transform tiptapBlock -> customBlock
        if (($node['type'] ?? null) === 'tiptapBlock') {
            $oldAttrs = $node['attrs'] ?? [];

            $node['type'] = 'customBlock';
            $node['attrs'] = [
                'config' => [
                    'fieldId' => $oldAttrs['id'] ?? null,
                    ...($oldAttrs['data'] ?? []),
                ],
                'id' => $oldAttrs['type'] ?? null,
            ];

            $changed = true;
        }

        // 2. Transform grid attrs
        if (($node['type'] ?? null) === 'grid') {
            $oldType = $node['attrs']['type'] ?? 'responsive';
            $oldCols = $node['attrs']['cols'] ?? '2';

            [$dataCols, $fromBreakpoint, $colSpans] = $this->mapGridType($oldType, $oldCols);

            $node['attrs'] = [
                'data-cols' => (string) $dataCols,
                'data-from-breakpoint' => $fromBreakpoint,
            ];

            $changed = true;

            // Transform child gridColumns with correct spans
            if (isset($node['content']) && is_array($node['content'])) {
                foreach ($node['content'] as $index => &$child) {
                    if (is_array($child) && ($child['type'] ?? null) === 'gridColumn') {
                        $child['attrs'] = $child['attrs'] ?? [];
                        $child['attrs']['data-col-span'] = (string) ($colSpans[$index] ?? 1);
                    }

                    if (is_array($child)) {
                        $this->transformNode($child, $changed);
                    }
                }

                return;
            }
        }

        // 3. Fix oversized images
        if (($node['type'] ?? null) === 'image') {
            $width = $node['attrs']['width'] ?? null;

            if (is_numeric($width) && $width > 500) {
                $node['attrs']['width'] = null;
                $node['attrs']['height'] = null;
                $changed = true;
            }
        }

        // 4. Recurse into content
        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as &$child) {
                if (is_array($child)) {
                    $this->transformNode($child, $changed);
                }
            }
        }
    }

    /**
     * @return array{0: int, 1: string, 2: array<int, int>}
     */
    protected function mapGridType(string $oldType, string $oldCols): array
    {
        return match ($oldType) {
            'asymetric-left-thirds' => [3, 'lg', [1, 2]],
            'asymetric-right-thirds' => [3, 'lg', [2, 1]],
            'asymetric-left-fourths' => [4, 'lg', [1, 3]],
            'asymetric-right-fourths' => [4, 'lg', [3, 1]],
            'fixed' => [(int) $oldCols, 'default', array_fill(0, (int) $oldCols, 1)],
            'responsive' => [(int) $oldCols, 'lg', array_fill(0, (int) $oldCols, 1)],
            default => [(int) $oldCols, 'lg', array_fill(0, (int) $oldCols, 1)],
        };
    }
};
