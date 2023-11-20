<?php

namespace Assist\KnowledgeBase\Observers;

use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Support\MediaEncoding\Concerns\ImplementsEncodedMediaProcessing;

class KnowledgeBaseItemObserver
{
    use ImplementsEncodedMediaProcessing;

    public function saved(KnowledgeBaseItem $knowledgeBaseItem): void
    {
        $this->convertPathShortcodesToIdShortcodes($knowledgeBaseItem, ['solution', 'notes']);

        $this->cleanupMediaItems($knowledgeBaseItem, ['solution', 'notes']);
    }
}
