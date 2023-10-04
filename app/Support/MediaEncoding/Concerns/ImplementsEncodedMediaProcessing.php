<?php

namespace App\Support\MediaEncoding\Concerns;

use Illuminate\Database\Eloquent\Model;
use App\Support\MediaEncoding\TiptapMediaEncoder;

trait ImplementsEncodedMediaProcessing
{
    public function convertPathShortcodesToIdShortcodes(Model $model, array $attributes): void
    {
        $saveModel = false;

        foreach ($attributes as $attribute) {
            $didConversions = TiptapMediaEncoder::convertPathShortcodeToIdShortcode($model, $attribute);

            if ($didConversions) {
                $saveModel = true;
            }
        }

        if ($saveModel) {
            $model::withoutEvents(fn () => $model->save());
        }
    }

    public function cleanupMediaItems(Model $model, array $attributes): void
    {
        $mediaItemsInContent = collect();

        foreach ($attributes as $attribute) {
            $mediaItemsInContent = $mediaItemsInContent->merge(TiptapMediaEncoder::getMediaItemsInContent($model->{$attribute}));
        }

        $storedMediaItems = $model->getMedia('media')->collect();

        $mediaItemsToDelete = $storedMediaItems->filter(fn ($storedMediaItem) => ! $mediaItemsInContent->contains('id', $storedMediaItem->id));

        $mediaItemsToDelete->each(fn ($mediaItem) => $mediaItem->delete());
    }
}
