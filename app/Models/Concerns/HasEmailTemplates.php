<?php

namespace App\Models\Concerns;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasEmailTemplates
{
    public function emailTemplate(): MorphOne
    {
        return $this->morphOne(EmailTemplate::class, 'related_to');
    }
}
