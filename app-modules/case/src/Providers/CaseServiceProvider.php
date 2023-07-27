<?php

namespace Assist\Case\Providers;

use Filament\Panel;
use Assist\Case\CasePlugin;
use Assist\Case\Models\CaseItem;
use Assist\Case\Models\CaseItemType;
use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Models\CaseUpdateItem;
use Illuminate\Support\ServiceProvider;
use Assist\Case\Models\CaseItemPriority;
use Illuminate\Database\Eloquent\Relations\Relation;

class CaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new CasePlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'case_item' => CaseItem::class,
            'case_item_priority' => CaseItemPriority::class,
            'case_item_status' => CaseItemStatus::class,
            'case_item_type' => CaseItemType::class,
            'case_update_item' => CaseUpdateItem::class,
        ]);
    }
}
