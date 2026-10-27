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

namespace AdvisingApp\MeetingCenter\Models;

use AdvisingApp\MeetingCenter\Enums\EmailAutoReplyDeliveryPreference;
use App\Models\BaseModel;
use App\Models\Media;
use Closure;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperEventRegistrationFormEmailAutoReply
 */
class EventRegistrationFormEmailAutoReply extends BaseModel implements HasMedia, HasRichContent
{
    use SoftDeletes;

    /** @use InteractsWithMedia<Media> */
    use InteractsWithMedia;

    use InteractsWithRichContent;

    protected $fillable = [
        'subject',
        'body',
        'is_enabled',
        'delivery_preference',
    ];

    protected $attributes = [
        'is_enabled' => false,
        'delivery_preference' => EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation->value,
    ];

    protected $casts = [
        'body' => 'array',
        'delivery_preference' => EmailAutoReplyDeliveryPreference::class,
        'is_enabled' => 'boolean',
        'subject' => 'array',
    ];

    /**
     * @return BelongsTo<EventRegistrationForm, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(EventRegistrationForm::class, 'form_id');
    }

    public function getBody(?EventAttendee $attendee): string
    {
        return $this->getRichContentAttribute('body')
            ?->mergeTags($this->getMergeData($attendee))
            ->toHtml() ?? '';
    }

    public function getSubject(?EventAttendee $attendee): string
    {
        $html = $this->getRichContentAttribute('subject')
            ?->mergeTags($this->getMergeData($attendee))
            ->toHtml() ?? '';

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * Event attendees only have an email address, so it is the only recipient information available.
     *
     * @return array<string, Closure>
     */
    public function getMergeData(?EventAttendee $attendee): array
    {
        return [
            'recipient email' => fn () => $attendee?->email,
        ];
    }

    public function setUpRichContent(): void
    {
        $mergeTags = [
            'recipient email' => '{{ recipient email }}',
        ];

        $this->registerRichContent('subject')
            ->mergeTags($mergeTags);

        $this->registerRichContent('body')
            ->fileAttachmentsDisk('s3-public')
            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make())
            ->mergeTags($mergeTags);
    }
}
