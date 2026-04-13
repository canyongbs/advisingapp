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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('resource_hub_articles')
            ->whereNotNull('article_details')
            ->eachById(function (object $record) {
                $body = json_decode($record->article_details, associative: true);

                if (! is_array($body)) {
                    return;
                }

                $changed = false;

                $this->processNodes($body, $changed);

                if (! $changed) {
                    return;
                }

                DB::table('resource_hub_articles')
                    ->where('id', $record->id)
                    ->update(['article_details' => json_encode($body)]);
            }, 100);
    }

    public function down(): void {}

    /**
     * @param  array<string, mixed>  $node
     */
    protected function processNodes(array &$node, bool &$changed): void
    {
        $type = $node['type'] ?? null;

        match ($type) {
            'grid' => $this->transformGrid($node, $changed),
            'hurdle' => $this->removeNode($node, $changed),
            'youtube' => $this->transformYoutube($node, $changed),
            'vimeo' => $this->transformVimeo($node, $changed),
            'video' => $this->transformVideo($node, $changed),
            'image' => $this->transformImage($node, $changed),
            'gridBuilder' => $this->transformGridBuilder($node, $changed),
            'checkedList' => $this->transformCheckedList($node, $changed),
            'tiptapBlock' => $this->removeNode($node, $changed),
            default => null,
        };

        if (isset($node['marks']) && is_array($node['marks'])) {
            $this->processMarks($node['marks'], $changed);
        }

        if (isset($node['content']) && is_array($node['content'])) {
            // Unwrap nodes marked for removal: splice their children into the parent
            $node['content'] = $this->unwrapRemovedNodes($node['content']);

            foreach ($node['content'] as &$child) {
                $this->processNodes($child, $changed);
            }

            // Clean up any nodes marked for removal during recursion
            $node['content'] = $this->unwrapRemovedNodes($node['content']);
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformGrid(array &$node, bool &$changed): void
    {
        $oldType = $node['attrs']['type'] ?? 'responsive';
        $oldCols = $node['attrs']['cols'] ?? '2';

        [$dataCols, $fromBreakpoint, $colSpans] = $this->mapGridType($oldType, $oldCols);

        $node['attrs'] = [
            'data-cols' => (string) $dataCols,
            'data-from-breakpoint' => $fromBreakpoint,
        ];

        $changed = true;

        if (! isset($node['content']) || ! is_array($node['content'])) {
            return;
        }

        foreach ($node['content'] as $index => &$child) {
            if (is_array($child) && ($child['type'] ?? null) === 'gridColumn') {
                $child['attrs'] = $child['attrs'] ?? [];
                $child['attrs']['data-col-span'] = (string) ($colSpans[$index] ?? 1);
            }
        }
    }

    /**
     * @return array{int, string, array<int>}
     */
    protected function mapGridType(string $type, string $cols): array
    {
        $fromBreakpoint = ($type === 'fixed') ? 'sm' : 'lg';

        return match ($type) {
            'asymetric-left-thirds' => [3, $fromBreakpoint, [2, 1]],
            'asymetric-right-thirds' => [3, $fromBreakpoint, [1, 2]],
            'asymetric-left-fourths' => [4, $fromBreakpoint, [3, 1]],
            'asymetric-right-fourths' => [4, $fromBreakpoint, [1, 3]],
            default => [(int) $cols, $fromBreakpoint, array_fill(0, (int) $cols, 1)],
        };
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function removeNode(array &$node, bool &$changed): void
    {
        $node['type'] = '__removed__';

        $changed = true;
    }

    /**
     * @param  array<int, array<string, mixed>>  $content
     *
     * @return array<int, array<string, mixed>>
     */
    protected function unwrapRemovedNodes(array $content): array
    {
        $result = [];

        foreach ($content as $child) {
            if (($child['type'] ?? null) === '__removed__' && ! empty($child['content'])) {
                foreach ($child['content'] as $grandchild) {
                    $result[] = $grandchild;
                }
            } elseif (($child['type'] ?? null) !== '__removed__') {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformYoutube(array &$node, bool &$changed): void
    {
        $node['type'] = 'videoEmbed';
        $src = $node['attrs']['src'] ?? '';

        $node['attrs'] = [
            'src' => $src,
            'type' => 'youtube',
            'width' => null,
            'height' => null,
        ];

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformVimeo(array &$node, bool &$changed): void
    {
        $node['type'] = 'videoEmbed';
        $src = $node['attrs']['src'] ?? '';

        $node['attrs'] = [
            'src' => $src,
            'type' => 'vimeo',
            'width' => null,
            'height' => null,
        ];

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformVideo(array &$node, bool &$changed): void
    {
        $node['type'] = 'videoEmbed';
        $src = $node['attrs']['src'] ?? '';

        $node['attrs'] = [
            'src' => $src,
            'type' => 'video',
            'width' => null,
            'height' => null,
        ];

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformImage(array &$node, bool &$changed): void
    {
        $width = $node['attrs']['width'] ?? null;

        if (is_numeric($width) && $width > 500) {
            $node['attrs']['width'] = null;
            $node['attrs']['height'] = null;
            $changed = true;
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformGridBuilder(array &$node, bool &$changed): void
    {
        // Convert gridBuilder to grid, gridBuilderColumn to gridColumn
        $node['type'] = 'grid';

        $oldCols = $node['attrs']['data-cols'] ?? $node['attrs']['cols'] ?? '2';
        $colCount = (int) $oldCols;
        $stackAt = $node['attrs']['data-stack-at'] ?? 'lg';

        $node['attrs'] = [
            'data-cols' => (string) $colCount,
            'data-from-breakpoint' => $stackAt,
        ];

        $changed = true;

        if (! isset($node['content']) || ! is_array($node['content'])) {
            return;
        }

        foreach ($node['content'] as $index => &$child) {
            if (is_array($child) && ($child['type'] ?? null) === 'gridBuilderColumn') {
                $child['type'] = 'gridColumn';
                $colSpan = $child['attrs']['data-col-span'] ?? $child['attrs']['span'] ?? 1;
                $child['attrs'] = [
                    'data-col-span' => (string) ($colSpan ?: 1),
                ];
            }
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformCheckedList(array &$node, bool &$changed): void
    {
        $node['type'] = 'bulletList';

        $changed = true;

        if (! isset($node['content']) || ! is_array($node['content'])) {
            return;
        }

        foreach ($node['content'] as &$child) {
            if (is_array($child) && in_array($child['type'] ?? null, ['checkedListItem', 'taskItem', 'listItem'])) {
                $child['type'] = 'listItem';

                // Remove the checked attribute
                unset($child['attrs']['checked']);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $marks
     */
    protected function processMarks(array &$marks, bool &$changed): void
    {
        foreach ($marks as &$mark) {
            if (($mark['type'] ?? null) === 'textStyle') {
                $mark['type'] = 'textColor';
                $mark['attrs'] = [
                    'data-color' => $mark['attrs']['color'] ?? null,
                ];
                $changed = true;
            }
        }
    }
};
