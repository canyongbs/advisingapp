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
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;

class SelectFormFieldBlock extends FormFieldBlock
{
    public static function type(): string
    {
        return 'select';
    }

    public static function getLabel(): string
    {
        return 'Drop Down';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return parent::configureEditorAction($action)
            ->modalWidth(Width::TwoExtraLarge);
    }

    public static function fields(): array
    {
        return [
            static::optionsKeyValueField(),
        ];
    }

    public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array
    {
        return [
            '$formkit' => 'select',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'options' => $field->config['options'],
            ...self::getDescriptionSectionsSchema($field),
        ];
    }

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [
            'string',
            'in:' . static::normalizeOptions($field->config['options'])->keys()->join(','),
        ];
    }

    /**
     * The stored option config is always a value => label map (see
     * FormFieldBlock::normalizeOptions()), but a KeyValue field's "key"
     * column is always rendered before its "value" column. To show the
     * editable Label before the disabled, auto-derived Value, the field is
     * edited internally as a label => value map and flipped back to a
     * value => label map when hydrating from/dehydrating to storage.
     */
    protected static function optionsKeyValueField(string $name = 'options'): KeyValue
    {
        return KeyValue::make($name)
            ->keyLabel('Label')
            ->valueLabel('Value')
            ->editableValues(false)
            ->reorderable()
            ->live(onBlur: true)
            ->afterStateHydrated(function (Set $set, ?array $state) use ($name): void {
                $set($name, static::deriveOptionValuesFromLabels(array_flip($state ?? [])));
            })
            ->afterStateUpdated(function (Set $set, ?array $state) use ($name): void {
                $set($name, static::deriveOptionValuesFromLabels($state ?? []));
            })
            ->dehydrateStateUsing(fn (?array $state): array => array_flip(static::deriveOptionValuesFromLabels($state ?? [])));
    }

    /**
     * @param array<int|string, mixed> $labels
     *
     * @return array<string, string>
     */
    protected static function deriveOptionValuesFromLabels(array $labels): array
    {
        return collect($labels)
            ->keys()
            ->mapWithKeys(fn (int|string $label): array => [(string) $label => static::slugifyOptionValue((string) $label)])
            ->all();
    }

    protected static function renderedView(): string
    {
        return 'form::blocks.submissions.select';
    }
}
