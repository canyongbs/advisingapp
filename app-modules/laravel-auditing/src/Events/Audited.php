<?php

namespace Assist\Auditing\Events;

use Assist\Auditing\Contracts\Audit;
use Assist\Auditing\Contracts\Auditable;
use Assist\Auditing\Contracts\AuditDriver;

class Audited
{
    /**
     * The Auditable model.
     *
     * @var \Assist\Auditing\Contracts\Auditable
     */
    public $model;

    /**
     * Audit driver.
     *
     * @var \Assist\Auditing\Contracts\AuditDriver
     */
    public $driver;

    /**
     * The Audit model.
     *
     * @var \Assist\Auditing\Contracts\Audit|null
     */
    public $audit;

    /**
     * Create a new Audited event instance.
     *
     * @param \Assist\Auditing\Contracts\Auditable   $model
     * @param \Assist\Auditing\Contracts\AuditDriver $driver
     * @param \Assist\Auditing\Contracts\Audit       $audit
     */
    public function __construct(Auditable $model, AuditDriver $driver, Audit $audit = null)
    {
        $this->model = $model;
        $this->driver = $driver;
        $this->audit = $audit;
    }
}
