<?php

namespace Assist\Application\Models;

use Assist\Form\Models\Submission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperApplicationSubmission
 */
class ApplicationSubmission extends Submission
{
    public function submissible(): BelongsTo
    {
        return $this
            ->belongsTo(Application::class, 'application_id');
    }

    public function fields(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                ApplicationField::class,
                'application_field_submission',
                'submission_id',
                'field_id'
            )
            ->withPivot(['id', 'response']);
    }
}
