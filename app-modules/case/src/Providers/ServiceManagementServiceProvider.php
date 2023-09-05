<?php

namespace Assist\Case\Providers;

use Filament\Panel;
use Assist\Case\Models\ServiceRequest;
use Illuminate\Support\ServiceProvider;
use Assist\Case\ServiceManagementPlugin;
use Assist\Case\Models\ServiceRequestType;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Observers\ServiceRequestObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Case\Observers\ServiceRequestUpdateObserver;

class ServiceManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ServiceManagementPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'service_request' => ServiceRequest::class,
            'service_request_priority' => ServiceRequestPriority::class,
            'service_request_status' => ServiceRequestStatus::class,
            'service_request_type' => ServiceRequestType::class,
            'service_request_update' => ServiceRequestUpdate::class,
        ]);

        $this->observers();
    }

    protected function observers(): void
    {
        ServiceRequest::observe(ServiceRequestObserver::class);
        ServiceRequestUpdate::observe(ServiceRequestUpdateObserver::class);
    }
}
