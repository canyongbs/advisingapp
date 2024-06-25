<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Support\MediaEncoding;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;

class TiptapMediaEncoder
{
    public static function encode(string $disk, string | array | null $state): array|string|null
    {
        return self::processContent($state, [self::class, 'getEncodedContent'], $disk);
    }

    public static function decode(string | array | null $state): array|string|null
    {
        return self::processContent($state, [self::class, 'getDecodedContent']);
    }

    public static function processContent(string | array | null $state, callable $processFunction, string $disk = null): array|string|null
    {
        if (blank($state)) {
            return $state;
        }

        if (isset($state['content'])) {
            $stateContent = collect($state['content'])->map(function (array $content) use ($processFunction, $disk) {
                return $processFunction($content, $disk);
            })->toArray();

            $state['content'] = $stateContent;
        }

        return $state;
    }

    public static function getEncodedContent(array $content, string $disk): array
    {
        return self::processContentItem($content, [self::class, 'encodeContent'], $disk);
    }

    public static function getDecodedContent(array $content): array
    {
        return self::processContentItem($content, [self::class, 'decodeContent']);
    }

    public static function processContentItem(array $content, callable $processFunction, string $disk = null): array
    {
        if (isset($content['type']) && $content['type'] === 'image') {
            $content['attrs']['src'] = $processFunction($content['attrs']['src'], $disk);

            if (isset($content['marks'])) {
                foreach ($content['marks'] as $key => $mark) {
                    if (isset($mark['attrs']['href'])) {
                        $content['marks'][$key]['attrs']['href'] = $processFunction($content['marks'][$key]['attrs']['href'], $disk);
                    }
                }
            }

            return $content;
        }

        if (isset($content['type']) && $content['type'] === 'link') {
            $content['attrs']['href'] = $processFunction($content['attrs']['href'], $disk);

            return $content;
        }

        if (is_array($content)) {
            $content = collect($content)->map(function ($item) use ($processFunction, $disk) {
                if (is_array($item)) {
                    return self::processContentItem($item, $processFunction, $disk);
                }

                return $item;
            })->toArray();
        }

        return $content;
    }

    public static function encodeContent(string $content, string $disk): string
    {
        $parsedFilesystemUrl = preg_quote(Storage::disk($disk)->url(''), '/');

        $isFromFilesystem = preg_match(
            pattern: "/{$parsedFilesystemUrl}.+/",
            subject: $content,
        ) === 1;

        $path = parse_url($content, PHP_URL_PATH);

        if ($isFromFilesystem && ! is_null($path)) {
            $defaultDirectory = config('filament-tiptap-editor.directory');

            $root = preg_quote(config('filesystems.disks.s3.root'), '/');

            $regexMatch = preg_match(
                pattern: "/\/?{$root}\/?(.+)/",
                subject: $path,
                matches: $matches,
                flags: PREG_OFFSET_CAPTURE,
                offset: 0
            );

            if (
                $regexMatch === 1
                && Storage::disk($disk)->exists($matches[1][0])
            ) {
                $pathWithoutRoot = $matches[1][0];

                return Str::contains($path, $defaultDirectory)
                    ? "{{media|path:{$pathWithoutRoot};disk:{$disk};}}"
                    : self::encodeExistingMedia($path);
            }
        }

        return $content;
    }

    public static function encodeExistingMedia(string $path): string
    {
        $root = preg_quote(config('filesystems.disks.s3.root'), '/');

        $match = preg_match(
            pattern: "/\/?{$root}\/([0-9]+)\/([^?]+)/",
            subject: $path,
            matches: $matches,
            flags: PREG_OFFSET_CAPTURE,
            offset: 0
        );

        if ($match === 1) {
            $id = $matches[1][0] ?? null;
            $fileName = $matches[2][0] ?? null;

            $media = Media::query()
                ->where(
                    [
                        [DB::raw('id::VARCHAR'), '=', $id],
                        ['file_name', '=', $fileName],
                    ]
                )->first();

            if (! is_null($media)) {
                $path = "{{media|id:{$id};}}";
            }
        }

        return $path;
    }

    public static function decodeContent(string $state): string
    {
        $state = self::decodeMediaIds($state);

        $state = self::decodePaths($state);

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

        if (is_null($content)) {
            return false;
        }

        preg_match_all($regex, json_encode($content, JSON_UNESCAPED_SLASHES), $matches, PREG_SET_ORDER);

        if (! empty($matches)) {
            foreach ($matches as $match) {
                $shortcode = $match[0];
                $path = $match[1];
                $disk = $match[2];

                $storedMedia = $model->addMediaFromDisk($path, $disk)->toMediaCollection($attribute);

                $content = str_replace($shortcode, "{{media|id:{$storedMedia->id};}}", json_encode($content, JSON_UNESCAPED_SLASHES));
            }

            $model->{$attribute} = json_decode($content);

            return true;
        }

        return false;
    }

    public static function getMediaItemsInContent(?array $content): Collection
    {
        $regex = '/{{media\|id:([^};]*);?}}/';

        preg_match_all($regex, json_encode($content, JSON_UNESCAPED_SLASHES), $matches, PREG_SET_ORDER);

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

    public static function copyMediaItemsToModel(array $content, HasMedia $model, string $collection): array
    {
        if (isset($content['type']) && $content['type'] === 'image') {
            $content['attrs']['src'] = self::replicateMediaItem($content['attrs']['src'], $model, $collection);

            if (isset($content['marks'])) {
                foreach ($content['marks'] as $key => $mark) {
                    if (isset($mark['attrs']['href'])) {
                        $content['marks'][$key]['attrs']['href'] = self::replicateMediaItem($content['marks'][$key]['attrs']['href'], $model, $collection);
                    }
                }
            }

            return $content;
        }

        if (isset($content['type']) && $content['type'] === 'link') {
            $content['attrs']['href'] = self::replicateMediaItem($content['attrs']['href'], $model, $collection);

            return $content;
        }

        if (is_array($content)) {
            $content = collect($content)->map(function ($item) use ($model, $collection) {
                if (is_array($item)) {
                    return self::copyMediaItemsToModel($item, $model, $collection);
                }

                return $item;
            })->toArray();
        }

        return $content;
    }

    public static function replicateMediaItem(string $state, HasMedia $model, string $collection): string
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

                $replicatedMedia = $media->copy(
                    model: $model,
                    collectionName: $collection
                );

                $replicatedMediaKey = $replicatedMedia->getKey();

                $state = str_replace($shortcode, "{{media|id:{$replicatedMediaKey};}}", $state);
            }
        }

        return $state;
    }
}
