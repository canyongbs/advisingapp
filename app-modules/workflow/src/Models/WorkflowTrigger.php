<?php

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class WorkflowTrigger extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    /**
     * @return HasOne<Workflow, $this>
     */
    public function workflow(): HasOne
    {
        return $this->hasOne(Workflow::class);
    }
}
