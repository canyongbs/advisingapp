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
            $knowledgeBaseItem->save();
        }
    }
}
