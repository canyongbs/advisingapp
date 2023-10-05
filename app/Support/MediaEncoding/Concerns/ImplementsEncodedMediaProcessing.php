<?php

namespace App\Support\MediaEncoding\Concerns;

use Illuminate\Database\Eloquent\Model;
use App\Support\MediaEncoding\TiptapMediaEncoder;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

trait ImplementsEncodedMediaProcessing
{
    public function convertPathShortcodesToIdShortcodes(Model $model, array $attributes): void
    {
        $saveModel = false;

        foreach ($attributes as $attribute) {
            // Check to see if a media collection for this attribute exists on the model
            if ($model->getRegisteredMediaCollections()->filter(fn (MediaCollection $collection) => $collection->name === $attribute)->isEmpty()) {
                continue;
            }

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

        $storedMediaItems = collect();

        foreach ($attributes as $attribute) {
            $mediaItemsInContent = $mediaItemsInContent->merge(TiptapMediaEncoder::getMediaItemsInContent($model->{$attribute}));

            $storedMediaItems = $storedMediaItems->merge($model->getMedia($attribute)->collect());
        }

        $mediaItemsToDelete = $storedMediaItems->filter(fn ($storedMediaItem) => ! $mediaItemsInContent->contains('id', $storedMediaItem->id));

        $mediaItemsToDelete->each(fn ($mediaItem) => $mediaItem->delete());
    }
}
