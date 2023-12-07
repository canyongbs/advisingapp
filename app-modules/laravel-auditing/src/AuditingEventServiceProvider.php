<?php

namespace Assist\Auditing;

class_alias(\Illuminate\Foundation\Support\Providers\EventServiceProvider::class, '\Assist\Auditing\ServiceProvider');

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
