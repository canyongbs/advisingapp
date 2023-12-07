<?php

namespace Assist\Auditing\Events;

use Assist\Auditing\Contracts\Auditable;
use Assist\Auditing\Contracts\AuditDriver;

class Auditing
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
     * Create a new Auditing event instance.
     *
     * @param \Assist\Auditing\Contracts\Auditable   $model
     * @param \Assist\Auditing\Contracts\AuditDriver $driver
     */
    public function __construct(Auditable $model, AuditDriver $driver)
    {
        $this->model = $model;
        $this->driver = $driver;
    }
}
