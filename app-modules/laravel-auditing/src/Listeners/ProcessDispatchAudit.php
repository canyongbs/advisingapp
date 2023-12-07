<?php

namespace Assist\LaravelAuditing\Listeners;

use Illuminate\Support\Facades\Config;
use Assist\LaravelAuditing\Facades\Auditor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\LaravelAuditing\Events\DispatchAudit;

class ProcessDispatchAudit implements ShouldQueue
{
    public function viaConnection(): string
    {
        return Config::get('audit.queue.connection', 'sync');
    }

    public function viaQueue(): string
    {
        return Config::get('audit.queue.queue', 'default');
    }

    public function withDelay(DispatchAudit $event): int
    {
        return Config::get('audit.queue.delay', 0);
    }

    public function handle(DispatchAudit $event)
    {
        Auditor::execute($event->model);
    }
}
