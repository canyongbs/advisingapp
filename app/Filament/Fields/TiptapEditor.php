<?php

namespace App\Filament\Fields;

use Illuminate\Support\Facades\Storage;
use FilamentTiptapEditor\TiptapEditor as BaseTiptapEditor;

class TiptapEditor extends BaseTiptapEditor
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BaseTiptapEditor $component, string | array | null $state) {
            if (! $state) {
                $component->state('<p></p>');
            }

            $state = $this->decodeMediaInState($component, $state);

            $component->state($state);

            $component->state($component->getHTML());
        });

        $this->dehydrateStateUsing(function (BaseTiptapEditor $component, string | array | null $state) {
            $state = $this->encodeMediaInState($component, $state);

            $component->state($state);

            if ($state && $this->expectsJSON()) {
                return $component->getJSON();
            }

            if ($state && $this->expectsText()) {
                return $component->getText();
            }

            return $state;
        });
    }

    protected function decodeMediaInState(BaseTiptapEditor $component, string | array | null $state): string | array | null
    {
        $regex = '/{{media\|path:([^}]*)}}/';

        preg_match($regex, $state, $matches, PREG_OFFSET_CAPTURE);

        if (! empty($matches)) {
            $path = $matches[1][0];

            $temporaryUrl = Storage::disk($component->getDisk())->temporaryUrl($path, now()->addMinutes(5));

            $urlString = $matches[0][0];

            $state = str_replace($urlString, $temporaryUrl, $state);
        }

        return $state;
    }

    protected function encodeMediaInState(BaseTiptapEditor $component, string | array | null $state): string | array | null
    {
        $diskConfig = Storage::disk($component->getDisk())->getConfig();

        $bucket = isset($diskConfig['bucket']) ? "\/{$diskConfig['bucket']}" : null;

        $regex = "/<img.*src=\"?'?(https?:\/\/[^\/]*{$bucket}(\/[^?]*)\??[^\"']*(?=\"?'?))/";

        preg_match($regex, $state, $matches, PREG_OFFSET_CAPTURE);

        if (! empty($matches)) {
            $path = $matches[2][0];

            $urlString = $matches[1][0];

            $state = str_replace($urlString, "{{media|path:{$path}}}", $state);
        }

        return $state;
    }
}
