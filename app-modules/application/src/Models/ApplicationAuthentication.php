<?php

namespace Assist\Application\Models;

use App\Models\Attributes\NoPermissions;
use Assist\Form\Models\SubmissibleAuthentication;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[NoPermissions]
class ApplicationAuthentication extends SubmissibleAuthentication
{
    public function submissible(): BelongsTo
    {
        return $this
            ->belongsTo(Application::class, 'application_id');
    }
}
