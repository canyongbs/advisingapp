<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Alert\Models;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Alert\Observers\AlertObserver;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Timeline\Models\Concerns\InteractsWithHistory;
use AdvisingApp\Timeline\Models\Contracts\HasHistory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property-read (Subscribable&(Student|Prospect))|null $concern
 *
 * @mixin IdeHelperAlert
 */
#[ObservedBy([AlertObserver::class])]
class Alert extends BaseModel implements Auditable, CanTriggerAutoSubscription, HasHistory
{
    use SoftDeletes;
    use AuditableTrait;
    use BelongsToEducatable;
    use InteractsWithHistory;

    protected $fillable = [
        'concern_id',
        'concern_type',
        'description',
        'severity',
        'suggested_intervention',
        'status_id',
    ];

    protected $casts = [
        'severity' => AlertSeverity::class,
    ];

    public function processCustomHistories(string $event, Collection $old, Collection $new, Collection $pending): void
    {
        if ($event !== 'updated') {
            return;
        }

        if ($new->has('status')) {
            $this->recordHistory('status_changed', $old->only('status'), $new->only('status'), $pending);
            $new->forget('status');
        }
    }

    /**
     * @return MorphMany<AlertHistory, $this>
     */
    public function histories(): MorphMany
    {
        return $this->morphMany(AlertHistory::class, 'subject');
    }

    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }

    /**
     * @return BelongsTo<AlertStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(AlertStatus::class, 'status_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('concern'));
        });
    }
}
