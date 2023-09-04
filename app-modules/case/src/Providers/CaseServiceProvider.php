<?php

namespace Assist\Case\Providers;

use Filament\Panel;
use Assist\Case\CasePlugin;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Models\CaseItemStatus;
use Illuminate\Support\ServiceProvider;
use Assist\Case\Models\CaseItemPriority;
use Assist\Case\Models\ServiceRequestType;
use Assist\Case\Observers\CaseItemObserver;
use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Case\Observers\CaseUpdateObserver;
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
            'case_item' => ServiceRequest::class,
            'case_item_priority' => CaseItemPriority::class,
            'case_item_status' => CaseItemStatus::class,
            'case_item_type' => ServiceRequestType::class,
            'case_update' => ServiceRequestUpdate::class,
        ]);

        $this->observers();
    }

    protected function observers(): void
    {
        ServiceRequest::observe(CaseItemObserver::class);
        ServiceRequestUpdate::observe(CaseUpdateObserver::class);
    }
}
