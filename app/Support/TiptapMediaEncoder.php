<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class TiptapMediaEncoder
{
    public static function encode(string $disk, string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $diskConfig = Storage::disk($disk)->getConfig();

            $bucket = isset($diskConfig['bucket']) ? "\/{$diskConfig['bucket']}" : null;

            $regex = "/<img.*src=\"?'?(https?:\/\/[^\/]*{$bucket}(\/[^?]*)\??[^\"']*(?=\"?'?))/";

            preg_match($regex, $state, $matches, PREG_OFFSET_CAPTURE);

            if (! empty($matches)) {
                $path = $matches[2][0];

                $urlString = $matches[1][0];

                $state = str_replace($urlString, "{{media|path:{$path};disk:{$disk};}}", $state);
            }
        }

        return $state;
    }

    public static function decode(string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $regex = '/{{media\|path:([^}]*);disk:([^}]*);}}/';

            preg_match($regex, $state, $matches, PREG_OFFSET_CAPTURE);

            if (! empty($matches)) {
                $path = $matches[1][0];
                $disk = $matches[2][0];

                $temporaryUrl = Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5));

                $urlString = $matches[0][0];

                $state = str_replace($urlString, $temporaryUrl, $state);
            }
        }

        return $state;
    }
}
