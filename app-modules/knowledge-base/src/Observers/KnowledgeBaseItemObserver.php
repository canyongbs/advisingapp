<?php

namespace Assist\KnowledgeBase\Observers;

use App\Support\TiptapMediaEncoder;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;

class KnowledgeBaseItemObserver
{
    public function saved(KnowledgeBaseItem $knowledgeBaseItem): void
    {
        $solutionConvert = TiptapMediaEncoder::convertPathShortcodeToIdShortcode($knowledgeBaseItem, 'solution');

        $notesConvert = TiptapMediaEncoder::convertPathShortcodeToIdShortcode($knowledgeBaseItem, 'notes');

        if ($solutionConvert || $notesConvert) {
            KnowledgeBaseItem::withoutEvents(fn () => $knowledgeBaseItem->save());
        }

        $mediaItemsInContent = collect()
            ->merge(TiptapMediaEncoder::getMediaItemsInContent($knowledgeBaseItem->solution))
            ->merge(TiptapMediaEncoder::getMediaItemsInContent($knowledgeBaseItem->notes));

        $storedMediaItems = $knowledgeBaseItem->getMedia('media')->collect();

        $mediaItemsToDelete = $storedMediaItems->filter(fn ($storedMediaItem) => ! $mediaItemsInContent->contains('id', $storedMediaItem->id));

        $mediaItemsToDelete->each(fn ($mediaItem) => $mediaItem->delete());
    }
}
