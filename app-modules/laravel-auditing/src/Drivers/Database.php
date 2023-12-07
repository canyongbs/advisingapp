<?php

namespace Assist\LaravelAuditing\Drivers;

use Illuminate\Support\Facades\Config;
use Assist\LaravelAuditing\Contracts\Audit;
use Assist\LaravelAuditing\Contracts\Auditable;
use Assist\LaravelAuditing\Contracts\AuditDriver;

class Database implements AuditDriver
{
    /**
     * {@inheritdoc}
     */
    public function audit(Auditable $model): ?Audit
    {
        $implementation = Config::get('audit.implementation', \Assist\LaravelAuditing\Models\Audit::class);

        return call_user_func([$implementation, 'create'], $model->toAudit());
    }

    /**
     * {@inheritdoc}
     */
    public function prune(Auditable $model): bool
    {
        if (($threshold = $model->getAuditThreshold()) > 0) {
            $forRemoval = $model->audits() /** @phpstan-ignore-line */
                ->latest()
                ->get()
                ->slice($threshold)
                ->pluck('id');

            if (! $forRemoval->isEmpty()) {
                return $model->audits()
                    ->whereIn('id', $forRemoval)
                    ->delete() > 0;
            }
        }

        return false;
    }
}
