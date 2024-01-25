<?php

namespace AdvisingApp\Form\Models;

use App\Models\BaseModel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Engagement\Actions\GenerateEmailMarkdownContent;

/**
 * @mixin IdeHelperFormEmailAutoReply
 */
class FormEmailAutoReply extends BaseModel
{
    protected $fillable = [
        'subject',
        'body',
        'is_enabled',
    ];

    protected $casts = [
        'body' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function getBody(Student|Prospect|null $author): string
    {
        return app(GenerateEmailMarkdownContent::class)(
            [$this->body],
            $this->getMergeData($author),
        );
    }

    public function getMergeData(Student|Prospect|null $author): array
    {
        return [
            'student full name' => $author->getAttribute($author->displayNameKey()),
            'student email' => $author->getAttribute($author->displayEmailKey()),
        ];
    }
}
