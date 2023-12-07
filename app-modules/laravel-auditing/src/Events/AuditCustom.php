<?php

namespace Assist\Auditing\Events;

use Assist\Auditing\Contracts\Auditable;

class AuditCustom
{
    /**
     * The Auditable model.
     *
     * @var \Assist\Auditing\Contracts\Auditable
     */
    public $model;

    /**
     * Create a new Auditing event instance.
     *
     * @param \Assist\Auditing\Contracts\Auditable $model
     */
    public function __construct(Auditable $model)
    {
        $this->model = $model;
    }
}
