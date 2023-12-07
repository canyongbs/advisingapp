<?php

namespace Assist\LaravelAuditing;

class_alias(\Illuminate\Foundation\Support\Providers\EventServiceProvider::class, '\Assist\LaravelAuditing\ServiceProvider');

use Assist\LaravelAuditing\Events\AuditCustom;
use Assist\LaravelAuditing\Events\DispatchAudit;
use Assist\LaravelAuditing\Listeners\RecordCustomAudit;
use Assist\LaravelAuditing\Listeners\ProcessDispatchAudit;

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
