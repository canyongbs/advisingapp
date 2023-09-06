<?php

namespace Assist\ServiceManagement\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\ServiceManagement\ServiceManagementPlugin;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Observers\ServiceRequestObserver;
use Assist\ServiceManagement\Observers\ServiceRequestUpdateObserver;

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
