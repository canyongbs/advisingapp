<?php

namespace Assist\LaravelAuditing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Assist\LaravelAuditing\Contracts\AuditDriver auditDriver(\Assist\LaravelAuditing\Contracts\Auditable $model);
 * @method static void execute(\Assist\LaravelAuditing\Contracts\Auditable $model);
 */
class Auditor extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Assist\LaravelAuditing\Contracts\Auditor::class;
    }
}
