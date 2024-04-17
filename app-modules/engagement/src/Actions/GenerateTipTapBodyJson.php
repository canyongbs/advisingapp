<?php

namespace AdvisingApp\Engagement\Actions;

class GenerateTipTapBodyJson
{
    public function __invoke(string $body, array $mergeData = []): array
    {
        return tiptap_converter()
            ->asJSON([
                'type' => 'doc',
                'content' => str($body)
                    ->explode("\n")
                    ->map(fn (string $line): array => $this->paragraph($line, $mergeData))
                    ->filter(fn (array $component) => ! empty($component['content']))
                    ->values()
                    ->toArray(),
            ], true);
    }

    private function paragraph(string $line, array $mergeData = []): array
    {
        preg_match_all('/{{[\s\S]*?}}|(\S+\s*)/', $line, $tokens);

        return [
            'type' => 'paragraph',
            'content' => collect($tokens[0])
                ->map(
                    fn ($token) => in_array($token, $mergeData)
                    ? $this->mergeTag($token)
                    : $this->text($token)
                )
                ->toArray(),
        ];
    }

    private function mergeTag(string $token): array
    {
        return [
            'type' => 'mergeTag',
            'attrs' => [
                'id' => str($token)->remove(['{{', '}}'])->trim()->toString(),
            ],
        ];
    }

    private function text(string $text): array
    {
        return [
            'type' => 'text',
            'text' => $text,
        ];
    }
}
