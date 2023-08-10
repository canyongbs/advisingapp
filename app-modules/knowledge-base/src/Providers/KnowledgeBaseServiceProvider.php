<?php

namespace Assist\KnowledgeBase\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\KnowledgeBase\KnowledgeBasePlugin;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

class KnowledgeBaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new KnowledgeBasePlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'knowledge_base_item' => KnowledgeBaseItem::class,
            'knowledge_base_category' => KnowledgeBaseCategory::class,
            'knowledge_base_quality' => KnowledgeBaseQuality::class,
            'knowledge_base_status' => KnowledgeBaseStatus::class,
        ]);
    }
}
