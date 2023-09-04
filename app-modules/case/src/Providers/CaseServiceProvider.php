<?php

namespace Assist\Case\Providers;

use Filament\Panel;
use Assist\Case\CasePlugin;
use Assist\Case\Models\ServiceRequest;
use Illuminate\Support\ServiceProvider;
use Assist\Case\Models\ServiceRequestType;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Case\Observers\CaseUpdateObserver;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Observers\ServiceRequestObserver;
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
            'case_item_priority' => ServiceRequestPriority::class,
            'case_item_status' => ServiceRequestStatus::class,
            'case_item_type' => ServiceRequestType::class,
            'case_update' => ServiceRequestUpdate::class,
        ]);

        $this->observers();
    }

    protected function observers(): void
    {
        ServiceRequest::observe(ServiceRequestObserver::class);
        ServiceRequestUpdate::observe(CaseUpdateObserver::class);
    }
}
