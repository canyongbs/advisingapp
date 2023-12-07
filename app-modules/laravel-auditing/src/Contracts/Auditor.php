<?php

namespace Assist\Auditing\Contracts;

interface Auditor
{
    /**
     * Get an audit driver instance.
     *
     * @param \Assist\Auditing\Contracts\Auditable $model
     *
     * @return AuditDriver
     */
    public function auditDriver(Auditable $model): AuditDriver;

    /**
     * Perform an audit.
     *
     * @param \Assist\Auditing\Contracts\Auditable $model
     *
     * @return void
     */
    public function execute(Auditable $model);
}
