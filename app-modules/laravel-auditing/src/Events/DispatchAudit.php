<?php

namespace Assist\LaravelAuditing\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Assist\LaravelAuditing\Contracts\Auditable;

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
