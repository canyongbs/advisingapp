<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use League\HTMLToMarkdown\HtmlConverter;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Timeline\Timelines\EngagementTimeline;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Enums\EngagementDeliveryStatus;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Engagement\Models\Contracts\HasDeliveryMethod;
use AdvisingApp\Engagement\Actions\GenerateEngagementBodyContent;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Educatable $recipient
 *
 * @mixin IdeHelperEngagement
 */
class Engagement extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline, HasDeliveryMethod
{
    use AuditableTrait;
    use BelongsToEducatable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'engagement_batch_id',
        'subject',
        'body',
        'recipient_id',
        'recipient_type',
        'scheduled',
        'deliver_at',
    ];

    protected $casts = [
        'body' => 'array',
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
        return $forModel->orderedEngagements()->with(['deliverable', 'batch'])->get();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->user();
    }

    public function engagementDeliverable(): HasOne
    {
        return $this->hasOne(EngagementDeliverable::class);
    }

    public function deliverable(): HasOne
    {
        return $this->engagementDeliverable();
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

    public function scopeIsAwaitingDelivery(Builder $query): void
    {
        $query->whereHas('engagementDeliverable', function (Builder $query) {
            $query->where('delivery_status', EngagementDeliveryStatus::Awaiting);
        });
    }

    public function scopeHasBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverable', function (Builder $query) {
            $query->whereNull('delivered_at');
        });
    }

    public function scopeHasNotBeenDelivered(Builder $query): void
    {
        $query->whereDoesntHave('engagementDeliverable', function (Builder $query) {
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
        return $this->deliverable->hasBeenDelivered();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->recipient instanceof Subscribable ? $this->recipient : null;
    }

    public function getBody(): HtmlString
    {
        return app(GenerateEngagementBodyContent::class)(
            $this->body,
            $this->getMergeData(),
        );
    }

    public function getBodyMarkdown(): string
    {
        return stripslashes((new HtmlConverter())->convert($this->getBody()));
    }

    public function getMergeData(): array
    {
        return [
            'student first name' => $this->recipient->getAttribute($this->recipient->displayFirstNameKey()),
            'student last name' => $this->recipient->getAttribute($this->recipient->displayLastNameKey()),
            'student full name' => $this->recipient->getAttribute($this->recipient->displayNameKey()),
            'student email' => $this->recipient->getAttribute($this->recipient->displayEmailKey()),
        ];
    }

    /**
     * @param class-string $type
     */
    public static function getMergeTags(string $type): array
    {
        return match ($type) {
            Student::class => [
                'student first name',
                'student last name',
                'student full name',
                'student email',
            ],
            default => [],
        };
    }

    public function getDeliveryMethod(): EngagementDeliveryMethod
    {
        return $this->deliverable->channel;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('recipient'));
        });
    }
}
