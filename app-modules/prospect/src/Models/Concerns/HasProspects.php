<?php

namespace Assist\Prospect\Models\Concerns;

use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasProspects
{
    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }
}
