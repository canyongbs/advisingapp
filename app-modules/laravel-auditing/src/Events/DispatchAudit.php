<?php

namespace Assist\Auditing\Events;

use Assist\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Events\Dispatchable;

class DispatchAudit
{
    use Dispatchable;

    /**
     * The Auditable model.
     *
     * @var Auditable
     */
    public $model;

    /**
     * Create a new DispatchAudit event instance.
     *
     * @param Auditable $model
     */
    public function __construct(Auditable $model)
    {
        $this->model = $model;
    }
}
