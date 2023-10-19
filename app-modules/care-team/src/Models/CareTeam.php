<?php

namespace Assist\CareTeam\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;

class CareTeam extends BaseModel
{
    /** @return MorphTo<Educatable> */
    public function educatable(): MorphTo
    {
        return $this->morphTo();
    }
}
