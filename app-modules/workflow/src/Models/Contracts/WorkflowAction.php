<?php

namespace AdvisingApp\Workflow\Models\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface WorkflowAction
{
    /**
     * @return BelongsTo<covariant Model, covariant Model>
     */
    public function workflowStep(): BelongsTo;
}
