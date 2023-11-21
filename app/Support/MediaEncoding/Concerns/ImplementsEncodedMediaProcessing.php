<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
