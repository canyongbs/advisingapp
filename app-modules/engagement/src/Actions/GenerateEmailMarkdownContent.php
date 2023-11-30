<?php

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
