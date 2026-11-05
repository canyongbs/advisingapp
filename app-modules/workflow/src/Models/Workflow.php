<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Workflow\Database\Factories\WorkflowFactory;
use App\Features\WorkflowNameCitextFeature;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperWorkflow
 */
class Workflow extends Model implements Auditable
{
    use UsesTenantConnection;
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    /** @use HasFactory<WorkflowFactory> */
    use HasFactory;

    protected $fillable = [
        'workflow_trigger_id',
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Build a workflow name that is unique case-insensitively among non-deleted workflows
     * belonging to the same owning record ($related), appending an incrementing "-2", "-3",
     * ... suffix when needed.
     *
     * Once the citext migration has run the name column is case-insensitively unique per
     * owner (ignoring soft-deleted rows), so fixed-name creation processes must resolve
     * collisions up front rather than hitting the unique index. Before the migration
     * activates the original base name is returned unchanged.
     */
    public static function generateUniqueName(string $baseName, Model $related): string
    {
        if (! WorkflowNameCitextFeature::active()) {
            return $baseName;
        }

        $name = $baseName;
        $counter = 2;

        while (
            static::query()
                ->where('related_type', $related->getMorphClass())
                ->where('related_id', $related->getKey())
                ->where('name', Str::lower($name))
                ->exists()
        ) {
            $name = "{$baseName}-{$counter}";
            $counter++;
        }

        return $name;
    }

    /**
     * @return BelongsTo<WorkflowTrigger, $this>
     */
    public function workflowTrigger(): BelongsTo
    {
        return $this->belongsTo(WorkflowTrigger::class);
    }

    /**
     * @return HasMany<WorkflowStep, $this>
     */
    public function workflowSteps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class);
    }

    /**
     * Keep the denormalized owner (related_type/related_id) in sync with the workflow's
     * trigger on creation. These columns exist only to scope the case-insensitive unique
     * index on `name` to the owning record, so a Form workflow and an Application workflow
     * may share a name while duplicates within the same owner are rejected.
     */
    protected static function booted(): void
    {
        static::creating(function (Workflow $workflow): void {
            // The related_type/related_id columns only exist once the citext migration has
            // run, and that migration activates the flag. Gating on it keeps pre-migration
            // inserts (which run before the columns exist) from writing to absent columns.
            if (! WorkflowNameCitextFeature::active()) {
                return;
            }

            if ($workflow->getAttribute('related_type') !== null || $workflow->getAttribute('related_id') !== null) {
                return;
            }

            $trigger = $workflow->workflowTrigger;

            if ($trigger === null) {
                return;
            }

            $workflow->setAttribute('related_type', $trigger->related_type);
            $workflow->setAttribute('related_id', $trigger->related_id);
        });
    }
}
