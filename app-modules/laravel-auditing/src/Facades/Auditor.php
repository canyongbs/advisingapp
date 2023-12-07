<?php

namespace Assist\Auditing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Assist\Auditing\Contracts\AuditDriver auditDriver(\Assist\Auditing\Contracts\Auditable $model);
 * @method static void execute(\Assist\Auditing\Contracts\Auditable $model);
 */
class Auditor extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Assist\Auditing\Contracts\Auditor::class;
    }
}
