<?php

namespace Assist\LaravelAuditing\Contracts;

interface AuditDriver
{
    /**
     * Perform an audit.
     *
     * @param \Assist\LaravelAuditing\Contracts\Auditable $model
     *
     * @return \Assist\LaravelAuditing\Contracts\Audit
     */
    public function audit(Auditable $model): ?Audit;

    /**
     * Remove older audits that go over the threshold.
     *
     * @param \Assist\LaravelAuditing\Contracts\Auditable $model
     *
     * @return bool
     */
    public function prune(Auditable $model): bool;
}
