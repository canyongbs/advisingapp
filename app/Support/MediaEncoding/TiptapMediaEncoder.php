<?php

namespace App\Support\MediaEncoding;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TiptapMediaEncoder
{
    public static function encode(string $disk, string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $state = self::encodeExistingMedia($state);

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

    public static function encodeExistingMedia(string $state): string
    {
        $regex = "/<img.*?src=\"?'?(https?:\/\/[^\/]*\/.*?\/([^?\"]*)\/([^?\"]*)\??[^\"']*(?=\"?'?))/";

        preg_match_all($regex, $state, $matches, PREG_SET_ORDER);

        if (! empty($matches)) {
            foreach ($matches as $match) {
                $urlString = $match[1];
                $id = $match[2];
                $fileName = $match[3];

                $media = Media::query()
                    ->where(
                        [
                            [DB::raw('id::VARCHAR'), '=', $id],
                            ['file_name', '=', $fileName],
                        ]
                    )->first();

                if (! $media) {
                    continue;
                }

                $state = str_replace($urlString, "{{media|id:{$id};}}", $state);
            }
        }

        return $state;
    }

    public static function decode(string | array | null $state): array|string|null
    {
        if (gettype($state) === 'string') {
            $state = self::decodeMediaIds($state);

            $state = self::decodePaths($state);
        }

        return $state;
    }

    public static function decodeMediaIds(string $state): string
    {
        $regex = '/{{media\|id:([^};]*);?}}/';

        preg_match_all($regex, $state, $matches, PREG_SET_ORDER);

        if (! empty($matches)) {
            foreach ($matches as $match) {
                $shortcode = $match[0];
                $mediaId = $match[1];

                /** @var Media $media */
                $media = Media::query()->find($mediaId);

                if (! $media) {
                    continue;
                }

                $state = str_replace($shortcode, $media->getTemporaryUrl(now()->addMinutes(5)), $state);
            }
        }

        return $state;
    }

    public static function decodePaths(string $state): string
    {
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

        return $state;
    }

    public static function convertPathShortcodeToIdShortcode(Model $model, string $attribute): bool
    {
        $content = $model->{$attribute};

        $regex = '/{{media\|path:([^}]*);disk:([^}]*);}}/';

        preg_match_all($regex, $content, $matches, PREG_SET_ORDER);

        if (! empty($matches)) {
            foreach ($matches as $match) {
                $shortcode = $match[0];
                $path = $match[1];
                $disk = $match[2];

                $storedMedia = $model->addMediaFromDisk($path, $disk)->toMediaCollection('media');

                $model->{$attribute} = str_replace($shortcode, "{{media|id:{$storedMedia->id};}}", $content);
            }

            return true;
        }

        return false;
    }

    public static function getMediaItemsInContent(string $content): Collection
    {
        $regex = '/{{media\|id:([^};]*);?}}/';

        preg_match_all($regex, $content, $matches, PREG_SET_ORDER);

        if (! empty($matches)) {
            $mediaIds = [];

            foreach ($matches as $match) {
                $mediaId = $match[1];

                $mediaIds[] = $mediaId;
            }

            return Media::query()->whereIn('id', $mediaIds)->get();
        }

        return collect();
    }
}
