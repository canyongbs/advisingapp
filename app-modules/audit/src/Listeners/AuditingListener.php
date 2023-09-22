<?php

namespace Assist\Audit\Listeners;

use OwenIt\Auditing\Events\Auditing;
use Illuminate\Database\Eloquent\Model;
use Assist\Audit\Settings\AuditSettings;

class AuditingListener
{
    public function handle(Auditing $event): bool
    {
        /** @var Model $model */
        $model = $event->model;

        return collect(resolve(AuditSettings::class)->audited_models)
            ->contains($model->getMorphClass());
    }
}
