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

namespace AdvisingApp\ResourceHub\Actions;

class GenerateTableOfContents
{
    /**
     * @param  array<string, mixed>|null  $content
     */
    public static function execute(?array $content, int $maxDepth = 3): string
    {
        if (blank($content) || ! isset($content['content'])) {
            return '';
        }

        $headings = static::extractHeadings($content['content'], $maxDepth);

        if (empty($headings)) {
            return '';
        }

        $result = '<ul>';
        $prev = $headings[0]['level'];

        foreach ($headings as $item) {
            $prev <= $item['level'] ?: $result .= str_repeat('</ul>', $prev - $item['level']);
            $prev >= $item['level'] ?: $result .= '<ul>';

            $result .= '<li><a href="#' . $item['id'] . '">' . e($item['text']) . '</a></li>';

            $prev = $item['level'];
        }

        $result .= '</ul>';

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     *
     * @return array<int, array{level: int, id: string, text: string}>
     */
    protected static function extractHeadings(array $nodes, int $maxDepth): array
    {
        $headings = [];

        foreach ($nodes as $node) {
            if (($node['type'] ?? null) === 'heading') {
                $level = $node['attrs']['level'] ?? 1;

                if ($level <= $maxDepth) {
                    /** @var array<int, array<string, mixed>> $children */
                    $children = $node['content'] ?? [];

                    $text = collect($children)
                        ->map(fn (array $node): ?string => $node['text'] ?? null)
                        ->implode(' ');

                    $id = $node['attrs']['id'] ?? str($text)->kebab()->toString();

                    $headings[] = [
                        'level' => $level,
                        'id' => $id,
                        'text' => $text,
                    ];
                }
            }

            if (! empty($node['content'])) {
                $headings = [...$headings, ...static::extractHeadings($node['content'], $maxDepth)];
            }
        }

        return $headings;
    }
}
