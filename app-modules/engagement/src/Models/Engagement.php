<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Engagement\Actions\GenerateEmailMarkdownContent;
use Illuminate\Support\Collection;
use Assist\Prospect\Models\Prospect;
use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\AssistDataModel\Models\Student;
use Assist\Timeline\Timelines\EngagementTimeline;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Educatable $recipient
 *
 * @mixin IdeHelperEngagement
 */
class Engagement extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline
{
    use AuditableTrait;

    protected $fillable = [
        'user_id',
        'engagement_batch_id',
        'subject',
        'body',
        'body_json',
        'recipient_id',
        'recipient_type',
        'scheduled',
        'deliver_at',
    ];

    protected $casts = [
        'body_json' => 'array',
        'deliver_at' => 'datetime',
        'scheduled' => 'boolean',
    ];

    // TODO Consider changing this relationship if we ever needed to timeline something else where records might be shared across entities
    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): EngagementTimeline
    {
        return new EngagementTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedEngagements()->with(['deliverables', 'batch'])->get();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->user();
    }

    public function engagementDeliverables(): HasMany
    {
        return $this->hasMany(EngagementDeliverable::class);
    }

    public function deliverables(): HasMany
    {
        return $this->engagementDeliverables();
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }

    public function engagementBatch(): BelongsTo
    {
        return $this->belongsTo(EngagementBatch::class);
    }

    public function batch(): BelongsTo
    {
        return $this->engagementBatch();
    }

    public function scopeIsScheduled(Builder $query): void
    {
        $query->where('scheduled', true);
    }

    public function scopeHasBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverables', function (Builder $query) {
            $query->whereNull('delivered_at');
        });
    }

    public function scopeHasNotBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverables', function (Builder $query) {
            $query->whereNotNull('delivered_at');
        });
    }

    public function scopeIsNotPartOfABatch(Builder $query): void
    {
        $query->whereNull('engagement_batch_id');
    }

    public function scopeSentToStudent(Builder $query): void
    {
        $query->where('recipient_type', resolve(Student::class)->getMorphClass());
    }

    public function scopeSentToProspect(Builder $query): void
    {
        $query->where('recipient_type', resolve(Prospect::class)->getMorphClass());
    }

    public function hasBeenDelivered(): bool
    {
        return (bool) $this->deliverables->filter(fn (EngagementDeliverable $deliverable) => $deliverable->hasBeenDelivered())->count() > 0;
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->recipient instanceof Subscribable ? $this->recipient : null;
    }

    public function getBody(): string
    {
        if (blank($this->body_json)) {
            return $this->body;
        }

        return app(GenerateEmailMarkdownContent::class)(
            [$this->body_json],
            $this->getMergeData(),
        );
    }

    public function getMergeData(): array
    {
        return [
            'student full name' => $this->recipient->getAttribute($this->recipient->displayNameKey()),
            'student email' => $this->recipient->getAttribute($this->recipient->displayEmailKey()),
        ];
    }
}
