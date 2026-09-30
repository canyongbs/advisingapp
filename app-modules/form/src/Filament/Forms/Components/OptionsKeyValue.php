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

namespace AdvisingApp\Form\Filament\Forms\Components;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlock;
use Closure;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\StateCasts\KeyValueStateCast;

/**
 * A "Label" / "Value" options editor for choice-style form fields (select,
 * radio, checkboxes). Rows are added, removed, and reordered entirely in the
 * browser, so those actions need no server round trips.
 *
 * The stored option config is a value => label map, but a KeyValue field's
 * "key" column is always rendered before its "value" column. To show the
 * editable Label before the disabled, auto-derived Value, the field is
 * edited internally as a label => value map, and flipped back to a
 * value => label map when hydrating from / dehydrating to storage.
 */
class OptionsKeyValue extends KeyValue
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->keyLabel('Label')
            ->valueLabel('Value')
            ->editableValues(false)
            ->reorderable()
            ->live(onBlur: true)
            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                FormFieldBlock::validateOptionValues(static::fillMissingValues(app(KeyValueStateCast::class)->get($value)), $fail);
            })
            ->afterStateHydrated(function (OptionsKeyValue $component, ?array $state): void {
                $component->state(static::fillMissingValues(array_flip($state ?? [])));
            })
            ->afterStateUpdated(function (OptionsKeyValue $component, ?array $state, ?array $old): void {
                $component->state(static::deriveValues($state ?? [], $old ?? []));
            })
            ->dehydrateStateUsing(fn (?array $state): array => array_flip(static::fillMissingValues($state ?? [])));
    }

    /**
     * Keeps the value of every row whose label is unchanged (so stable codes
     * that were persisted, e.g. "us" => "United States", are not rewritten by
     * unrelated edits) and re-derives it from the label for new or renamed
     * rows, whose value would otherwise be stale.
     *
     * @param array<int|string, mixed> $options label => value
     * @param array<int|string, mixed> $previous label => value, before the update
     *
     * @return array<string, string>
     */
    protected static function deriveValues(array $options, array $previous): array
    {
        return collect($options)
            ->mapWithKeys(fn (mixed $value, int|string $label): array => [
                (string) $label => (filled($value) && ($previous[$label] ?? null) === $value)
                    ? (string) $value
                    : FormFieldBlock::slugifyOptionValue((string) $label),
            ])
            ->all();
    }

    /**
     * Fills in a value, derived from the label, for options that have none.
     *
     * @param array<int|string, mixed> $options label => value
     *
     * @return array<string, string>
     */
    protected static function fillMissingValues(array $options): array
    {
        return collect($options)
            ->mapWithKeys(fn (mixed $value, int|string $label): array => [
                (string) $label => filled($value) ? (string) $value : FormFieldBlock::slugifyOptionValue((string) $label),
            ])
            ->all();
    }
}
