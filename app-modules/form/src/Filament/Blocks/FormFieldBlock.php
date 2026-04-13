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
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

abstract class FormFieldBlock extends RichContentCustomBlock
{
    public const MAPPED_STUDENT_FIELD_HELP_TEXT = 'This data is synchronized from your college\'s student information system. To update this data, please update your information in the source system and wait 24 hours for it to be reflected here.';

    public const MAPPED_PROSPECT_FIELD_HELP_TEXT = 'This field has been pre-populated with the information we have on file. Please feel free to update it and we will update our records accordingly.';

    public static function getId(): string
    {
        return static::type();
    }

    public static function getLabel(): string
    {
        return (string) str(static::type())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            Hidden::make('fieldId'),
            TextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255),
            TextInput::make('description')
                ->label('Field Description')
                ->string()
                ->maxLength(255),
            Checkbox::make('isRequired')
                ->label('Required'),
            ...static::fields(),
        ]);
    }

    public static function getPreviewLabel(array $config): string
    {
        return $config['label'] ?? static::getLabel();
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return view(static::previewView(), $config)->render();
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return view(static::renderedView(), $config)->render();
    }

    public static function fields(): array
    {
        return [];
    }

    abstract public static function type(): string;

    abstract public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array;

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getNestedValidationRules(SubmissibleField $field): array
    {
        return [];
    }

    public static function getSubmissionState(SubmissibleField $field, mixed $response): array
    {
        return [
            'field' => $field,
            'response' => $response,
        ];
    }

    protected static function previewView(): string
    {
        return 'form::blocks.previews.default';
    }

    protected static function renderedView(): string
    {
        return 'form::blocks.submissions.default';
    }

    /**
     * @return array<string, mixed>
     */
    protected static function getDescriptionSectionsSchema(
        SubmissibleField $field,
        string $sectionKey = 'label'
    ): array {
        if (empty($field->config['description'])) {
            return [];
        }

        return [
            'sectionsSchema' => [
                $sectionKey => [
                    'children' => [
                        '$label',
                        [
                            '$el' => 'div',
                            'attrs' => [
                                'class' => 'text-xs text-gray-500 mt-1 font-normal',
                            ],
                            'children' => $field->config['description'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
