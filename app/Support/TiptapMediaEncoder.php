<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use FilamentTiptapEditor\TiptapEditor as BaseTiptapEditor;

class TiptapMediaEncoder
{
    public static function encode(BaseTiptapEditor $component, string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $diskConfig = Storage::disk($component->getDisk())->getConfig();

            $bucket = isset($diskConfig['bucket']) ? "\/{$diskConfig['bucket']}" : null;

            $regex = "/<img.*src=\"?'?(https?:\/\/[^\/]*{$bucket}(\/[^?]*)\??[^\"']*(?=\"?'?))/";

            preg_match($regex, $state, $matches, PREG_OFFSET_CAPTURE);

            if (! empty($matches)) {
                $path = $matches[2][0];

                $urlString = $matches[1][0];

                $state = str_replace($urlString, "{{media|path:{$path}}}", $state);
            }
        }

        return $state;
    }

    public static function decode(BaseTiptapEditor $component, string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $regex = '/{{media\|path:([^}]*)}}/';

            preg_match($regex, $state, $matches, PREG_OFFSET_CAPTURE);

            if (! empty($matches)) {
                $path = $matches[1][0];

                $temporaryUrl = Storage::disk($component->getDisk())->temporaryUrl($path, now()->addMinutes(5));

                $urlString = $matches[0][0];

                $state = str_replace($urlString, $temporaryUrl, $state);
            }
        }

        return $state;
    }
}
