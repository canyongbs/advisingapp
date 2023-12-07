<?php

namespace Assist\LaravelAuditing\Events;

use Assist\LaravelAuditing\Contracts\Auditable;
use Assist\LaravelAuditing\Contracts\AuditDriver;

class Auditing
{
    /**
     * The Auditable model.
     *
     * @var \Assist\LaravelAuditing\Contracts\Auditable
     */
    public $model;

    /**
     * Audit driver.
     *
     * @var \Assist\LaravelAuditing\Contracts\AuditDriver
     */
    public $driver;

    /**
     * Create a new Auditing event instance.
     *
     * @param \Assist\LaravelAuditing\Contracts\Auditable   $model
     * @param \Assist\LaravelAuditing\Contracts\AuditDriver $driver
     */
    public function __construct(Auditable $model, AuditDriver $driver)
    {
        $this->model = $model;
        $this->driver = $driver;
    }
}
