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

namespace AdvisingApp\Form\Filament\Blocks;

use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Storage;

class UploadFormFieldBlock extends FormFieldBlock
{
    public ?string $icon = 'heroicon-m-document-arrow-up';

    public string $rendered = 'form::blocks.submissions.upload';

    public static function type(): string
    {
        return 'upload';
    }

    /**
     * @return array<Component>
     */
    public function fields(): array
    {
        return [
            Checkbox::make('multiple')
                ->live(),
            TextInput::make('limit')
                ->numeric()
                ->minValue(1)
                ->maxValue(5)
                ->default(1)
                ->required()
                ->visible(fn (Get $get): bool => (bool) $get('multiple')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array
    {
        return [
            '$formkit' => 'upload',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'multiple' => $field->config['multiple'] ?? false,
            'accept' => static::getExtensionsFull(),
            'limit' => $field->config['limit'] ?? 1,
            'size' => $field->config['size'] ?? null,
            'uploadUrl' => route('widgets.forms.form-upload-url'),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        return [];
    }

    /**
     * @return array<int, string>
     */
    public static function getExtensionsFull(): array
    {
        return collect(static::defaultMimes())
            ->unique()
            ->sort()
            ->keys()
            ->values()
            ->toArray();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function defaultMimes(): array
    {
        return [
            'application/pdf' => ['pdf'],
            'application/vnd.ms-excel' => ['xls'],
            'application/vnd.ms-powerpoint' => ['ppt'],
            'application/vnd.ms-word' => ['doc'],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/pdf' => ['pdf'],
            'image/png' => ['png'],
            'text/csv' => ['csv'],
            'text/markdown' => ['md', 'markdown', 'mkd'],
            'text/plain' => ['txt', 'text'],
            'application/octet-stream' => ['log'],
            '.log' => ['log'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubmissionState(SubmissibleField $field, mixed $response): array
    {
        $media = (isset($field->pivot) && $field->pivot->hasMedia('files')) ? $field->pivot->getMedia('files')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->file_name,
            'temporary_url' => Storage::disk($media->disk)->temporaryUrl(
                $media->getPathRelativeToRoot(),
                now()->addDay(),
                ['ResponseContentDisposition' => 'attachment; filename="' . $media->file_name . '"']
            ),
        ])
            ->toArray() : [];

        return [
            ...parent::getSubmissionState($field, $response),
            'media' => $media,
        ];
    }
}
