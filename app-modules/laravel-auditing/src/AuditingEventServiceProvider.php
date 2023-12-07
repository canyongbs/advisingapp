<?php

namespace Assist\Auditing;

if (app() instanceof \Illuminate\Foundation\Application) {
    class_alias(\Illuminate\Foundation\Support\Providers\EventServiceProvider::class, '\Assist\Auditing\ServiceProvider');
} else {
    class_alias(\Laravel\Lumen\Providers\EventServiceProvider::class, '\Assist\Auditing\ServiceProvider');
}

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Assist\Auditing\Events\AuditCustom;
use Assist\Auditing\Events\DispatchAudit;
use Assist\Auditing\Listeners\RecordCustomAudit;
use Assist\Auditing\Listeners\ProcessDispatchAudit;

class AuditingEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AuditCustom::class => [
            RecordCustomAudit::class,
        ],
        DispatchAudit::class => [
            ProcessDispatchAudit::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
