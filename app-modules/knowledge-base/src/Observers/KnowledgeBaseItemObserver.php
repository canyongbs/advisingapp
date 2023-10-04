<?php

namespace Assist\KnowledgeBase\Observers;

use Assist\KnowledgeBase\Models\KnowledgeBaseItem;

class KnowledgeBaseItemObserver
{
    public function saved(KnowledgeBaseItem $knowledgeBaseItem): void
    {
        $regex = '/{{media\|path:([^}]*);disk:([^}]*);}}/';

        preg_match_all($regex, $knowledgeBaseItem->solution, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $knowledgeBaseItem->addMediaFromDisk($match[1], $match[2])->toMediaCollection('media');
        }
    }
}
