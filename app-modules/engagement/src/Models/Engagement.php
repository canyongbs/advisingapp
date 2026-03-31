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

namespace AdvisingApp\Engagement\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Engagement\Models\Concerns\EngagementFileAttachmentProvider;
use AdvisingApp\Engagement\Models\Contracts\HasDeliveryMethod;
use AdvisingApp\Engagement\Observers\EngagementObserver;
use AdvisingApp\Notification\Enums\EmailType;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Concerns\BelongsToEducatable;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\Timeline\Models\Timeline;
use AdvisingApp\Timeline\Timelines\EngagementTimeline;
use App\Models\BaseModel;
use App\Models\User;
use CanyonGBS\Common\Parser\Parser;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Symfony\Component\Mime\Email;

/**
 * @property-read ?Educatable $recipient
 *
 * @mixin IdeHelperEngagement
 */
#[ObservedBy([EngagementObserver::class])]
class Engagement extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline, HasDeliveryMethod, HasMedia, HasRichContent
{
    use AuditableTrait;
    use BelongsToEducatable;
    use InteractsWithMedia;
    use InteractsWithRichContent;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'engagement_batch_id',
        'subject',
        'body',
        'recipient_id',
        'recipient_type',
        'recipient_route',
        'scheduled_at',
        'dispatched_at',
        'channel',
        'source_id',
        'source_type',
        'dispatch_failed_at',
        'email_type',
    ];

    protected $casts = [
        'body' => 'array',
        'scheduled_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'channel' => NotificationChannel::class,
        'subject' => 'array',
        'dispatch_failed_at' => 'datetime',
        'email_type' => EmailType::class,
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
        return $forModel->orderedEngagements()->with(['latestEmailMessage', 'latestSmsMessage', 'batch'])->get();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->user();
    }

    /**
     * @return MorphMany<EmailMessage, $this>
     */
    public function emailMessages(): MorphMany
    {
        return $this->morphMany(
            related: EmailMessage::class,
            name: 'related',
            type: 'related_type',
            id: 'related_id',
            localKey: 'id',
        );
    }

    public function latestEmailMessage(): MorphOne
    {
        return $this->morphOne(EmailMessage::class, 'related')->latestOfMany();
    }

    /**
     * @return MorphMany<SmsMessage, $this>
     */
    public function smsMessages(): MorphMany
    {
        return $this->morphMany(
            related: SmsMessage::class,
            name: 'related',
            type: 'related_type',
            id: 'related_id',
            localKey: 'id',
        );
    }

    public function latestSmsMessage(): MorphOne
    {
        return $this->morphOne(SmsMessage::class, 'related')->latestOfMany();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<EngagementBatch, $this>
     */
    public function engagementBatch(): BelongsTo
    {
        return $this->belongsTo(EngagementBatch::class);
    }

    public function batch(): BelongsTo
    {
        return $this->engagementBatch();
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

    public function getSubscribable(): ?Subscribable
    {
        return $this->recipient instanceof Subscribable ? $this->recipient : null;
    }

    public function getBody(): HtmlString
    {
        if ($this->batch) {
            $attribute = $this->batch->getRichContentAttribute('body')?->mergeTags($this->getMergeData());

            return new HtmlString($attribute?->toHtml() ?? '');
        }

        $attribute = $this->getRichContentAttribute('body')?->mergeTags($this->getMergeData());

        return new HtmlString($attribute?->toHtml() ?? '');
    }

    public function getBodyText(): string
    {
        if ($this->batch) {
            $text = $this->batch->getRichContentAttribute('body')?->mergeTags($this->getMergeData())?->toText() ?? '';
        } else {
            $text = $this->getRichContentAttribute('body')?->mergeTags($this->getMergeData())?->toText() ?? '';
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

    public function getSubject(): ?HtmlString
    {
        if ($this->batch) {
            $attribute = $this->batch->getRichContentAttribute('subject')?->mergeTags($this->getMergeData());
        } else {
            $attribute = $this->getRichContentAttribute('subject')?->mergeTags($this->getMergeData());
        }

        $subject = $attribute?->toText() ?? '';
        $subject = html_entity_decode($subject, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $subject = trim(preg_replace('/\s+/u', ' ', $subject) ?? '');

        return $subject ? new HtmlString(Str::limit($subject, 988, '')) : null;
    }

    public function getBodyMarkdown(): string
    {
        return stripslashes((new HtmlConverter())->convert($this->getBody()));
    }

    public function getSubjectMarkdown(): ?string
    {
        return $this->getSubject() ? stripslashes((new HtmlConverter())->convert($this->getSubject())) : null;
    }

    public function getMergeData(): array
    {
        return [
            'recipient first name' => fn () => $this->recipient?->getAttribute($this->recipient->displayFirstNameKey()), /** @phpstan-ignore method.notFound */
            'recipient last name' => fn () => $this->recipient?->getAttribute($this->recipient->displayLastNameKey()), /** @phpstan-ignore method.notFound */
            'recipient full name' => fn () => $this->recipient?->getAttribute($this->recipient->displayNameKey()),
            'recipient email' => fn () => $this->recipient?->primaryEmailAddress?->address,
            'recipient preferred name' => fn () => $this->recipient?->getAttribute($this->recipient->displayPreferredNameKey()), /** @phpstan-ignore method.notFound */
            'user first name' => fn () => $this->user ? (new Parser())->parse($this->user->name)->getFirstname() : null,
            'user full name' => fn () => $this->user?->name,
            'user job title' => fn () => $this->user?->job_title,
            'user email' => fn () => $this->user?->email,
            'user phone number' => fn () => $this->user?->phone_number,
        ];
    }

    public static function getMergeTags(bool $withUserTags = true): array
    {
        $tags = array_keys((new self())->getMergeData());

        if (! $withUserTags) {
            $tags = array_values(array_filter($tags, fn (string $tag) => ! str_starts_with($tag, 'user ')));
        }

        return $tags;
    }

    public function getDeliveryMethod(): NotificationChannel
    {
        return $this->channel;
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('subject')
            ->mergeTags($this->getMergeData());

        $this->registerRichContent('body')
            ->fileAttachmentsDisk('s3-public')
            ->fileAttachmentProvider(EngagementFileAttachmentProvider::make())
            ->fileAttachmentsVisibility('public')
            ->mergeTags($this->getMergeData());
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('recipient'));
        });
    }
}
