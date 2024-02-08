<?php

namespace AdvisingApp\InAppCommunication\Actions;

use App\Models\User;

class ConvertMessageJsonToText
{
    public function __invoke(array $content, bool $isRoot = true): string
    {
        $text = '';

        foreach (($isRoot ? [$content] : $content) as $component) {
            if (($component['type'] ?? null) === 'text') {
                $text .= ' ' . trim($component['text'] ?? '');

                continue;
            }

            if (($component['type'] ?? null) === 'mention') {
                $text .= ' @' . User::find($component['attrs']['id'])?->name;

                continue;
            }

            $text .= $this($component['content'] ?? [], isRoot: false);
        }

        return $text;
    }
}
