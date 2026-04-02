<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Form\Models;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\BaseModel;
use Closure;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperFormEmailAutoReply
 */
class FormEmailAutoReply extends BaseModel implements HasMedia, HasRichContent
{
    use SoftDeletes;
    use InteractsWithMedia;
    use InteractsWithRichContent;

    protected $fillable = [
        'subject',
        'body',
        'is_enabled',
    ];

    protected $casts = [
        'body' => 'array',
        'is_enabled' => 'boolean',
        'subject' => 'array',
    ];

    /**
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function getBody(Student|Prospect|null $author): string
    {
        return $this->getRichContentAttribute('body')
            ?->mergeTags($this->getMergeData($author))
            ->toHtml() ?? '';
    }

    public function getSubject(Student|Prospect|null $author): string
    {
        $html = $this->getRichContentAttribute('subject')
            ?->mergeTags($this->getMergeData($author))
            ->toHtml() ?? '';

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * @return array<string, Closure>
     */
    public function getMergeData(Student|Prospect|null $author): array
    {
        return [
            'recipient first name' => fn () => $author?->getAttribute($author->displayFirstNameKey()),
            'recipient last name' => fn () => $author?->getAttribute($author->displayLastNameKey()),
            'recipient full name' => fn () => $author?->getAttribute($author->displayNameKey()),
            'recipient email' => fn () => $author?->primaryEmailAddress?->address,
            'recipient preferred name' => fn () => $author?->getAttribute($author->displayPreferredNameKey()),
        ];
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('subject');

        $this->registerRichContent('body')
            ->fileAttachmentsDisk('s3-public')
            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make());
    }
}
