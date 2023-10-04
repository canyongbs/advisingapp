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

            $regex = "/<img.*?src=\"?'?(https?:\/\/[^\/]*{$bucket}(\/[^?]*)\??[^\"']*(?=\"?'?))/";

            preg_match_all($regex, $state, $matches, PREG_SET_ORDER);

            if (! empty($matches)) {
                foreach ($matches as $match) {
                    $path = $match[2];

                    $urlString = $match[1];

                    $state = str_replace($urlString, "{{media|path:{$path};disk:{$disk};}}", $state);
                }
            }
        }

        return $state;
    }

    public static function decode(string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $regex = '/{{media\|path:([^}]*);disk:([^}]*);}}/';

            preg_match_all($regex, $state, $matches, PREG_SET_ORDER);

            if (! empty($matches)) {
                foreach ($matches as $match) {
                    $path = $match[1];
                    $disk = $match[2];

                    $temporaryUrl = Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5));

                    $urlString = $match[0];

                    $state = str_replace($urlString, $temporaryUrl, $state);
                }
            }
        }

        return $state;
    }
}
