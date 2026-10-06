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
use Illuminate\Support\Arr;

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
        return $action
            ->slideOver()
            ->schema([
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

    /**
     * The rich editor header always shows the block's type (e.g. "Text input"),
     * never the user-configured field label, so it isn't duplicated with the
     * label already rendered inside the block's own preview below it.
     */
    public static function getPreviewLabel(array $config): string
    {
        return static::getLabel();
    }

    public static function toPreviewHtml(array $config): ?string
    {
        // Preview blades reference $label and $isRequired directly, so guarantee they
        // exist even when the block is previewed before its config has been filled
        // (e.g. dragging a new block in). getLabel() supplies the block default.
        $config['label'] ??= static::getLabel();
        $config['isRequired'] ??= false;

        return view(static::previewView(), $config)->render();
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return view(static::renderedView(), $config)->render();
    }

    /**
     * @return array<int, mixed>
     */
    public static function fields(): array
    {
        return [];
    }

    /**
     * Resolves a submitted value to a single option value, preferring an exact match over a case-insensitive one.
     *
     * @param array<int, int|string> $optionValues
     */
    public static function resolveOptionValue(array $optionValues, string $response): int | string | null
    {
        $matchers = [
            fn (string $value): bool => $value === $response,
            fn (string $value): bool => strcasecmp($value, $response) === 0,
        ];

        foreach ($matchers as $matches) {
            foreach ($optionValues as $optionValue) {
                if ($matches((string) $optionValue)) {
                    return $optionValue;
                }
            }
        }

        return null;
    }

    /**
     * Normalizes stored options to a value => label map. Options are rows when their entries are arrays; sequential
     * keys alone do not make a map a list of rows, because a map keyed 0, 1 is a valid set of option values.
     *
     * @param array<int|string, mixed> $options
     *
     * @return array<int|string, string>
     */
    public static function getOptionLabels(array $options): array
    {
        if (is_array(Arr::first($options))) {
            return array_column($options, 'label', 'value');
        }

        return $options;
    }

    /**
     * @param array<int|string, string> $options
     */
    public static function getOptionLabel(array $options, string $response): ?string
    {
        $optionValue = static::resolveOptionValue(array_keys($options), $response);

        return $optionValue === null ? null : $options[$optionValue];
    }

    abstract public static function type(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array;

    /**
     * @return array<int, string>
     */
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

    /**
     * @return array<string, mixed>
     */
    public static function getSubmissionState(SubmissibleField $field, mixed $response): array
    {
        return [
            'field' => $field,
            'response' => $response,
        ];
    }

    /**
     * Sent to FormKit as a list because JavaScript reorders integer-like keys of an object, which would lose the editor's sort order.
     *
     * @return array<int|string, mixed>
     */
    protected static function getFormKitOptions(SubmissibleField $field): array
    {
        $options = $field->config['options'];

        assert(is_array($options));

        if (isset($options[0]) && is_array($options[0])) {
            return $options;
        }

        return collect($options)
            ->map(fn (mixed $label, int|string $value): array => ['value' => (string) $value, 'label' => $label])
            ->values()
            ->all();
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
