<?php

namespace Assist\LaravelAuditing\Events;

use Assist\LaravelAuditing\Contracts\Auditable;

class AuditCustom
{
    /**
     * The Auditable model.
     *
     * @var \Assist\LaravelAuditing\Contracts\Auditable
     */
    public $model;

    /**
     * Create a new Auditing event instance.
     *
     * @param \Assist\LaravelAuditing\Contracts\Auditable $model
     */
    public function __construct(Auditable $model)
    {
        $this->model = $model;
    }
}
