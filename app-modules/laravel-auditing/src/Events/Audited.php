<?php

namespace Assist\LaravelAuditing\Events;

use Assist\LaravelAuditing\Contracts\Audit;
use Assist\LaravelAuditing\Contracts\Auditable;
use Assist\LaravelAuditing\Contracts\AuditDriver;

class Audited
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
     * The Audit model.
     *
     * @var \Assist\LaravelAuditing\Contracts\Audit|null
     */
    public $audit;

    /**
     * Create a new Audited event instance.
     *
     * @param \Assist\LaravelAuditing\Contracts\Auditable   $model
     * @param \Assist\LaravelAuditing\Contracts\AuditDriver $driver
     * @param \Assist\LaravelAuditing\Contracts\Audit       $audit
     */
    public function __construct(Auditable $model, AuditDriver $driver, Audit $audit = null)
    {
        $this->model = $model;
        $this->driver = $driver;
        $this->audit = $audit;
    }
}
