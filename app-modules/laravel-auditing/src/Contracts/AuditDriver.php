<?php

namespace Assist\Auditing\Contracts;

interface AuditDriver
{
    /**
     * Perform an audit.
     *
     * @param \Assist\Auditing\Contracts\Auditable $model
     *
     * @return \Assist\Auditing\Contracts\Audit
     */
    public function audit(Auditable $model): ?Audit;

    /**
     * Remove older audits that go over the threshold.
     *
     * @param \Assist\Auditing\Contracts\Auditable $model
     *
     * @return bool
     */
    public function prune(Auditable $model): bool;
}
