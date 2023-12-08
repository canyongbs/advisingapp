<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\Engagement\Actions;

class GenerateEmailMarkdownContent
{
    public function __invoke(array $content, array $mergeData = [], string $markdown = ''): string
    {
        foreach ($content as $component) {
            $markdown .= match ($component['type']) {
                'bulletList', 'paragraph' => PHP_EOL . PHP_EOL . $this($component['content'] ?? [], $mergeData),
                'doc' => $this($component['content'], $mergeData),
                'heading' => PHP_EOL . PHP_EOL . str_repeat('#', $component['attrs']['level'] ?? 1) . ' ' . $this($component['content'] ?? [], $mergeData),
                'horizontalRule' => PHP_EOL . PHP_EOL . '---',
                'image' => ' ' . '![' . ($component['attrs']['alt'] ?? '') . '](' . ($component['attrs']['src'] ?? '') . ')',
                'listItem' => PHP_EOL . '- ' . $this($component['content'] ?? [], $mergeData),
                'mergeTag' => ' ' . $this->text($mergeData[$component['attrs']['id'] ?? null] ?? '', $component),
                'orderedList' => PHP_EOL . PHP_EOL . $this->orderedList($component, $mergeData),
                'text' => ' ' . $this->text($component['text'] ?? '', $component),
            };
        }

        return trim($markdown);
    }

    public function orderedList(array $component, array $mergeData): string
    {
        $markdown = '';

        $number = $component['attrs']['start'] ?? 1;

        foreach ($component['content'] ?? [] as $item) {
            $markdown .= PHP_EOL . $number . '. ' . $this($item['content'] ?? [], $mergeData);

            $number++;
        }

        return $markdown;
    }

    public function text(string $text, array $component): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        foreach ($component['marks'] ?? [] as $mark) {
            $text = match ($mark['type'] ?? null) {
                'bold' => "**{$text}**",
                'italic' => "*{$text}*",
                'link' => "[{$text}](" . ($mark['attrs']['href'] ?? '') . ')',
                'small' => "<small>{$text}</small>",
                default => $text,
            };
        }

        return $text;
    }
}
